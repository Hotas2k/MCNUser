<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use MCNUser\Authentication\TokenConsumerInterface;

/**
 * Class AuthTokenInterface
 * @package MCNUser\Repository
 */
interface AuthTokenInterface
{
    /**
     * @param TokenConsumerInterface $owner
     * @param string                 $token
     *
     * @return \MCNUser\Entity\AuthToken|null
     */
    public function getByOwnerAndToken(TokenConsumerInterface $owner, $token);

    /**
     * @param TokenConsumerInterface $owner
     *
     * @return mixed
     */
    public function consumeAllTokensAndReturnCount(TokenConsumerInterface $owner);
}
