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

namespace MCNuserTest\Service;

use Doctrine\Common\Collections\Criteria;
use MCNUser\Options\UserOptions;
use MCNUser\Service\User;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Class UserTest
 * @package MCNuserTest\Service
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var \MCNUser\Service\User
     */
    protected $service;

    /**
     * @var \MCNUser\Options\UserOptions
     */
    protected $options;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $user;

    /**
     * @var \Zend\EventManager\EventManager
     */
    protected $evm;

    protected function setUp()
    {
        $this->evm     = new EventManager();
        $this->user    = $this->getMock('MCNUser\Entity\User');
        $this->manager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->options = new UserOptions();

        $this->service = new User($this->manager, $this->options);
        $this->service->setEventManager($this->evm);
    }

    public function testSave_TriggerEventAndObjectManagerPersistAndFlushOnNewObject()
    {
        $called = (object) [
            'prePersist'  => false,
            'postPersist' => false,
            'preFlush'    => false,
            'postFlush'   => false
        ];

        $this->manager
            ->expects($this->once())
            ->method('contains')
            ->with($this->user)
            ->will($this->returnValue(false));

        $this->manager
            ->expects($this->once())
            ->method('persist')
            ->with($this->user);

        $this->manager
            ->expects($this->once())
            ->method('flush');

        $this->evm->attach('persist.pre', function(Event $e) use ($called) {
            $called->prePersist = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->evm->attach('persist.post', function(Event $e) use ($called) {
            $called->postPersist = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->evm->attach('flush.pre', function(Event $e) use ($called) {
            $called->preFlush = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->evm->attach('flush.post', function(Event $e) use ($called) {
            $called->postFlush = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->service->save($this->user);

        $this->assertTrue($called->prePersist);
        $this->assertTrue($called->postPersist);
        $this->assertTrue($called->preFlush);
        $this->assertTrue($called->postFlush);
    }

    public function testSave_DoNotTriggerPersistOnExistingObject()
    {
        $called = (object) [
            'prePersist'  => false,
            'postPersist' => false,
            'preFlush'  => false,
            'postFlush' => false
        ];

        $this->manager
            ->expects($this->once())
            ->method('contains')
            ->with($this->user)
            ->will($this->returnValue(true));

        $this->manager
            ->expects($this->once())
            ->method('flush');

        $this->evm->attach('persist.pre', function(Event $e) use ($called) {
            $called->prePersist = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->evm->attach('persist.post', function(Event $e) use ($called) {
            $called->postPersist = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->evm->attach('flush.pre', function(Event $e) use ($called) {
            $called->preFlush = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->evm->attach('flush.post', function(Event $e) use ($called) {
            $called->postFlush = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->service->save($this->user);

        $this->assertFalse($called->prePersist);
        $this->assertFalse($called->postPersist);
        $this->assertTrue($called->preFlush);
        $this->assertTrue($called->postFlush);
    }

    public function testRemove_TriggerPreAndPostEvent()
    {
        $called = (object) [
            'pre'  => false,
            'post' => false,
        ];

        $this->manager
            ->expects($this->once())
            ->method('remove')
            ->with($this->user);

        $this->manager
            ->expects($this->once())
            ->method('flush');

        $this->evm->attach('remove.pre', function(Event $e) use ($called) {
            $called->pre = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->evm->attach('remove.post', function(Event $e) use ($called) {
            $called->post = true;
            $this->assertEquals($this->user, $e->getParam('user'));
        });

        $this->service->remove($this->user);

        $this->assertTrue($called->pre);
        $this->assertTrue($called->post);
    }

    /**
     * @expectedException        \MCNUser\Service\Exception\logicException
     * @expectedExceptionMessage No search service has been provided
     */
    public function testSearch_ThrowsExceptionOnNoSearchService()
    {
        $this->service->search(Criteria::create());
    }
}

