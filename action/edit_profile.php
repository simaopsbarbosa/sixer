<?php
// /action/edit_profile.php: Handles profile update form submission

declare(strict_types=1);
require_once '../utils/database.php';
require_once '../utils/session.php';
require_once '../database/user_class.php';
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
    session_destroy();
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';
    if (!CSRF::validateToken($token)) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        header('Location: ../pages/edit_profile.php');
        exit;
    }

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($full_name) || empty($email) || empty($current_password)) {
        $_SESSION['error'] = 'Full name, email, and current password are required.';
        header('Location: ../pages/edit_profile.php');
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email address.';
        header('Location: ../pages/edit_profile.php');
        exit;
    }

    $db = Database::getInstance();
    // Check if email is taken by another user
    $stmt = $db->prepare('SELECT user_id FROM user_registry WHERE email = ? AND user_id != ?');
    $stmt->execute([$email, $user['user_id']]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Email already in use.';
        header('Location: ../pages/edit_profile.php');
        exit;
    }

    // Verify current password
    $stmt = $db->prepare('SELECT password_hash FROM user_registry WHERE user_id = ?');
    $stmt->execute([$user['user_id']]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($current_password, $row['password_hash'])) {
        $_SESSION['error'] = 'Current password is incorrect.';
        header('Location: ../pages/edit_profile.php');
        exit;
    }

    // Build update query
    $params = [$full_name, $email];
    $set = 'full_name = ?, email = ?';
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $_SESSION['error'] = 'New password must be at least 8 characters.';
            header('Location: ../pages/edit_profile.php');
            exit;
        }
        $set .= ', password_hash = ?';
        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
    }
    $params[] = $user['user_id'];
    $stmt = $db->prepare("UPDATE user_registry SET $set WHERE user_id = ?");
    $stmt->execute($params);

    // Update session user data
    $stmt = $db->prepare('SELECT * FROM user_registry WHERE user_id = ?');
    $stmt->execute([$user['user_id']]);
    $updatedUser = $stmt->fetch();
    $session->login($updatedUser);

    $_SESSION['success'] = 'Profile updated successfully!';
    header('Location: ../pages/edit_profile.php');
    exit;
} else {
    header('Location: ../pages/edit_profile.php');
    exit;
}
