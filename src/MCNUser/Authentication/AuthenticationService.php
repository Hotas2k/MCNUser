<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

use MCNUser\Service\UserInterface;
use Zend\Authentication\Storage\Session;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\Http\Request;
use Zend\Session\Storage\StorageInterface;
use MCNUser\Options\Authentication\AuthenticationOptions as Options;

/**
 * Class Authentication
 * @package MCNUser\Service
 */
class AuthenticationService implements EventsCapableInterface
{
    /**
     * @var \Zend\EventManager\EventManager
     */
    protected $evm;

    /**
     * @var \Zend\Authentication\Storage\Session
     */
    protected $storage;

    /**
     * @var \MCNUser\Options\Authentication\AuthenticationOptions
     */
    protected $options;

    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * @param \MCNUser\Service\UserInterface  $service
     * @param \MCNUser\Options\Authentication\AuthenticationOptions $options
     * @param PluginManager                   $pluginManager
     */
    public function __construct(UserInterface $service, Options $options = null, PluginManager $pluginManager = null)
    {
        $this->service       = $service;
        $this->storage       = new Session();
        $this->options       = ($options === null) ? new Options() : $options;
        $this->pluginManager = ($pluginManager === null) ? new PluginManager() : $pluginManager;
    }

    /**
     * Get the plugin manager
     *
     * @return \MCNUser\Authentication\PluginManager
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager()
    {
        if ($this->evm === null) {

            $this->evm = new EventManager();
            $this->evm->setEventClass('MCNUser\Authentication\AuthEvent');
        }

        return $this->evm;
    }

    /**
     * Get the options for authentication
     *
     * @return \MCNUser\Options\Authentication\AuthenticationOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Authenticate a request
     *
     * @see \MCNUser\Authentication\AuthEvent for available events to hook into
     *
     * @param \Zend\Http\Request $request
     * @param string             $plugin
     *
     * @throws Exception\DomainException If an attempt to authenticate via a unregistered plugin occurs
     *
     * @return Result
     */
    public function authenticate(Request $request, $plugin = 'standard')
    {
        if (! $this->getPluginManager()->has($plugin)) {

            throw new Exception\DomainException(
                sprintf('The plugin %s has not been registered with the plugin manager', $plugin)
            );
        }

        /**
         * @var $plugin \MCNUser\Authentication\Plugin\PluginInterface
         */
        $plugin = $this->getPluginManager()->get($plugin);

        $this->getEventManager()->trigger(__FUNCTION__.'.pre', null, compact('plugin', 'request'));

        $result = $plugin->authenticate($request, $this->service);

        if ($result->getCode() == Result::SUCCESS) {

            $response = $this->getEventManager()->trigger(
                AuthEvent::EVENT_AUTH_SUCCESS,
                $result->getIdentity(),
                compact('plugin', 'request')
            );

            if (! $response->stopped()) {

                $this->storage->write($result->getIdentity());

            } else {

                $result->setCode(Result::FAILURE_UNCATEGORIZED);
                $result->setMessage($response->last());
            }

        } else {

            $this->getEventManager()->trigger(AuthEvent::EVENT_AUTH_FAILURE, $result, compact('plugin', 'request'));
        }

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $result, compact('plugin', 'request'));

        return $result;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->storage->read();
    }

    /**
     * @return bool
     */
    public function hasIdentity()
    {
        return !$this->storage->isEmpty();
    }

    /**
     * Remove the current identity
     */
    public function clearIdentity()
    {
        $this->storage->clear();
    }
}
