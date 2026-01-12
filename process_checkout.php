<?php
session_start();
require_once 'config/database.php';
require_once 'includes/payhere_helper.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Check if this is a PayHere order creation request
$is_payhere_order = isset($_POST['create_payhere_order']) && $_POST['create_payhere_order'] == '1';

// Get payment method
$payment_method = $_POST['paymentMethod'] ?? 'card';

// Get form data
$user_name = trim($_POST['userName'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$country = trim($_POST['country'] ?? 'Sri Lanka');
$amount = trim($_POST['amount'] ?? '');

// Legacy bank transfer fields (optional now)
$account_number = trim($_POST['accountNumber'] ?? '');
$bank = trim($_POST['bank'] ?? '');
$branch = trim($_POST['branch'] ?? '');
$confirmation_status = $_POST['confirmationStatus'] ?? 'no';

// Get user ID if logged in
$user_id = $_SESSION['user_id'] ?? null;

// Validation
$errors = [];

if (empty($user_name)) {
    $errors[] = 'User name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required';
}

if (empty($address)) {
    $errors[] = 'Delivery address is required';
}

if (empty($city)) {
    $errors[] = 'City is required';
}

if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
    $errors[] = 'Valid amount is required';
}

// Validate payment method specific requirements
if ($payment_method === 'cod' && $confirmation_status !== 'yes') {
    $errors[] = 'Please confirm Cash on Delivery';
}

// Handle file upload (only for bank transfer if bank slip provided)
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

// If there are validation errors, return error
if (!empty($errors)) {
    if ($is_payhere_order) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    } else {
        $_SESSION['checkout_error'] = implode(', ', $errors);
        header('Location: checkout.php');
        exit;
    }
}

// Determine order and payment status based on payment method
$order_status = 'pending';
$payment_status = 'pending';

if ($payment_method === 'cod') {
    $order_status = 'confirmed';
    $payment_status = 'pending'; // Will be completed on delivery
} elseif ($payment_method === 'card') {
    $order_status = 'pending';
    $payment_status = 'pending'; // Will be updated by PayHere notification
}

// Insert order into database
try {
    $conn = getDBConnection();
    
    // Prepare statement with new fields
    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id, user_name, account_number, bank, branch, amount, 
         confirmation_status, bank_slip_path, order_status, payment_method, 
         payment_status, payhere_order_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // For card payments, create a PayHere order ID
    $payhere_order_id = null;
    
    $stmt->bind_param("issssdssssss", 
        $user_id, 
        $user_name, 
        $account_number, 
        $bank, 
        $branch, 
        $amount, 
        $confirmation_status, 
        $bank_slip_path, 
        $order_status,
        $payment_method,
        $payment_status,
        $payhere_order_id
    );
    
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        
        // For PayHere card payments, generate order ID and hash
        if ($payment_method === 'card' && $is_payhere_order) {
            // Generate PayHere order ID
            $payhere_order_id = generatePayHereOrderId($order_id);
            
            // Update order with PayHere order ID
            $update_stmt = $conn->prepare("UPDATE orders SET payhere_order_id = ? WHERE id = ?");
            $update_stmt->bind_param("si", $payhere_order_id, $order_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Generate hash for PayHere
            $hash = generatePayHereHash(
                PAYHERE_MERCHANT_ID,
                $payhere_order_id,
                $amount,
                'LKR'
            );
            
            // Log the order creation
            logPaymentActivity('PayHere order created', [
                'order_id' => $order_id,
                'payhere_order_id' => $payhere_order_id,
                'amount' => $amount,
                'user_name' => $user_name
            ]);
            
            // Return JSON response for AJAX request
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'order_id' => $order_id,
                'payhere_order_id' => $payhere_order_id,
                'hash' => $hash,
                'merchant_id' => PAYHERE_MERCHANT_ID
            ]);
            exit;
        }
        
        // For COD and other methods, redirect with success message
        $_SESSION['checkout_success'] = 'Order placed successfully! Order ID: #' . str_pad($order_id, 6, '0', STR_PAD_LEFT);
        
        if ($payment_method === 'cod') {
            $_SESSION['checkout_success'] .= ' - Cash on Delivery confirmed.';
        }
        
        // Redirect to dashboard if logged in, otherwise to success page
        if ($user_id) {
            header('Location: dashboard.php?order_success=1');
        } else {
            header('Location: checkout.php?success=1');
        }
    } else {
        if ($is_payhere_order) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to create order']);
            exit;
        } else {
            $_SESSION['checkout_error'] = 'Failed to submit order. Please try again.';
            header('Location: checkout.php');
        }
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    if ($is_payhere_order) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    } else {
        $_SESSION['checkout_error'] = 'An error occurred: ' . $e->getMessage();
        header('Location: checkout.php');
    }
}
?>
