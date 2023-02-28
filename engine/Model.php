<?php

namespace Engine;

use Engine\Core\Config\Config;
use Engine\Core\Database\Connection;
use Engine\Core\Database\QueryBuilder;
use Engine\DI\DI;

abstract class Model
{
    /**
     * @var DI
     */
    protected DI $di;

    protected Connection $db;

    protected ?array $config;

    public QueryBuilder $queryBuilder;

    /**
     * Model constructor.
     * @param DI $di
     */
    public function __construct(DI $di)
    {
        $this->di      = $di;
        $this->db      = $this->di->get('db');
        $this->config  = $this->di->get('config');

        $this->queryBuilder = new QueryBuilder();
    }
}