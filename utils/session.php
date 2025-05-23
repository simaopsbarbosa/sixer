<?php
declare(strict_types=1);

class Session {
    private static ?Session $instance = null;

    public static function getInstance(): Session {
        if (self::$instance === null) {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    public function __construct() {
        session_start();
    }

    public function isLoggedIn() {
        return isset($_SESSION["user"]) && $_SESSION["user"] !== null;
    }

    public function getUser() {
        return $_SESSION["user"] ?? null;
    }

    public function login($user) {
        $_SESSION["user"] = $user;
    }

    public function logout() {
        session_destroy();
    }
}
?>