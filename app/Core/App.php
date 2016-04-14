<?php

namespace App\Core;

use App\Core\Contracts\Router;
use App\Core\Contracts\ViewComposer;
use App\Controllers\Contracts\Controller;
use App\Core\Exceptions\NoActionFoundException;
use App\Core\Exceptions\NoRouteFoundException;
use App\Core\Exceptions\NoControllerFoundException;
use App\Core\Exceptions\NoViewFoundException;

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
     | The name of the controller. Provided by
     | the Router
     |----------------------------------------------
     */
    protected $controller;

    /*
     |----------------------------------------------
     | The name of the action to call. Provided by
     | the Router
     |----------------------------------------------
     */
    protected $action;

    /*
     |----------------------------------------------
     | Parameters, if any, to pass into action.
     | They are extracted from route by Router
     |----------------------------------------------
     */
    protected $params;



    /* Constructor */
    public function __construct(Router $router, ViewComposer $viewComposer)
    {
        $this->controllersNamespace = '\\'. config()->get('app.name') . '\\Controllers\\';
        $this->router = $router;
        $this->viewComposer = $viewComposer;
    }


    /**
     * This function is what kicks off the app
     *
     * @return bool|mixed
     */
    public function start()
    {
        try{
            $this->router->routeRequest();

            $this->setupController();

            $this->setupAction();

            $this->setupParametersForAction();

            return call_user_func_array([ $this->controller, $this->action ], $this->params);
        }

        // No route is a 404 not found error
        catch(NoRouteFoundException $e){

            $this->handleNoRouteException($e);

        }

        // All other Exceptions are 500 server errors
        catch(\Exception $e){

            $this->handleServerException($e);

        }
        return true;
    }


    /**
     * Creates an instance of the Controller required to handle
     * the request
     *
     * @return $this
     * @throws NoControllerFoundException
     */
    protected function setupController()
    {
        $controllerName = $this->router->getControllerName();

        if (! file_exists(config()->get('paths.controllersPath') . '/' . $controllerName . '.php'))
            throw new NoControllerFoundException( 'Controller not found: ' . $controllerName );

        $fullControllerName = $this->getFullControllerName($controllerName);

        $this->controller = new $fullControllerName($this->viewComposer);

        return $this;
    }


    /**
     * Sets the action within the Controller that is to be called
     * to handle the request
     *
     * @return $this
     * @throws NoActionFoundException
     */
    protected function setupAction()
    {
        if (! method_exists( $this->controller, $action = $this->router->getActionName() ) )
            throw new NoActionFoundException('Action \'' . $action . '\' not found in ' . get_class($this->controller));

        $this->action = $action;

        return $this;
    }

    /**
     * Fetch any parameters within the route to pass into
     * the action
     *
     * @return $this
     */
    protected function setupParametersForAction()
    {
        $this->params = ( $params = $this->router->getRouteParameters() ) ? $params : [];

        return $this;
    }

    /**
     * Returns Controller name with namespace prepended
     *
     * @param $controller
     * @return string
     */
    protected function getFullControllerName($controller)
    {
        return $this->controllersNamespace . $controller;
    }

    /**
     * Handle user error exceptions:
     *
     * NoRouteFoundException
     *
     * @param $e
     */
    protected function handleNoRouteException(\Exception $e)
    {
        try {
            http_response_code(404);

            $this->viewComposer->getView('/errors/404', [
                    'message' => $e->getMessage()
                ]
            );
        } catch (\Exception $e) {
            echo '404 - Page not found';
            die();
        }
    }

    /**
     * Handle exceptions that are the result of server error:
     *
     * NoControllerFoundException
     * NoActionFoundException
     * NoViewFoundException
     *
     * @param $e
     */
    protected function handleServerException(\Exception $e)
    {
        try {
            http_response_code(500);

            $this->viewComposer->getView('/errors/500', [
                    'message' => $e->getMessage()
                ]
            );
        } catch (\Exception $e) {
            echo '500 - Internal Server Error';
            die();
        }
    }
}