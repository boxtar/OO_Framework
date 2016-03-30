<?php
/*
 |-------------------------------------------------------
 | Application files
 |-------------------------------------------------------
 */

if (! function_exists('base_path')):
    function base_path(){
        return realpath(__DIR__ . '/..');
    }
endif;

return [

    'helpersFile'   => base_path() . '/app/helpers.php',

    'routesFile'    => base_path() . '/app/routes.php',

];