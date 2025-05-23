<?php
declare(strict_types=1);
require_once(__DIR__ . '/../utils/database.php');

class Service {
    public int $id;
    public int $freelancer_id;
    public string $title;
    public float $price;
    public string $info;
    public string $eta;
    public bool $delisted;
    public $picture; // Can be null or binary

    public function __construct(
        int $id,
        int $freelancer_id,
        string $title,
        float $price,
        string $info,
        string $eta,
        bool $delisted = false,
        $picture = null
    ) {
        $this->id = $id;
        $this->freelancer_id = $freelancer_id;
        $this->title = $title;
        $this->price = $price;
        $this->info = $info;
        $this->eta = $eta;
        $this->delisted = $delisted;
        $this->picture = $picture;
    }

    public static function create($freelancer_id, $title, $price, $info, $eta, $picture = null) {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO services_list (freelancer_id, service_title, service_price, service_info, service_eta, service_picture) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$freelancer_id, $title, $price, $info, $eta, $picture]);
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
            $service['service_eta'],
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
                $service['service_eta'],
                (bool)$service['service_delisted'],
                $service['service_picture'] ?? null
            );
        }, $services);
    }

    public function update() {
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE services_list SET service_title = ?, service_price = ?, service_info = ?, service_eta = ?, service_delisted = ?, service_picture = ? WHERE service_id = ?');
        $stmt->execute([
            $this->title,
            $this->price,
            $this->info,
            $this->eta,
            $this->delisted,
            $this->picture,
            $this->id
        ]);
    }
}
