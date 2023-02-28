<?php

namespace Engine\Core\Migration;

use Engine\Core\Database\Connection;
use Engine\Core\Database\QueryBuilder;

abstract class Migration
{
    use MigrationBuilder;

    protected string $table;
    protected string $prefix = '';
    protected string $create = '';
    protected string $migrationName;
    protected Connection $db;
    protected QueryBuilder $queryBuilder;


    public function __construct()
    {
        $className = \explode('\\', \get_class($this));
        $migrationName = \strtolower($className[\count($className) - 1]);
        $this->migrationName=$migrationName;

        $this->db = Connection::getInstance();

        $this->queryBuilder = new QueryBuilder();

        $this->create .= "CREATE TABLE $this->table (";
    }

    public abstract function start();

    public abstract function rollback();

    /**
     * @return string
     */
    public function getCreate(): string
    {
        return $this->create;
    }


}