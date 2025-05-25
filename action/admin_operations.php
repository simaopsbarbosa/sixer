<?php
// action/admin_operations.php
// Handles admin operations like adding categories and elevating users

declare(strict_types=1);
require_once '../utils/session.php';
require_once '../utils/database.php';

// Check if user is logged in and is an admin
$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$user = $session->getUser();
if (!$user || $user['access_level'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

header('Content-Type: application/json');

// Process different admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['admin_action'] ?? '';
    $db = Database::getInstance();
    
    try {
        switch ($action) {
            case 'promote_user':
                $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
                if ($user_id <= 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
                    exit;
                }
                
                $stmt = $db->prepare('UPDATE user_registry SET access_level = ? WHERE user_id = ?');
                $stmt->execute(['admin', $user_id]);
                
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'User promoted to admin successfully']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'User promotion failed or user already has admin access']);
                }
                break;
                
            case 'add_category':
                $category_name = trim($_POST['category_name'] ?? '');
                
                if (empty($category_name)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Category name is required']);
                    exit;
                }
                
                try {
                    // Check if category already exists
                    $stmt = $db->prepare('SELECT COUNT(*) FROM categories WHERE category_name = ?');
                    $stmt->execute([$category_name]);
                    if ($stmt->fetchColumn() > 0) {
                        echo json_encode(['success' => false, 'error' => 'Category already exists']);
                        exit;
                    }
                    
                    // Add the new category to the categories table
                    $stmt = $db->prepare('INSERT INTO categories (category_name) VALUES (?)');
                    $stmt->execute([$category_name]);
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Category "' . $category_name . '" added successfully'
                    ]);
                    
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
                }
                break;
                
            case 'add_skill':
                $skill_name = trim($_POST['skill_name'] ?? '');
                if (empty($skill_name)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Skill name is required']);
                    exit;
                }
                
                // Check if skill already exists
                $stmt = $db->prepare('SELECT COUNT(*) FROM skills WHERE skill_name = ?');
                $stmt->execute([$skill_name]);
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'error' => 'Skill already exists']);
                    exit;
                }
                
                $stmt = $db->prepare('INSERT INTO skills (skill_name) VALUES (?)');
                $stmt->execute([$skill_name]);
                echo json_encode(['success' => true, 'message' => 'Skill added successfully']);
                break;
                
            case 'add_language':
                $lang_code = trim($_POST['lang_code'] ?? '');
                $lang_name = trim($_POST['lang_name'] ?? '');
                
                if (empty($lang_code) || empty($lang_name)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Language code and name are required']);
                    exit;
                }
                
                // Check if language already exists
                $stmt = $db->prepare('SELECT COUNT(*) FROM languages WHERE lang_code = ?');
                $stmt->execute([$lang_code]);
                if ($stmt->fetchColumn() > 0) {
                    echo json_encode(['success' => false, 'error' => 'Language already exists']);
                    exit;
                }
                
                $stmt = $db->prepare('INSERT INTO languages (lang_code, lang_name) VALUES (?, ?)');
                $stmt->execute([$lang_code, $lang_name]);
                echo json_encode(['success' => true, 'message' => 'Language added successfully']);
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Unknown action']);
                break;
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['admin_action'] ?? '';
    $db = Database::getInstance();
    
    try {
        switch ($action) {
            case 'get_categories':
                // Check if we have a categories table
                $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='service_categories'");
                $stmt->execute();
                $table_exists = $stmt->fetchColumn();
                
                if ($table_exists) {
                    $stmt = $db->query('SELECT * FROM service_categories ORDER BY category_name');
                    $categories = $stmt->fetchAll();
                    echo json_encode(['success' => true, 'categories' => $categories]);
                } else {
                    // No categories table, get distinct categories from services
                    $stmt = $db->query('SELECT DISTINCT service_category as category_name FROM services_list ORDER BY service_category');
                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode(['success' => true, 'categories' => $categories]);
                }
                break;
                
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Unknown action']);
                break;
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
