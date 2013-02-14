<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Factory;

use MCNUser\Authentication\AuthenticationService;
use MCNUser\Factory\AuthenticationServiceFactory;
use MCNUserTest\Bootstrap;
use Zend\Stdlib\ArrayObject;

class AuthenticationServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryWithBasicConfiguration()
    {
        $factory = new AuthenticationServiceFactory();
        $result = $factory->createService(Bootstrap::getServiceManager());

        $this->assertTrue($result instanceof AuthenticationService);
    }

    /**
     * @expectedException MCNUser\Factory\Exception\RuntimeException
     */
    public function testExceptionThrownOnMissingConfigurationKey()
    {
        $sm = Bootstrap::getServiceManager();
        $config = $sm->get('Config');

        unset($config['MCNUser']);

        $sm->setService('Config', $config);

        $factory = new AuthenticationServiceFactory();
        $factory->createService(Bootstrap::getServiceManager());
    }

    /**
     * @expectedException MCNUser\Factory\Exception\InvalidArgumentException
     */
    public function testInvalidSlKey()
    {
        $sm = Bootstrap::getServiceManager();
        $config = $sm->get('Config');

        $config['MCNUser']['authentication']['user_service_sl_key'] = null;

        $sm->setService('Config', $config);

        $factory = new AuthenticationServiceFactory();
        $factory->createService(Bootstrap::getServiceManager());
    }

    /**
     * @expectedException MCNUser\Factory\Exception\LogicException
     */
    public function testInvalidInstanceOfSlKey()
    {
        $sm = Bootstrap::getServiceManager();
        $config = $sm->get('Config');

        $sm->setService('fail', new ArrayObject());
        $config['MCNUser']['authentication']['user_service_sl_key'] = 'fail';

        $sm->setService('Config', $config);

        $factory = new AuthenticationServiceFactory();
        $factory->createService(Bootstrap::getServiceManager());
    }
}
