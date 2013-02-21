<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Class AuthTokenInterface
 * @package MCNUser\Repository
 */
interface AuthTokenInterface extends ObjectRepository
{
    public function match(Criteria $criteria);
}
