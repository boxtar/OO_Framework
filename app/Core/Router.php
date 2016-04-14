<?php

namespace App\Core;

use App\Core\Exceptions\NoRouteFoundException;
use App\Core\Contracts\Router as RouterContract;

class Router implements RouterContract
{

    /*
     |------------------------------------------------------------
     | Routes registered by the app
     |------------------------------------------------------------
     */
    protected $registeredRoutes = [];

    /*
     |------------------------------------------------------------
     | String representation of the targeted Controller Class
     |------------------------------------------------------------
     */
    protected $controller;

    /*
     |------------------------------------------------------------
     | Name of the method to call within the Controller
     |------------------------------------------------------------
     */
    protected $action = 'index';

    /*
     |------------------------------------------------------------
     | Parameters from the Request URI to be passed to the
     | method (action) within the Controller
     |------------------------------------------------------------
     */
    protected $params = [];

    /*
     |------------------------------------------------------------
     | Registered routes prepared for regex matching to capture
     | variable request URI parameters
     |------------------------------------------------------------
     */
    protected $preparedRoutes = [];


    public function __construct()
    {
        $this->registeredRoutes = require_once config()->get('files.routesFile');
    }

    /**
     * Entry point for checking the request URI and routing it accordingly
     *
     * @return array|bool
     * @throws \Exception
     */
    public function routeRequest()
    {

        // TODO:Check registered routes to make sure they are valid and that the default '/' route is provided

        $this->prepareRegisteredRoutes();

        // Match the request URI to a registered route
        if (! $this->matchRequestUriToRoute())
            throw new NoRouteFoundException('No route found for URI: ' . $this->getRoute());

        return $this;
    }

    public function getControllerName()
    {
        return isset( $this->controller ) ? $this->controller : false;
    }

    public function getActionName()
    {
        return isset( $this->action ) ? $this->action : false;
    }

    public function getRouteParameters()
    {
        return (! empty( $this->params ) ) ? $this->params : false;
    }


    /**
     * Sets up another array of the registered routes with variable parameters
     * replaced with a regular expression for matching up with the request URI.
     * This enables us to capture parameters to pass to the registered Controller.
     *
     * @return array
     */
    protected function prepareRegisteredRoutes()
    {
        foreach($this->registeredRoutes as $route => $routeAction){
            $prepared = preg_replace('/{[\w-]+}/', '([\w-]+)', $route);
            $this->preparedRoutes[$prepared] = $routeAction;
        }
        return $this->preparedRoutes;
    }

    /**
     * Matches the Request URI to a registered route and sets
     * the Controller, Action and parameters
     *
     * @return bool
     */
    protected function matchRequestUriToRoute()
    {
        /*
         |---------------------------------------------------------
         | If no Request URI then setup default Route which
         | must always be present
         |---------------------------------------------------------
         */
        if($this->getRoute() === ''){
            $this->setControllerAndAction($this->registeredRoutes['/']);
            return true;
        }


        foreach($this->preparedRoutes as $route => $routeAction){

            if(preg_match( '@^' . $route . '$@', $this->getRoute(), $matches )){

                $this->setControllerAndAction($routeAction);

                /*
                 |---------------------------------------------------------
                 | $matches will always have at least 1 element since we
                 | have matched the URI to a registered route
                 |---------------------------------------------------------
                 */
                for( $i = 1 ; $i <= count($matches) - 1 ; $i++ ){
                    $this->params[] = $matches[$i];
                }

                return true;
            }

        }
        return false;
    }


    protected function setControllerAndAction($routeAction)
    {
        $bits = preg_split('/[\.@]/', $routeAction);

        if( ! empty($bits) ){

            $this->controller = $bits[0];

            $this->action = count($bits) > 1 ? $bits[1] : 'index';

        }
    }

    protected function getRouteParts($route)
    {
        return explode('/', $route);
    }

    protected function getRoute()
    {
        return $this->sanitizeUri($_SERVER['REQUEST_URI']);
    }

    protected function sanitizeUri($uri)
    {
        return filter_var(trim($uri, '/'), FILTER_SANITIZE_URL);
    }

}