<?php

namespace App\Core\Contracts;

interface ViewComposer{

    public function setViewPath($path);

    public function getView($view);

}