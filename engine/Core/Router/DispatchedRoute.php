<?php

namespace Engine\Core\Router;

final class DispatchedRoute
{
    private string $controller;
    private array $parameters;


    /**
     * @param   string  $controller
     * @param   array   $parameters
     */
    public function __construct(string $controller, array $parameters = [])
    {
        $this->controller = $controller;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function get_controller(): string
    {
        return $this->controller;
    }

    /**
     * @return array
     */
    public function get_parameters(): array
    {
        return $this->parameters;
    }

}