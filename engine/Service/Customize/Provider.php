<?php

namespace Engine\Service\Customize;

use Engine\Core\Customize\Customize;
use Engine\Service\AbstractProvider;

class Provider extends AbstractProvider
{
    public string $serviceName = 'customize';

    public function init()
    {
        $customize = new Customize();

        $this->di->set($this->serviceName, $customize);

        return $this;
    }
}