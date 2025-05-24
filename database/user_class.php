<?php
declare(strict_types=1);
require_once(__DIR__ . '/../utils/database.php');

require_once(__DIR__ . '/../database/user_class.php');

class User {
    public int $id;
    public string $email;
    public string $full_name;

    public function __construct(int $id, string $full_name, string $email) {
        $this->id = $id;
        $this->full_name = $full_name;
        $this->email = $email;
    }

    public static function create($full_name, $email, $password) {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO user_registry (full_name, email, password_hash, join_date) VALUES (?, ?, ?, DATETIME(\'now\'))');
        $stmt->execute([$full_name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }

    public static function get_user_by_email_password($email, $password) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM user_registry WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    public static function get_user_by_email($email) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM user_registry WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) return null;
        return new User($user['user_id'], $user['full_name'], $user['email']);
    }

    public function update() {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE user_registry SET full_name = ? WHERE user_id = ?');
        $stmt->execute([$this->full_name, $this->id]);
    }

    public static function get_user_purchases($user_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM purchases WHERE client_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}