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
 * Class TokenServiceInterface
 * @package MCNUser\Authentication
 */
interface TokenServiceInterface
{
    /**
     * Create a new authentication token
     *
     * @param TokenConsumerInterface $entity
     * @param \DateInterval          $valid_until
     *
     * @return \MCNUser\Entity\AuthToken
     */
    public function create(TokenConsumerInterface $entity, DateInterval $valid_until = null);

    /**
     * @param TokenConsumerInterface $entity
     * @param string                 $token
     *
     * @throws Exception\TokenHasExpiredException
     * @throws Exception\TokenNotFoundException
     * @throws Exception\TokenIsConsumedException
     *
     * @return \MCNUser\Entity\AuthToken
     */
    public function useToken(TokenConsumerInterface $entity, $token);

    /**
     * Uses a token and then consumes it
     *
     * @param TokenConsumerInterface $entity
     * @param string                 $token
     *
     * @return void
     */
    public function useAndConsume(TokenConsumerInterface $entity, $token);
}
