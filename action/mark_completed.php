<?php
// action/mark_completed.php: Handles marking a purchase as completed

require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../database/service_class.php';

$session = Session::getInstance();

require_once '../utils/csrf.php';
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
    $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : null;
    
    if (!$service_id || !$client_id) {
        header('Location: ../pages/service.php?error=missing_data');
        exit;
    }

    // Check if the current user is the freelancer for this service
    $service = Service::getById($service_id);
    if (!$service || $user['user_id'] != $service->freelancer_id) {
        header('Location: ../pages/service.php?id=' . $service_id . '&error=not_authorized');
        exit;
    }

    // Mark the purchase as completed
    if (Service::markPurchaseCompleted($client_id, $service_id)) {
        header('Location: ../pages/service.php?id=' . $service_id . '&client_id=' . $client_id . '&forum=open&success=marked_completed');
        exit;
    } else {
        header('Location: ../pages/service.php?id=' . $service_id . '&client_id=' . $client_id . '&forum=open&error=update_failed');
        exit;
    }
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}
?>
