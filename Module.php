<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;


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
            'factories' => array(

                'mcn.listener.user.authentication.update-login' => function(ServiceLocatorInterface $sm) {

                    return new Listener\Authentication\LastLogin($sm->get('mcn.service.user'));
                },

                'mcn.service.user' => function(ServiceLocatorInterface $sm) {

                    return new Service\User($sm->get('doctrine.entitymanager.ormdefault'));
                }
            )
        );
    }
}
