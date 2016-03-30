<?php

namespace App\Core;


use App\Core\Contracts\ViewComposer as ViewComposerContract;

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
            throw new \Exception('No view found: [' . $view . ']');
        require_once $view;
    }
}