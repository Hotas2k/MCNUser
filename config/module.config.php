<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

return array(
    'MCNUser' => array(
        'authentication' => array(

        )
    ),

    'service_manager' => array(
        'factories' => array(

            // services
            'mcn.service.user.authentication' => 'MCNUser\Factory\AuthenticationServiceFactory',

            // options
            'mcn.options.user.authentication' => 'MCNUser\Factory\AuthenticationOptionsFactory'
        )
    ),

    'doctrine' => array(
        'driver' => array(
            'mcnuser_annotation_driver' => array(
                'class'     => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths'     => array(
                    __DIR__ . '/../src/MCNUser/Entity/',
                ),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'MCNUser\Entity' => 'mcnuser_annotation_driver'
                )
            )
        )
    ),
);
