# Professional Payment Management System Documentation

## Overview

The payment management system provides a complete, production-ready solution for managing invoice payments with multiple payment methods, payment tracking, audit trails, and professional receipt generation.

## Features

### 1. Payment Recording
- Record payments against invoices with date, amount, and method
- Support for multiple payment methods:
  - Cash
  - Bank Transfer
  - Card
  - Cheque
  - Online Payment
- Optional reference numbers for transaction tracking
- Notes field for additional payment details

### 2. Payment Status Tracking
Automatic status calculation:
- **Pending**: No payments received (paid_amount = 0)
- **Partially Paid**: Some payments received but balance remains
- **Paid**: Total amount received equals invoice total

### 3. Payment History & Audit Trail
- Complete payment history for every invoice
- Tracks who recorded each payment (created_by field)
- Payment modification tracking (updated_by field)
- Payment date and method stored for reference
- Complete audit trail for compliance

### 4. Real-time Balance Calculation
- Automatically calculated remaining balance
- Live balance display while entering new payments
- Prevention of overpayment
- Real-time payment status updates

### 5. Payment Receipt Generation
- Professional PDF payment receipt
- Shows all payment transactions
- Displays collection progress
- Can be downloaded or printed
- Suitable for customer records

### 6. Validation & Security
- Amount validation (cannot exceed remaining balance)
- Negative amount prevention
- Proper authorization checks
- Transaction-based database operations
- Automatic recalculation on payment modifications

## Database Schema

### Payments Table
```sql
CREATE TABLE payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    invoice_id BIGINT NOT NULL FOREIGN KEY,
    payment_date DATE NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    payment_method VARCHAR(255) NOT NULL DEFAULT 'cash',
    reference_number VARCHAR(255) NULLABLE,
    notes TEXT NULLABLE,
    created_by BIGINT NULLABLE FOREIGN KEY,
    updated_by BIGINT NULLABLE FOREIGN KEY,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEXES:
    - invoice_id
    - payment_method
    - (invoice_id, payment_date)
);
```

### Invoices Table (Enhanced Fields)
- `total_amount`: DECIMAL - The invoice total
- `paid_amount`: DECIMAL - Sum of all payments
- `balance`: DECIMAL - Calculated as total_amount - paid_amount
- `payment_status`: ENUM('pending', 'partial', 'paid')

## Models & Relationships

### Invoice Model
```php
// Relationships
$invoice->payments() // hasMany Payment
$invoice->customer() // belongsTo Customer

// Methods
$invoice->recalculatePaid() // Recalculates all payment totals
$invoice->calculatePaymentStatus() // Returns current status
$invoice->canAcceptPayment($amount) // Validates payment amount
$invoice->isFullyPaid() // Boolean check
$invoice->isPartiallPaid() // Boolean check
$invoice->isPending() // Boolean check
$invoice->getRemainingBalanceAttribute() // Returns balance
```

### Payment Model
```php
// Relationships
$payment->invoice() // belongsTo Invoice
$payment->createdBy() // belongsTo User
$payment->updatedBy() // belongsTo User

// Accessors
$payment->formatted_amount // Formatted with 2 decimals
$payment->formatted_method // Human-readable method name
```

## Services

### PaymentService
Encapsulates all payment business logic:

```php
// Record a new payment
$paymentService->recordPayment($invoice, [
    'payment_date' => '2026-07-10',
    'amount' => 50000,
    'payment_method' => 'cash',
    'reference_number' => null,
    'notes' => 'Initial payment'
]);

// Update existing payment
$paymentService->updatePayment($payment, $data);

// Delete payment
$paymentService->deletePayment($payment);

// Get payment history
$paymentService->getPaymentHistory($invoice);

// Get summary statistics
$paymentService->generatePaymentSummary($invoice);

// Get detailed statistics
$paymentService->getPaymentStatistics($invoice);
```

## API Routes

### Payment Management Routes
```
POST   /invoices/{invoice}/payment
       Record a new payment
       Required fields: payment_date, amount, payment_method

DELETE /invoices/{invoice}/payment/{payment}
       Delete a payment and recalculate totals

GET    /invoices/{invoice}/payment-receipt
       Download payment receipt PDF

GET    /invoices/{invoice}/payment-history
       Get payment history as JSON (API)
```

