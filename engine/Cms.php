<?php

namespace Engine;

use App\Model\User\User;
use Engine\Core\Auth\Auth;
use Engine\Core\Response\Response;
use Engine\Core\Router\DispatchedRoute;
use Engine\Core\Router\Router;
use Engine\DI\DI;
use Engine\Helper\Common;

class Cms
{
    private DI $di;
    public Router $router;
    private Response $response;

    /**
     * @param DI $di
     */
    public function __construct(DI $di)
    {
        $this->di = $di;
        $this->response = $this->di->get('response');
        $this->router = $this->di->get('router');
    }

    /**
     * run app
     */
    public function run(): void
    {
        try {
            require_once __DIR__ . "/../routes/routes.php";
            if (Auth::authProtection()===409){
                $this->response->send(409);
            }

            $routerDispatch = $this->router->dispatch(Common::getMethod(), Common::getPathUrl());
            if ($routerDispatch === null) {
                $routerDispatch = new DispatchedRoute('ErrorController:page404');
            }
            if (str_starts_with($routerDispatch->get_controller(), 'Error')) {
                $routerDispatch = new DispatchedRoute($routerDispatch->get_controller());
            }

            [$class, $action] = \explode(':', $routerDispatch->get_controller(), 2);
            $controller = "\\App\\Controller\\" . $class;
            if ($class === 'ErrorController') {
                $controller = '\\Engine\\' . $class;
            }
            $parameters = $routerDispatch->get_parameters();
            $Controller = new $controller($this->di);
            if (!empty($_GET)) {
                $Controller->setGetParams($_GET);
            }
            $parameters = $this->getClassParameters($controller, $action, $parameters);

            \call_user_func_array([$Controller, $action], $parameters);
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function getClassParameters($controller, $action, $parameters): array
    {
        $types = [];
        $r = new \ReflectionMethod($controller, $action);
        $params = $r->getParameters();
        foreach ($params as $param) {
            $type = (string)$param->getType();
            if (str_contains($type, 'App\\Request\\')) {
                $types[] = new $type($this->di);
            }
            if (str_contains($type, 'App\\Model\\') && isset($parameters['id'])) {
                $types[] = new $type($parameters['id']);
                unset($parameters['id']);
            }
        }

        if (!empty($type)) {
            $types[] = $parameters;
            return $types;
        }

        return $parameters;
    }
}