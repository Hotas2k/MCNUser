<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Listener\Authentication;

use MCNUser\Listener\Authentication\RememberMeAuthTrigger;
use Zend\Http\Request;

/**
 * @property mixed event
 * @property mixed service
 * @property RememberMeAuthTrigger listener
 */
class RememberMeAuthTriggerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->event   = $this->getMock('Zend\Mvc\MvcEvent');
        $this->service = $this->getMock('MCNUser\Authentication\AuthenticationService', array(), array(), '', false);

        $this->listener = new RememberMeAuthTrigger($this->service);
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
            ->expects($this->exactly(2))
            ->method('getRequest')
            ->will($this->returnValue($request));

        $this->listener->attemptAuthenticationByCookie($this->event);
    }
}
