<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication;

use MCNUser\Authentication\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \MCNUser\Authentication\Exception\OutOfBoundsException
     */
    public function testSetCodeThrowExceptionOnInvalidRange()
    {
        $result = new Result();
        $result->setCode(1000000);
    }
}
