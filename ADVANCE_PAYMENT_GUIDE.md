# Advance Payment Feature Guide

**Status**: ✅ Complete & Ready to Use

## Overview

The advance payment feature allows you to record an initial or partial payment **when creating an invoice**. This is useful for deposits, retainers, or upfront payments.

## How It Works

### During Invoice Creation

1. **Fill in Invoice Details**
   - Add invoice number, date, customer, and line items as usual

2. **Enable Advance Payment**
   - Scroll to "Advance Payment (Optional)" section
   - Check the checkbox: "This invoice has an advance/initial payment"
   - The advance payment fields will appear

3. **Enter Payment Details**
   - **Advance Amount**: Enter the amount received (e.g., 50,000 LKR)
   - **Payment Method**: Select from Cash, Bank Transfer, Card, Cheque, or Online
   - **Reference Number**: Optional (e.g., check number, transaction ID)

4. **Review Summary**
   - Invoice Total, Advance Paid, and Remaining Due display in real-time
   - Payment Status shows: "Pending", "Partially Paid", or "Fully Paid"

5. **Submit**
   - Click "Create Invoice"
   - Invoice is created with the advance payment automatically recorded
   - Invoice status updates to "partial" if advance is less than total

## Real-time Calculations

As you enter the advance amount, the system automatically calculates:

```
Remaining Balance = Invoice Total - Advance Amount
Payment Status = Based on how much was paid
```

**Example**:
```
Invoice Total:     LKR 100,000
Advance Paid:      LKR 30,000
Remaining Due:     LKR 70,000
Payment Status:    Partially Paid
```

## Usage Scenarios

### Scenario 1: Simple Deposit
- Customer pays 20% upfront
- Invoice for LKR 100,000
- Advance Payment: LKR 20,000
- Remaining: LKR 80,000
- Status: **Partially Paid**

### Scenario 2: Full Upfront Payment
- Payment received at time of invoice
- Invoice for LKR 50,000
- Advance Payment: LKR 50,000
- Remaining: LKR 0
- Status: **Paid**

### Scenario 3: No Advance Payment
- Leave checkbox unchecked
- Invoice for LKR 75,000
- Advance Payment: None
- Status: **Pending**
- Collect full payment later

## Database Impact

### What Gets Created

When you create an invoice with an advance payment:

1. **Invoice Record**
   - Full invoice with all items
   - Total amount set
   - Payment status calculated

2. **Payment Record** (Automatic)
   - Payment amount recorded
   - Payment method stored
   - Payment date = Invoice date
   - Created by = Current user
   - Notes = "Advance/Initial payment at invoice creation"

### Database Fields

```
payments table:
- invoice_id     → Links to the invoice
- amount         → Advance amount
- payment_date   → Same as invoice date
- payment_method → Selected method
- reference_number → Tracking reference
- notes          → "Advance/Initial payment..."
- created_by     → Current user ID
```

## UI Features

### Form Section
```
┌─────────────────────────────────────────────────────┐
│ 🟣 Advance Payment (Optional)      [Initial Payment]│
├─────────────────────────────────────────────────────┤
│ 📋 Receive an advance or partial payment...         │
│                                                     │
│ ☑ This invoice has an advance/initial payment      │
│                                                     │
│ [When checked, shows:]                              │
│                                                     │
│ Advance Amount (LKR): [30000        ]              │
│ Payment Method:       [Cash ▼       ]              │
│ Reference Number:     [             ]              │
│                                                     │
│ Invoice Total:        LKR 100,000                   │
│ Advance Paid:         LKR 30,000                    │
│ Remaining Due:        LKR 70,000                    │
│ Status:               Partially Paid                │
└─────────────────────────────────────────────────────┘
```

## Validation

### Advance Amount Validation
- ✅ Can be zero or empty (optional)
- ✅ Cannot be negative
- ✅ Can exceed invoice total (creates overpayment)
- ⚠️ Warning shown if advance > total

### Payment Method
- ✅ Must be selected if advance amount > 0
- ✅ Options: Cash, Bank Transfer, Card, Cheque, Online

