<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Factory;

use MCNUser\Factory\AuthenticationServiceFactory;
use MCNUserTest\Util\ServiceManagerFactory;
use Zend\Stdlib\ArrayObject;

class AuthenticationServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \MCNUser\Factory\Exception\InvalidArgumentException
     */
    public function testInvalidSlKey()
    {
        $sm = ServiceManagerFactory::getServiceManager();
        $sm->get('mcn.options.user.authentication')->setUserServiceSlKey(null);

        $factory = new AuthenticationServiceFactory();
        $factory->createService($sm);
    }

    /**
     * @expectedException \MCNUser\Factory\Exception\LogicException
     */
    public function testInvalidInstanceOfSlKey()
    {
        $sm = ServiceManagerFactory::getServiceManager();
        $sm->setService('fail', new ArrayObject());

        $sm->get('mcn.options.user.authentication')->setUserServiceSlKey('fail');

        $factory = new AuthenticationServiceFactory();
        $factory->createService($sm);
    }
}
