# PayHere Payment Integration Setup Guide

## Overview

This guide will help you set up the PayHere payment gateway integration with Visa/Mastercard, PayPal, and Cash on Delivery options.

## Files Created/Modified

### New Files Created:

1. **migrate_payment_methods.php** - Database migration script
2. **includes/payhere_helper.php** - PayHere helper functions
3. **payhere_notify.php** - Payment notification handler
4. **PAYHERE_SETUP.md** - This setup guide

### Modified Files:

1. **checkout.php** - Added payment method selection UI
2. **process_checkout.php** - Updated to handle multiple payment methods
3. **assets/css/checkout.css** - Added payment method card styles

## Setup Instructions

### Step 1: Run Database Migration

First, you need to update your database to support the new payment methods.

1. Open your browser and navigate to:

   ```
   http://localhost/ecommerce/Ecommerce-Website/migrate_payment_methods.php
   ```

2. This will add the following columns to your `orders` table:
   - `payment_method` - Stores the selected payment method (card/paypal/cod)
   - `payment_status` - Tracks payment status (pending/completed/failed/cancelled)
   - `transaction_id` - Stores PayHere payment ID
   - `payhere_order_id` - Unique order ID for PayHere
   - `payment_method_type` - Stores card type (VISA/MASTER/etc.)

### Step 2: Configure PayHere Credentials

1. Open `includes/payhere_helper.php`

2. Update the following constants with your PayHere credentials:

   ```php
   define('PAYHERE_MERCHANT_ID', '1233568'); // Replace with your Merchant ID
   define('PAYHERE_MERCHANT_SECRET', 'MzM1MTYzNjg2NDMxODkxNDIxMTkzNDU2ODUxMDM1MTUwODM3NjA3'); // Replace with your Merchant Secret
   define('PAYHERE_SANDBOX', true); // Set to false for production
   ```

3. To get your credentials:
   - Go to your PayHere Account
   - Navigate to Side Menu > Integrations
   - Copy your Merchant ID
   - Add your domain/app and get the Merchant Secret (approval takes up to 24 hours)

### Step 3: Update checkout.php with Your Merchant ID

1. Open `checkout.php`

2. Find the PayHere configuration in the JavaScript section (around line 220):

   ```javascript
   var payment = {
       "sandbox": true, // Set to false for production
       "merchant_id": "1233568", // Replace with your Merchant ID
   ```

3. Replace `"1233568"` with your actual PayHere Merchant ID

### Step 4: Configure Notification URL

The PayHere notification URL is automatically set in the code. Make sure your application is accessible via a public URL (not localhost) for PayHere to send notifications.

**For Testing on Localhost:**

- Use tools like ngrok to expose your localhost to the internet
- Run: `ngrok http 80`
- Use the ngrok URL as your base URL

**For Production:**

- Your notification URL will be: `https://yourdomain.com/payhere_notify.php`
- Make sure this file is accessible and not blocked by .htaccess

### Step 5: Test the Integration

#### Testing Cash on Delivery (COD):

1. Go to checkout page
2. Select "Cash on Delivery" payment method
3. Fill in all required fields
4. Check the confirmation checkbox
5. Click "Place Order"
6. Order should be created with status "confirmed" and payment_status "pending"

#### Testing Card Payment (PayHere Sandbox):

1. Go to checkout page
2. Select "Credit/Debit Card" payment method
3. Fill in all required fields
4. Click "Pay with Card"
5. PayHere popup should appear
6. Use PayHere test card details:

   - **Test Visa Card:**
     - Card Number: 4916217501611292
     - Expiry: Any future date (MM/YY)
     - CVV: Any 3 digits
     - Name: Any name

7. Complete the payment
8. You should be redirected back to checkout with success message
9. Check the database - order should be updated with transaction_id and payment_status "completed"

## Payment Flow Explanation

### Card Payment Flow (PayHere):

