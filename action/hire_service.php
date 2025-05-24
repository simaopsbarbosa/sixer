<?php
// action/hire_service.php: Handles hiring a freelancer for a service (creates a purchase entry)

declare(strict_types=1);
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../utils/database.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

$user = $session->getUser();
if (!$user) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : null;
    if (!$service_id) {
        header('Location: ../pages/payment.php?error=missing_service');
        exit;
    }
    $db = Database::getInstance();
    // prevent duplicate active purchases
    $stmt = $db->prepare('SELECT COUNT(*) FROM purchases WHERE client_id = ? AND service_id = ? AND completed = 0');
    $stmt->execute([$user['user_id'], $service_id]);
    if ($stmt->fetchColumn() > 0) {
        header('Location: ../pages/service.php?id=' . $service_id . '&error=already_hired');
        exit;
    }
    $stmt = $db->prepare('INSERT INTO purchases (client_id, service_id, completed, review_text, review_rating) VALUES (?, ?, 0, NULL, NULL)');
    $stmt->execute([$user['user_id'], $service_id]);
    header('Location: ../pages/service.php?id=' . $service_id . '&success=hire');
    exit;
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}
