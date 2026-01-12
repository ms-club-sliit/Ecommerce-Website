# Payment Flow Diagrams

## 1. Card Payment Flow (PayHere)

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER JOURNEY                             │
└─────────────────────────────────────────────────────────────────┘

1. User visits checkout.php
   │
   ├─→ Selects "Credit/Debit Card" payment method
   │
   ├─→ Fills in form (name, email, phone, address, amount)
   │
   └─→ Clicks "Pay with Card" button

        ↓

2. JavaScript intercepts form submission
   │
   ├─→ Validates form fields
   │
   ├─→ Creates FormData with payment details
   │
   └─→ Sends AJAX request to process_checkout.php
       (with create_payhere_order=1)

        ↓

3. Server (process_checkout.php)
   │
   ├─→ Validates input data
   │
   ├─→ Creates order in database (status: pending)
   │
   ├─→ Generates unique PayHere order ID
   │    Example: ORD00000123_1234567890
   │
   ├─→ Generates secure hash using:
   │    - Merchant ID
   │    - Order ID
   │    - Amount
   │    - Currency
   │    - Merchant Secret (server-side only)
   │
   └─→ Returns JSON response:
       {
         "success": true,
         "order_id": 123,
         "payhere_order_id": "ORD00000123_1234567890",
         "hash": "ABC123...",
         "merchant_id": "121XXXX"
       }

        ↓

4. JavaScript receives response
   │
   ├─→ Configures PayHere payment object
   │
   └─→ Calls payhere.startPayment(payment)

        ↓

5. PayHere popup appears
   │
   ├─→ User enters card details:
   │    - Card number
   │    - Expiry date
   │    - CVV
   │    - Cardholder name
   │
   └─→ Clicks "Pay" button

        ↓

6. PayHere processes payment
   │
   ├─→ Validates card
   │
   ├─→ Processes transaction
   │
   ├─→ Generates transaction ID
   │
   └─→ Sends notification to notify_url (payhere_notify.php)

        ↓

7. Server receives notification (payhere_notify.php)
   │
   ├─→ Receives POST data:
   │    - merchant_id
   │    - order_id (our PayHere order ID)
   │    - payment_id (PayHere transaction ID)
   │    - payhere_amount
   │    - payhere_currency
   │    - status_code (2=success, 0=pending, -1=cancelled, etc.)
   │    - md5sig (verification hash)
   │    - method (VISA, MASTER, etc.)
   │
   ├─→ Verifies signature by generating local hash
   │
   ├─→ Compares local hash with received md5sig
   │
   ├─→ If valid and status_code = 2:
   │    └─→ Updates order in database:
   │         - payment_status = 'completed'
   │         - order_status = 'confirmed'
   │         - transaction_id = payment_id
   │         - payment_method_type = method (VISA/MASTER)
   │
   ├─→ Logs activity to payment_log.txt
   │
   └─→ Sends 200 OK response to PayHere

        ↓

8. PayHere notifies browser
   │
   └─→ Calls payhere.onCompleted(orderId)

        ↓

9. JavaScript redirects user
   │
   └─→ window.location.href = 'checkout.php?success=1&order_id=123'

        ↓

10. User sees success message
    └─→ "Payment completed successfully!"

```

## 2. Cash on Delivery (COD) Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    CASH ON DELIVERY FLOW                         │
└─────────────────────────────────────────────────────────────────┘

1. User visits checkout.php
   │
   ├─→ Selects "Cash on Delivery" payment method
   │
   ├─→ Fills in form (name, email, phone, address, amount)
   │
   ├─→ Checks confirmation checkbox
   │    "I confirm I will pay cash on delivery"
   │
   └─→ Clicks "Place Order" button

        ↓

2. Form submits normally (no AJAX)
   │
   └─→ POST request to process_checkout.php
       (with paymentMethod=cod, confirmationStatus=yes)

        ↓

3. Server (process_checkout.php)
   │
   ├─→ Validates input data
   │
   ├─→ Checks confirmation status = 'yes'
   │
   ├─→ Creates order in database:
   │    - payment_method = 'cod'
   │    - payment_status = 'pending'
   │    - order_status = 'confirmed'
   │    - No transaction_id (will be updated on delivery)
   │
   └─→ Sets success message in session

        ↓

4. Server redirects user
   │
   └─→ Location: checkout.php?success=1

        ↓

5. User sees success message
   └─→ "Order placed successfully! Order ID: #000123"
       "Cash on Delivery confirmed."

```

## 3. PayPal Flow (Future Implementation)

```
┌─────────────────────────────────────────────────────────────────┐
│                      PAYPAL FLOW (PLANNED)                       │
└─────────────────────────────────────────────────────────────────┘

1. User selects "PayPal"
   │
   └─→ Currently shows: "PayPal integration coming soon!"

Future Implementation:
   │
   ├─→ Initialize PayPal SDK
   │
   ├─→ Create PayPal order
   │
   ├─→ Show PayPal checkout
   │
   ├─→ Handle PayPal callback
   │
   └─→ Update order status

```

## 4. Database State Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    DATABASE STATE CHANGES                        │
└─────────────────────────────────────────────────────────────────┘

CARD PAYMENT:

Initial State (Order Created):
┌──────────────────────────────────────────────────────┐
│ payment_method: 'card'                                │
│ payment_status: 'pending'                             │
│ order_status: 'pending'                               │
│ payhere_order_id: 'ORD00000123_1234567890'           │
│ transaction_id: NULL                                  │
│ payment_method_type: NULL                             │
└──────────────────────────────────────────────────────┘
                      ↓
            [PayHere processes]
                      ↓
