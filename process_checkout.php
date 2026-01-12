<?php
session_start();
require_once 'config/database.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Get form data
$user_name = trim($_POST['userName'] ?? '');
$account_number = trim($_POST['accountNumber'] ?? '');
$bank = trim($_POST['bank'] ?? '');
$branch = trim($_POST['branch'] ?? '');
$amount = trim($_POST['amount'] ?? '');
$confirmation_status = $_POST['confirmationStatus'] ?? 'no';

// Get user ID if logged in
$user_id = $_SESSION['user_id'] ?? null;

// Validation
$errors = [];

if (empty($user_name)) {
    $errors[] = 'User name is required';
}

if (empty($account_number)) {
    $errors[] = 'Account number is required';
}

if (empty($bank)) {
    $errors[] = 'Bank selection is required';
}

if (empty($branch)) {
    $errors[] = 'Branch is required';
}

if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
    $errors[] = 'Valid amount is required';
}

// Handle file upload
$bank_slip_path = null;
if (isset($_FILES['bankSlip']) && $_FILES['bankSlip']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['bankSlip'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validate file type
    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = 'Invalid file type. Only JPG, JPEG, and PNG are allowed';
    }
    
    // Validate file size
    if ($file['size'] > $max_size) {
        $errors[] = 'File size exceeds 5MB limit';
    }
    
    if (empty($errors)) {
        // Create upload directory if it doesn't exist
        $upload_dir = 'uploads/bank_slips/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $unique_filename = ($user_id ?? 'guest') . '_' . time() . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $unique_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $bank_slip_path = $upload_path;
        } else {
            $errors[] = 'Failed to upload bank slip';
        }
    }
}

// If there are validation errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['checkout_error'] = implode(', ', $errors);
    header('Location: checkout.php');
    exit;
}

// Insert order into database
try {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, user_name, account_number, bank, branch, amount, confirmation_status, bank_slip_path, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    
    $stmt->bind_param("issssdss", $user_id, $user_name, $account_number, $bank, $branch, $amount, $confirmation_status, $bank_slip_path);
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        $_SESSION['checkout_success'] = 'Order submitted successfully! Order ID: #' . str_pad($order_id, 6, '0', STR_PAD_LEFT);
        
        // Redirect to dashboard if logged in, otherwise to success page
        if ($user_id) {
            header('Location: dashboard.php?order_success=1');
        } else {
            header('Location: checkout.php?success=1');
        }
    } else {
        $_SESSION['checkout_error'] = 'Failed to submit order. Please try again.';
        header('Location: checkout.php');
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $_SESSION['checkout_error'] = 'An error occurred: ' . $e->getMessage();
    header('Location: checkout.php');
}
?>
