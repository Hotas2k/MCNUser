<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Listener\Authentication\RememberMe;

use MCNUser\Authentication\AuthEvent;
use MCNUser\Authentication\TokenServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Header\SetCookie;
use Zend\Http\Response as HttpResponse;
use Zend\Stdlib\ResponseInterface;
use MCNUser\Options\Authentication\Plugin\RememberMe as Options;

/**
 * Class RememberMe
 * @package MCNUser\Listener\Authentication\RememberMe
 */
class CookieHandler implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var \Zend\Http\Response
     */
    protected $response;

    /**
     * @var \MCNUser\Authentication\TokenService
     */
    protected $service;

    /**
     * @var \MCNUser\Options\Authentication\Plugin\RememberMe
     */
    protected $options;

    /**
     * @param \MCNUser\Authentication\TokenServiceInterface     $service
     * @param \Zend\Stdlib\ResponseInterface                    $response
     * @param \MCNUser\Options\Authentication\Plugin\RememberMe $options
     */
    public function __construct(TokenServiceInterface $service, ResponseInterface $response, Options $options)
    {
        $this->service  = $service;
        $this->options  = $options;
        $this->response = $response;
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
        $this->listeners[] = $events->attach(AuthEvent::EVENT_LOGOUT,       array($this, 'clearCookieOnLogout'));
        $this->listeners[] = $events->attach(AuthEvent::EVENT_AUTH_SUCCESS, array($this, 'setRememberMeCookie'));
    }

    /**
     * @param \MCNUser\Authentication\AuthEvent $e
     */
    public function setRememberMeCookie(AuthEvent $e)
    {
        if (! $this->response instanceof HttpResponse) {

            return;
        }

        $entity  = $e->getEntity();
        $request = $e->getRequest();

        $accepted = array('1', 'true');

        if (! in_array($request->getPost('remember_me'), $accepted)) {

            return;
        }

        $token = $this->service->create($e->getEntity(), $this->options->getValidInterval());

        $hash    = $entity[$this->options->getEntityIdentityProperty()] . '|' . $token->getToken();
        $expires = $token->getValidUntil() ? $token->getValidUntil()->getTimestamp() : null;

        $this->response->getHeaders()->addHeader(
            new SetCookie('remember_me', $hash, $expires, '/')
        );
    }

    /**
     * Clears the cookie and removes the token stopping it from being used in the future
     */
    public function clearCookieOnLogout(AuthEvent $e)
    {
        $this->response->getHeaders()->addHeader(
            new SetCookie('remember_me', '', 0, '/')
        );
    }
}
