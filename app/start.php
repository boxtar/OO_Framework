<?php

use App\Core\App;
use App\Core\Router;
use App\Core\ViewComposer;

require_once 'helpers.php';

// TODO: Think about making the app a singleton

$app = new App(
    new Router(),
    new ViewComposer()
);

// Lets do this!
$app->start();
