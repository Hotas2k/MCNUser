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
use ErrorException;
use MCN\Stdlib\ClassUtils;
use MCNUser\Entity\User\AuthToken as TokenEntity;
use MCNUser\Entity\User as UserEntity;
use Zend\Json\Server\Error;
use Zend\Stdlib\ErrorHandler;

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
                sprintf('The class %s does not use the required trait MCNUser\Entity\AuthTokenTrait', get_class($entity))
            );
        }

        try {

            ErrorHandler::start();

            $resource = fopen('/dev/urandom', 'r');

            $token = base64_encode(fread($resource, 100));

            fclose($resource);

            ErrorHandler::stop(true);

        } catch(ErrorException $e) {

            throw new Exception\LogicException('Failed to generate token', null, $e);
        }

        $tokenEntity = new TokenEntity();
        $tokenEntity->setToken($token);
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
    public function consumeToken($entity, $token, $renew = false)
    {

    }
}
