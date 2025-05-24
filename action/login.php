<?php
declare(strict_types=1);

require_once '/../utils/database.php';
require_once '/../utils/session.php';
require_once '/../database/user_class.php';
require_once '../utils/csrf.php';

if (!verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

$session = Session::getInstance();

if ($session->isLoggedIn()) {
    header('Location: ../pages/profile.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRF($csrf_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
    }
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password are required.';
        header('Location: ../pages/login.php');
        exit;
    }

    $user = User::get_user_by_email_password($email, $password);
    if ($user) {
        $session->login($user);
        unset($_SESSION['error']);
        header('Location: ../pages/profile.php');
        exit;
    } else {
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: ../pages/login.php');
        exit;
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Only POST requests are allowed.";
    exit;
}
?>
