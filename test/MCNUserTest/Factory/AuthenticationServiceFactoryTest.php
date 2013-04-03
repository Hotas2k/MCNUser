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

namespace MCNUserTest\Factory;

use MCN\View\Helper\ServiceManager;
use MCNUser\Factory\AuthenticationServiceFactory;
use MCNUserTest\Util\ServiceManagerFactory;
use Zend\Stdlib\ArrayObject;

class AuthenticationServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $sm;

    protected function setUp()
    {
        $this->sm = ServiceManagerFactory::getServiceManager();
    }

    /**
     * @expectedException \MCNUser\Factory\Exception\InvalidArgumentException
     */
    public function testInvalidSlKey()
    {
        $this->sm->get('mcn.options.user.authentication')->setUserServiceSlKey(null);

        $factory = new AuthenticationServiceFactory();
        $factory->createService($this->sm);
    }

    /**
     * @expectedException \MCNUser\Factory\Exception\LogicException
     */
    public function testInvalidInstanceOfSlKey()
    {
        $this->sm = ServiceManagerFactory::getServiceManager();
        $this->sm->setService('fail', new ArrayObject());

        $this->sm->get('mcn.options.user.authentication')->setUserServiceSlKey('fail');

        $factory = new AuthenticationServiceFactory();
        $factory->createService($this->sm);
    }

    public function testAddingPlugins()
    {
    }
}
