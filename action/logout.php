<?php
declare(strict_types=1);
require_once '/../utils/session.php';
require_once '../utils/csrf.php';

$session = Session::getInstance();

$csrf_token = $_POST['csrf_token'] ?? '';
if (!CSRF::verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

$session->logout();

header('Location: ../pages/login.php');
exit;
