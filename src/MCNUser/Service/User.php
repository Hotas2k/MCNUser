<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Service;

use MCNUser\Entity\User as UserEntity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectManager;
use MCNUser\Entity\User as User0;
use MCNUser\Options\UserOptions as Options;

/**
 * Class User
 * @package MCNUser\Service
 */
class User implements UserInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param \MCNUser\Options\UserOptions               $options
     */
    public function __construct(ObjectManager $manager, Options $options = null)
    {
        $this->options       = ($options === null) ? new Options() : $options;
        $this->objectManager = $manager;
    }

    /**
     * Get the user repository
     *
     * @return \MCNUser\Repository\UserInterface
     */
    protected function getRepository()
    {
        return $this->objectManager->getRepository($this->options->getUserEntity());
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
        return $this->getRepository()->findOneBy(array(
            $property => $value
        ));
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
        return $this->getOneBy('id', $id, $relations);
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
        return $this->getOneBy('email', $email, $relations);
    }

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return array|\Zend\Paginator\Paginator
     */
    public function search(Criteria $criteria)
    {

    }

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return array|\Zend\Paginator\Paginator
     */
    public function fetchAll(Criteria $criteria)
    {
        return $this->getRepository()->matching($criteria);
    }

    /**
     * Save the user
     *
     * @param \MCNUser\Entity\User $user
     *
     * @return void
     */
    public function save(UserEntity $user)
    {
        if (! $this->objectManager->contains($user)) {

            $this->objectManager->persist($user);
        }

        $this->objectManager->flush($user);
    }

    /**
     * Remove a user
     *
     * @param \MCNUser\Entity\User $user
     *
     * @return void
     */
    public function remove(UserEntity $user)
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush($user);
    }
}
