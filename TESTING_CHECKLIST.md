# Payment Integration Testing Checklist

## Pre-Testing Setup

### Database Setup

- [ ] Run `migrate_payment_methods.php`
- [ ] Verify new columns added to orders table
- [ ] Check database connection works

### Configuration

- [ ] Update PAYHERE_MERCHANT_ID in `includes/payhere_helper.php`
- [ ] Update PAYHERE_MERCHANT_SECRET in `includes/payhere_helper.php`
- [ ] Update merchant_id in `checkout.php` JavaScript section
- [ ] Set sandbox mode to `true` for testing
- [ ] Create logs directory if it doesn't exist

## Test Cases

### Test 1: Cash on Delivery (COD)

**Steps:**

1. [ ] Navigate to checkout page
2. [ ] Select "Cash on Delivery" payment method
3. [ ] Fill in all required fields:
   - [ ] Full Name
   - [ ] Email
   - [ ] Phone Number
   - [ ] Delivery Address
   - [ ] City
   - [ ] Country
   - [ ] Amount (e.g., 1000)
4. [ ] Check the confirmation checkbox
5. [ ] Click "Place Order"

**Expected Results:**

- [ ] Order created successfully
- [ ] Redirected to success page
- [ ] Order appears in database with:
  - payment_method = 'cod'
  - payment_status = 'pending'
  - order_status = 'confirmed'
- [ ] Success message displayed

### Test 2: Card Payment (Sandbox)

**Steps:**

1. [ ] Navigate to checkout page
2. [ ] Select "Credit/Debit Card" payment method
3. [ ] Fill in all required fields
4. [ ] Click "Pay with Card"
5. [ ] Wait for PayHere popup to appear
6. [ ] Enter test card details:
   - Card Number: 4916217501611292
   - Expiry: 12/25
   - CVV: 123
   - Name: Test User
7. [ ] Click Pay in PayHere popup
8. [ ] Wait for payment processing

**Expected Results:**

- [ ] PayHere popup appears
- [ ] Payment processes successfully
- [ ] Popup closes
- [ ] Redirected to success page
- [ ] Order in database with:
  - payment_method = 'card'
  - payment_status = 'completed'
  - transaction_id populated
  - payhere_order_id populated
  - payment_method_type = 'VISA'

### Test 3: PayPal (Placeholder)

**Steps:**

1. [ ] Navigate to checkout page
2. [ ] Select "PayPal" payment method
3. [ ] Click "Pay with PayPal"

**Expected Results:**

- [ ] Alert message: "PayPal integration coming soon!"

### Test 4: Form Validation

**Steps:**

1. [ ] Try submitting form with empty fields
2. [ ] Try submitting with invalid email
3. [ ] Try submitting with invalid amount

**Expected Results:**

- [ ] Browser validation prevents submission
- [ ] Appropriate error messages shown

### Test 5: Payment Method Switching

**Steps:**

1. [ ] Select "Card" payment method
2. [ ] Switch to "COD"
3. [ ] Switch to "PayPal"
4. [ ] Switch back to "Card"

**Expected Results:**

- [ ] Button text changes appropriately:
  - Card: "Pay with Card"
  - PayPal: "Pay with PayPal"
  - COD: "Place Order"
- [ ] Confirmation checkbox appears only for COD
- [ ] Visual feedback on selected payment method

## PayHere Notification Testing (Advanced)

### For Localhost Testing (Using ngrok)

