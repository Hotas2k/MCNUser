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
                'MCNUser\Options\Authentication\Plugins\Standard' => array(

                    'identity_property'   => 'email',
                    'credential_property' => 'password',

                    // Credential treatment is applied on the user supplied password
                    // before comparing it with the password stored in the backend
                    'credential_treatment' => function($password) {

                        return sha1($password);
                    }
                )
            )
        )
    )
);
