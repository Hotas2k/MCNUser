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
 * Class UserInterface
 * @package MCNUser\Repository
 */
interface UserInterface extends ObjectRepository
{
    public function matching(Criteria $criteria);
}
