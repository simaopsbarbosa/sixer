<?php
// action/edit_profile_description.php
// Handles AJAX requests to update the user's about me description

declare(strict_types=1);
require_once '../utils/session.php';
require_once '../utils/database.php';
require_once '../utils/csrf.php';

header('Content-Type: application/json');

$session = Session::getInstance();

$csrf_token = $_POST['csrf_token'] ?? '';
if (!CSRF::verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

if (!$session->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $session->getUser()['user_id'] ?? null;
if (!$user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid user']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$new_about = trim($data['aboutme'] ?? '');

if ($new_about === '') {
    $new_about = null;
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare('UPDATE user_registry SET aboutme = ? WHERE user_id = ?');
    $stmt->execute([$new_about, $user_id]);
    echo json_encode(['success' => true, 'aboutme' => $new_about]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
