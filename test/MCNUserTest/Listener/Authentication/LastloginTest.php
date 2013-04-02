<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Listener;

use MCNUser\Authentication\AuthEvent;
use MCNUser\Entity\User;
use MCNUser\Listener\Authentication\LastLogin;
use Zend\EventManager\EventManager;

class LastloginTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->service  = $this->getMock('MCNUser\Service\UserInterface');
        $this->listener = new LastLogin($this->service);
        $this->event    = $this->getMock('MCNUser\Authentication\AuthEvent');
        $this->user     = new User();
    }

    public function testAttachesOnSuccessEvent()
    {
        $evm = $this->getMock('Zend\EventManager\EventManager');

        $evm->expects($this->once())
            ->method('attach')
            ->with(AuthEvent::EVENT_AUTH_SUCCESS);

        $this->listener->attach($evm);
    }

    public function testUpdatesLastLogin()
    {
        $this->event
            ->expects($this->once())
            ->method('getTarget')
            ->will($this->returnValue($this->user));

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->listener->update($this->event);

        $this->assertEquals('127.0.0.1', $this->user->getLastLoginIp());
        $this->assertEquals(new \DateTime(), $this->user->getLastLoginAt());
    }
}
