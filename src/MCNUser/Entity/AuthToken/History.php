<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity\AuthToken;

use DateTime;
use MCN\Object\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class History
 * @package MCNUser\Entity\AuthToken
 *
 * @ORM\Entity
 * @ORM\Table(name="mcn_token_history")
 */
class History extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var \MCNUser\Entity\AuthToken
     *
     * @ORM\ManyToOne(targetEntity="MCNUser\Entity\AuthToken")
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $ip;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $http_user_agent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt(DateTime $created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param \MCNUser\Entity\AuthToken $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return \MCNUser\Entity\AuthToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $user_agent
     */
    public function setHttpUserAgent($user_agent)
    {
        $this->http_user_agent = $user_agent;
    }

    /**
     * @return string
     */
    public function getHttpUserAgent()
    {
        return $this->http_user_agent;
    }
}
