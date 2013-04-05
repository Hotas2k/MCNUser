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

namespace MCNUserTest\Service;

use DateInterval;
use DateTime;
use MCNUser\Service\Token as TokenService;
use MCNUser\Entity\Token;
use MCNUserTest\TestAsset\Service\TokenOwnerEntity;

/**
 * Class TokenTest
 * @package MCNUserTest\Service
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectRepository;

    /**
     * @var \MCNUser\Service\Token
     */
    protected $service;

    protected function setUp()
    {
        $this->objectManager    = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->objectRepository = $this->getMock('MCNUser\Repository\TokenInterface');

        $this->objectManager
             ->expects($this->any())
             ->method('getRepository')
             ->will($this->returnValue($this->objectRepository));

        $this->service = new TokenService($this->objectManager);
    }

    protected function getEntity()
    {
        return new TokenOwnerEntity();
    }

    public function testCreateToken()
    {
        $this->objectManager->expects($this->once())->method('persist');
        $this->objectManager->expects($this->once())->method('flush');

        $token = $this->service->create($this->getEntity(), 'default');

        $this->assertInstanceOf('MCNUser\Entity\Token', $token);
        $this->assertEquals(1, $token->getOwner());
    }

    public function testCreateTokenWithValidUntilConstraint()
    {
        $this->objectManager->expects($this->once())->method('persist');
        $this->objectManager->expects($this->once())->method('flush');

        $interval = new DateInterval('PT1H');

        $token = $this->service->create($this->getEntity(), 'default', $interval);

        $this->assertInstanceOf('MCNUser\Entity\Token', $token);

        $dt = new DateTime();
        $dt->add($interval);

        $this->assertEquals($dt, $token->getValidUntil());
    }

    /**
     * @expectedException \MCNUser\Service\Exception\TokenNotFoundException
     */
    public function testUseTokenWithNonExistingToken()
    {
        $this->service->useToken($this->getEntity(), 'i do not exists', 'default');
    }

    /**
     * @expectedException \MCNUser\Service\Exception\TokenIsConsumedException
     */
    public function testUseTokenForTokenIsConsumedException()
    {
        $token = new Token();
        $token->setConsumed(true);

        $this->objectRepository->expects($this->once())->method('get')->will($this->returnValue($token));

        $this->service->useToken($this->getEntity(), 'mock token', 'default');
    }

    /**
     * @expectedException \MCNUser\Service\Exception\TokenHasExpiredException
     */
    public function testUseTokenForTokenHasExpiredException()
    {
        $token = new Token();
        $token->setValidUntil(DateTime::createFromFormat('U', time() - 1));

        $this->objectRepository->expects($this->once())->method('get')->will($this->returnValue($token));

        $this->service->useToken($this->getEntity(), 'mock token', 'default');
    }

    /**
     *
     */
    public function testUseTokenSuccessfully()
    {
        $token = new Token();

        $this->objectRepository
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($token));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($entity) use ($token) {

                $this->assertEquals('test', $entity->getHttpUserAgent());
                $this->assertEquals('127.0.0.1', $entity->getIp());
                $this->assertEquals($token, $entity->getToken());

                return $entity instanceof Token\History;
            }));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $_SERVER['REMOTE_ADDR']     = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'test';

        $result = $this->service->useToken($this->getEntity(), 'mock token', 'default');

        $this->assertEquals($token, $result);
    }
}
