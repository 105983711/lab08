<?php
session_start();

// Session check for logged-in access
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $email = trim($_POST['email']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: profile.php?error=invalid_email');
        exit();
    }
    
    try {
        // Database update using SQL UPDATE query
        $stmt = $pdo->prepare("UPDATE user SET email = ? WHERE username = ?");
        $stmt->execute([$email, $username]);
        
        // Update session email
        $_SESSION['email'] = $email;
        
        // Redirect back to profile.php with updated info
        header('Location: profile.php?success=1');
        exit();
        
    } catch(PDOException $e) {
        // Handle error
        header('Location: profile.php?error=database');
        exit();
    }
} else {
    header('Location: profile.php');
    exit();
}
?>