<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Service\User;

use DateTime;

class AuthToken
{
    public function create(UserEntity $user, DateTime $valid_until = null)
    {}

    /**
     * @param TokenEntity $token
     * @param bool $renew
     *
     */
    public function consumeToken(TokenEntity $token, $renew = false)
    {

    }
}
