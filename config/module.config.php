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

            'mcn.service.user.authentication' => 'MCNUser\Factory\AuthenticationServiceFactory'
        )
    )
);
