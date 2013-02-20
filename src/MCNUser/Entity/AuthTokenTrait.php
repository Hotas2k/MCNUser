<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AuthTokenTrait
 * @package MCNUser\Entity
 */
trait AuthTokenTrait
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MCNUser\Entity\AuthToken")
     */
    protected $auth_tokens;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAuthTokens()
    {
        return $this->auth_tokens;
    }

    abstract public function getId();
}
