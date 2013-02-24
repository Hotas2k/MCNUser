<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Listener\Authentication;

use MCNUser\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use MCNUser\Authentication\Exception;

/**
 * Class RememberMeAuthTrigger
 * @package MCNUser\Listener\Authentication
 */
class RememberMeAuthTrigger implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $handles = array();

    /**
     * @var \MCNUser\Authentication\AuthenticationService
     */
    protected $service;

    /**
     * @param \MCNUser\Authentication\AuthenticationService $service
     */
    public function __construct(AuthenticationService $service)
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
        $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'attemptAuthenticationByCookie'), PHP_INT_MAX);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->handles as &$handle) {

            $events->detach($handle);
            unset($handle);
        }
    }

    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function attemptAuthenticationByCookie(MvcEvent $e)
    {
        $request = $e->getRequest();

        if (! $request instanceof HttpRequest) {

            return;
        }

        $cookie = $request->getHeader('Cookie');

        if (isSet($cookie->remember_me) && !empty($cookie->remember_me) && !$this->service->hasIdentity()) {

            try {

                $this->service->authenticate($e->getRequest(), 'remember-me');

            } catch(Exception\DomainException $e) {

            }
        }
    }
}
