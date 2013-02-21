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
            'plugins' => array(
                'MCNUser\Options\Authentication\Plugin\Standard' => array(
                    'http_identity_field' => 'identity',
                    'http_credential_field' => 'credential',

                    'entity_identity_property'   => 'email',
                    'entity_credential_property' => 'password',

                    // Credential treatment is applied on the user supplied password
                    // before comparing it with the password stored in the backend
                    'credential_treatment' => function($password) {

                        return sha1($password);
                    }
                )
            )
        )
    ),

    'service_manager' => array(
        'factories' => array(

            // services
            'mcn.service.user.authentication' => 'MCNUser\Factory\AuthenticationServiceFactory',

            // options
            'mcn.options.user.authentication' => 'MCNUser\Factory\AuthenticationOptionsFactory'
        ),

        'invokables' => array(

            'mcn.authentication.plugin.standard' => 'MCNUser\Options\Authentication\Plugin\Standard'
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
