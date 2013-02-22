<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Options\Authentication\Plugin;

use Zend\Stdlib\AbstractOptions;

/**
 * Class AbstractPluginOptions
 * @package MCNUser\Options\Authentication\Plugin
 */
abstract class AbstractPluginOptions extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $enabled = true;

    /**
     * Class name of representing plugin
     *
     * @return string
     */
    abstract public function getClassName();

    /**
     * Plugin alias
     *
     * @return string
     */
    abstract public function getPluginManagerAlias();

    /**
     * SL alias
     *
     * @return string
     */
    abstract public function getServiceManagerAlias();

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
