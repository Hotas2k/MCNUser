<?php
/**
 * Copyright (c) 2011-2013 Antoine Hedgecock.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Antoine Hedgecock <antoine@pmg.se>
 * @author      Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright   2011-2013 Antoine Hedgecock
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 */

namespace MCNUser\Service;

use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectManager;
use MCNStdlib\Interfaces\Mail\MailServiceInterface;
use MCNStdlib\Interfaces\SearchServiceInterface;
use MCNStdlib\Interfaces\UserEntityInterface;
use MCNUser\Options\UserOptions as Options;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class User
 * @package MCNUser\Service
 */
class User implements UserServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var string
     */
    protected $eventIdentifier = 'user.service';

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \MCNStdlib\Interfaces\Mail\MailServiceInterface
     */
    protected $mailService = null;

    /**
     * @var \MCNStdlib\Interfaces\SearchServiceInterface
     */
    protected $searchService = null;

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
     * Set the mail service provider
     *
     * @param MailServiceInterface $mailService
     *
     * @return self
     */
    public function setMailService(MailServiceInterface $mailService)
    {
        $this->mailService = $mailService;
        return $this;
    }

    /**
     * Set the search service provider
     *
     * @param SearchServiceInterface $searchService
     *
     * @return $this
     */
    public function setSearchService(SearchServiceInterface $searchService)
    {
        $this->searchService = $searchService;
        return $this;
    }

    /**
     * Get the user repository
     *
     * @return \MCNUser\Repository\UserInterface
     */
    protected function getRepository()
    {
        return $this->objectManager->getRepository($this->options->getEntityClass());
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
     * @return \Zend\Paginator\Paginator
     */
    public function search(Criteria $criteria)
    {

    }

    /**
     * @param \Doctrine\Common\Collections\Criteria $criteria
     *
     * @return \Zend\Paginator\Paginator
     */
    public function fetchAll(Criteria $criteria)
    {
        return $this->getRepository()->matching($criteria);
    }

    /**
     * Save the user
     *
     * @param \MCNStdlib\Interfaces\UserEntityInterface $user
     *
     * @return void
     */
    public function save(UserEntityInterface $user)
    {
        if (! $this->objectManager->contains($user)) {

            $this->getEventManager()->trigger('persist', $this, array('user' => $user));
            $this->objectManager->persist($user);
        }

        $this->getEventManager()->trigger('flush.pre', $this, array('user' => $user));
        $this->objectManager->flush($user);
        $this->getEventManager()->trigger('flush.post', $this, array('user' => $user));
    }

    /**
     * Remove a user
     *
     * @param \MCNStdlib\Interfaces\UserEntityInterface $user
     *
     * @return void
     */
    public function remove(UserEntityInterface $user)
    {
        $this->getEventManager()->trigger('delete.pre', $this, array('user' => $user));
        $this->objectManager->remove($user);
        $this->objectManager->flush($user);
        $this->getEventManager()->trigger('delete.post', $this, array('user' => $user));
    }
}
