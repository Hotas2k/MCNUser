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
     * Get a token entity
     *
     * @param mixed  $entity
     * @param string $token
     *
     * @throws Exception\LogicException
     *
     * @return \MCNUser\Entity\AuthToken|null
     */
    protected function getToken($entity, $token)
    {
        if (! ClassUtils::uses($entity, 'MCNUser\Entity\AuthTokenTrait')) {

            throw new Exception\LogicException(
                sprintf(
                    'The class %s does not use the required trait MCNUser\Entity\AuthTokenTrait',
                    get_class($entity)
                )
            );
        }

        return $this->getRepository()->findOneBy(array(
            'owner' => $entity->getId(),
            'token' => $token
        ));
    }

    /**
     * @inheritdoc
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
        $tokenEntity->setToken(base64_encode(Math\Rand::getBytes(100)));
        $tokenEntity->setOwner($entity->getId());

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
     * @inheritdoc
     */
    public function consumeToken($entity, $token)
    {
        $token = $this->getToken($entity, $token);

        if (! $token) {

            throw new Exception\TokenNotFoundException;
        }

        if ($token->getUsedAt() !== null) {

            throw new Exception\TokenAlreadyConsumedException;
        }

        $dt = new DateTime;

        if ($token->getValidUntil() && $token->getValidUntil() < $dt) {

            throw new Exception\TokenHasExpiredException;
        }

        $ip = (new RemoteAddress())->getIpAddress();

        $token->setUsedAt($dt);
        $token->setUsedByIp($ip);

        $this->objectManager->flush($token);

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

    /**
     * @inheritdoc
     */
    public function removeToken($entity, $token)
    {
        $token = $this->getToken($entity, $token);

        $this->objectManager->remove($token);
        $this->objectManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function removeAllTokensForEntity($entity)
    {
        if (! ClassUtils::uses($entity, 'MCNUser\Entity\AuthTokenTrait')) {

            throw new Exception\LogicException(
                sprintf(
                    'The class %s does not use the required trait MCNUser\Entity\AuthTokenTrait',
                    get_class($entity)
                )
            );
        }

        $criteria = new Criteria();
        $criteria->where($criteria->expr()->eq('owner', $entity->getId()));

        $tokens = $this->getRepository()->matching($criteria);

        foreach ($tokens as $token) {

            $this->objectManager->remove($token);
        }

        $this->objectManager->flush();
    }
}
