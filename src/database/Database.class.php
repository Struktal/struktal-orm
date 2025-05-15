<?php

namespace jensostertag\DatabaseObjects\Database;

use \PDO;

class Database {
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    /**
     * Returns the database instance
     * @return Database
     */
    public static function getInstance(): Database {
        if(self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Connects to the database using the given parameters
     * @param string $host
     * @param string $database
     * @param string $user
     * @param string $password
     * @return void
     */
    public static function connect(string $host, string $database, string $user, string $password): void {
        self::getInstance()->connection = new PDO("mysql:host={$host};dbname={$database}", $user, $password, [
            PDO::MYSQL_ATTR_FOUND_ROWS => true
        ]);
    }

    /**
     * Sets the PDO database connection
     * @param PDO $connection
     * @return void
     */
    public static function setConnection(PDO $connection): void {
        self::getInstance()->connection = $connection;
    }

    /**
     * Returns the PDO database connection
     * @return PDO|null
     */
    public static function getConnection(): ?PDO {
        return self::getInstance()->connection;
    }
}
