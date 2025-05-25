<?php
declare(strict_types=1);

require_once '../utils/session.php';
require_once '../templates/common.php';
require_once '../database/user_class.php';
require_once '../utils/database.php';

// Check if user is logged in and has admin access
$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = $session->getUser();
if (!$user || $user['access_level'] !== 'admin') {
    header('Location: profile.php');
    exit;
}

// Process POST requests
$success_message = '';
$error_message = '';

// Handle user promotion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $db = Database::getInstance();
    
    // Promote user to admin
    if ($_POST['action'] === 'promote_user' && !empty($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        try {
            $stmt = $db->prepare('UPDATE user_registry SET access_level = ? WHERE user_id = ?');
            $stmt->execute(['admin', $user_id]);
            if ($stmt->rowCount() > 0) {
                $success_message = 'User has been promoted to admin successfully.';
            } else {
                $error_message = 'User promotion failed or user already has admin rights.';
            }
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
    
    // Add new service category
    elseif ($_POST['action'] === 'add_category' && !empty($_POST['category_name'])) {
        $category_name = trim($_POST['category_name']);
        
        try {
            // Check if category already exists
            $stmt = $db->prepare('SELECT COUNT(*) FROM categories WHERE category_name = ?');
            $stmt->execute([$category_name]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = 'Category already exists.';
            } else {
                // Add the new category to the categories table
                $stmt = $db->prepare('INSERT INTO categories (category_name) VALUES (?)');
                $stmt->execute([$category_name]);
                $success_message = "Category \"$category_name\" has been added successfully.";
            }
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }

    // Add new skill
    elseif ($_POST['action'] === 'add_skill' && !empty($_POST['skill_name'])) {
        $skill_name = trim($_POST['skill_name']);
        
        try {
            // Check if skill already exists
            $stmt = $db->prepare('SELECT COUNT(*) FROM skills WHERE skill_name = ?');
            $stmt->execute([$skill_name]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = 'Skill already exists.';
            } else {
                $stmt = $db->prepare('INSERT INTO skills (skill_name) VALUES (?)');
                $stmt->execute([$skill_name]);
                $success_message = "Skill \"$skill_name\" has been added successfully.";
            }
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch all users except the current admin
$db = Database::getInstance();
$stmt = $db->prepare('SELECT user_id, full_name, email, access_level, join_date FROM user_registry WHERE user_id != ? ORDER BY join_date DESC');
$stmt->execute([$user['user_id']]);
$users = $stmt->fetchAll();

// Fetch all categories from the categories table
$stmt = $db->query('SELECT category_name FROM categories ORDER BY category_name');
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch all skills
$stmt = $db->query('SELECT skill_name FROM skills ORDER BY skill_name');
$skills = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch system statistics
$stmt = $db->query('SELECT COUNT(*) FROM user_registry');
$total_users = $stmt->fetchColumn();

$stmt = $db->query('SELECT COUNT(*) FROM user_registry WHERE access_level = "admin"');
$admin_count = $stmt->fetchColumn();

$stmt = $db->query('SELECT COUNT(*) FROM services_list');
$services_count = $stmt->fetchColumn();

$stmt = $db->query('SELECT COUNT(*) FROM purchases WHERE completed = 1');
$completed_services = $stmt->fetchColumn();

$stmt = $db->query('SELECT COUNT(*) FROM purchases');
$total_purchases = $stmt->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/admin.css" />
    <title>sixer - admin panel</title>
    <style>
        #matrix-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.15; /* Reduced opacity to ensure content remains readable */
        }
    </style>
</head>
<body>
    <canvas id="matrix-canvas"></canvas>
    <?php drawHeader(); ?>
    <main>
        <div class="admin-container">
            <h1>Admin Panel</h1>
            
            <div id="message-container">
                <?php if (!empty($success_message)): ?>
                    <div class="message success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="message error"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="admin-stats">
                <div class="stat-card">
                    <h3><?= $total_users ?></h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-card">
                    <h3><?= $admin_count ?></h3>
                    <p>Admins</p>
                </div>
                <div class="stat-card">
                    <h3><?= $services_count ?></h3>
                    <p>Total Services</p>
                </div>
                <div class="stat-card">
                    <h3><?= $completed_services ?> / <?= $total_purchases ?></h3>
                    <p>Completed / Total Purchases</p>
                </div>
            </div>
            
            <div class="admin-sections">
                <section class="admin-section">
                    <h2>System Monitoring</h2>
                    <div class="system-monitoring">
                        <div class="monitoring-row">
                            <div class="monitoring-card">
                                <h3>Recent Activity</h3>
                                <p>Total purchases in last 24h: 
                                    <?php
                                        $stmt = $db->query("SELECT COUNT(*) FROM purchases WHERE julianday('now') - julianday(datetime(purchase_date, 'unixepoch')) < 1");
                                        echo $stmt->fetchColumn() ?: '0';
                                    ?>
                                </p>
                                <p>New user registrations in last 7 days: 
                                    <?php
                                        $stmt = $db->query("SELECT COUNT(*) FROM user_registry WHERE datetime(join_date) > datetime('now', '-7 day')");
                                        echo $stmt->fetchColumn() ?: '0';
                                    ?>
                                </p>
                            </div>
                            <div class="monitoring-card">
                                <h3>System Health</h3>
                                <p>Service listings: <?= $services_count ?></p>
                                <p>Conversion rate: 
                                    <?= $total_purchases > 0 ? round(($completed_services / $total_purchases) * 100, 1) : 0 ?>%
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="admin-section">
                    <h2>User Management</h2>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Join Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= $u['user_id'] ?></td>
                                    <td><?= htmlspecialchars($u['full_name']) ?> <?php if ($u['access_level'] === 'admin'): ?><span class="admin-badge">Admin</span><?php endif; ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['access_level']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($u['join_date'])) ?></td>
                                    <td>
                                        <?php if ($u['access_level'] !== 'admin'): ?>
                                        <form class="promote-user-form" data-user-id="<?= $u['user_id'] ?>">
                                            <input type="hidden" name="admin_action" value="promote_user">
                                            <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                            <button type="submit" class="admin-button">Make Admin</button>
                                        </form>
                                        <?php else: ?>
                                        <button class="admin-button disabled" disabled>Admin</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <section class="admin-section">
                    <h2>Service Categories</h2>
                    
                    <div class="category-container">
                        <div class="current-items">
                            <h3>Current Categories</h3>
                            <ul class="item-list">
                                <?php foreach ($categories as $category): ?>
                                    <li><?= htmlspecialchars(is_array($category) ? $category['category_name'] : $category) ?></li>
                                <?php endforeach; ?>
                                <?php if (empty($categories)): ?>
                                    <li class="empty-message">No categories defined yet.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="add-new-form">
                            <h3>Add New Category</h3>
                            <form id="add-category-form" class="ajax-form">
                                <input type="hidden" name="admin_action" value="add_category">
                                <div class="form-group">
                                    <label for="category_name">Category Name</label>
                                    <input type="text" id="category_name" name="category_name" required>
                                </div>
                                <button type="submit" class="admin-button">Add Category</button>
                            </form>
                        </div>
                    </div>
                </section>
                
                <section class="admin-section">
                    <h2>Skill Management</h2>
                    <div class="category-container">
                        <div class="current-items">
                            <h3>Current Skills</h3>
                            <ul class="item-list">
                                <?php foreach ($skills as $skill): ?>
                                    <li><?= htmlspecialchars($skill) ?></li>
                                <?php endforeach; ?>
                                <?php if (empty($skills)): ?>
                                    <li class="empty-message">No skills defined yet.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="add-new-form">
                            <h3>Add New Skill</h3>
                            <form id="add-skill-form" class="ajax-form">
                                <input type="hidden" name="admin_action" value="add_skill">
                                <div class="form-group">
                                    <label for="skill_name">Skill Name</label>
                                    <input type="text" id="skill_name" name="skill_name" required>
                                </div>
                                <button type="submit" class="admin-button">Add Skill</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to show messages
        function showMessage(message, isSuccess) {
            const container = document.getElementById('message-container');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');
            messageDiv.classList.add(isSuccess ? 'success' : 'error');
            messageDiv.textContent = message;
            
            // Clear existing messages
            container.innerHTML = '';
            container.appendChild(messageDiv);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                messageDiv.style.opacity = '0';
                setTimeout(() => {
                    container.removeChild(messageDiv);
                }, 500);
            }, 5000);
        }

        // Function to handle form submissions via AJAX
        function setupAjaxForm(form, successCallback) {
            if (!form) return;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Processing...';
                }
                
                fetch('../action/admin_operations.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, true);
                        if (successCallback) successCallback(data);
                        form.reset();
                    } else {
                        showMessage(data.error || 'An error occurred', false);
                    }
                })
                .catch(error => {
                    showMessage('An error occurred: ' + error.message, false);
                })
                .finally(() => {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Add';
                        
                        // If it's a promotion button, update the text
                        if (form.classList.contains('promote-user-form')) {
                            submitButton.textContent = 'Make Admin';
                        }
                    }
                });
            });
        }
        
        // Setup all AJAX forms
        document.querySelectorAll('.ajax-form').forEach(form => {
            setupAjaxForm(form, data => {
                // Reload the page after a successful operation to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            });
        });
        
        // Setup promotion forms
        document.querySelectorAll('.promote-user-form').forEach(form => {
            setupAjaxForm(form, data => {
                // After successful promotion, replace the form with a disabled admin button
                const userId = form.dataset.userId;
                const row = form.closest('tr');
                if (row) {
                    const actionCell = form.closest('td');
                    if (actionCell) {
                        actionCell.innerHTML = '<button class="admin-button disabled" disabled>Admin</button>';
                    }
                    // Update the role cell
                    const roleCell = row.querySelector('td:nth-child(4)');
                    if (roleCell) {
                        roleCell.textContent = 'admin';
                    }
                    // Add the admin badge to the name
                    const nameCell = row.querySelector('td:nth-child(2)');
                    if (nameCell) {
                        const userName = nameCell.textContent.trim();
                        nameCell.innerHTML = userName + ' <span class="admin-badge">Admin</span>';
                    }
                }
            });
        });
        
        // Add tab functionality if we decide to implement it later
        const adminSections = document.querySelectorAll('.admin-section');
        function showSection(index) {
            adminSections.forEach((section, i) => {
                if (i === index) {
                    section.classList.add('active');
                } else {
                    section.classList.remove('active');
                }
            });
        }
        
        // Initialize with all sections visible
        adminSections.forEach(section => section.classList.add('active'));
        
        // Matrix Rain Animation Code
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas to full window size
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        // Characters to use in the rain (use a mix of characters for a tech/hacker look)
        const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲンABCDEFGHIJKLMNOPQRSTUVWXYZ#$%&*+<=>?@';
        const fontSize = 14;
        const columns = canvas.width / fontSize; // Number of columns based on canvas width
        
        // Array to track the y position of each drop
        const drops = [];
        for(let i = 0; i < columns; i++) {
            drops[i] = Math.random() * -100; // Start above the canvas for a staggered effect
        }
        
        // Colors for the matrix rain in green shades
        const colors = ['#0F0', '#00FF00', '#22FF22', '#44FF44', '#88FF88'];
        
        function draw() {
            // Add semi-transparent black rectangle to create trailing effect
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Set font style
            ctx.font = fontSize + 'px monospace';
            
            // Loop over drops
            for(let i = 0; i < drops.length; i++) {
                // Pick a random character
                const text = chars[Math.floor(Math.random() * chars.length)];
                
                // Pick a random green color
                ctx.fillStyle = colors[Math.floor(Math.random() * colors.length)];
                
                // Draw the character
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                
                // Move drop down
                drops[i]++;
                
                // Send the drop back to the top after it reaches the bottom
                // Also randomize the reset to make it look more natural
                if(drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
            }
        }
        
        // Handle window resizing
        window.addEventListener('resize', function() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            const newColumns = canvas.width / fontSize;
            
            // Adjust drops array length based on new columns count
            if (newColumns > drops.length) {
                // Add more drops
                for (let i = drops.length; i < newColumns; i++) {
                    drops[i] = Math.random() * -100;
                }
            } else if (newColumns < drops.length) {
                // Remove excess drops
                drops.length = Math.floor(newColumns);
            }
        });
        
        // Run the animation
        setInterval(draw, 35); // Speed of animation
    });
    </script>
</body>
</html>
