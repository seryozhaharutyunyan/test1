<?php

namespace Engine\DI;

class DI
{
    /**
     * @var array
     */
    private array $container = [];

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): DI
    {
        $this->container[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->has($key);
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function has(string $key): mixed
    {
         return $this->container[$key] ?? null;
    }
}