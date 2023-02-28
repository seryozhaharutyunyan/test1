<?php

namespace Engine\Core\Router;

use Engine\Core\Auth\Auth;
use Engine\Core\Response\Response;
use Engine\DI\DI;

final class Router
{

    private array $routes;
    private string $host;
    private ?UrlDispatcher $dispatcher = null;

    /**
     * @param string $host
     * @throws \Exception
     */
    public function __construct(string $host)
    {
        $this->host = $host;
    }

    /**
     * @return array
     */
    public function get_routes(): array
    {
        return $this->routes;
    }

    /**
     * @param string $key
     * @param string $pattern
     * @param string $controller
     * @param string $method
     *
     * @return void
     */
    public function add(string $key, string $method, string $pattern, string $controller, string $guarded = ''): void
    {
        $this->routes[$key] = [
            'pattern' => $pattern,
            'controller' => $controller,
            'method' => strtoupper($method),
            'guarded' => strtolower($guarded)
        ];
    }

    /**
     * @param $method
     * @param $uri
     *
     * @return DispatchedRoute|null
     */
    public function dispatch($method, $uri): ?DispatchedRoute
    {
        return $this->getDispatcher()->dispatch($method, $uri);
    }


    /**
     * @return UrlDispatcher|int
     */
    public function getDispatcher(): ?UrlDispatcher
    {
        if ($this->dispatcher === null) {
            $this->dispatcher = new UrlDispatcher();
            foreach ($this->routes as $route) {
                if ($route['guarded'] === 'auth' && !Auth::authorized()) {
                    $this->dispatcher->register($route['method'], $route['pattern'], 'ErrorController:unauthorized');
                } else {
                    $this->dispatcher->register($route['method'], $route['pattern'], $route['controller']);
                }

            }
        }

        return $this->dispatcher;
    }
}