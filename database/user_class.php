<?php
declare(strict_types=1);
require_once(__DIR__ . '/../utils/database.php');

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
    
    // Get the total number of completed services for this freelancer
    public static function getTotalCompletedServices(int $user_id): int {
        $db = Database::getInstance();
        // Get all service ids for this freelancer
        $stmt = $db->prepare('SELECT service_id FROM services_list WHERE freelancer_id = ?');
        $stmt->execute([$user_id]);
        $services = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // If no services, return 0
        if (empty($services)) {
            return 0;
        }
        
        // Sum up the total customers for all services
        $total = 0;
        foreach ($services as $service_id) {
            require_once(__DIR__ . '/service_class.php');
            $total += Service::getTotalCustomers($service_id);
        }
        
        return $total;
    }
    
    // Get the average rating for all services by this freelancer
    public static function getAverageRating(int $user_id): float {
        $db = Database::getInstance();
        // Get all service ids for this freelancer
        $stmt = $db->prepare('SELECT service_id FROM services_list WHERE freelancer_id = ?');
        $stmt->execute([$user_id]);
        $services = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // If no services, return 0
        if (empty($services)) {
            return 0.0;
        }
        
        // Get the ratings for all services
        $total_rating = 0.0;
        $total_reviews = 0;
        
        foreach ($services as $service_id) {
            require_once(__DIR__ . '/service_class.php');
            $rating_info = Service::getServiceRatingInfo($service_id);
            $total_rating += $rating_info['avg'] * $rating_info['count']; // Weighted by number of reviews
            $total_reviews += $rating_info['count'];
        }
        
        // Calculate the weighted average
        if ($total_reviews > 0) {
            return round($total_rating / $total_reviews, 1);
        } else {
            return 0.0;
        }
    }
}