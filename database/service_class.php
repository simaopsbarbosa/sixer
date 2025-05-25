<?php
declare(strict_types=1);
require_once(__DIR__ . '/../utils/database.php');

class Service {
    public int $id;
    public int $freelancer_id;
    public string $title;
    public float $price;
    public string $info;
    public int $eta;
    public string $category;
    public bool $delisted;
    public $picture; // Can be null or binary

    public function __construct(
        int $id,
        int $freelancer_id,
        string $title,
        float $price,
        string $info,
        int $eta,
        string $category,
        bool $delisted = false,
        $picture = null
    ) {
        $this->id = $id;
        $this->freelancer_id = $freelancer_id;
        $this->title = $title;
        $this->price = $price;
        $this->info = $info;
        $this->eta = $eta;
        $this->category = $category;
        $this->delisted = $delisted;
        $this->picture = $picture;
    }

    public static function create($freelancer_id, $title, $price, $info, $eta, $category, $picture = null) {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO services_list (freelancer_id, service_title, service_price, service_info, service_eta, service_category, service_picture) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$freelancer_id, $title, $price, $info, $eta, $category, $picture]);
        return $db->lastInsertId();
    }

    public static function get_by_id($service_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM services_list WHERE service_id = ?');
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();
        if (!$service) return null;
        return new Service(
            $service['service_id'],
            $service['freelancer_id'],
            $service['service_title'],
            $service['service_price'],
            $service['service_info'],
            (int)$service['service_eta'],
            $service['service_category'],
            (bool)$service['service_delisted'],
            $service['service_picture'] ?? null
        );
    }

    public static function get_by_freelancer($freelancer_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM services_list WHERE freelancer_id = ?');
        $stmt->execute([$freelancer_id]);
        $services = $stmt->fetchAll();
        return array_map(function($service) {
            return new Service(
                $service['service_id'],
                $service['freelancer_id'],
                $service['service_title'],
                $service['service_price'],
                $service['service_info'],
                (int)$service['service_eta'],
                $service['service_category'],
                (bool)$service['service_delisted'],
                $service['service_picture'] ?? null
            );
        }, $services);
    }

    public static function update_service($service_id, $title, $category, $price, $eta, $info, $picture = null) {
        $db = Database::getInstance();
        if ($picture !== null) {
            $stmt = $db->prepare('UPDATE services_list SET service_title = ?, service_category = ?, service_price = ?, service_eta = ?, service_info = ?, service_picture = ? WHERE service_id = ?');
            $stmt->bindParam(1, $title);
            $stmt->bindParam(2, $category);
            $stmt->bindParam(3, $price);
            $stmt->bindParam(4, $eta);
            $stmt->bindParam(5, $info);
            $stmt->bindParam(6, $picture, PDO::PARAM_LOB);
            $stmt->bindParam(7, $service_id);
            $stmt->execute();
        } else {
            $stmt = $db->prepare('UPDATE services_list SET service_title = ?, service_category = ?, service_price = ?, service_eta = ?, service_info = ? WHERE service_id = ?');
            $stmt->execute([$title, $category, $price, $eta, $info, $service_id]);
        }
    }

    public static function delete_by_id($service_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM services_list WHERE service_id = ?');
        $stmt->execute([$service_id]);
    }

    // Add a purchase for a user and service
    public static function addPurchase(int $client_id, int $service_id): bool {
        $db = Database::getInstance();
        // Prevent duplicate active purchase
        $stmt = $db->prepare('SELECT COUNT(*) FROM purchases WHERE client_id = ? AND service_id = ? AND completed = 0');
        $stmt->execute([$client_id, $service_id]);
        if ($stmt->fetchColumn() > 0) return false;
        $stmt = $db->prepare('INSERT INTO purchases (client_id, service_id, completed, purchase_date, review_text, review_rating) VALUES (?, ?, 0, CURRENT_TIMESTAMP, NULL, NULL)');
        return $stmt->execute([$client_id, $service_id]);
    }

    // Returns true if user has an active (uncompleted) purchase for this service
    public static function isUserCustomer(int $user_id, int $service_id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM purchases WHERE client_id = ? AND service_id = ? AND completed = 0');
        $stmt->execute([$user_id, $service_id]);
        return $stmt->fetchColumn() > 0;
    }

    public static function get_service_messages($service_id, $client_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM messages WHERE service_id = ? AND user_id = ? ORDER BY date_time ASC');
        $stmt->execute([$service_id, $client_id]);
        return $stmt->fetchAll();
    }
    // Returns active clients for a service (with uncompleted purchases)
    public static function get_active_clients_for_service($service_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT DISTINCT client_id FROM purchases WHERE service_id = ? AND completed = 0');
        $stmt->execute([$service_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Returns all clients who have ever made a purchase for this service
    public static function get_all_clients_for_service($service_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT DISTINCT client_id FROM purchases WHERE service_id = ?');
        $stmt->execute([$service_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function get_all_message_users_for_service($service_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT DISTINCT user_id FROM messages WHERE service_id = ?');
        $stmt->execute([$service_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Mark a purchase as completed for a given client and service (if uncompleted)
    public static function markPurchaseCompleted(int $client_id, int $service_id): bool {
        $db = Database::getInstance();
        // Only update if there is an uncompleted purchase
        $stmt = $db->prepare('UPDATE purchases SET completed = 1 WHERE client_id = ? AND service_id = ? AND completed = 0');
        $stmt->execute([$client_id, $service_id]);
        // Return true if any row was updated
        return $stmt->rowCount() > 0;
    }

    // Add a review to a purchase
    public static function addReviewToPurchase(int $purchase_id, int $rating, string $review): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE purchases SET review_rating = ?, review_text = ? WHERE purchase_id = ?');
        $stmt->execute([$rating, $review, $purchase_id]);
        return $stmt->rowCount() > 0;
    }

    // Get the average rating and number of reviews for a service
    public static function getServiceRatingInfo(int $service_id): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT AVG(review_rating) as avg_rating, COUNT(review_rating) as num_reviews FROM purchases WHERE service_id = ? AND review_rating IS NOT NULL');
        $stmt->execute([$service_id]);
        $row = $stmt->fetch();
        $count = $row ? (int)$row['num_reviews'] : 0;
        $avg = ($row && $row['avg_rating'] !== null && $count > 0) ? round((float)$row['avg_rating'], 1) : 0.0;
        return ['avg' => $avg, 'count' => $count];
    }

    // Get the total number of customers (purchases) for a service
    public static function getTotalCustomers(int $service_id): int {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) FROM purchases WHERE service_id = ?');
        $stmt->execute([$service_id]);
        return (int)$stmt->fetchColumn();
    }

    // Helper to render stars (full, empty) for a given float rating
    public static function getStars(float $rating): string {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($rating >= $i) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }
}
