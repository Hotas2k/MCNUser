<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
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
