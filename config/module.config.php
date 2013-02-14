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

            // Route to redirect to after a successful login
            'successful_login_route' => 'user/profile',

            // If a param by the name of "redirect" exists
            // and is not empty a user will be redirected to that page
            'enable_redirection' => true,

            // service locator key for the class to use
            'user_service_sl_key' => 'mcn.service.user',

            // list of plugins
            'plugins' => array(
                new \MCNUser\Options\Authentication\Plugin\Standard(array(

                    'entity_identity_property'   => 'email',
                    'entity_credential_property' => 'password',

                    // Credential treatment is applied on the user supplied password
                    // before comparing it with the password stored in the backend
                    'credential_treatment' => function($password) {

                        return sha1($password);
                    }
                ))
            )
        )
    )
);