## Frontend Components

### Invoice Show View (`invoices.show`)
Comprehensive payment management interface includes:

1. **Payment Summary Cards** (4 columns)
   - Invoice Total
   - Amount Paid
   - Outstanding Balance
   - Payment Status with progress percentage

2. **Payment Progress Bar**
   - Visual representation of collection progress
   - Shows percentage collected
   - Gradient background for visual appeal

3. **Payment History Table**
   - Date with relative time (e.g., "2 days ago")
   - Payment method with icon
   - Reference number
   - Recorded by (user who recorded payment)
   - Amount in green
   - Actions (view notes, delete)
   - Statistics (total transactions, average, etc.)

4. **Record Payment Modal**
   - Date picker (defaults to today)
   - Amount input with live balance calculation
   - Payment method dropdown with icons
   - Reference number field
   - Notes textarea
   - Real-time validation display
   - Remaining balance summary card

### Professional UI Features
- Color-coded status badges (green/amber/red)
- Icons for payment methods
- Responsive design (mobile-friendly)
- Accessibility features
- Semantic HTML
- Clean typography and spacing
- Consistent with existing design system

## Controller Methods

### InvoiceController

```php
// Record a new payment
public function recordPayment(Request $request, Invoice $invoice, PaymentService $paymentService)

// Delete a payment
public function deletePayment(Invoice $invoice, Payment $payment, PaymentService $paymentService)

// Generate payment receipt PDF
public function paymentReceipt(Invoice $invoice)

// Get payment history as JSON
public function paymentHistory(Invoice $invoice)
```

## Validation Rules

### Payment Recording
```php
[
    'payment_date'   => 'required|date|date_format:Y-m-d',
    'amount'         => 'required|numeric|min:0.01',
    'payment_method' => 'required|in:cash,bank_transfer,card,cheque,online',
    'reference_number' => 'nullable|string|max:100',
    'notes'          => 'nullable|string|max:500',
]
```

### Business Logic Validation
- Payment amount cannot be zero or negative
- Payment amount cannot exceed remaining balance (unless configured otherwise)
- Payment method must be valid
- Payment date must be a valid date

## Usage Examples

### Recording a Payment (Frontend)
1. Navigate to invoice detail page
2. Click "Record Payment" button
3. Modal opens with payment form
4. Enter payment date, amount, and method
5. Optional: Add reference number and notes
6. Click "Record Payment" to save
7. Invoice totals automatically recalculate
8. Success message displayed
9. Payment appears in history table

### Recording a Payment (Programmatic)
```php
$paymentService = app(PaymentService::class);

try {
    $payment = $paymentService->recordPayment($invoice, [
        'payment_date' => '2026-07-10',
        'amount' => 50000,
        'payment_method' => 'bank_transfer',
        'reference_number' => 'TXN-123456',
        'notes' => 'Payment received from customer'
    ]);
    // Success - payment recorded
} catch (\InvalidArgumentException $e) {
    // Handle validation error
    echo $e->getMessage();
}
```

### Generating Payment Receipt
```php
// In controller or route
return $invoiceController->paymentReceipt($invoice);

// Downloads PDF: Payment-Receipt-INV-0001.pdf
```

### Getting Payment Summary
```php
$paymentService = app(PaymentService::class);
$summary = $paymentService->generatePaymentSummary($invoice);

// Returns array:
[
    'invoice_total' => 100000,
    'total_paid' => 50000,
    'remaining_balance' => 50000,
    'payment_status' => 'partial',
    'payment_count' => 1,
    'last_payment_date' => '2026-07-10'
]
```

## Database Transactions

All payment operations use database transactions to ensure data consistency:

```php
// Payment creation automatically wrapped in transaction
// If validation fails, entire transaction is rolled back
// Invoice recalculation happens within same transaction
```

This prevents scenarios where:
- Payment is created but invoice totals fail to update
- Partial data corruption due to failed operations
- Inconsistent state between payments and invoice totals

## Audit Trail

Every payment records:
- **created_by**: User ID who recorded the payment
- **updated_by**: User ID who last updated the payment (if modified)
- **created_at**: Timestamp when payment was recorded
- **updated_at**: Timestamp of last modification

