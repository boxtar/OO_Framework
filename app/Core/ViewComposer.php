<?php

namespace App\Core;


use App\Core\Contracts\ViewComposer as ViewComposerContract;
use App\Core\Exceptions\NoViewFoundException;

class ViewComposer implements ViewComposerContract
{

    /*
     |----------------------------------------------
     | Location of views
     | TODO: make this configurable
     |----------------------------------------------
     */
    protected $viewsDirectory;

    public function __construct($viewPath = '')
    {
        $this->viewsDirectory = empty($viewPath) ? config()->get('paths.viewsPath') : $viewPath;
    }

    public function setViewPath($path)
    {

    }

    public function getView($view, $data=[])
    {
        if (! file_exists( $view = $this->viewsDirectory . '/' . $view . '.php' ))
            throw new NoViewFoundException('View not found: [' . $view . ']');
        require_once $view;
    }
}