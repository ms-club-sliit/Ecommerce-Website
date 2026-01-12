# 🎯 QUICK REFERENCE GUIDE

## 🔑 Configuration Locations

### 1️⃣ PayHere Merchant ID & Secret

**File:** `includes/payhere_helper.php`
**Lines:** 9-11

```php
define('PAYHERE_MERCHANT_ID', '121XXXX');     // ← CHANGE THIS
define('PAYHERE_MERCHANT_SECRET', 'XXXXXXX'); // ← CHANGE THIS
define('PAYHERE_SANDBOX', true);              // false for production
```

### 2️⃣ PayHere Merchant ID (JavaScript)

**File:** `checkout.php`
**Line:** ~220

```javascript
var payment = {
    "sandbox": true,                    // false for production
    "merchant_id": "121XXXX",          // ← CHANGE THIS
```

---

## 🧪 Test Credentials

### PayHere Sandbox Test Card

```
Card Number: 4916217501611292
Expiry:      12/25 (any future date)
CVV:         123 (any 3 digits)
Name:        Test User (any name)
```

---

## 📂 Important Files

| File                          | Purpose           | Action Required    |
| ----------------------------- | ----------------- | ------------------ |
| `includes/payhere_helper.php` | PayHere functions | ✏️ Add credentials |
| `checkout.php`                | Checkout page     | ✏️ Add merchant ID |
| `payhere_notify.php`          | Payment webhook   | ✅ Ready           |
| `process_checkout.php`        | Form processor    | ✅ Ready           |
| `migrate_payment_methods.php` | DB migration      | ✅ **COMPLETED**   |

---

## ⚡ Quick Test

### Test COD (No Setup Required)

```bash
1. Open: http://localhost/ecommerce/Ecommerce-Website/checkout.php
2. Select: Cash on Delivery
3. Fill form & submit
4. ✅ Should work immediately!
```

### Test Card Payment (Requires Setup)

```bash
1. Configure PayHere credentials (see above)
2. Open checkout page
3. Select: Credit/Debit Card
4. Fill form
5. Click "Pay with Card"
6. Use test card (see above)
7. ✅ Payment should process!
```

---

## 🗄️ Database Columns Added

```sql
-- New columns in 'orders' table:
payment_method        (card/paypal/cod)
payment_status        (pending/completed/failed/cancelled)
transaction_id        (PayHere payment ID)
payhere_order_id      (Unique order ID)
payment_method_type   (VISA/MASTER/etc)
```

---

## 🎨 Payment Methods

| Method    | Status     | Button Text       |
| --------- | ---------- | ----------------- |
| 💳 Card   | ✅ Ready   | "Pay with Card"   |
| 📧 PayPal | ⚠️ UI Only | "Pay with PayPal" |
| 💵 COD    | ✅ Ready   | "Place Order"     |

---

## 📊 Payment Status Codes (PayHere)

| Code | Status         | Description        |
| ---- | -------------- | ------------------ |
| 2    | ✅ Success     | Payment completed  |
| 0    | ⏳ Pending     | Payment processing |
| -1   | ❌ Cancelled   | User cancelled     |
| -2   | ❌ Failed      | Payment failed     |
| -3   | ⚠️ Chargedback | Payment disputed   |

---

## 🔍 Debugging

### Check Logs

```bash
File: logs/payment_log.txt
Contains:
- Order creation logs
- Payment notifications
- Hash verifications
- Status updates
```

### Common Issues

**Popup Not Showing?**

```
✓ Check browser console
✓ Verify merchant ID
✓ Check PayHere SDK loaded
```

**Payment Not Updating?**

```
✓ Check logs/payment_log.txt
✓ Verify notify URL accessible
✓ Check hash generation
```

**Form Errors?**

```
✓ Fill all required fields
✓ Valid email format
✓ Numeric amount
```

---

## 🌐 URLs

### Development

