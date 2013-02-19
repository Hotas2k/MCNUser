<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNUser;

use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Module
 *
 * @package MCNUser
 */
class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
    }


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

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'factories' => array(

                'mcn.user.authentication' => function(ControllerManager $sm) {

                    return new Controller\AuthenticationController(
                        $sm->getServiceLocator()->get('mcn.service.user.authentication')
                    );
                }
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'isAuth' => function ($sm) {
                    return new View\Helper\IsAuth($sm->getServiceLocator()->get('mcn.service.user.authentication'));
                },
            ),
        );

    }

    /**
     * @return array
     */
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
