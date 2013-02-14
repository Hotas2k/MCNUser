<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUserTest\Authentication\TestAsset;

use Doctrine\Common\Collections\Criteria;
use MCNUser\Entity\User;
use MCNUser\Service\UserInterface;

class UserService implements UserInterface
{
    public $users = array(

    );

    public function __construct()
    {
        $user = new User();
        $user->fromArray(array(
            'id'    => 1,
            'email' => 'hello@world.com',
            'password' => sha1('password')
        ));

        $this->users[] = $user;
    }

    /**
     * Get a user by their ID
     *
     * @param integer $id
     * @param array   $relations
     *
     * @return \MCNUser\Entity\User|null
     */
    public function getById($id, array $relations = array())
    {
        // TODO: Implement getById() method.
    }

    /**
     * Get a user by their email
     *
     * @param string $email
     * @param array  $relations
     *
     * @return \MCNUser\Entity\User|null
     */
    public function getByEmail($email, array $relations = array())
    {
        // TODO: Implement getByEmail() method.
    }

    /**
     * @param string $username
     * @param array  $relations
     * @return \MCNUser\Entity\User|null
     */
    public function getByUsername($username, array $relations = array())
    {
        // TODO: Implement getByUsername() method.
    }

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return array|\Zend\Paginator\Paginator
     */
    public function search(Criteria $criteria)
    {
        // TODO: Implement search() method.
    }

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return array|\Zend\Paginator\Paginator
     */
    public function fetchAll(Criteria $criteria)
    {
        // TODO: Implement fetchAll() method.
    }

    /**
     * Get a user by a given property
     *
     * @param string $property
     * @param mixed  $value
     * @param array  $relations
     *
     * @return \MCNUser\Entity\User|null
     */
    public function getOneBy($property, $value, array $relations = array())
    {
        foreach ($this->users as $user) {

            if ($user[$property] == $value) {

                return $user;
            }
        }

        return null;
    }
}
