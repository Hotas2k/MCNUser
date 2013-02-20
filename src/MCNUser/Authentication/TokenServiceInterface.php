<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

use DateInterval;
use MCNUser\Entity\User as UserEntity;
use MCNUser\Entity\User\AuthToken as TokenEntity;

/**
 * Class AuthTokenInterface
 * @package MCNUser\Service\User
 */
interface TokenServiceInterface
{
    /**
     * Create a new authentication token
     *
     * @param mixed         $entity Should use the trait MCNUser\Entity\AuthTokenTrait
     * @param \DateInterval $valid_until
     *
     * @internal param \MCNUser\Entity\User $user
     * @return TokenEntity
     */
    public function create($entity, DateInterval $valid_until = null);

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
    public function consumeToken(TokenEntity $token, $renew = false);
}
