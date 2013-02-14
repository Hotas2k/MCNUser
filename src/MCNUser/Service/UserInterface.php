<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Service;

use Doctrine\Common\Collections\Criteria;

/**
 * Class UserServiceInterface
 * @package MCNUser\Service
 */
interface UserInterface
{
    /**
     * Get a user by a given property
     *
     * @param string $property
     * @param mixed  $value
     * @param array  $relations
     *
     * @return \MCNUser\Entity\User|null
     */
    public function getOneBy($property, $value, array $relations = array());

    /**
     * Get a user by their ID
     *
     * @param integer $id
     * @param array   $relations
     *
     * @return \MCNUser\Entity\User|null
     */
    public function getById($id, array $relations = array());

    /**
     * Get a user by their email
     *
     * @param string $email
     * @param array  $relations
     *
     * @return \MCNUser\Entity\User|null
     */
    public function getByEmail($email, array $relations = array());

    /**
     * @param string $username
     * @param array  $relations
     * @return \MCNUser\Entity\User|null
     */
    public function getByUsername($username, array $relations = array());

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return array|\Zend\Paginator\Paginator
     */
    public function search(Criteria $criteria);

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return array|\Zend\Paginator\Paginator
     */
    public function fetchAll(Criteria $criteria);
}
