<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserText\Listener\Authentication;

use MCNUser\Authentication\AuthEvent;
use MCNUser\Entity\User;
use MCNUser\Listener\Authentication\Activated;

class ActivatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    public $event;

    protected function setUp()
    {
        $this->user     = new User();
        $this->event    = $this->getMock('MCNUser\Authentication\AuthEvent');
        $this->listener = new Activated();
    }

    public function testFailsOnUnactivatedAccount()
    {
        $this->event
            ->expects($this->once())
            ->method('getTarget')
            ->will($this->returnValue($this->user));

        $this->event
            ->expects($this->once())
            ->method('stopPropagation')
            ->with(true);

        $this->user->setActivated(false);
        $this->listener->isActivated($this->event);
    }

    public function testSuccessOnActivatedAccount()
    {
        $this->event
            ->expects($this->once())
            ->method('getTarget')
            ->will($this->returnValue($this->user));

        $this->event
            ->expects($this->never())
            ->method('stopPropagation');

        $this->user->setActivated(true);
        $this->listener->isActivated($this->event);
    }
}
