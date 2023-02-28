<?php

namespace Engine\Core\Router;

final class UrlDispatcher
{
    private array $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];
    private array $routes = [
        'GET'  => [],
        'POST' => [],
        'PATCH' => [],
        'PUT' => [],
        'DELETE'=>[]
    ];
    protected array $patterns = [
        'int' => '[0-9]+',
        'str' => '[A-Za-z\.\-_%]+',
        'any' => '[A-Za-z0-9\.\-_%]+',
    ];

    /**
     * @param $key
     * @param $pattern
     *
     * @return void
     */
    public function addPattern($key, $pattern): void
    {
        $this->patterns[$key] = $pattern;
    }

    /**
     * @param $method
     *
     * @return array
     */
    public function routes($method): array
    {
        return $this->routes[$method] ?? [];
    }

    /**
     * @param $method
     * @param $uri
     *
     * @return DispatchedRoute|null
     */
    public function dispatch($method, $uri): DispatchedRoute|null
    {
        $routes = $this->routes(\strtoupper($method));

        if (array_key_exists($uri, $routes)) {
            return new DispatchedRoute($routes[$uri]);
        }

        return $this->doDispatch($method, $uri);
    }

    /**
     * @param   string  $method
     * @param   string  $pattern
     * @param   string  $controller
     *
     * @return void
     */
    public function register(string $method, string $pattern, string $controller)
    {
        $convert                                      = $this->convertPattern($pattern);
        $this->routes[\strtoupper($method)][$convert] = $controller;
    }

    /**
     * @param string $pattern
     *
     * @return array|string
     */
    private function convertPattern(string $pattern): array|string
    {
        if ( ! str_contains($pattern, '{')) {
            return $pattern;
        }

        return \preg_replace_callback('#\{(\w+):(\w+)\}#', [$this, 'replacePattern'], $pattern);
    }

    /**
     * @param $matches
     *
     * @return string
     */
    private function replacePattern($matches): string
    {
        return "(?<$matches[1]>" . strtr($matches[2], $this->patterns) . ")";
    }

    /**
     * @param   array  $parameters
     *
     * @return array
     */
    private function processParam(array $parameters): array
    {
        foreach ($parameters as $key => $value) {
            if (\is_int($key)) {
                unset($parameters[$key]);
            }
        }

        return $parameters;
    }

    /**
     * @param $method
     * @param $uri
     *
     * @return DispatchedRoute|void
     */
    private function doDispatch($method, $uri)
    {
        $routes = $this->routes($method);

        foreach ($routes as $route => $controller) {
            $pattern = "#^$route$#s";

            if (\preg_match($pattern, $uri, $parameters)) {
                return new DispatchedRoute($controller, $this->processParam($parameters));
            }
        }
    }

}