<?php
session_start();
require_once 'config/database.php';

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validation
    if (empty($email)) {
        $response['errors'][] = 'Email is required';
    }
    
    if (empty($password)) {
        $response['errors'][] = 'Password is required';
    }
    
    // If no errors, proceed with login
    if (empty($response['errors'])) {
        $conn = getDBConnection();
        
        // First check if user is an admin
        $stmt = $conn->prepare("SELECT id, name, email, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $isAdmin = false;
        
        if ($result->num_rows === 1) {
            // Admin found
            $user = $result->fetch_assoc();
            $isAdmin = true;
        } else {
            // Not an admin, check regular users table
            $stmt->close();
            $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
            }
        }
        
        if (isset($user)) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $response['success'] = true;
                $response['message'] = 'Login successful! Redirecting...';
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                $_SESSION['is_admin'] = $isAdmin;
                $_SESSION['last_activity'] = time();
                
                // Set remember me cookie if checked (only for regular users)
                if ($remember && !$isAdmin) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
                    
                    // Store token in database
                    $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->bind_param("si", $token, $user['id']);
                    $stmt->execute();
                }
            } else {
                $response['errors'][] = 'Invalid email or password';
            }
        } else {
            $response['errors'][] = 'Invalid email or password';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Return JSON response for AJAX or redirect for form submission
if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    if ($response['success']) {
        // Check if there's a redirect destination
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect_to = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect_to);
        } else {
            // Redirect based on user type
            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
                header('Location: admin/index.php');
            } else {
                header('Location: dashboard.php?login=success');
            }
        }
    } else {
        $_SESSION['login_errors'] = $response['errors'];
        $_SESSION['login_email'] = $email;
        header('Location: login.php?error=1');
    }
}
exit;
?>
