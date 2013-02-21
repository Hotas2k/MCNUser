<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

use DateInterval;

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
     * @return \MCNUser\Entity\AuthToken
     */
    public function create($entity, DateInterval $valid_until = null);

    /**
     * Consume a token
     *
     * @param mixed  $entity
     * @param string $token
     *
     * @throws Exception\TokenHasExpiredException
     * @throws Exception\TokenAlreadyConsumedException
     *
     * @return \MCNUser\Entity\AuthToken
     */
    public function consumeToken($entity, $token);

    /**
     * Consume a token and return a new one
     *
     * @param mixed $entity
     * @param string $token
     *
     * @throws Exception\TokenHasExpiredException
     * @throws Exception\TokenAlreadyConsumedException
     *
     * @return \MCNUser\Entity\AuthToken
     */
    public function consumeAndRenewToken($entity, $token);
}
