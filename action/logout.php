<?php
declare(strict_types=1);
require_once '/../utils/session.php';
require_once '../utils/csrf.php';

if (!verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

$session = Session::getInstance();
$session->logout();

header('Location: ../pages/login.php');
exit;
