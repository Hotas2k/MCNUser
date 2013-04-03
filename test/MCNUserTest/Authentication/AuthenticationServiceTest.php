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

namespace MCNUserTest\Authentication;


use MCNUser\Authentication\AuthEvent;
use MCNUser\Authentication\AuthenticationService;
use MCNUser\Authentication\Result;
use MCNUser\Entity\User;
use PHPUnit_Framework_TestCase;
use Zend\Http\Request;

class AuthenticationServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \MCNUser\Authentication\AuthenticationService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    protected function setUp()
    {
        $this->request     = new Request();
        $this->userService = $this->getMock('MCNUser\Service\UserInterface');

        $this->service = new AuthenticationService($this->userService);
    }

    /**
     * @expectedException \MCNUser\Authentication\Exception\DomainException
     */
    public function testAuthenticateThrowsExceptionOnInvalidPlugin()
    {
        $this->service->authenticate($this->request, 'i am a plugin that does not exist');
    }

    public function testAuthFailsIfSuccessEventIsStopped()
    {
        $this->service->getEventManager()->attach(AuthEvent::EVENT_AUTH_SUCCESS, function(AuthEvent $e) {

            $e->stopPropagation(true);
        });

        $result = Result::create(Result::SUCCESS);
        $plugin = $this->getMock('MCNUser\Authentication\Plugin\AbstractPlugin');

        $plugin->expects($this->once())
               ->method('authenticate')
               ->withAnyParameters()
               ->will($this->returnValue($result));

        $this->service->getPluginManager()->setService('success', $plugin);

        $result = $this->service->authenticate($this->request, 'success');

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
    }

    public function testSuccessfulLogin()
    {
        $user = new User();

        $result = Result::create(Result::SUCCESS, $user);
        $plugin = $this->getMock('MCNUser\Authentication\Plugin\AbstractPlugin');

        $plugin->expects($this->once())
            ->method('authenticate')
            ->withAnyParameters()
            ->will($this->returnValue($result));

        $this->service->getPluginManager()->setService('success', $plugin);

        $this->assertFalse($this->service->hasIdentity());

        $result = $this->service->authenticate($this->request, 'success');

        $this->assertTrue($this->service->hasIdentity());
        $this->assertEquals(Result::SUCCESS, $result->getCode());
        $this->assertEquals($result->getIdentity(), $user);
        $this->assertEquals($this->service->getIdentity(), $user);
    }
}