1. User selects payment method and fills form
2. User clicks "Pay with Card"
3. AJAX request creates order in database with status "pending"
4. Server generates PayHere order ID and hash
5. PayHere popup appears with payment form
6. User enters card details and completes payment
7. PayHere processes payment and sends notification to `payhere_notify.php`
8. Notification handler verifies payment and updates order status
9. User is redirected back to checkout with success message

### Cash on Delivery Flow:

1. User selects COD and fills form
2. User confirms they will pay on delivery
3. User clicks "Place Order"
4. Order is created with status "confirmed" and payment_status "pending"
5. User is redirected to success page

### PayPal Flow:

Currently shows "coming soon" message. You can integrate PayPal SDK following similar pattern.

## Database Schema Changes

The `orders` table now includes:

```sql
-- New columns added
payment_method ENUM('card', 'paypal', 'cod') DEFAULT 'card'
payment_status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending'
transaction_id VARCHAR(255) DEFAULT NULL  -- PayHere payment ID
payhere_order_id VARCHAR(100) DEFAULT NULL  -- Unique order ID for PayHere
payment_method_type VARCHAR(50) DEFAULT NULL  -- Card type (VISA, MASTER, etc.)
```

## Security Considerations

1. **Hash Verification**: All PayHere payments are verified using MD5 hash to ensure authenticity
2. **Merchant Secret**: Never expose your merchant secret in client-side code
3. **HTTPS Required**: Always use HTTPS in production for secure payment processing
4. **Input Validation**: All user inputs are validated and sanitized

## Troubleshooting

### PayHere Popup Not Appearing:

- Check browser console for JavaScript errors
- Verify PayHere SDK is loaded: `<script src="https://www.payhere.lk/lib/payhere.js"></script>`
- Ensure Merchant ID is correct

### Payment Notification Not Received:

- Verify notify_url is publicly accessible (not localhost)
- Check `logs/payment_log.txt` for notification attempts
- Verify Merchant Secret matches in PayHere dashboard

### Order Not Updating After Payment:

- Check payment notification logs
- Verify hash generation matches PayHere's hash
- Check database permissions

### Database Migration Failed:

- Check if columns already exist
- Verify database connection in `config/database.php`
- Check MySQL user permissions

## Logs

Payment activities are logged in `logs/payment_log.txt`. Check this file for:

- Payment notifications received
- Hash verification results
- Order updates
- Error messages

## Production Checklist

Before going live:

- [ ] Update `PAYHERE_MERCHANT_ID` with production Merchant ID
- [ ] Update `PAYHERE_MERCHANT_SECRET` with production Merchant Secret
- [ ] Set `PAYHERE_SANDBOX` to `false` in `includes/payhere_helper.php`
- [ ] Set `sandbox: false` in checkout.php JavaScript
- [ ] Verify notify_url is accessible via HTTPS
- [ ] Add your production domain in PayHere dashboard
- [ ] Wait for domain approval from PayHere (up to 24 hours)
- [ ] Test payment flow with real card (small amount)
- [ ] Set up proper error handling and user notifications
- [ ] Configure email notifications for successful orders
- [ ] Set up proper logging and monitoring

## Support

For PayHere specific issues:

- PayHere Documentation: https://www.payhere.lk/developers
- PayHere Support: support@payhere.lk

For code-related issues:

- Check the payment logs
- Review browser console for errors
- Verify database updates

## Additional Features You Can Add

1. **Order Management Dashboard**: Display payment status in admin panel
2. **Email Notifications**: Send confirmation emails with payment details
3. **Refund System**: Implement PayHere refund API
4. **Recurring Payments**: Use PayHere's recurring payment feature
5. **Multiple Currency**: Add support for USD, EUR, etc.
6. **Invoice Generation**: Create PDF invoices after successful payment
7. **Payment History**: Show payment history in user dashboard

---

**Version**: 1.0  
**Last Updated**: January 2026  
**Created by**: Your Development Team
