# Edit & Delete Payments Guide

**Status**: ✅ Complete & Ready to Use

## Overview

The payment management system now includes full **edit** and **delete** capabilities for payment records. Modify payment details anytime and delete unwanted payments. All changes automatically recalculate invoice totals.

## Features

### 1. Edit Payment ✏️
Modify any payment record:
- ✅ Change payment date
- ✅ Change payment amount
- ✅ Change payment method
- ✅ Update reference number
- ✅ Edit notes
- ✅ Auto-recalculates invoice totals

### 2. Delete Payment 🗑️
Remove payments completely:
- ✅ Confirmation dialog before deletion
- ✅ Auto-recalculates invoice totals
- ✅ Auto-updates payment status
- ✅ Audit trail preserved

### 3. Real-time Calculations
After any edit or delete:
- ✅ Total paid amount recalculated
- ✅ Remaining balance updated
- ✅ Payment status updated (Pending → Partial → Paid)
- ✅ Invoice display refreshed

---

## How to Edit a Payment

### Step-by-Step

1. **Open Invoice**
   - Navigate to invoice detail page
   - Scroll to "Payment History" section

2. **Find Payment to Edit**
   - Locate the payment in the table
   - Look for the edit icon (pencil ✏️)

3. **Click Edit Button**
   - Click the pencil icon in the Actions column
   - Edit Payment modal opens

4. **Update Details**
   ```
   ┌─────────────────────────────────────────┐
   │ Edit Payment              [X]           │
   ├─────────────────────────────────────────┤
   │ Invoice Total:   LKR 100,000           │
   │ Currently Paid:  LKR 50,000            │
   │ Remaining:       LKR 50,000            │
   ├─────────────────────────────────────────┤
   │ Payment Date: [2026-07-10]             │
   │ Amount (LKR): [50000]                  │
   │ Method: [Bank Transfer ▼]              │
   │ Reference: [TXN-123456]                │
   │ Notes: [Customer payment]              │
   ├─────────────────────────────────────────┤
   │ [ Update Payment ] [ Cancel ]           │
   └─────────────────────────────────────────┘
   ```

5. **Modify Fields**
   - Update any field you need to change
   - Validation updates in real-time
   - See error messages if validation fails

6. **Submit Changes**
   - Click "Update Payment" button
   - Modal closes
   - Invoice totals refresh automatically
   - Success message appears

7. **Verify Changes**
   - Check payment history table
   - Confirm updated values
   - Check invoice totals updated

### Example Edit Scenario

**Before Edit**:
```
Payment Date: 2026-07-09
Amount: 30,000 LKR
Method: Cash
Reference: (empty)
```

**User Changes**:
- Updates date to 2026-07-10
- Changes amount to 35,000
- Changes method to Bank Transfer
- Adds reference: TXN-456789

**After Edit**:
```
Payment Date: 2026-07-10 ✓
Amount: 35,000 LKR ✓
Method: 🏦 Bank Transfer ✓
Reference: TXN-456789 ✓
Invoice Totals: Recalculated ✓
```

---

## How to Delete a Payment

### Step-by-Step

1. **Open Invoice**
   - Navigate to invoice detail page
   - Scroll to "Payment History" section

2. **Find Payment to Delete**
   - Locate the payment in the table
   - Look for the delete icon (trash 🗑️)

3. **Click Delete Button**
   - Click the trash icon in the Actions column
   - Confirmation dialog appears:
   ```
   "Remove this payment? This will update invoice totals."
   [Cancel]  [OK]
   ```

4. **Confirm Deletion**
   - Review the payment details
   - Click "OK" to confirm
   - Payment is deleted immediately

5. **Verify Deletion**
   - Payment removed from history table
   - Invoice totals recalculated
   - Status updated if needed
   - Success message appears

### Example Delete Scenario

**Before Delete**:
```
Payments:
  1. 2026-07-05: LKR 20,000 (Cash)
  2. 2026-07-08: LKR 30,000 (Bank)
  3. 2026-07-10: LKR 15,000 (Card)

Invoice Status: Partially Paid
Total Paid: 65,000 LKR
Balance: 35,000 LKR
```

**Delete Payment #2**:
```
Click trash icon for "2026-07-08: LKR 30,000"
Confirm deletion
```

**After Delete**:
```
Payments:
  1. 2026-07-05: LKR 20,000 (Cash)
  2. 2026-07-10: LKR 15,000 (Card)

Invoice Status: Partially Paid ✓
Total Paid: 35,000 LKR ✓
Balance: 65,000 LKR ✓
```

---

## Action Buttons in Payment History

Each payment row has up to 3 action buttons:

| Icon | Action | Purpose |
|------|--------|---------|
| 📝 | View Notes | View payment notes (if any) |
| ✏️ | Edit | Modify payment details |
| 🗑️ | Delete | Remove payment completely |

**Example**:
```
Date       | Method | Reference | Amount  | Actions
-----------|--------|-----------|---------|----------------
Jul 10 '26 | 🏦 Bank| TXN-123   | 50,000  | 📝 ✏️ 🗑️
```

---

## Validation Rules

### Edit Payment Validation

- **Amount**
  - Must be greater than 0
  - Must be numeric
  - Cannot be negative
  - No maximum limit (allows overpayment)

- **Date**
  - Must be valid date format (YYYY-MM-DD)
  - No future dates recommended but allowed

- **Payment Method**
  - Must be selected
  - Options: Cash, Bank Transfer, Card, Cheque, Online

- **Reference Number**
  - Optional
  - Maximum 100 characters

