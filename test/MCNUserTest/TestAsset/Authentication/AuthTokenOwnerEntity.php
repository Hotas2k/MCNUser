<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\TestAsset\Authentication;

use MCNUser\Authentication\TokenConsumerInterface;
use MCNUser\Entity\User;

/**
 * Class AuthTokenOwnerEntity
 *
 * @package MCNUserTest\TestAsset\Authentication
 */
class AuthTokenOwnerEntity extends User implements TokenConsumerInterface
{
    public function getId()
    {
        return 1;
    }
}
