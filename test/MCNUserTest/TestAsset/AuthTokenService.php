<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\TestAsset;

use DateInterval;
use MCNUser\Entity\User\AuthToken as TokenEntity;
use MCNUser\Service\AuthTokenInterface;
use MCNUser\Service\Exception;
use MCNUser\Service\UserEntityInterface;

class AuthTokenService implements AuthTokenInterface
{

    /**
     * Create a new authentication token
     *
     * @param \MCNUser\Entity\User $user
     * @param \DateInterval $valid_until
     *
     * @return TokenEntity
     */
    public function create(UserEntityInterface $user, DateInterval $valid_until = null)
    {
        // TODO: Implement create() method.
    }

    /**
     * Consume a token
     *
     * @param TokenEntity $token
     * @param bool        $renew Optional
     *
     * @throws Exception\ExpiredTokenException
     * @throws Exception\AlreadyConsumedException
     *
     * @return TokenEntity|void
     */
    public function consumeToken(TokenEntity $token, $renew = false)
    {
        // TODO: Implement consumeToken() method.
    }
}
