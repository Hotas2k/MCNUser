<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Factory;

use MCNUser\Authentication\AuthenticationService;
use MCNUser\Authentication\PluginManager;
use MCNUser\Options\Authentication\AuthenticationOptions;
use MCNUser\Service\UserInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthenticationServiceFactory
 * @package MCNUser\Authentication
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create an instance of the authentication service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sl
     *
     * @throws Exception\LogicException on invalid configuration
     *
     * @return \MCNUser\Authentication\AuthenticationService|mixed
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        // Make sure configuration has been specified
        if (! isSet($sl->get('Config')['MCNUser']['authentication'])) {

            throw new Exception\RuntimeException('No configuration specified.');
        }

        $config = $sl->get('Config')['MCNUser']['authentication'];

        $options = new AuthenticationOptions($config);

        if (! $sl->has($options->getUserServiceSlKey())) {

            throw new Exception\InvalidArgumentException(
                sprintf('Unknown name/alias %s for user service implementation', $options->getUserServiceSlKey())
            );
        }

        $userService = $sl->get($options->getUserServiceSlKey());

        if (! $userService instanceof UserInterface) {

            throw new Exception\LogicException(
                sprintf(
                    'The user service implemented provided, class: %s, sl_key: %s ' .
                    'must implement MCNUser\Service\UserServiceInterface',
                    get_class($userService),
                    $options->getUserServiceSlKey()
                )
            );
        }

        $pluginManager = new PluginManager();

        /**
         * @var $alias         string
         * @var $pluginOptions \MCNUser\Options\Authentication\Plugin\Standard
         */
        foreach ($options->getPlugins() as $alias => $pluginOptions) {

            $className = $pluginOptions->getClassName();

            $plugin = new $className($pluginOptions);

            $pluginManager->setService($alias, $plugin);
        }

        return new AuthenticationService($userService, $options, $pluginManager);
    }
}
