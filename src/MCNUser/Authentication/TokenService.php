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
use Doctrine\ORM\EntityManager;
use MCN\Stdlib\ClassUtils;
use MCNUser\Entity\AuthToken as TokenEntity;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Json\Server\Error;
use Zend\Math;

/**
 * Class AuthToken
 * @package MCNUser\Service\User
 */
class TokenService implements TokenServiceInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository()
    {
        return $this->entityManager->getRepository('MCNUser\Entity\AuthToken');
    }

    /**
     * Create a new authentication token
     *
     * @param mixed         $entity
     * @param \DateInterval $valid_until
     *
     * @throws Exception\LogicException If an issue arrives during token initiation
     *
     * @return TokenEntity
     */
    public function create($entity, DateInterval $valid_until = null)
    {
        if (! ClassUtils::uses($entity, 'MCNUser\Entity\AuthTokenTrait')) {

            throw new Exception\LogicException(
                sprintf(
                    'The class %s does not use the required trait MCNUser\Entity\AuthTokenTrait',
                    get_class($entity)
                )
            );
        }

        $tokenEntity = new TokenEntity();
        $tokenEntity->setToken(Math\Rand::getBytes(100));
        $tokenEntity->setOwner($entity->getId());

        if ($valid_until) {

            $dt = new DateTime();
            $dt->add($valid_until);

            $tokenEntity->setValidUntil($dt);
        }

        $this->entityManager->persist($tokenEntity);
        $this->entityManager->flush($tokenEntity);

        return $tokenEntity;
    }

    /**
     * @inheritdoc
     */
    public function consumeToken($entity, $token)
    {
        if (! ClassUtils::uses($entity, 'MCNUser\Entity\AuthTokenTrait')) {

            throw new Exception\LogicException(
                sprintf(
                    'The class %s does not use the required trait MCNUser\Entity\AuthTokenTrait',
                    get_class($entity)
                )
            );
        }

        /**
         * @var $token \MCNUser\Entity\AuthToken
         */
        $token = $this->getRepository()->findOneBy(array(
            'owner' => $entity->getId(),
            'token' => $token
        ));

        if (! $token) {

            throw new Exception\TokenNotFoundException;
        }

        if ($token->getUsedAt() !== null) {

            throw new Exception\AlreadyConsumedException;
        }

        $dt = new DateTime;

        if ($token->getValidUntil() < $dt) {

            throw new Exception\ExpiredTokenException;
        }

        $ip = (new RemoteAddress())->getIpAddress();

        $token->setUsedAt($dt);
        $token->setUsedByIp($ip);

        $this->entityManager->flush($token);

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function consumeAndRenewToken($entity, $token)
    {
        $token = $this->consumeToken($entity, $token);

        $interval = $token->getValidUntil() !== null ? $token->getCreatedAt()->diff($token->getValidUntil()) : null;

        return $this->create($entity, $interval);
    }
}
