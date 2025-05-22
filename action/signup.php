<?php
require_once '../utils/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get POST data and sanitize
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: ../pages/signup.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email address.';
        header('Location: ../pages/signup.php');
        exit;
    }

    if ($password !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: ../pages/signup.php');
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Get DB instance
        $db = Database::getInstance()->getConnection();

        // Check if email already exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM user_registry WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = 'Email is already registered.';
            header('Location: ../pages/signup.php');
            exit;
        }

        // Insert user to DB with default acess_level (client)
        $stmt = $db->prepare("INSERT INTO user_registry (username, email, password_hash, join_date) VALUES (:name, :email, :password, DATETIME('now'))");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        unset($_SESSION['error']);
        $_SESSION['success'] = 'Signup successful! You can now log in.';
        header('Location: ../pages/login.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . htmlspecialchars($e->getMessage());
        header('Location: ../pages/signup.php');
        exit;
    }
} else {
    http_response_code(405);
    echo "Only POST requests are allowed.";
}
