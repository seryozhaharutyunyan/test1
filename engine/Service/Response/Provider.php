<?php

namespace Engine\Service\Response;

use Engine\Core\Request\Request;
use Engine\Core\Response\Response;
use Engine\Core\Router\Router;
use Engine\Service\AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * @var string
     */
    public string $serviceName='response';

    public function init()
    {
        $response= new Response($this->di);

        $this->di->set($this->serviceName, $response);
    }
}