Final State (After Notification):
┌──────────────────────────────────────────────────────┐
│ payment_method: 'card'                                │
│ payment_status: 'completed'                           │
│ order_status: 'confirmed'                             │
│ payhere_order_id: 'ORD00000123_1234567890'           │
│ transaction_id: '320024044513'                        │
│ payment_method_type: 'VISA'                           │
└──────────────────────────────────────────────────────┘


CASH ON DELIVERY:

Order State:
┌──────────────────────────────────────────────────────┐
│ payment_method: 'cod'                                 │
│ payment_status: 'pending'                             │
│ order_status: 'confirmed'                             │
│ payhere_order_id: NULL                                │
│ transaction_id: NULL                                  │
│ payment_method_type: NULL                             │
└──────────────────────────────────────────────────────┘
                      ↓
            [On delivery - manual update]
                      ↓
After Delivery:
┌──────────────────────────────────────────────────────┐
│ payment_method: 'cod'                                 │
│ payment_status: 'completed'                           │
│ order_status: 'completed'                             │
│ payhere_order_id: NULL                                │
│ transaction_id: 'COD_' + timestamp                    │
│ payment_method_type: 'CASH'                           │
└──────────────────────────────────────────────────────┘

```

## 5. Security Flow (Hash Verification)

```
┌─────────────────────────────────────────────────────────────────┐
│                    HASH GENERATION & VERIFICATION                │
└─────────────────────────────────────────────────────────────────┘

HASH GENERATION (Server-side - Before Payment):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Step 1: Concatenate values
   merchant_id + order_id + amount + currency + MD5(merchant_secret)
   Example:
   "121XXXX" + "ORD123_1234567890" + "1000.00" + "LKR" + "ABC123..."

Step 2: Apply MD5 hash
   MD5(concatenated_string)

Step 3: Convert to uppercase
   hash = strtoupper(md5_result)

Step 4: Send to client
   Return hash in JSON response


HASH VERIFICATION (Server-side - After Payment):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Step 1: Receive from PayHere
   - merchant_id
   - order_id
   - payhere_amount
   - payhere_currency
   - status_code
   - md5sig (hash to verify)

Step 2: Generate local hash
   merchant_id + order_id + payhere_amount + payhere_currency +
   status_code + MD5(merchant_secret)

Step 3: Apply MD5 and uppercase
   local_md5sig = strtoupper(md5(concatenated))

Step 4: Compare
   if (local_md5sig === md5sig) {
       ✓ Payment is genuine
       ✓ Update order status
   } else {
       ✗ Possible fraud attempt
       ✗ Reject and log
   }

```

## 6. Error Handling Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                      ERROR SCENARIOS                             │
└─────────────────────────────────────────────────────────────────┘

Scenario 1: User Cancels Payment
   User closes PayHere popup
          ↓
   payhere.onDismissed() triggered
          ↓
   Alert: "Payment was cancelled. Please try again."
          ↓
   User remains on checkout page
          ↓
   Can retry payment


Scenario 2: Payment Fails
   Card declined / Insufficient funds
          ↓
   PayHere sends notification with status_code = -2
          ↓
   payhere_notify.php updates:
   - payment_status = 'failed'
   - order_status = 'cancelled'
          ↓
   payhere.onCompleted() still called (payment completed, but failed)
          ↓
   User redirected to checkout
          ↓
   Shows appropriate error message


Scenario 3: Network Error
   Connection lost during payment
          ↓
   payhere.onError() triggered
          ↓
   Alert: "Payment error occurred: [error message]"
          ↓
   User can retry


Scenario 4: Invalid Hash (Security)
   Fraudulent notification received
          ↓
   payhere_notify.php verifies hash
          ↓
   local_hash ≠ received_hash
          ↓
   Return 400 Bad Request
          ↓
   Log security event
          ↓
   Order remains unchanged

```

## 7. File Interaction Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    FILE INTERACTIONS                             │
└─────────────────────────────────────────────────────────────────┘

checkout.php
    │
    ├─→ Loads: assets/css/checkout.css
    ├─→ Loads: assets/js/checkout.js
    ├─→ Loads: https://www.payhere.lk/lib/payhere.js
    │
    ├─→ AJAX POST → process_checkout.php
    │                      │
    │                      ├─→ Requires: config/database.php
    │                      ├─→ Requires: includes/payhere_helper.php
    │                      │
    │                      └─→ Returns JSON response
    │
    └─→ On PayHere success → checkout.php?success=1


payhere_notify.php (Webhook)
    │
    ├─→ Receives POST from PayHere servers
    │
    ├─→ Requires: config/database.php
    ├─→ Requires: includes/payhere_helper.php
    │
    ├─→ Logs to: logs/payment_log.txt
    │
    └─→ Returns: HTTP 200 OK


includes/payhere_helper.php
    │
    ├─→ generatePayHereHash()
    ├─→ verifyPayHerePayment()
    ├─→ generatePayHereOrderId()
    ├─→ logPaymentActivity()
    └─→ getPaymentMethodName()


Database (orders table)
    │
    └─→ Columns:
        - id
        - user_id
        - user_name
        - amount
        - payment_method ← NEW
        - payment_status ← NEW
        - transaction_id ← NEW
        - payhere_order_id ← NEW
        - payment_method_type ← NEW
        - order_status
        - created_at
        - updated_at

```

---

**Legend:**

- → : Data flow / Function call
- ↓ : Sequential step
- ✓ : Success condition
- ✗ : Failure condition
- [ ] : External process
