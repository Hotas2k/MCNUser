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

namespace MCNUser\Authentication\Plugin;

use DateTime;
use MCNUser\Authentication\Result;
use MCNUser\Options\Authentication\Plugin\RememberMe as Options;
use MCNUser\Authentication\TokenServiceInterface;
use MCNUser\Service\UserInterface;
use Zend\Http\Client\Cookies;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request as HttpRequest;
use MCNUser\Authentication\Exception;
use Zend\Http\Response as HttpResponse;

/**
 * Class RememberMe
 * @package MCNUser\Authentication\Plugin
 */
class RememberMe extends AbstractPlugin
{
    /**
     * @var \MCNUser\Authentication\TokenServiceInterface
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
    public function __construct(TokenServiceInterface $service, HttpResponse $response, Options $options = null)
    {
        $this->service  = $service;
        $this->options  = ($options == null ? new Options() : $options);
        $this->response = $response;
    }

    /**
     * Uses a stored token to renew
     *
     * @param \Zend\Http\Request             $request
     * @param \MCNUser\Service\UserInterface $service
     *
     * @throws \MCNUser\Authentication\Exception\DomainException
     *
     * @return \MCNUser\Authentication\Result
     */
    public function authenticate(HttpRequest $request, UserInterface $service)
    {
        if (! $request->getCookie() || !isSet($request->getCookie()->remember_me)) {

            throw new Exception\DomainException('No remember me cookie has been set');
        }

        list ($identity, $token) = explode('|', $request->getCookie()->remember_me);

        $user = $service->getOneBy($this->options->getEntityIdentityProperty(), $identity);

        if (! $user) {

            return Result::create(Result::FAILURE_IDENTITY_NOT_FOUND, null, Result::MSG_IDENTITY_NOT_FOUND);
        }

        try {

            $this->service->useAndConsume($user, $token);

        } catch (Exception\TokenIsConsumedException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token has already been consumed.');

        } catch(Exception\TokenHasExpiredException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token has expired.');

        } catch(Exception\TokenNotFoundException $e) {

            return Result::create(Result::FAILURE_UNCATEGORIZED, $user, 'Token was not found.');
        }


        // create a new token to use
        $token = $this->service->create($user, $this->options->getValidInterval());

        if (null !== $validUntil = $this->options->getValidInterval()) {

            $validUntil = new DateTime();
            $validUntil->add($this->options->getValidInterval());
        }

        $validUntil = $token->getValidUntil() !== null ? $token->getValidUntil()->getTimestamp() : null;

        $cookie = new SetCookie('remember_me', $identity . '|' . $token->getToken(), $validUntil);

        $this->response->getHeaders()->addHeader($cookie);

        return Result::create(Result::SUCCESS, $user);
    }
}
