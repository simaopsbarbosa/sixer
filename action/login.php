<?php
require_once '../utils/database.php';
session_start();

// Check if POST data is present
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo "Email and password are required.";
        exit;
    }

    try {
        $db = Database::getInstance()->getConnection();

        // Fetch user by email
        $stmt = $db->prepare('SELECT user_id, password_hash FROM user_registry WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo "Email not found. Please consider signing up.";

        } elseif (!password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo "Incorrect password.";

        } else {
            // Successful login
            $_SESSION['user_id'] = $user['user_id'];
            header('Location: ../pages/profile.php');
            exit;
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Database error: " . htmlspecialchars($e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Only POST requests are allowed.";
}
?>
