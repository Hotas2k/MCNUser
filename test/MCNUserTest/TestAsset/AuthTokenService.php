<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\TestAsset;

use DateInterval;
use MCNUser\Authentication\Exception;
use MCNUser\Authentication\TokenServiceInterface;

class AuthTokenService implements TokenServiceInterface
{
    /**
     * Create a new authentication token
     *
     * @param mixed         $entity Should use the trait MCNUser\Entity\AuthTokenTrait
     * @param \DateInterval $valid_until
     *
     * @return \MCNUser\Entity\AuthToken
     */
    public function create($entity, DateInterval $valid_until = null)
    {
        // TODO: Implement create() method.
    }

    /**
     * Consume a token
     *
     * @param mixed  $entity
     * @param string $token
     *
     * @throws Exception\ExpiredTokenException
     * @throws Exception\AlreadyConsumedException
     *
     * @return \MCNUser\Entity\AuthToken
     */
    public function consumeToken($entity, $token)
    {
        switch ($token)
        {
            case 'already-used':
                throw new Exception\AlreadyConsumedException;

            case 'not-found':
                throw new Exception\TokenNotFoundException;

            case 'has-expired':
                throw new Exception\ExpiredTokenException;
        }
    }

    /**
     * Consume a token and return a new one
     *
     * @param mixed $entity
     * @param string $token
     *
     * @throws Exception\ExpiredTokenException
     * @throws Exception\AlreadyConsumedException
     *
     * @return \MCNUser\Entity\AuthToken
     */
    public function consumeAndRenewToken($entity, $token)
    {
        $this->consumeToken($entity, $token);
    }
}
