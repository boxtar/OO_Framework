<?php

namespace App\Core;

use App\Core\Contracts\Router;
use App\Core\Contracts\ViewComposer;
use App\Controllers\Contracts\Controller;

class App{

    /*
     |----------------------------------------------
     | The applications Router
     |----------------------------------------------
     */
    protected $router;

    /*
     |----------------------------------------------
     | The applications View Composer
     |----------------------------------------------
     */
    public $viewComposer;


    /*
     |----------------------------------------------
     | The namespace for the Controllers
     | TODO: Make configurable
     |----------------------------------------------
     */
    protected $controllersNamespace;

    /*
     |----------------------------------------------
     |----------------------------------------------
     */
    protected $controller;

    /*
     |----------------------------------------------
     |----------------------------------------------
     */
    protected $action;

    /*
     |----------------------------------------------
     |----------------------------------------------
     */
    public function __construct(Router $router, ViewComposer $viewComposer)
    {
        $this->controllersNamespace = '\\'. config()->get('app.appName') . '\\Controllers\\';
        $this->router = $router;
        $this->viewComposer = $viewComposer;
    }


    protected $params;

    public function start()
    {
        $this->router->routeRequest();

        $this->setupController();

        $this->setupAction();

        $this->setupParametersForController();

        return call_user_func_array([ $this->controller, $this->action ], $this->params);
    }

    protected function setupController()
    {
        $controllerName = $this->router->getControllerName();

        if (! file_exists(config()->get('paths.controllersPath') . '/' . $controllerName . '.php'))
            throw new \Exception( $controllerName . ' not found' );

        $fullControllerName = $this->getFullControllerName($controllerName);

        $this->controller = new $fullControllerName($this->viewComposer);

        return $this;
    }

    protected function setupAction()
    {
        if (! method_exists( $this->controller, $action = $this->router->getActionName() ) )
            throw new \Exception('Method ' . $action . ' not found in ' . get_class($this->controller));

        $this->action = $action;

        return $this;
    }

    protected function setupParametersForController()
    {
        $this->params = ( $params = $this->router->getRouteParameters() ) ? $params : [];

        return $this;
    }

    protected function getFullControllerName($controller)
    {
        return $this->controllersNamespace . $controller;
    }


    /**
     * @param Controller $controller
     * @internal param $ \Yaldi\Controllers\Contracts\Controller $
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

}