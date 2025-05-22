<?php
require_once '../utils/database.php';
session_start();

// Check if POST data is present
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password are required.';
        header('Location: ../pages/login.php');
        exit;
    }

    try {
        $db = Database::getInstance()->getConnection();

        // Fetch user by email
        $stmt = $db->prepare('SELECT user_id, password_hash FROM user_registry WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = 'Email not found. Please consider signing up.';
            header('Location: ../pages/login.php');
            exit;

        } elseif (!password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Incorrect password.';
            header('Location: ../pages/login.php');
            exit;

        } else {
            // Successful login
            unset($_SESSION['error']);
            $_SESSION['user_id'] = $user['user_id'];
            header('Location: ../pages/profile.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . htmlspecialchars($e->getMessage());
        header('Location: ../pages/login.php');
        exit;
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Only POST requests are allowed.";
}
?>
