<?php

/*
 |-------------------------------------------------------
 | Application paths
 |-------------------------------------------------------
 */

if (! function_exists('base_path')):
    function base_path(){
        return realpath(__DIR__ . '/..');
    }
endif;

return [

    'appPath'         => base_path() . '/app',

    'assetsPath'      => base_path() . '/assets',

    'basePath'        => base_path(),

    'configPath'      => base_path() . '/config',

    'controllersPath' => base_path() . '/app/Controllers',

    'corePath'        => base_path() . '/app/Core',

    'publicPath'      => base_path() . '/public',

    'viewsPath'       => base_path() . '/app/Views',

];