### Reference Number
- ✅ Optional field
- ✅ Use for tracking (check #, transaction ID, etc.)

## After Invoice Creation

### What Happens Next

1. **Invoice Created** ✓
   - Full invoice with advance payment recorded
   - Payment history shows the advance payment

2. **View Invoice**
   - Navigate to invoice detail page
   - See "Partially Paid" status (if advance < total)
   - Payment history table shows advance payment
   - "Recorded By" field shows your name

3. **Record Additional Payments**
   - Click "Record Payment" to add more payments
   - Remaining balance updates automatically
   - Status changes to "Paid" when fully paid

4. **Download Payment Receipt**
   - Once payments are recorded
   - Click "Payment Receipt" to download PDF
   - Shows all payments including advance

## Example Workflow

### Step-by-Step

1. **Create New Invoice**
   ```
   Invoice Number: INV-0001
   Date: 2026-07-10
   Customer: ABC Company
   Items: 
     - Item 1: 50,000 LKR
     - Item 2: 50,000 LKR
   Total: 100,000 LKR
   ```

2. **Add Advance Payment**
   ```
   ☑ This invoice has an advance/initial payment
   Advance Amount: 40,000
   Payment Method: Bank Transfer
   Reference: TXN-123456
   ```

3. **Invoice Created**
   ```
   Status: Partially Paid (40%)
   Paid: 40,000 LKR
   Remaining: 60,000 LKR
   ```

4. **Later - Record Second Payment**
   ```
   Date: 2026-07-15
   Amount: 60,000
   Method: Cash
   ```

5. **Final Status**
   ```
   Status: Paid (100%)
   Total Paid: 100,000 LKR
   Remaining: 0 LKR
   ```

## Tips & Best Practices

### Do's ✓
- ✓ Use for retainers and deposits
- ✓ Use payment reference for tracking
- ✓ Record payment method accurately
- ✓ Check the "Partially Paid" status after creation
- ✓ Download payment receipt when fully paid

### Don'ts ✗
- ✗ Don't leave payment method blank if advance > 0
- ✗ Don't forget to record additional payments later
- ✗ Don't use unclear reference numbers
- ✗ Don't create duplicate advance payments (edit invoice if needed)

## Common Issues & Solutions

### Issue: Advance payment not appearing
**Solution**: 
- Ensure checkbox is checked before submitting
- Verify advance amount is > 0
- Check browser console for errors

### Issue: Payment status wrong
**Solution**:
- Invoice totals calculated from line items + tax
- Advance compared against final total
- If incorrect, check manual_total field

### Issue: Want to change advance amount
**Solution**:
- Create new invoice (can't edit advance)
- OR delete payment and record new one
- Then record correct advance payment

## Technical Details

### Form Fields Added
```html
<input name="has_advance" type="checkbox" />
<input name="advance_amount" type="number" />
<select name="advance_payment_method" />
<input name="advance_reference" type="text" />
```

### Controller Logic
```php
// In InvoiceController::store()
if ($request->has_advance && $request->advance_amount > 0) {
    Payment::create([
        'invoice_id' => $invoice->id,
        'amount' => $request->advance_amount,
        'payment_method' => $request->advance_payment_method,
        'payment_date' => $request->invoice_date,
        'notes' => 'Advance/Initial payment at invoice creation',
        'created_by' => auth()->id(),
    ]);
    $invoice->recalculatePaid();
}
```

### Automatic Features
- ✅ Payment automatically linked to invoice
- ✅ Invoice status automatically updated
- ✅ Balance automatically calculated
- ✅ User tracking (created_by)
- ✅ No extra steps needed

## Payment Method Icons

In the UI and payment history:
- 💵 Cash
- 🏦 Bank Transfer
- 💳 Card
- 📋 Cheque
- 🌐 Online

## Reporting & Analysis

### You Can See
- Invoice with advance payment
- Total collected
- Outstanding balance
- Payment history with all transactions
- Payment receipt PDF

### Tracking
- Who recorded the advance
- When it was recorded
- Payment method used
- Reference number for reconciliation

## Integration with Other Features

### Works With
- ✓ Multiple payments system
- ✓ Payment receipts
- ✓ Invoice PDF
- ✓ Dashboard reports
- ✓ Customer management

### Does Not Affect
- Invoice creation/editing (except adding payment)
- Line item management
- Tax calculations
- Customer details

## Summary

| Feature | Details |
|---------|---------|
| **Where** | Invoice creation form |
| **When** | Optional, during invoice creation |
| **Payment Methods** | Cash, Bank, Card, Cheque, Online |
| **Auto-Updates** | Status, balance, payment count |
| **Tracking** | User, date, method, reference |
| **Later Payments** | Can record additional via "Record Payment" |
| **PDF Receipt** | Available after payments recorded |

---

## Quick Start

1. Create invoice as usual
2. Scroll to "Advance Payment" section
3. Check "This invoice has advance..."
4. Enter amount and payment method
5. Submit → Done! Advance payment recorded
6. View invoice → See "Partially Paid" status
7. Record additional payments when received

🎉 **The advance payment system is production-ready and fully integrated!**
