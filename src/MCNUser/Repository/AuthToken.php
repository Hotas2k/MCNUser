<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Repository;

use Doctrine\ORM\EntityRepository;
use MCNUser\Authentication\TokenConsumerInterface;

/**
 * Class AuthToken
 * @package MCNUser\Repository
 */
class AuthToken extends EntityRepository implements AuthTokenInterface
{
    /**
     * @param TokenConsumerInterface $owner
     * @param string                 $token
     *
     * @return \MCNUser\Entity\AuthToken|null
     */
    public function getByOwnerAndToken(TokenConsumerInterface $owner, $token)
    {
        return $this->findOneBy(array(
            'token' => $token,
            'owner' => $owner->getId()
        ));
    }

    /**
     * @param TokenConsumerInterface $owner
     *
     * @return integer
     */
    public function consumeAllTokensAndReturnCount(TokenConsumerInterface $owner)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->update('MCNUser\Entity\AuthToken', 'token')
                ->set('consumed', true)
                ->where('token.owner = :id')
                ->setParameter('id', $owner->getId());

        return $builder->getQuery()->execute();
    }
}
