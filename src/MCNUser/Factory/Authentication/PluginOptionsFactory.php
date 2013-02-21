<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser\Factory\Authentication;

use MCNUser\Options\Authentication\Plugin\AbstractPluginOptions;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PluginOptionsFactory
 * @package MCNUser\Factory\Authentication
 */
class PluginOptionsFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param string                  $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (
            class_exists($requestedName) &&
            in_array('MCNUser\Options\Authentication\Plugin\AbstractPluginOptions', class_parents($requestedName))
        );
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');
        $config = isSet($config['MCNUser']['authentication']['plugins'][$requestedName])
                ? $config['MCNUser']['authentication']['plugins'][$requestedName] : array();

        if ($config instanceof AbstractPluginOptions) {

            return $config;
        }

        return new $requestedName($config);
    }
}
