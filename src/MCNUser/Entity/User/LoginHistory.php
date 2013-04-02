<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use MCN\Object\Entity\AbstractEntity;

/**
 * Class LoginHistory
 * @package MCNUser\Entity\User
 *
 * @ORM\Table(name="mcn_user_login_history")
 * @ORM\Entity
 */
class LoginHistory extends AbstractEntity
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
     * @var string
     *
     * @ORM\Column
     */
    protected $httpUserAgent;

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $remoteIp;

    /**
     * @var \MCNUser\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="MCNUser\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
}
