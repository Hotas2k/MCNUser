<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Factory\Authentication;

use MCNUserTest\Util\ServiceManagerFactory;

class PluginOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $sm;

    public function setUp()
    {
        $this->sm = ServiceManagerFactory::getServiceManager();
    }

    /**
     * @group factory
     */
    public function testFoo()
    {
        $plugin = $this->sm->get('MCNUser\Options\Authentication\Plugin\Standard');
    }
}
