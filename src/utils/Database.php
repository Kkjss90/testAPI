<?php
namespace App\utils;
use PDO;

class Database {
    private static $connection;

    public static function getConnection() {
        if (!self::$connection) {
            $config = require __DIR__ . '/../config/db.php';
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
            self::$connection = new PDO($dsn, $config['user'], $config['password']);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}
