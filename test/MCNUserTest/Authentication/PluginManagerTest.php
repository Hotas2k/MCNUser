<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication;

use MCNUser\Authentication\PluginManager;
use Zend\Stdlib\ArrayObject;

class PluginManagerText extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \MCNUser\Authentication\Exception\InvalidPluginException
     */
    public function testExceptionOnInvalidPlugin()
    {
        $manager = new PluginManager();
        $manager->validatePlugin(new ArrayObject());
    }
}
