<?php
require_once '../utils/session.php';
require_once '../utils/database.php';
require_once '../database/service_class.php';

$session = Session::getInstance();
$user = $session->getUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $client_id = isset($_POST['client_id']) ? (int)$_POST['client_id'] : null;

    if (!$user || !$service_id || $message === '') {
        header('Location: ../pages/service.php?id=' . urlencode($service_id) . '&error=invalid_input');
        exit;
    }

    $service = Service::get_by_id($service_id);
    if (!$service) {
        header('Location: ../pages/service.php?id=' . urlencode($service_id) . '&error=service_not_found');
        exit;
    }

    $is_freelancer = $user['user_id'] === $service->freelancer_id;
    $msg_user_id = $is_freelancer ? $client_id : $user['user_id'];
    $is_reply = $is_freelancer ? 1 : 0;

    $db = Database::getInstance();
    $stmt = $db->prepare('INSERT INTO messages (service_id, user_id, message_text, is_reply, date_time) VALUES (?, ?, ?, ?, datetime("now"))');
    $stmt->execute([$service_id, $msg_user_id, $message, $is_reply]);

    header('Location: ../pages/service.php?id=' . urlencode($service_id) . '&success=message_sent');
    exit;
}

header('Location: ../pages/service.php?error=invalid_request');
exit;
