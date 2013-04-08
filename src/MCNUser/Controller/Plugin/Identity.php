<?php
/**
 * @Author Jonas Eriksson <jonas@pmg.se>
 * Date: 2/22/13
 * Time: 1:46 PM
 */

namespace MCNUser\Controller\Plugin;

use MCNStdlib\Interfaces\UserEntityInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class Auth
 * @package MCNUser\Controller\Plugin
 */
class Identity extends AbstractPlugin
{
    /**
     * @var \MCNStdlib\Interfaces\UserEntityInterface
     */
    protected $entity;

    /**
     * @param \MCNStdlib\Interfaces\UserEntityInterface $entity
     */
    public function __construct(UserEntityInterface $entity = null)
    {
        $this->entity = $entity;
    }

    /**
     * @return \MCNStdlib\Interfaces\UserEntityInterface
     */
    public function __invoke()
    {
        return $this->entity;
    }
}
