<?php

class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    // Private constructor to prevent direct object creation
    private function __construct() {
        $this->connection = new PDO('sqlite:../database/sixer.db');
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Public static method to get the single instance
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the PDO connection
    public function getConnection(): PDO {
        return $this->connection;
    }
}

?>
