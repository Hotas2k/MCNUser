<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser;

/**
 * Class Module
 *
 * @package MCNUser
 */
class Module
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(

                'mcn.service.user' => 'MCNUser\Service\User'
            ),

            'factories' => array(

                'mcn.service.user.authentication' => 'MCNUser\Factory\AuthenticationServiceFactory'
            )
        );
    }
}
