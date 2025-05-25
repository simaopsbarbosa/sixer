<?php
// action/edit_profile_skills.php
// Handles AJAX requests to update the user's skills

declare(strict_types=1);
require_once '../utils/session.php';
require_once '../utils/database.php';

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
$skills = $data['skills'] ?? [];

if (!is_array($skills)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid skills data']);
    exit;
}

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    $stmt = $db->prepare('DELETE FROM user_skills WHERE user_id = ?');
    $stmt->execute([$user_id]);

    $stmt = $db->prepare('INSERT INTO user_skills (user_id, skill_name) VALUES (?, ?)');
    foreach ($skills as $skill) {
        $stmt->execute([$user_id, $skill]);
    }

    $db->commit();
    echo json_encode(['success' => true, 'skills' => $skills]);
} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
