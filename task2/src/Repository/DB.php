<?php

namespace AddressFinder\Repository;
class DB
{
    private static $pdo;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public function __sleep()
    {

    }

    public function __wakeup()
    {

    }

    public static function getPDO(array $config): \PDO
    {
        if (!is_null(self::$pdo)) {
            return self::$pdo;
        }

        self::$pdo = new \PDO("mysql:host=" . $config['dbHost'] . ";dbname=" . $config['dbName'] , $config['dbUser'], $config['dbPassword']);
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        self::$pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);

        return self::$pdo;
    }
}