<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\TestAsset\Authentication;

use MCNUser\Entity\AuthTokenTrait;

class AuthTokenOwnerEntity
{
    use AuthTokenTrait;

    public function getId()
    {
        return 1;
    }
}