1. [ ] Install ngrok: https://ngrok.com/download
2. [ ] Run: `ngrok http 80`
3. [ ] Copy the https URL (e.g., https://abc123.ngrok.io)
4. [ ] Update notify_url in checkout.php to use ngrok URL
5. [ ] Test card payment
6. [ ] Check `logs/payment_log.txt` for notification receipt

**Expected Results:**

- [ ] Payment notification received
- [ ] Hash verified successfully
- [ ] Order status updated
- [ ] Log entry created

### For Production/Test Server

1. [ ] Deploy to test server with public URL
2. [ ] Test card payment
3. [ ] Check logs for notification

**Expected Results:**

- [ ] Same as above

## Database Verification

### After COD Order

```sql
SELECT id, user_name, amount, payment_method, payment_status,
       order_status, created_at
FROM orders
WHERE payment_method = 'cod'
ORDER BY id DESC LIMIT 1;
```

**Expected:**

- payment_method: cod
- payment_status: pending
- order_status: confirmed

### After Card Payment

```sql
SELECT id, user_name, amount, payment_method, payment_status,
       transaction_id, payhere_order_id, payment_method_type
FROM orders
WHERE payment_method = 'card'
ORDER BY id DESC LIMIT 1;
```

**Expected:**

- payment_method: card
- payment_status: completed
- transaction_id: populated (PayHere payment ID)
- payhere_order_id: populated (ORD00000001_timestamp format)
- payment_method_type: VISA or MASTER

## UI/UX Testing

### Desktop

- [ ] Payment method cards display correctly
- [ ] Form fields aligned properly
- [ ] Icons display correctly (Visa, Mastercard, PayPal, Cash)
- [ ] Hover effects work on payment cards
- [ ] Submit button shows loading state

### Mobile (< 768px)

- [ ] Payment cards stack vertically
- [ ] Form responsive and readable
- [ ] Touch targets adequate size
- [ ] No horizontal scrolling

### Tablet (768px - 1024px)

- [ ] Layout appropriate for screen size
- [ ] Payment cards display well

## Error Handling Testing

### Test Scenario 1: Declined Card (Sandbox)

**Steps:**

1. [ ] Use test card that simulates decline
2. [ ] Complete payment flow

**Expected:**

- [ ] Error message shown
- [ ] Order remains pending
- [ ] User can retry

### Test Scenario 2: User Cancels Payment

**Steps:**

1. [ ] Start card payment
2. [ ] Close PayHere popup before completing

**Expected:**

- [ ] Dismissal message shown
- [ ] User remains on checkout page
- [ ] Order remains pending
- [ ] User can retry

### Test Scenario 3: Network Error

**Steps:**

1. [ ] Simulate network interruption during payment

**Expected:**

- [ ] Appropriate error message
- [ ] No duplicate orders created

## Browser Compatibility

### Desktop Browsers

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### Mobile Browsers

- [ ] Chrome Mobile
- [ ] Safari iOS
- [ ] Samsung Internet

## Performance Testing

- [ ] Page load time < 3 seconds
- [ ] PayHere popup appears within 2 seconds
- [ ] Form submission responsive
- [ ] No JavaScript errors in console

## Security Testing

- [ ] Merchant secret not visible in browser
- [ ] Hash generated server-side only
- [ ] SQL injection protection verified
- [ ] XSS protection verified
- [ ] CSRF tokens working (if implemented)

## Log Verification

### Check Payment Logs

```bash
# View recent payment logs
tail -n 50 logs/payment_log.txt
```

**Verify logs contain:**

- [ ] Order creation logs
- [ ] Payment notification logs
- [ ] Hash verification logs
- [ ] Status update logs

## Final Checklist

- [ ] All test cases pass
- [ ] Database correctly updated
- [ ] Logs working properly
- [ ] No JavaScript errors
- [ ] No PHP errors
- [ ] UI looks good on all devices
- [ ] Error handling works
- [ ] Success messages display correctly

## Production Deployment Checklist

Before going live:

- [ ] Update merchant ID to production
- [ ] Update merchant secret to production
- [ ] Set sandbox mode to false
- [ ] Test on production with real card (small amount)
- [ ] Verify notify_url is accessible via HTTPS
- [ ] Domain approved by PayHere
- [ ] SSL certificate valid
- [ ] Backup database
- [ ] Monitor first few transactions
- [ ] Set up error alerting

---

**Testing Date**: ******\_******  
**Tested By**: ******\_******  
**Status**: ⬜ Passed ⬜ Failed ⬜ Needs Revision  
**Notes**:
