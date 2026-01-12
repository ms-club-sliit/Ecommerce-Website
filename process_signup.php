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
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name)) {
        $response['errors'][] = 'Name is required';
    }
    
    if (empty($email)) {
        $response['errors'][] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = 'Invalid email format';
    }
    
    if (empty($phone)) {
        $response['errors'][] = 'Phone number is required';
    }
    
    if (empty($password)) {
        $response['errors'][] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $response['errors'][] = 'Password must be at least 6 characters long';
    }
    
    if ($password !== $confirm_password) {
        $response['errors'][] = 'Passwords do not match';
    }
    
    // If no errors, proceed with registration
    if (empty($response['errors'])) {
        $conn = getDBConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['errors'][] = 'Email already registered';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Registration successful! Redirecting to login...';
                
                // Set session variables
                $_SESSION['signup_success'] = true;
                $_SESSION['signup_email'] = $email;
            } else {
                $response['errors'][] = 'Registration failed. Please try again.';
            }
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
        header('Location: login.php?signup=success');
    } else {
        $_SESSION['signup_errors'] = $response['errors'];
        $_SESSION['form_data'] = $_POST;
        header('Location: signup.php?error=1');
    }
}
exit;
?>