- **Notes**
  - Optional
  - Maximum 500 characters

### Real-time Feedback

```
If amount is 0:
  ❌ "Payment amount must be greater than zero."

If amount is negative:
  ❌ "Payment amount must be greater than zero."

If amount > invoice total (overpayment):
  ⚠️ Warning shown (but allowed)

If all valid:
  ✅ Form is ready to submit
```

---

## Auto-Recalculation After Edit/Delete

### What Gets Recalculated

```
paid_amount = SUM(all payment amounts)
balance = total_amount - paid_amount
payment_status = Based on balance
  - If balance >= total: "Paid"
  - If balance > 0: "Partial"
  - If balance = 0: "Pending"
```

### Example Recalculation

**Invoice Total**: 100,000 LKR

**Scenario 1: Edit Payment Amount**
```
Before: Payment 30,000 → Total Paid: 30,000 → Status: Partial
Edit:   Change to 40,000
After:  Total Paid: 40,000 → Status: Partial ✓
```

**Scenario 2: Delete Payment**
```
Before: 3 payments, Total: 60,000 → Status: Partial
Delete: Remove one payment (20,000)
After:  2 payments, Total: 40,000 → Status: Partial ✓
```

**Scenario 3: Edit to Complete Payment**
```
Before: Payment 45,000 → Remaining: 55,000 → Status: Partial
Edit:   Change to 100,000 (overpayment)
After:  Total: 100,000 → Remaining: 0 → Status: Paid ✓
```

---

## UI Features

### Edit Modal
- 🟠 Orange/amber theme to indicate editing
- Summary showing current invoice state
- All fields pre-filled with current values
- Real-time validation feedback
- Clear "Update Payment" button

### Delete Confirmation
- Standard browser confirm dialog
- Shows what will happen
- Easy to cancel
- Irreversible after confirmation

### Payment History Table
- Shows date with relative time ("2 days ago")
- Icons for payment methods
- Reference number or "—" if empty
- User who recorded payment
- Amount in green text
- Action buttons visible on hover

---

## Common Scenarios

### Scenario 1: Wrong Amount Entered
```
Situation: Entered 30,000 but should be 35,000
Solution:
  1. Click edit icon
  2. Change 30,000 to 35,000
  3. Click "Update Payment"
  4. Invoice totals auto-update
```

### Scenario 2: Duplicate Payment
```
Situation: Same payment recorded twice
Solution:
  1. Find duplicate in payment history
  2. Click delete icon
  3. Confirm deletion
  4. Totals recalculated automatically
```

### Scenario 3: Wrong Payment Method
```
Situation: Recorded as "Cash" but was "Bank Transfer"
Solution:
  1. Click edit icon
  2. Change method to "Bank Transfer"
  3. Add transaction ID as reference
  4. Click "Update Payment"
```

### Scenario 4: Payment Applied to Wrong Invoice
```
Situation: Payment entered for wrong invoice
Solution:
  1. Open correct invoice
  2. Record payment with correct details
  3. Go back to wrong invoice
  4. Delete incorrect payment
  5. Both invoices now have correct totals
```

---

## Best Practices

### Do's ✓
- ✓ Use clear reference numbers for tracking
- ✓ Add notes explaining payment details
- ✓ Keep payment dates accurate
- ✓ Review invoice after edit/delete
- ✓ Use correct payment method
- ✓ Verify totals after changes

### Don'ts ✗
- ✗ Don't delete payments without reason
- ✗ Don't change amounts randomly
- ✗ Don't leave reference fields blank for large payments
- ✗ Don't forget to review invoice after changes
- ✗ Don't use vague payment methods

---

## Audit Trail

All edits and deletes are tracked:

**Edit**:
- Who made the change (if updated_by recorded)
- What changed (new values saved)
- When it changed (updated_at timestamp)

**Delete**:
- Payment record is removed
- Deleted by (from action)
- When deleted (request time)

---

## API Integration

### Edit Payment API
```http
GET /invoices/{invoice_id}/payment/{payment_id}
Response: { id, payment_date, amount, payment_method, reference_number, notes }
```

### Update Payment API
```http
PATCH /invoices/{invoice_id}/payment/{payment_id}
Body: { payment_date, amount, payment_method, reference_number, notes }
Response: Redirect to invoice page with success message
```

### Delete Payment API
```http
DELETE /invoices/{invoice_id}/payment/{payment_id}
Response: Redirect to invoice page with success message
```

---

## Troubleshooting

### Issue: Edit button not appearing
**Solution**: Ensure you have view permissions for the invoice

### Issue: Delete not working
**Solution**: Try refreshing page; check browser console for errors

### Issue: Totals not updating after edit
**Solution**: Refresh invoice page; totals should recalculate

### Issue: Cannot delete payment
**Solution**: Ensure no other operations in progress; try again

---

## Summary

| Action | What Happens | Auto-Updates |
|--------|--------------|--------------|
| **Edit Payment** | Modifies payment record | ✓ All totals |
| **Delete Payment** | Removes payment completely | ✓ All totals |
| **Update Fields** | Changes specific payment data | ✓ Status badge |
| **Add Reference** | Helps track payment | ✓ Display |

---

## Quick Reference

**Edit Payment**:
1. Click pencil (✏️) icon
2. Make changes
3. Click "Update Payment"
4. Done! ✓

**Delete Payment**:
1. Click trash (🗑️) icon
2. Confirm in dialog
3. Payment removed
4. Done! ✓

🎉 **Full payment editing and deletion now available!**