```
Checkout:     http://localhost/ecommerce/Ecommerce-Website/checkout.php
Migration:    http://localhost/ecommerce/Ecommerce-Website/migrate_payment_methods.php
Notify:       http://localhost/ecommerce/Ecommerce-Website/payhere_notify.php
```

### Production (Example)

```
Checkout:     https://yourdomain.com/checkout.php
Notify:       https://yourdomain.com/payhere_notify.php
```

---

## 📱 UI Features

- ✅ Responsive design (mobile-friendly)
- ✅ Modern card-based selection
- ✅ Visual payment method icons
- ✅ Loading states
- ✅ Success/error messages
- ✅ Form validation
- ✅ Conditional fields

---

## 🔐 Security Checklist

- [✅] Hash verification implemented
- [✅] Merchant secret server-side only
- [✅] SQL injection prevention
- [✅] Input validation
- [✅] Payment logging
- [ ] HTTPS (required for production)
- [ ] Rate limiting (recommended)
- [ ] Error monitoring (recommended)

---

## 📋 Before Going Live

### Required

1. [ ] Update PAYHERE_MERCHANT_ID (production)
2. [ ] Update PAYHERE_MERCHANT_SECRET (production)
3. [ ] Set PAYHERE_SANDBOX = false
4. [ ] Set sandbox = false in checkout.php
5. [ ] Test with real card (small amount)
6. [ ] Verify HTTPS enabled
7. [ ] Check notify URL accessible
8. [ ] Wait for PayHere domain approval

### Recommended

1. [ ] Set up email notifications
2. [ ] Create admin dashboard
3. [ ] Add order tracking
4. [ ] Set up monitoring
5. [ ] Backup database
6. [ ] Document processes

---

## 💻 Command Reference

### Run Migration

```powershell
C:\xampp\php\php.exe migrate_payment_methods.php
```

### Check PHP Version

```powershell
C:\xampp\php\php.exe -v
```

### View Logs

```powershell
Get-Content logs/payment_log.txt -Tail 50
```

### Database Query (Check Orders)

```sql
SELECT
    id,
    user_name,
    amount,
    payment_method,
    payment_status,
    order_status,
    transaction_id,
    created_at
FROM orders
ORDER BY id DESC
LIMIT 10;
```

---

## 🎯 Payment Flow Summary

### Card Payment: 5 Steps

```
1. User selects card & fills form
2. AJAX creates order (pending)
3. PayHere popup appears
4. User pays with card
5. Order updated (completed) ✅
```

### COD: 2 Steps

```
1. User selects COD & confirms
2. Order created (confirmed) ✅
```

---

## 📞 Get Help

### Documentation

- 📖 PAYHERE_SETUP.md - Full setup guide
- 📊 PAYMENT_FLOW_DIAGRAMS.md - Visual flows
- ✅ TESTING_CHECKLIST.md - Test guide
- 📝 PAYMENT_INTEGRATION_SUMMARY.md - Summary

### PayHere Resources

- 🌐 https://www.payhere.lk/developers
- 📧 support@payhere.lk

### Check Status

```bash
✅ Database migrated
✅ Files created
✅ UI updated
⏳ Awaiting PayHere credentials
```

---

## 🎉 Success Criteria

Your integration is ready when:

- ✅ Database migration completed
- ✅ COD payments work
- ✅ PayHere credentials configured
- ✅ Card payments process successfully
- ✅ Order status updates correctly
- ✅ Payment logs working

---

## 📊 Integration Stats

```
Database Columns:    5 new
Payment Methods:     3 total
Files Created:       9 new
Files Modified:      3 updated
Lines of Code:       ~1,500+
Documentation:       5 files
Setup Time:          ~30 minutes
```

---

**Version:** 1.0  
**Status:** ✅ Ready for Configuration  
**Updated:** January 12, 2026

---

**Next Step:** Configure your PayHere credentials and start testing! 🚀
