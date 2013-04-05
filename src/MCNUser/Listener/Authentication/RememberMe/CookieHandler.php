<?php
/**
 * Copyright (c) 2011-2013 Antoine Hedgecock.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Antoine Hedgecock <antoine@pmg.se>
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNUser\Listener\Authentication\RememberMe;

use MCNUser\Authentication\AuthEvent;
use MCNUser\Service\Token\ServiceInterface as TokenServiceInterface;
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
     * @param \MCNUser\Service\Token\ServiceInterface           $service
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