This enables:
- Complete audit trail for compliance
- User accountability
- Historical tracking
- Compliance with financial regulations

## Error Handling

### Common Validation Errors
```
// Amount exceeds remaining balance
"Payment exceeds remaining balance of LKR 50,000.00"

// Invalid amount
"Payment amount must be greater than zero."

// Invalid payment method
"The selected payment method is invalid."
```

All errors are:
- User-friendly and clear
- Redirect with input preservation (withInput())
- Displayed in error alert box
- Log-friendly for debugging

## Future Enhancements

1. **Payment Plan Support**
   - Multiple installment schedules
   - Automatic reminders for due dates

2. **Payment Gateway Integration**
   - Online payment processing
   - Automatic reconciliation

3. **Export Features**
   - Export to Excel
   - Export to CSV
   - Bulk payment reports

4. **Advanced Reporting**
   - Payment analytics dashboard
   - Customer payment history
   - Payment method statistics
   - Collection trends

5. **Partial Invoice Support**
   - Advance payments
   - Deposits and retainers

6. **Payment Permissions**
   - Record payment permission
   - Edit payment permission
   - Delete payment permission
   - View payment history permission

## Security Considerations

1. **Authorization**: Only authenticated users can record payments
2. **Validation**: All input is validated on server side
3. **Transactions**: Database transactions ensure consistency
4. **Audit**: All payments are tracked with user information
5. **Soft Deletes**: Payments can be soft-deleted for audit trail
6. **Rate Limiting**: Can be added for payment API endpoints

## Performance Optimization

### Indexes
- `payments.invoice_id` - Fast lookup by invoice
- `payments.payment_method` - Filter by method
- `(payments.invoice_id, payments.payment_date)` - Common queries

### Eager Loading
- Payments loaded with relationships: `createdBy`, `updatedBy`
- Prevents N+1 queries in payment lists

### Caching
- Can be added for frequently accessed summaries
- Cache invalidation on payment changes

## Testing

### Unit Tests
```php
// Test payment validation
test('cannot record payment exceeding balance')
test('can record payment within limit')
test('negative amounts are rejected')

// Test status calculations
test('status changes to partial when payment received')
test('status changes to paid when fully paid')
test('status reverts to partial when payment deleted')
```

### Feature Tests
```php
// Test complete payment flow
test('user can record payment via web interface')
test('payment history displays correctly')
test('payment receipt generates PDF')
```

## Integration with Existing System

The payment system integrates seamlessly with:
- **Invoice Module**: Enhanced with payment tracking
- **User Module**: Audit trail with user tracking
- **PDF Export**: Payment receipt generation
- **Dashboard**: Can display payment statistics
- **Reports**: Payment analytics and trends

## Configuration

No additional configuration required. The system uses:
- Default currency: LKR (configure in .env if needed)
- Default timezone: Application default
- Date format: Y-m-d (ISO 8601)
- Decimal precision: 2 places

## Support & Troubleshooting

### Issue: Payment not updating invoice totals
**Solution**: Check that `recalculatePaid()` is called after payment operations. Always use PaymentService methods which handle this automatically.

### Issue: Overpayment allowed when it shouldn't be
**Solution**: Verify payment amount validation is not bypassed. Always use PaymentService methods for validation.

### Issue: Audit trail missing user information
**Solution**: Ensure payments are recorded with authenticated user. Check `Auth::id()` returns valid user ID.

## File Structure

```
app/
  Models/
    Invoice.php          (Enhanced with payment methods)
    Payment.php          (With user relationships)
  Services/
    PaymentService.php   (Business logic)
  Http/
    Controllers/
      InvoiceController.php (Payment methods)

database/
  migrations/
    2026_07_10_000000_enhance_payments_table.php

resources/
  views/
    invoices/
      show.blade.php           (Payment interface)
      payment-receipt.blade.php (Receipt PDF)

routes/
  web.php (Payment routes)
```

## Summary

The payment management system provides:
✅ Multiple payment method support
✅ Real-time balance calculation
✅ Professional receipt generation
✅ Complete audit trail
✅ Robust validation
✅ Clean, professional UI
✅ Production-ready code
✅ Security best practices
✅ Database optimization
✅ Transaction safety

The system is ready for production use and follows Laravel and financial software best practices.
