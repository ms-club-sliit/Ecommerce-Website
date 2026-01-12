<?php
/**
 * PayHere Payment Notification Handler
 * This URL receives payment status notifications from PayHere
 */

require_once 'config/database.php';
require_once 'includes/payhere_helper.php';

// Log the incoming request
logPaymentActivity('Payment notification received', $_POST);

// Get POST parameters from PayHere
$merchant_id = $_POST['merchant_id'] ?? '';
$order_id = $_POST['order_id'] ?? '';
$payhere_amount = $_POST['payhere_amount'] ?? '';
$payhere_currency = $_POST['payhere_currency'] ?? '';
$status_code = $_POST['status_code'] ?? '';
$md5sig = $_POST['md5sig'] ?? '';
$payment_id = $_POST['payment_id'] ?? '';
$method = $_POST['method'] ?? '';
$status_message = $_POST['status_message'] ?? '';

// Optional card details (if payment was made by card)
$card_holder_name = $_POST['card_holder_name'] ?? null;
$card_no = $_POST['card_no'] ?? null;
$card_expiry = $_POST['card_expiry'] ?? null;

// Custom parameters
$custom_1 = $_POST['custom_1'] ?? '';
$custom_2 = $_POST['custom_2'] ?? '';

// Verify the payment
$is_valid = verifyPayHerePayment(
    $merchant_id,
    $order_id,
    $payhere_amount,
    $payhere_currency,
    $status_code,
    $md5sig
);

if (!$is_valid) {
    logPaymentActivity('Payment verification failed - Invalid signature', [
        'order_id' => $order_id,
        'received_md5sig' => $md5sig
    ]);
    http_response_code(400);
    exit('Invalid signature');
}

// Verify merchant ID matches
if ($merchant_id !== PAYHERE_MERCHANT_ID) {
    logPaymentActivity('Payment verification failed - Invalid merchant ID', [
        'received_merchant_id' => $merchant_id,
        'expected_merchant_id' => PAYHERE_MERCHANT_ID
    ]);
    http_response_code(400);
    exit('Invalid merchant');
}

try {
    $conn = getDBConnection();
    
    // Update order based on payment status
    if ($status_code == 2) {
        // Payment successful
        $payment_status = 'completed';
        $order_status = 'confirmed';
        
        logPaymentActivity('Payment successful', [
            'order_id' => $order_id,
            'payment_id' => $payment_id,
            'amount' => $payhere_amount,
            'method' => $method
        ]);
    } elseif ($status_code == 0) {
        // Payment pending
        $payment_status = 'pending';
        $order_status = 'pending';
        
        logPaymentActivity('Payment pending', [
            'order_id' => $order_id,
            'payment_id' => $payment_id
        ]);
    } else {
        // Payment failed/cancelled/chargedback
        $payment_status = 'failed';
        $order_status = 'cancelled';
        
        logPaymentActivity('Payment failed', [
            'order_id' => $order_id,
            'status_code' => $status_code,
            'status_message' => $status_message
        ]);
    }
    
    // Find the order by payhere_order_id
    $stmt = $conn->prepare("UPDATE orders SET 
        payment_status = ?,
        order_status = ?,
        transaction_id = ?,
        payment_method_type = ?,
        updated_at = CURRENT_TIMESTAMP
        WHERE payhere_order_id = ?");
    
    $stmt->bind_param("sssss", 
        $payment_status,
        $order_status,
        $payment_id,
        $method,
        $order_id
    );
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            logPaymentActivity('Order updated successfully', [
                'order_id' => $order_id,
                'payment_status' => $payment_status,
                'order_status' => $order_status
            ]);
            
            // Send success response to PayHere
            http_response_code(200);
            echo 'OK';
        } else {
            logPaymentActivity('Order not found', ['order_id' => $order_id]);
            http_response_code(404);
            echo 'Order not found';
        }
    } else {
        logPaymentActivity('Database update failed', [
            'error' => $stmt->error,
            'order_id' => $order_id
        ]);
        http_response_code(500);
        echo 'Database error';
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    logPaymentActivity('Exception occurred', [
        'error' => $e->getMessage(),
        'order_id' => $order_id
    ]);
    http_response_code(500);
    echo 'Server error';
}
?>
