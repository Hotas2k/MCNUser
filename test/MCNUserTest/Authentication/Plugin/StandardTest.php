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

use MCNUser\Authentication\Plugin\Standard;
use MCNUser\Authentication\Result;
use MCNUser\Entity\User;
use MCNUser\Options\Authentication\Plugin\Standard as StandardOptions;
use MCNUserTest\TestAsset\UserService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class StandardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \MCNUser\Options\Authentication\Plugin\Standard
     */
    public $options;

    /**
     * @var \Zend\Crypt\Password\Bcrypt
     */
    public $bcrypt;

    /**
     * @var \MCNUser\Entity\User
     */
    protected $user;

    /**
     * @var \MCNUser\Authentication\Plugin\Standard
     */
    protected $plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    public function setUp()
    {
        $this->options     = new StandardOptions();
        $this->userService = $this->getMock('MCNStdlib\Interfaces\UserServiceInterface');

        $this->bcrypt = new Bcrypt(array(
            'salt' => $this->options->getBcryptSalt(),
            'cost' => $this->options->getBcryptCost()
        ));

        $this->user = new User();
        $this->user->fromArray(array(
            'id'    => 1,
            'email' => 'hello@world.com',
            'password' => $this->bcrypt->create('password')
        ));

        $this->plugin = new Standard($this->options);
    }

    public function testForIdentityNotFound()
    {
        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'email'    => 'wrong email'
            ))
        );

        $result = $this->plugin->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND);
        $this->assertNull($result->getIdentity());
    }

    public function testForInvalidCredential()
    {
        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'identity' => 'hello@world.com',
                'credential' => 'wrong password'
            ))
        );

        $this->userService
            ->expects($this->any())
            ->method('getOneBy')
            ->will($this->returnValue($this->user));

        $result = $this->plugin->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode()     == Result::FAILURE_INVALID_CREDENTIAL);
        $this->assertTrue($result->getIdentity() == $this->user);
    }

    public function testForSuccessfulLogin()
    {
        $this->userService
            ->expects($this->any())
            ->method('getOneBy')
            ->will($this->returnValue($this->user));

        $request = new Request();
        $request->setPost(
            new Parameters(array(
                'identity'   => 'hello@world.com',
                'credential' => 'password'
            ))
        );

        $result = $this->plugin->authenticate($request, $this->userService);

        $this->assertTrue($result->getCode()     == Result::SUCCESS);
        $this->assertTrue($result->getIdentity() == $this->user);
    }
}
