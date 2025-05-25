<?php
declare(strict_types=1);
require_once '../utils/session.php';
require_once '../utils/database.php';
require_once '../utils/csrf.php';


$session = Session::getInstance();
$csrf_token = $_POST['csrf_token'] ?? '';
if (!CSRF::verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

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
    $stmt = $db->prepare('INSERT INTO purchases (client_id, service_id, completed, purchase_date, review_text, review_rating) VALUES (?, ?, 0, CURRENT_TIMESTAMP, NULL, NULL)');
    $stmt->execute([$user['user_id'], $service_id]);
    header('Location: ../pages/service.php?id=' . $service_id . '&success=hire');
    exit;
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}
