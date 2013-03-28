<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectManager;
use MCN\Stdlib\ClassUtils;
use MCNUser\Entity\AuthToken as TokenEntity;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Math;

/**
 * Class TokenService
 * @package MCNUser\Authentication
 */
class TokenService implements TokenServiceInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \MCNUser\Repository\AuthTokenInterface
     */
    protected function getRepository()
    {
        return $this->objectManager->getRepository('MCNUser\Entity\AuthToken');
    }

    /**
     * Consume the given token of a consumer
     *
     * @param TokenConsumerInterface $owner
     * @param string                 $token
     *
     * @throws Exception\TokenNotFoundException
     */
    public function consumeToken(TokenConsumerInterface $owner, $token)
    {
        $token = $this->getRepository()->getByOwnerAndToken($owner, $token);

        if (! $token) {

            throw new Exception\TokenNotFoundException;
        }

        $token->setConsumed(true);
        $this->objectManager->flush($token);
    }

    /**
     * Consume all the tokens of a given consumer
     *
     * Will consume all the tokens of the given Consumer object and returns the number of tokens affected
     *
     * @param TokenConsumerInterface $owner
     *
     * @return integer The number of tokens affected
     */
    public function consumeAllTokens(TokenConsumerInterface $owner)
    {
        $this->getRepository()->consumeAllTokensAndReturnCount($owner);
    }

    /**
     * @param TokenConsumerInterface $owner
     * @param DateInterval           $valid_until
     *
     * @return TokenEntity
     */
    public function create(TokenConsumerInterface $owner, DateInterval $valid_until = null)
    {
        $tokenEntity = new TokenEntity();
        $tokenEntity->setToken(base64_encode(Math\Rand::getBytes(100)));
        $tokenEntity->setOwner($owner->getId());

        if ($valid_until) {

            $dt = new DateTime();
            $dt->add($valid_until);

            $tokenEntity->setValidUntil($dt);
        }

        $this->objectManager->persist($tokenEntity);
        $this->objectManager->flush($tokenEntity);

        return $tokenEntity;
    }

    /**
     * @param TokenConsumerInterface $owner
     * @param string                 $token
     *
     * @throws Exception\TokenHasExpiredException
     * @throws Exception\TokenNotFoundException
     * @throws Exception\TokenIsConsumedException
     *
     * @return TokenEntity
     */
    public function useToken(TokenConsumerInterface $owner, $token)
    {
        $token = $this->getRepository()->getByOwnerAndToken($owner, $token);

        if (! $token) {

            throw new Exception\TokenNotFoundException;
        }

        if ($token->isConsumed()) {

            throw new Exception\TokenIsConsumedException;
        }

        if ($token->getValidUntil() && new DateTime() > $token->getValidUntil()) {

            throw new Exception\TokenHasExpiredException;
        }

        $history = new TokenEntity\History();
        $history->setToken($token);
        $history->setCreatedAt(new DateTime());

        if (isSet($_SERVER['HTTP_USER_AGENT'])) {

            $history->setHttpUserAgent($_SERVER['HTTP_USER_AGENT']);
        }

        $this->objectManager->persist($history);
        $this->objectManager->flush($history);

        return $token;
    }

    /**
     * Uses a token and then marks it as invalid
     *
     * @uses self::useToken
     *
     * @param TokenConsumerInterface $owner
     * @param string                 $token
     *
     * @return void
     */
    public function useAndConsume(TokenConsumerInterface $owner, $token)
    {
        $token = $this->useToken($owner, $token);
        $token->setConsumed(true);

        $this->objectManager->flush($token);
    }
}
