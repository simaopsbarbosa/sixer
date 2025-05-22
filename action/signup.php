<?php
require_once '../utils/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get POST data and sanitize
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        die('All fields are required.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email address.');
    }

    if ($password !== $confirmPassword) {
        die('Passwords do not match.');
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
            die('Email is already registered.');
        }

        // Insert user to DB with default acess_level (client)

        $stmt = $db->prepare("INSERT INTO user_registry (username, email, password_hash, join_date) VALUES (:name, :email, :password, DATETIME('now'))");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        echo 'Signup successful!';
        header('Location: ../pages/login.php'); // Redirect to login
        exit;
    } catch (PDOException $e) {
        die('Database error: ' . $e->getMessage());
    }
} else {
    die('Invalid request method.');
}
