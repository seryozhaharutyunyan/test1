<?php
require_once __DIR__ . '/Bootstrap.php';

use \Engine\Core\Database\Connection;
use Engine\Core\Database\QueryBuilder;
use Engine\Core\Migration\Migrations;
use \Engine\Helper\Store;
use Engine\Core\Config\Config;

$db = Connection::getInstance();
$queryBuilder = new QueryBuilder();

$sql = $sql = 'SHOW TABLES';
$tables = $db->setAll($sql, [], \PDO::FETCH_ASSOC);
foreach ($tables as $table) {
    if ($table["Tables_in_" . Config::item('db_name', 'database')] === 'migrations') {
        $sql = $queryBuilder->select()
            ->from('migrations')
            ->sql();
        $migrations = $db->setAll($sql);
    }
}

fwrite(STDERR, "Migration name \n");
$migrationName = fgets(STDIN);
$migrationName = trim($migrationName);

$flag = false;
if (isset($migrations) && !empty($migrations)) {
    foreach ($migrations as $migration) {
        if ($migrationName === $migration->name) {
            $flag = true;
        }
    }
}
if (isset($migrations) && empty($migrations) && $migrationName === 'all') {
    (new Migrations())->rollback();
    echo "Success";
    exit();
}
if ($migrationName === 'all' && isset($migrations) && !empty($migrations)) {
    $flag = true;
}

$migrationName = ucfirst($migrationName);
if ($flag) {
    if ($migrationName === 'All') {
        $migrations = Store::scanDir('migration\\Classes');
        krsort($migrations);

        foreach ($migrations as $migration) {
            $m = "\\Migration\\Classes\\$migration";
            (new $m())->rollback();
        }

        (new Migrations())->rollback();
    } else {
        $m = "\\Migration\\Classes\\$migrationName";
        (new $m())->rollback();
    }

    echo "Success";
} else {
    echo 'No such migration exists';
}

