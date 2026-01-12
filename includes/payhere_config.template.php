<?php
/**
 * PayHere Configuration Template
 * Copy this to includes/payhere_config.php and update with your credentials
 * DO NOT commit this file with real credentials to version control
 */

return [
    // PayHere Merchant Credentials
    'merchant_id' => '121XXXX', // Your PayHere Merchant ID
    'merchant_secret' => 'XXXXXXXXXXXXX', // Your PayHere Merchant Secret
    
    // Environment Settings
    'sandbox' => true, // Set to false for production
    
    // Currency Settings
    'currency' => 'LKR', // LKR, USD, GBP, EUR, AUD
    
    // URLs (automatically generated if not set)
    'notify_url' => null, // Will use default: yoursite.com/payhere_notify.php
    'return_url' => null, // Will use default: yoursite.com/checkout.php?success=1
    'cancel_url' => null, // Will use default: yoursite.com/checkout.php
    
    // Payment Options
    'enable_card_payment' => true,
    'enable_paypal' => false, // Set to true when PayPal is integrated
    'enable_cod' => true,
    
    // Order Settings
    'order_prefix' => 'ORD', // Prefix for order IDs
    'min_order_amount' => 100, // Minimum order amount in LKR
    
    // Email Notifications
    'send_email_notifications' => false, // Enable email notifications
    'admin_email' => 'admin@vedalife.com',
    
    // Logging
    'enable_logging' => true, // Enable payment activity logging
    'log_file' => 'logs/payment_log.txt',
    
    // Test Card Details (Sandbox Only)
    'test_cards' => [
        'visa' => [
            'number' => '4916217501611292',
            'cvv' => '123',
            'expiry' => '12/25',
            'name' => 'Test User'
        ]
    ]
];
?>
