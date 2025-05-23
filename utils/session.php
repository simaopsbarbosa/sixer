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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isLoggedIn() {
        return $_SESSION["user"] !== null;
    }

    public function getUser() {
        return $_SESSION["user"];
    }

    public function login($user) {
        $_SESSION["user"] = $user;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = array();
        
        // If using session cookies, clear the cookie as well
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }
}
?>