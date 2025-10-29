<?php
session_start();

// Session check for logged-in access
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

// Fetch current user data
$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Update success message
$success = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - User Profile System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border: #dee2e6;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px 0;
        }

        .logo {
            color: white;
            font-size: 2rem;
            font-weight: 700;
        }

        .user-welcome {
            color: white;
            font-size: 1.2rem;
            font-weight: 500;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 25px;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
        }

        .card-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
        }

        .card-header p {
            opacity: 0.9;
            font-size: 1rem;
            position: relative;
        }

        .card-body {
            padding: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
            margin-right: 25px;
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            border: 4px solid white;
        }

        .info-details {
            flex-grow: 1;
        }

        .info-item {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-label {
            font-weight: 600;
            width: 140px;
            color: var(--gray);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            flex-grow: 1;
            font-weight: 500;
            font-size: 1.05rem;
        }

        .edit-icon {
            margin-left: 15px;
            color: var(--primary);
            cursor: pointer;
            transition: var(--transition);
            padding: 8px;
            border-radius: 8px;
            background: rgba(67, 97, 238, 0.1);
        }

        .edit-icon:hover {
            color: var(--secondary);
            transform: scale(1.1);
            background: rgba(67, 97, 238, 0.2);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #e8f5e8, #d4edda);
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
            background: rgba(248, 249, 250, 0.5);
            border-radius: 12px 12px 0 0;
            padding: 5px;
        }

        .tab {
            padding: 15px 25px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
            transition: var(--transition);
            flex: 1;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .tab.active {
            background: white;
            color: var(--primary);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .tab:hover:not(.active) {
            background: rgba(67, 97, 238, 0.05);
            color: var(--primary);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tab-content.active {
            display: block;
        }

        .logout-btn {
            text-decoration: none;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            float: right;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.4);
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            z-index: 1;
            transition: var(--transition);
        }

        input[type="email"] {
            width: 100%;
            padding: 16px 16px 16px 50px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 16px;
            transition: var(--transition);
            background-color: rgba(248, 249, 250, 0.8);
            font-weight: 500;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
            transform: translateY(-2px);
        }

        input:focus + .input-icon {
            color: var(--primary);
            transform: translateY(-50%) scale(1.1);
        }

        button, .btn {
            text-decoration: none;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 16px 24px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        button::before, .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        button:hover::before, .btn:hover::before {
            left: 100%;
        }

        button:hover, .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .action-buttons button {
            flex: 1;
        }

        @media (max-width: 768px) {
            .container {
                max-width: 100%;
            }
            
            .user-info {
                flex-direction: column;
                text-align: center;
            }
            
            .avatar {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .info-label {
                width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-user-cog"></i> Profile Manager
            </div>
            <div class="user-welcome">
                Welcome, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-id-card"></i> User Profile</h2>
                <p>Manage your account information and preferences</p>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo $success; ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="tabs">
                    <div class="tab active" data-tab="profile">
                        <i class="fas fa-user"></i> Profile Overview
                    </div>
                    <div class="tab" data-tab="edit">
                        <i class="fas fa-edit"></i> Edit Profile
                    </div>
                </div>
                
                <!-- Profile Tab -->
                <div class="tab-content active" id="profileTab">
                    <div class="user-info">
                        <div class="avatar">
                            <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                        </div>
                        <div class="info-details">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-user"></i> Username:
                                </span>
                                <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-envelope"></i> Email:
                                </span>
                                <span class="info-value" id="currentEmail"><?php echo htmlspecialchars($user['email']); ?></span>
                                <i class="fas fa-edit edit-icon" id="editEmailBtn" title="Edit Email"></i>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="fas fa-id-card"></i> Student ID:
                                </span>
                                <span class="info-value"><?php echo htmlspecialchars($user['password']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
                
                <!-- Edit Profile Tab -->
                <div class="tab-content" id="editTab">
                    <h3 style="margin-bottom: 25px; color: var(--primary);">
                        <i class="fas fa-user-edit"></i> Update Your Information
                    </h3>
                    
                    <form method="POST" action="update_profile.php">
                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                                       placeholder="Enter your new email address" required>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <button type="button" class="btn btn-outline" id="cancelEditBtn">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        const tabs = document.querySelectorAll('.tab');
        const editEmailBtn = document.getElementById('editEmailBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                switchTab(tabName);
            });
        });
        
        function switchTab(tabName) {
            // Update active tab
            tabs.forEach(tab => {
                if (tab.getAttribute('data-tab') === tabName) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });
            
            // Update active tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                if (content.id === `${tabName}Tab`) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
        }
        
        // Edit email button functionality
        editEmailBtn.addEventListener('click', function() {
            switchTab('edit');
        });
        
        // Cancel edit button functionality
        cancelEditBtn.addEventListener('click', function() {
            switchTab('profile');
        });

        // Add loading animation to form submission
        document.querySelector('form')?.addEventListener('submit', function() {
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            button.disabled = true;
        });
    </script>
</body>
</html>