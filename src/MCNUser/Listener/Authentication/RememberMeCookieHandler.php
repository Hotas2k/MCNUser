<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Listener\Authentication;

use MCNUser\Authentication\AuthEvent;
use MCNUser\Authentication\TokenServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Header\SetCookie;
use Zend\Http\Response;
use MCNUser\Options\Authentication\Plugin\RememberMe as Options;

/**
 * Class RememberMe
 * @package MCNUser\Listener\Authentication\RememberMe
 */
class RememberMeCookieHandler implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $handles = array();

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
     * @param \MCNUser\Authentication\TokenServiceInterface $service
     * @param \Zend\Http\Response $response
     * @param \MCNUser\Options\Authentication\Plugin\RememberMe $options
     */
    public function __construct(TokenServiceInterface $service, Response $response, Options $options)
    {
        $this->service  = $service;
        $this->response = $response;
        $this->options  = $options;
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
        $events->attach(AuthEvent::EVENT_LOGOUT,       array($this, 'clearCookieOnLogout'));
        $events->attach(AuthEvent::EVENT_AUTH_SUCCESS, array($this, 'setRememberMeCookie'));
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->handles as $idx => $handle) {

            $events->detach($handle);
            unset($this->handles[$idx]);
        }
    }

    /**
     * @param \MCNUser\Authentication\AuthEvent $e
     */
    public function setRememberMeCookie(AuthEvent $e)
    {
        $entity  = $e->getEntity();
        $request = $e->getRequest();

        if (! $request->getPost('remember_me')) {

            return;
        }

        $token = $this->service->create($e->getEntity(), $this->options->getValidInterval());

        $hash    = $entity->getId()  . '|' . $token->getToken();
        $expires = $token->getValidUntil() ? $token->getValidUntil()->getTimestamp() : null;

        $this->response->getHeaders()->addHeader(
            new SetCookie('remember_me', $hash, $expires)
        );
    }

    /**
     * Clears the cookie and removes the token stopping it from being used in the future
     */
    public function clearCookieOnLogout(AuthEvent $e)
    {
        $request = $e->getRequest();
    }
}
