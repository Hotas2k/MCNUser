<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Listener\Authentication;

use DateTime;
use MCNUser\Authentication\AuthEvent;
use MCNUser\Service\UserInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\PhpEnvironment\RemoteAddress;

/**
 * Class LastLogin
 * @package MCNUser\Listener\Authentication
 */
class LastLogin implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var \MCNUser\Service\UserInterface
     */
    protected $service;

    /**
     * @param \MCNUser\Service\UserInterface $service
     */
    public function __construct(UserInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(AuthEvent::EVENT_AUTH_SUCCESS, array($this, 'update'), PHP_INT_MAX);
    }

    /**
     * Update last login information
     *
     * @param \MCNUser\Authentication\AuthEvent $e
     *
     * @return void
     */
    public function update(AuthEvent $e)
    {
        $address = new RemoteAddress();

        /**
         * @var $user \MCNUser\Entity\User
         */
        $user = $e->getTarget();
        $user->setLastLoginAt(new DateTime);
        $user->setLastLoginIp($address->getIpAddress());

        $this->service->save($user);
    }
}
