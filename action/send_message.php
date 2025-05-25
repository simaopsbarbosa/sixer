<?php
require_once '../utils/session.php';
require_once '../utils/database.php';
require_once '../database/service_class.php';

$session = Session::getInstance();
require_once '../utils/csrf.php';
$csrf_token = $_POST['csrf_token'] ?? '';
if (!CSRF::verifyCSRF($csrf_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}
$user = $session->getUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : null;

    if (!$user || !$service_id || $message === '') {
        $redirect_error = '../pages/service.php?id=' . urlencode($service_id) . '&error=invalid_input';
        if ($client_id) {
            $redirect_error .= '&client_id=' . urlencode($client_id);
        }
        header('Location: ' . $redirect_error);
        exit;
    }

    $service = Service::get_by_id($service_id);
    if (!$service) {
        $redirect_error = '../pages/service.php?id=' . urlencode($service_id) . '&error=service_not_found';
        if ($client_id) {
            $redirect_error .= '&client_id=' . urlencode($client_id);
        }
        header('Location: ' . $redirect_error);
        exit;
    }

    $is_freelancer = $user['user_id'] === $service->freelancer_id;
    $msg_user_id = $is_freelancer ? $client_id : $user['user_id'];
    $is_reply = $is_freelancer ? 1 : 0;

    $db = Database::getInstance();
    $stmt = $db->prepare('INSERT INTO messages (service_id, user_id, message_text, is_reply, date_time) VALUES (?, ?, ?, ?, datetime("now"))');
    $stmt->execute([$service_id, $msg_user_id, $message, $is_reply]);

    $redirect_url = '../pages/service.php?id=' . urlencode($service_id) . '&success=message_sent&forum=open';
    if ($is_freelancer && $client_id) {
        $redirect_url .= '&client_id=' . urlencode($client_id);
    }
    
    header('Location: ' . $redirect_url);
    exit;
}

$redirect_url = '../pages/service.php?error=invalid_request';
if (isset($_POST['service_id'])) {
    $redirect_url .= '&id=' . urlencode($_POST['service_id']);
    if (isset($_POST['client_id'])) {
        $redirect_url .= '&client_id=' . urlencode($_POST['client_id']);
    }
}
header('Location: ' . $redirect_url);
exit;
