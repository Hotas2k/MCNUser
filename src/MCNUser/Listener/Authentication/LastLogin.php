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
use Zend\Http\PhpEnvironment\RemoteAddress;

/**
 * Class LastLogin
 * @package MCNUser\Listener\Authentication
 */
class LastLogin implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $handler = array();

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
        $this->handler[] = $events->attach(AuthEvent::EVENT_AUTH_SUCCESS, array($this, 'updateTimestamp'), PHP_INT_MAX);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        array_walk($this->handler, array($events, 'detach'));
    }

    /**
     * Update last login information
     *
     * @param \MCNUser\Authentication\AuthEvent $e
     *
     * @return void
     */
    public function updateTimestamp(AuthEvent $e)
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
