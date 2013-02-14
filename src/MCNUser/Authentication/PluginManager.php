<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Authentication;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package MCNUser\Authentication
 */
class PluginManager extends AbstractPluginManager
{
    /**
     * Default set of plugins
     *
     * @var array
     */
    protected $invokableClasses = array(
        'standard' => 'MCNUser\Authentication\Plugin\Standard',
    );

    /**
     * Validate the plugin
     *
     * Checks that the plugin loaded is an instance of Plugin\PluginInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidPluginException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Plugin\PluginInterface) {

            return;
        }

        throw new Exception\InvalidPluginException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Plugin\PluginInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
