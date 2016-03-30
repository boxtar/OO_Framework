<?php

namespace App\Controllers;

use App\Controllers\Contracts\Controller as ControllerContract;
use App\Core\ViewComposer;

abstract class Controller implements ControllerContract
{

    protected $viewComposer;

    public function __construct(ViewComposer $viewComposer)
    {
        $this->viewComposer = $viewComposer;
    }

    protected function view($view, $data=[])
    {
        $this->viewComposer->getView($view, $data);
    }
}

