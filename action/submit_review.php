<?php
// filepath: /home/simao/ltw/action/submit_review.php
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../database/service_class.php';

$session = Session::getInstance();
require_once '../utils/csrf.php';
$data = json_decode(file_get_contents('php://input'), true);
$csrf_token = $data['csrf_token'] ?? '';

if (!CSRF::verifyCSRF($csrf_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}
if (!$session->isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$purchase_id = $data['purchase_id'] ?? null;
$rating = $data['rating'] ?? null;
$review = $data['review'] ?? null;

if (!$purchase_id || !$rating || $review === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing data']);
    exit;
}

if (!Service::addReviewToPurchase((int)$purchase_id, (int)$rating, trim($review))) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save review']);
    exit;
}

echo json_encode(['success' => true]);
