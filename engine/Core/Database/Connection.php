<?php

namespace Engine\Core\Database;

use Engine\Core\Config\Config;
use Engine\Singleton;
use PDO;


class Connection
{
    use Singleton;

    private PDO $link;


    /**
     * @return void
     * @throws \Exception
     */
    private function connect(): void
    {
        $config = Config::group('database');
        $dsn    = "$config[driver]:host=$config[host];dbname=$config[db_name];charset=$config[charset]";

        $this->link = new PDO($dsn, $config['username'], $config['password']);

    }


    /**
     * @param string $sql
     * @param array $values
     * @return bool
     */
    public function execute(string $sql, array $values=[]): bool
    {
        $sth = $this->link->prepare($sql);

        return $sth->execute($values);
    }

    /**
     * @param string $sql
     * @param array $values
     * @return array
     */
    public function setAll(string $sql, array $values=[], int $statement=PDO::FETCH_OBJ): array
    {
        return $this->query('fetchAll', $sql, $values, $statement);
    }

    public function set(string $sql, array $values=[], int $statement=PDO::FETCH_OBJ): array|object
    {
        return $this->query('fetch', $sql, $values, $statement);
    }

    /**
     * @param string $method
     * @param string $sql
     * @param array $values
     * @return array|object
     */
    private function query(string $method, string $sql, array $values=[], int $statement=PDO::FETCH_OBJ): array|object
    {

        $sth = $this->link->prepare($sql);

        $sth->execute($values);

        $result = $sth->{$method}($statement);

        if ($result === false) {
            return [];
        }

        return $result;
    }

    public function lastInsertId(): bool|string
    {
        return $this->link->lastInsertId();
    }

}