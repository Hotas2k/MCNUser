<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Factory;

use MCNUser\Factory\AuthenticationOptionsFactory;
use MCNUserTest\Bootstrap;

class AuthenticationOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException MCNUser\Factory\Exception\RuntimeException
     */
    public function testExceptionThrownOnMissingConfigurationKey()
    {
        $sm = Bootstrap::getServiceManager();
        $config = $sm->get('Config');

        unset($config['MCNUser']);

        $sm->setService('Config', $config);

        $factory = new AuthenticationOptionsFactory();
        $factory->createService(Bootstrap::getServiceManager());
    }
}
