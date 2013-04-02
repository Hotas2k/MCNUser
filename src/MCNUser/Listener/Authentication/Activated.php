<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Listener\Authentication;

use MCNUser\Authentication\AuthEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class Activated
 * @package MCNUser\Listener\Authentication
 */
class Activated implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $this->attach(AuthEvent::EVENT_AUTH_SUCCESS, array($this, 'isActivated'));
    }

    /**
     * @param AuthEvent $e
     *
     * @return string
     */
    public function isActivated(AuthEvent $e)
    {
        $user = $e->getTarget();

        if (! $user->isActivated()) {

            $e->stopPropagation(true);
            return 'You may not login until your account has been activated';
        }
    }
}
