<?php

use App\Core\Config;

/*
 |-------------------------------------------------------
 | Helper for obtaining the Config class
 |-------------------------------------------------------
 */

function config(){
    return Config::getInstance();
}

function url($path=''){
    return config()->get('app.url') . $path;
}


