<?php
/**
 * PayHere Helper Functions
 * Contains functions for PayHere payment gateway integration
 */

// PayHere Configuration
define('PAYHERE_MERCHANT_ID', '1233568'); // Replace with your Merchant ID
define('PAYHERE_MERCHANT_SECRET', 'MTQxOTc5OTY0NzkwOTIwNzI3Mzk2NDEyMjQ5OTMyMTYyNTg3MzI='); // Replace with your Merchant Secret
define('PAYHERE_SANDBOX', true); // Set to false for production

/**
 * Generate hash for PayHere payment
 * 
 * @param string $merchant_id
 * @param string $order_id
 * @param float $amount
 * @param string $currency
 * @return string
 */
function generatePayHereHash($merchant_id, $order_id, $amount, $currency = 'LKR') {
    $merchant_secret = PAYHERE_MERCHANT_SECRET;
    
    // Format amount to 2 decimal places
    $formatted_amount = number_format($amount, 2, '.', '');
    
    // Generate hash as per PayHere documentation
    $hash = strtoupper(
        md5(
            $merchant_id . 
            $order_id . 
            $formatted_amount . 
            $currency . 
            strtoupper(md5($merchant_secret)) 
        ) 
    );
    
    return $hash;
}

/**
 * Verify PayHere payment notification
 * 
 * @param string $merchant_id
 * @param string $order_id
 * @param float $payhere_amount
 * @param string $payhere_currency
 * @param int $status_code
 * @param string $md5sig
 * @return bool
 */
function verifyPayHerePayment($merchant_id, $order_id, $payhere_amount, $payhere_currency, $status_code, $md5sig) {
    $merchant_secret = PAYHERE_MERCHANT_SECRET;
    
    // Generate local hash
    $local_md5sig = strtoupper(
        md5(
            $merchant_id . 
            $order_id . 
            $payhere_amount . 
            $payhere_currency . 
            $status_code . 
            strtoupper(md5($merchant_secret)) 
        ) 
    );
    
    // Verify hash matches and payment is successful
    return ($local_md5sig === $md5sig);
}

/**
 * Get PayHere payment status message
 * 
 * @param int $status_code
 * @return string
 */
function getPayHereStatusMessage($status_code) {
    $status_messages = [
        2 => 'Payment Successful',
        0 => 'Payment Pending',
        -1 => 'Payment Cancelled',
        -2 => 'Payment Failed',
        -3 => 'Payment Chargedback'
    ];
    
    return $status_messages[$status_code] ?? 'Unknown Status';
}

/**
 * Get payment method display name
 * 
 * @param string $method_code
 * @return string
 */
function getPaymentMethodName($method_code) {
    $methods = [
        'VISA' => 'Visa Card',
        'MASTER' => 'Mastercard',
        'AMEX' => 'American Express',
        'EZCASH' => 'eZ Cash',
        'MCASH' => 'mCash',
        'GENIE' => 'Genie',
        'VISHWA' => 'Vishwa',
        'PAYAPP' => 'PayApp',
        'HNB' => 'HNB',
        'FRIMI' => 'Frimi'
    ];
    
    return $methods[$method_code] ?? $method_code;
}

/**
 * Log payment activity (for debugging)
 * 
 * @param string $message
 * @param array $data
 */
function logPaymentActivity($message, $data = []) {
    $log_file = __DIR__ . '/../logs/payment_log.txt';
    $log_dir = dirname($log_file);
    
    // Create logs directory if it doesn't exist
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$message}\n";
    
    if (!empty($data)) {
        $log_entry .= json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
    $log_entry .= str_repeat('-', 80) . "\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Generate unique order ID for PayHere
 * 
 * @param int $database_order_id
 * @return string
 */
function generatePayHereOrderId($database_order_id) {
    return 'ORD' . str_pad($database_order_id, 8, '0', STR_PAD_LEFT) . '_' . time();
}
?>
