<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class UserOptions
 * @package MCNUser\Options
 */
class UserOptions extends AbstractOptions
{
    /**
     * The FQCN of the class entity
     *
     * @var string
     */
    protected $user_entity = 'MCNUser\Entity\User';

    /**
     * @param string $user_entity
     */
    public function setUserEntity($user_entity)
    {
        $this->user_entity = $user_entity;
    }

    /**
     * @return string
     */
    public function getUserEntity()
    {
        return $this->user_entity;
    }
}
