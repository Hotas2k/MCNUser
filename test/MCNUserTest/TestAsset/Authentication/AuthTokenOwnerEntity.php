<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\TestAsset\Authentication;

use MCNUser\Entity\AuthTokenTrait;

/**
 * Class AuthTokenOwnerEntity
 *
 * @todo replace this class when phpunit 3.8 is released and use getMockForTrait()
 *
 * @package MCNUserTest\TestAsset\Authentication
 */
class AuthTokenOwnerEntity
{
    use AuthTokenTrait;

    public function getId()
    {
        return 1;
    }
}
