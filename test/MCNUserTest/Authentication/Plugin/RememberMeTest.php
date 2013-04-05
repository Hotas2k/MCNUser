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

namespace MCNUserTest\Authentication\Plugin;

use MCNUser\Service\Exception\TokenIsConsumedException;
use MCNUser\Service\Exception\TokenHasExpiredException;
use MCNUser\Service\Exception\TokenNotFoundException;
use MCNUser\Authentication\Plugin\RememberMe;
use MCNUser\Authentication\Result;
use MCNUser\Entity\Token;
use MCNUser\Entity\User;
use MCNUser\Options\Authentication\Plugin\RememberMe as RememberMeOptions;
use MCNUserTest\TestAsset;
use Zend\Http\Client\Cookies;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;
use Zend\Http\Response;
use DateTime;

/**
 * Class RememberMeTest
 * @package MCNUserTest\Authentication\Plugin
 */
class RememberMeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \Zend\Http\Response
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    /**
     * @var \MCNUser\Authentication\Plugin\RememberMe
     */
    protected $plugin;

    /**
     * @var \MCNUser\Options\Authentication\Plugin\RememberMe
     */
    protected $options;

    public function setUp()
    {
        $this->options = new RememberMeOptions(array(
            'entity_identity_property' => 'email'
        ));

        $this->request = new Request();
        $this->request->getHeaders()->addHeader(new Cookie(array('remember_me' => 'a coookie for |  you sir!')));

        $this->response = new Response();

        $this->service     = $this->getMock('MCNUser\Service\Token\ServiceInterface');
        $this->userService = $this->getMock('MCNStdlib\Interfaces\UserServiceInterface');

        $this->plugin = new RememberMe($this->service, $this->response, $this->options);
    }

    /**
     * @expectedException \MCNUser\Service\Exception\DomainException
     */
    public function testThrowExceptionOnNoCookie()
    {
        $this->request->getHeaders()->clearHeaders();

        $this->plugin->authenticate($this->request, $this->userService);
    }

    public function testForNonExistingUserAccount()
    {
        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals($result->getCode(), Result::FAILURE_IDENTITY_NOT_FOUND);
    }

    public function testForUsedToken()
    {
        $this->userService
             ->expects($this->once())
             ->method('getOneBy')
             ->will($this->returnValue(new TestAsset\Service\TokenOwnerEntity()));

        $this->service
             ->expects($this->once())
             ->method('useAndConsume')
             ->will($this->throwException(new TokenIsConsumedException()));

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token has already been consumed.', $result->getMessage());
    }

    public function testForInvalidToken()
    {
        $this->userService
            ->expects($this->once())
            ->method('getOneBy')
            ->will($this->returnValue(new TestAsset\Service\TokenOwnerEntity()));

        $this->service
            ->expects($this->once())
            ->method('useAndConsume')
            ->will($this->throwException(new TokenNotFoundException()));


        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token was not found.', $result->getMessage());
    }

    public function testForPassedExpirationDate()
    {
        $this->userService
            ->expects($this->once())
            ->method('getOneBy')
            ->will($this->returnValue(new TestAsset\Service\TokenOwnerEntity()));

        $this->service
            ->expects($this->once())
            ->method('useAndConsume')
            ->will($this->throwException(new TokenHasExpiredException()));

        $result = $this->plugin->authenticate($this->request, $this->userService);

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals('Token has expired.', $result->getMessage());
    }

    public function testSuccessfulLogin()
    {

        $token = new Token();
        $token->prePersist();
        $token->setToken('hello.world');
        $token->setOwner(1);

        $this->userService
            ->expects($this->once())
            ->method('getOneBy')
            ->will($this->returnValue(new TestAsset\Service\TokenOwnerEntity()));

        $this->service
            ->expects($this->once())
            ->method('useAndConsume');

        $this->service
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($token));

        $result = $this->plugin->authenticate($this->request, $this->userService);


        /**
         * @var $cookie \Zend\Http\Header\SetCookie
         */
        $cookie = $this->response->getHeaders()->get('set-cookie')[0];

        $this->assertEquals($cookie->getName(), 'remember_me');
        $this->assertEquals(Result::SUCCESS, $result->getCode());
    }
}
