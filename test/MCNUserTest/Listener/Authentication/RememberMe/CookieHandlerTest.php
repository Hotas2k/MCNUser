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

namespace MCNUserTest\Listener\Authentication;

use DateInterval;
use DateTime;
use MCNUser\Authentication\AuthEvent;
use MCNUser\Entity\Token;
use MCNUser\Entity\User;
use MCNUser\Listener\Authentication\RememberMe\CookieHandler;
use MCNUserTest\TestAsset\Service\TokenOwnerEntity;
use Zend\Http\Request;
use Zend\Http\Response;
use MCNUser\Options\Authentication\Plugin\RememberMe as Options;

/**
 * @property User entity
 * @property Request request
 * @property Response response
 * @property \PHPUnit_Framework_MockObject_MockObject tokenService
 * @property \PHPUnit_Framework_MockObject_MockObject plugin
 * @property AuthEvent event
 * @property Options options
 */
class CookieHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->entity = new TokenOwnerEntity();
        $this->entity->setEmail('hello@world.com');

        $this->request  = new Request();
        $this->response = new Response();

        $this->tokenService = $this->getMock('MCNUser\Service\Token\ServiceInterface');
        $this->options      = new Options();


        $this->event = new AuthEvent(AuthEvent::EVENT_AUTH_SUCCESS, $this->entity, array('request' => $this->request));
        $this->listener = new CookieHandler($this->tokenService, $this->response, $this->options);
    }

    public function testNoHeaderIfMissingOrFalseRememberMePostParam()
    {
        $this->listener->setRememberMeCookie($this->event);

        $this->assertEquals(0, count($this->response->getHeaders()));

        $this->request->getPost()->set('remember_me', false);

        $this->listener->setRememberMeCookie($this->event);

        $this->assertEquals(0, count($this->response->getHeaders()));
    }

    public function testHeaderOnRememberMe()
    {
        $this->listener->setRememberMeCookie($this->event);

        $this->request->getPost()->set('remember_me', true);

        $token = new Token();
        $token->fromArray(array(
            'token' => 'hash'
        ));

        $this->tokenService
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($token));

        $this->listener->setRememberMeCookie($this->event);

        /**
         * @var \Zend\Http\Header\SetCookie $cookie
         */
        $cookie = $this->response->getHeaders()->get('SetCookie')[0];

        $this->assertNull($cookie->getExpires());
        $this->assertEquals('hello@world.com|hash', $cookie->getValue());
    }

    public function testHeaderExpires()
    {
        $this->listener->setRememberMeCookie($this->event);

        $this->request->getPost()->set('remember_me', true);

        $dt = new DateTime();
        $dt->add(new DateInterval('PT1H'));

        $token = new Token();
        $token->fromArray(array(
            'token'       => 'hash',
            'valid_until' => $dt
        ));

        $this->tokenService
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($token));

        $this->listener->setRememberMeCookie($this->event);

        /**
         * @var \Zend\Http\Header\SetCookie $cookie
         */
        $cookie = $this->response->getHeaders()->get('SetCookie')[0];

        $this->assertEquals($dt->getTimestamp(), strtotime($cookie->getExpires()));
        $this->assertEquals('hello@world.com|hash', $cookie->getValue());
    }

    public function testRemoveCookie()
    {
        $this->listener->clearCookieOnLogout($this->event);

        /**
         * @var \Zend\Http\Header\SetCookie $cookie
         */
        $cookie = $this->response->getHeaders()->get('SetCookie')[0];

        $this->assertEquals(0,  strtotime($cookie->getExpires()));
        $this->assertEquals('', $cookie->getValue());
    }
}
