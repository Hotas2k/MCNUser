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

use MCNUser\Listener\Authentication\RememberMe\AuthTrigger;
use Zend\Http\Request;

use MCNUser\Authentication\Exception;

/**
 * @property mixed event
 * @property mixed service
 * @property AuthTrigger listener
 */
class AuthTriggerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->event   = $this->getMock('Zend\Mvc\MvcEvent');
        $this->service = $this->getMock('MCNUser\Authentication\AuthenticationService', array(), array(), '', false);

        $this->listener = new AuthTrigger($this->service);
    }

    public function testIgnoreOnInvalidRequest()
    {
        $this->event->expects($this->once())->method('getRequest');

        $this->assertNull(
            $this->listener->attemptAuthenticationByCookie($this->event)
        );
    }

    public function testTriggerAuth()
    {
        $request = new Request();
        $request->getHeaders()->addHeaderLine('Cookie', 'remember_me=1|hello; foo=test');

        $this->event
            ->expects($this->atLeastOnce())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->listener->attemptAuthenticationByCookie($this->event);
    }

    public function testHandleAuthServiceExceptionOnInvalidPlugin()
    {
        $request = new Request();
        $request->getHeaders()->addHeaderLine('Cookie', 'remember_me=1|hello; foo=test');

        $this->event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->service
            ->expects($this->once())
            ->method('authenticate')
            ->will($this->throwException(new Exception\DomainException));

        $this->listener->attemptAuthenticationByCookie($this->event);
    }
}
