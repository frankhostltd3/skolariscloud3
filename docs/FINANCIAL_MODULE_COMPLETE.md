# Financial Module - Complete Implementation Guide

## Overview
The Financial Module is a comprehensive financial management system for educational institutions, providing complete CRUD operations for expense tracking, fee management, student invoicing, and payment recording. The module is fully multi-tenant, production-ready, and immediately deployable.

## Database Architecture

### Tables Structure

#### 1. expense_categories
Hierarchical expense categorization with parent-child relationships.

```sql
- id (primary key)
- school_id (foreign key to schools)
- parent_id (self-referential foreign key, nullable)
- name (string 255)
- code (string 50)
- description (text, nullable)
- color (string 7, hex color)
- icon (string 50, Bootstrap icon class)
- is_active (boolean, default true)
- budget_limit (decimal 15,2, nullable)
- timestamps
```

#### 2. expenses
Expense records with approval workflow and file upload support.

```sql
- id (primary key)
- school_id (foreign key to schools)
- expense_category_id (foreign key to expense_categories)
- currency_id (foreign key to currencies)
- title (string 255)
- description (text, nullable)
- amount (decimal 15,2)
- expense_date (date)
- payment_method (enum: cash, bank_transfer, mobile_money, cheque, card)
- reference_number (string 100, nullable)
- vendor_name (string 255, nullable)
- vendor_contact (string 255, nullable)
- receipt_path (string 255, nullable)
- status (enum: pending, approved, rejected, default pending)
- approved_by (foreign key to users, nullable)
- approved_at (datetime, nullable)
- rejected_reason (text, nullable)
- notes (text, nullable)
- created_by (foreign key to users)
- timestamps
- softDeletes
```

#### 3. fee_structures
Academic year-based fee definitions for different fee types.

```sql
- id (primary key)
- school_id (foreign key to schools)
- fee_name (string 255)
- fee_type (enum: tuition, registration, examination, transport, accommodation, meals, uniform, books, activity, other)
- amount (decimal 15,2)
- academic_year (string 10)
- term (string 50, nullable)
- due_date (date, nullable)
- description (text, nullable)
- is_active (boolean, default true)
- timestamps
```

#### 4. invoices
Student invoices with payment tracking.

```sql
- id (primary key)
- school_id (foreign key to schools)
- invoice_number (string 50, unique per school)
- student_id (foreign key to users)
- fee_structure_id (foreign key to fee_structures)
- invoice_date (date)
- due_date (date)
- total_amount (decimal 15,2)
- paid_amount (decimal 15,2, default 0)
- balance (virtual calculated field: total_amount - paid_amount)
- status (enum: paid, partial, unpaid, overdue)
- notes (text, nullable)
- timestamps
```

#### 5. payments
Payment records with auto-generated receipts.

```sql
- id (primary key)
- school_id (foreign key to schools)
- invoice_id (foreign key to invoices)
- receipt_number (string 50, unique per school)
- payment_date (date)
- amount (decimal 15,2)
- payment_method (enum: cash, bank_transfer, mobile_money, cheque, card)
- reference_number (string 100, nullable)
- notes (text, nullable)
- timestamps
```

## Models

### ExpenseCategory Model
**Location:** `app/Models/Finance/ExpenseCategory.php`

**Key Features:**
- Tenant connection: `protected $connection = 'tenant'`
- Hierarchical structure with parent/children relationships
- School scoping with `scopeForSchool()`
- Budget tracking and statistics

**Relationships:**
- `school()` - BelongsTo School
- `parent()` - BelongsTo ExpenseCategory (self)
- `children()` - HasMany ExpenseCategory (self)
- `expenses()` - HasMany Expense

**Scopes:**
- `scopeActive($query)` - Active categories only
- `scopeForSchool($query, $schoolId)` - Filter by school

**Attributes:**
- `getStatusBadgeClassAttribute()` - Returns Bootstrap badge class (success/secondary)
- `getStatusTextAttribute()` - Returns "Active" or "Inactive"

### Expense Model
**Location:** `app/Models/Finance/Expense.php`

**Key Features:**
- Tenant connection with soft deletes
- Approval workflow (pending → approved/rejected)
- File upload support for receipts
- Payment method tracking

**Relationships:**
- `school()` - BelongsTo School
- `category()` - BelongsTo ExpenseCategory
- `currency()` - BelongsTo Currency
- `creator()` - BelongsTo User (created_by)
- `approver()` - BelongsTo User (approved_by)

**Scopes:**
- `scopeForSchool($query, $schoolId)` - Filter by school
- `scopeApproved($query)` - Approved expenses only
- `scopePending($query)` - Pending expenses only
- `scopeRejected($query)` - Rejected expenses only
- `scopeThisMonth($query)` - Current month expenses
- `scopeThisYear($query)` - Current year expenses

**Attributes:**
- `getStatusBadgeClassAttribute()` - Badge color based on status
- `getPaymentMethodLabelAttribute()` - Human-readable payment method

### FeeStructure Model
**Location:** `app/Models/Finance/FeeStructure.php`

**Key Features:**
- Academic year-based fee definitions
- 10 different fee types
- Term support for multi-term schools

**Relationships:**
- `school()` - BelongsTo School
- `invoices()` - HasMany Invoice

**Scopes:**
- `scopeForSchool($query, $schoolId)` - Filter by school
- `scopeActive($query)` - Active fee structures only

### Invoice Model
**Location:** `app/Models/Finance/Invoice.php`

**Key Features:**
- Auto-generated invoice numbers (INV-YYYYMM-XXXX)
- Automatic balance calculation
- Payment status tracking
- Overdue detection

**Relationships:**
- `school()` - BelongsTo School
- `student()` - BelongsTo User
- `feeStructure()` - BelongsTo FeeStructure
- `payments()` - HasMany Payment

**Scopes:**
- `scopeForSchool($query, $schoolId)` - Filter by school
- `scopeOverdue($query)` - Overdue invoices only
- `scopeByStatus($query, $status)` - Filter by status

**Attributes:**
- `getBalanceAttribute()` - Calculated: total_amount - paid_amount
- `getStatusBadgeClassAttribute()` - Badge color based on status

### Payment Model
**Location:** `app/Models/Finance/Payment.php`

**Key Features:**
- Auto-generated receipt numbers (RCP-YYYYMM-XXXX)
- Automatic invoice update on payment
- 5 payment methods supported

**Relationships:**
- `school()` - BelongsTo School
- `invoice()` - BelongsTo Invoice

**Scopes:**
- `scopeForSchool($query, $schoolId)` - Filter by school
- `scopeToday($query)` - Today's payments
- `scopeThisMonth($query)` - Current month payments

**Attributes:**
- `getPaymentMethodLabelAttribute()` - Human-readable payment method

## Controllers

### 1. ExpenseCategoryController
**Location:** `app/Http/Controllers/Tenant/Finance/ExpenseCategoryController.php`

**Methods:**
- `index()` - List all expense categories with search/filter, statistics
- `create()` - Show create form
- `store(Request)` - Create new category with validation
- `show(ExpenseCategory)` - Display category details with statistics
- `edit(ExpenseCategory)` - Show edit form
- `update(Request, ExpenseCategory)` - Update category
- `destroy(ExpenseCategory)` - Delete category (with safety checks)

**Key Features:**
- Parent category dropdown (hierarchical)
- Color picker and icon selector
- Budget limit tracking
- Statistics: total expenses, budget used, child categories count

### 2. ExpenseController
**Location:** `app/Http/Controllers/Tenant/Finance/ExpenseController.php`

**Methods:**
- `index()` - List expenses with filters (search, category, status, date range), statistics cards
- `create()` - Show create form with all fields
- `store(Request)` - Create expense with file upload
- `show(Expense)` - Display expense details with approve/reject buttons
- `edit(Expense)` - Show edit form
- `update(Request, Expense)` - Update expense
- `destroy(Expense)` - Soft delete expense
- `approve(Expense)` - Approve pending expense
- `reject(Request, Expense)` - Reject expense with reason

**Key Features:**
- File upload for receipts (stored in tenant-specific storage)
- Approval workflow with user tracking
- Vendor information capture
- Payment method tracking
- Statistics: pending count, approved count, monthly total, rejected count

### 3. FeeStructureController
**Location:** `app/Http/Controllers/Tenant/Finance/FeeStructureController.php`

**Methods:**
- `index()` - List fee structures with filters (academic year, term, type, status)
- `create()` - Show create form
- `store(Request)` - Create fee structure
- `show(FeeStructure)` - Display fee details with invoice statistics
- `edit(FeeStructure)` - Show edit form
- `update(Request, FeeStructure)` - Update fee structure
- `destroy(FeeStructure)` - Delete fee structure

**Key Features:**
- 10 fee types dropdown
- Academic year and term fields
- Due date tracking
- Statistics: total invoices, total amount, paid amount, outstanding

### 4. InvoiceController
**Location:** `app/Http/Controllers/Tenant/Finance/InvoiceController.php`

**Methods:**
- `index()` - List invoices with filters (student, status, date range), KPI cards
- `create()` - Show invoice creation form
- `store(Request)` - Create invoice with auto-numbering
- `show(Invoice)` - Display invoice with payment history
- `edit(Invoice)` - Show edit form
- `update(Request, Invoice)` - Update invoice
- `destroy(Invoice)` - Delete invoice (if no payments)
- `generateInvoiceNumber()` - Generate unique invoice number

**Key Features:**
- Auto-generated invoice numbers (INV-202501-0001)
- Student selection dropdown
- Fee structure selection
- Payment history display
- Statistics: total invoices, total amount, paid amount, outstanding balance

### 5. PaymentController
**Location:** `app/Http/Controllers/Tenant/Finance/PaymentController.php`

**Methods:**
- `index()` - List payments with filters, statistics
- `create()` - Show payment recording form
- `store(Request)` - Record payment with invoice update
- `show(Payment)` - Display payment details
- `receipt(Payment)` - Generate printable receipt
- `generateReceiptNumber()` - Generate unique receipt number

**Key Features:**
- Auto-generated receipt numbers (RCP-202501-0001)
- Invoice selection with balance display
- Payment method dropdown (5 methods)
- Automatic invoice paid_amount update
- Receipt printing with school header
- Statistics: total payments, total amount, today's amount, this month's amount

## Routes

All routes are registered under `/tenant/finance/*` prefix with `tenant.finance.*` naming convention.

```php
Route::prefix('tenant/finance')->name('tenant.finance.')->group(function () {
    // Expense Categories (7 routes)
    Route::resource('expense-categories', ExpenseCategoryController::class);
    
    // Expenses (9 routes: 7 resource + 2 custom)
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
    
    // Fee Structures (7 routes)
    Route::resource('fee-structures', FeeStructureController::class);
    
    // Invoices (7 routes)
    Route::resource('invoices', InvoiceController::class);
    
    // Payments (5 routes: create/store/show + receipt)
    Route::resource('payments', PaymentController::class)->except(['edit', 'update', 'destroy']);
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
});
```

**Total Routes:** 35 routes

## Views

### Directory Structure
```
resources/views/tenant/finance/
├── expense-categories/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── expenses/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── fee-structures/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── invoices/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
└── payments/
    ├── index.blade.php
    ├── create.blade.php
    ├── show.blade.php
    └── receipt.blade.php
```

**Total Views:** 20 files

### View Features
- Bootstrap 5 responsive design
- Statistics cards with color-coded KPIs
- Status badges (success, warning, danger, info)
- Search and filter forms
- Pagination support
- Empty state messages
- Toast notifications
- Confirmation dialogs
- Print-optimized receipt template

## Navigation Integration

Added to admin sidebar menu under "Finance" collapsible section:

```blade
<li class="nav-item">
    <a class="nav-link" data-bs-toggle="collapse" href="#financeMenu" role="button">
        <i class="bi bi-cash-coin"></i> Finance
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="collapse" id="financeMenu">
        <ul class="nav flex-column ms-3">
            <li class="nav-item">
                <a href="{{ route('tenant.finance.expense-categories.index') }}" class="nav-link">
                    <i class="bi bi-tag"></i> Expense Categories
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.finance.expenses.index') }}" class="nav-link">
                    <i class="bi bi-receipt"></i> Expenses
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.finance.fee-structures.index') }}" class="nav-link">
                    <i class="bi bi-list-check"></i> Fee Structures
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.finance.invoices.index') }}" class="nav-link">
                    <i class="bi bi-file-earmark-text"></i> Invoices
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tenant.finance.payments.index') }}" class="nav-link">
                    <i class="bi bi-credit-card"></i> Payments
                </a>
            </li>
        </ul>
    </div>
</li>
```

## Usage Examples

### 1. Creating an Expense Category
```php
POST /tenant/finance/expense-categories

Data:
- name: "Utilities"
- code: "UTIL"
- color: "#3498db"
- icon: "bi-lightning-charge"
- budget_limit: 50000.00
- is_active: 1
```

### 2. Creating an Expense
```php
POST /tenant/finance/expenses

Data:
- title: "January Electricity Bill"
- expense_category_id: 5
- amount: 15000.00
- currency_id: 1
- expense_date: "2025-01-15"
- payment_method: "bank_transfer"
- vendor_name: "National Power Company"
- receipt: (file upload)
- status: "pending"
```

### 3. Approving an Expense
```php
POST /tenant/finance/expenses/12/approve

Result:
- status: "approved"
- approved_by: (current user id)
- approved_at: (current timestamp)
```

### 4. Creating an Invoice
```php
POST /tenant/finance/invoices

Data:
- student_id: 45
- fee_structure_id: 3
- invoice_date: "2025-01-10"
- due_date: "2025-02-10"
- notes: "Term 1 Tuition Fee"

Auto-generated:
- invoice_number: "INV-202501-0001"
- total_amount: (from fee_structure)
- paid_amount: 0
- status: "unpaid"
```

### 5. Recording a Payment
```php
POST /tenant/finance/payments

Data:
- invoice_id: 23
- payment_date: "2025-01-15"
- amount: 150000.00
- payment_method: "mobile_money"
- reference_number: "MM123456789"

Auto-generated:
- receipt_number: "RCP-202501-0001"

Result:
- Invoice paid_amount updated (+150000)
- Invoice balance recalculated
- Invoice status updated (unpaid → partial or paid)
```

### 6. Printing a Receipt
```php
GET /tenant/finance/payments/12/receipt

Returns:
- HTML receipt template ready for printing
- School header with logo
- Payment details
- Invoice summary
- Balance information
```

## Business Logic

### Invoice Balance Calculation
```php
$invoice->balance = $invoice->total_amount - $invoice->paid_amount;
```

### Invoice Status Updates
- `paid` - balance = 0
- `partial` - 0 < paid_amount < total_amount
- `unpaid` - paid_amount = 0
- `overdue` - due_date < today AND status != paid

### Expense Approval Workflow
1. Create expense with `status = 'pending'`
2. Admin reviews and approves/rejects
3. If approved: `status = 'approved'`, `approved_by = (user id)`, `approved_at = (timestamp)`
4. If rejected: `status = 'rejected'`, `rejected_reason = (text)`

### Payment Processing
1. Record payment with amount and invoice_id
2. Update invoice: `paid_amount += payment.amount`
3. Recalculate invoice balance
4. Update invoice status based on balance
5. Generate receipt number automatically

### Auto-Numbering Systems
```php
// Invoices
INV-YYYYMM-XXXX
Example: INV-202501-0001, INV-202501-0002

// Receipts
RCP-YYYYMM-XXXX
Example: RCP-202501-0001, RCP-202501-0002
```

## Security Features

### Tenant Isolation
- All models use `protected $connection = 'tenant'`
- All queries scope to `school_id`
- Ownership verification before updates/deletes

### Authorization
- Admin-only access to financial module
- User tracking for expense creation/approval
- Audit trail with created_by and approved_by

### Validation
- Form request validation for all create/update operations
- Unique constraints per school (invoice numbers, receipt numbers)
- Numeric validation for amounts
- Date validation for due dates
- Enum validation for payment methods and statuses

### CSRF Protection
- All forms include `@csrf` token
- Laravel CSRF middleware validates all POST/PUT/DELETE requests

## Integration Points

### Multi-Currency Support
```php
// Use formatMoney() helper throughout
formatMoney($amount); // Returns formatted amount with currency symbol

// Get current school currency
$currency = currentCurrency(); // Returns default currency object
```

### Student System
- Invoices link to students via `student_id`
- Student dropdown in invoice creation form
- Student information displayed on invoices and receipts

### Reporting System
- Financial reports can query expenses/payments
- KPI calculations for dashboards
- Date range filtering for reports

### Payment Gateway Integration
- Payment records can link to gateway transactions
- Reference numbers track external payment IDs
- Payment methods indicate gateway used

## Statistics & KPIs

### Expense Categories Index
- Total expense categories count
- Active categories count
- Budget utilization percentage

### Expenses Index
- Pending expenses count (yellow card)
- Approved expenses count (green card)
- Monthly expenses total (blue card)
- Rejected expenses count (red card)

### Fee Structures Show
- Total invoices generated from fee
- Total amount billed
- Total paid amount
- Outstanding balance

### Invoices Index
- Total invoices count (blue card)
- Total amount billed (green card)
- Total paid amount (info card)
- Outstanding balance (yellow card)

### Payments Index
- Total payments count (blue card)
- Total amount received (green card)
- Today's payments (info card)
- This month's payments (yellow card)

## Performance Optimization

### Database Indexes
- Primary keys on all tables
- Foreign keys with indexes
- Unique indexes on invoice_number and receipt_number
- Composite indexes on (school_id, status) for frequent filters

### Query Optimization
- Eager loading: `with('category', 'currency', 'school')`
- Count relationships: `withCount('expenses', 'payments')`
- Selective columns: `select('id', 'name', 'amount')`
- Pagination: `paginate(perPage())`

### File Storage
- Receipts stored in tenant-specific directories: `storage/app/tenant_{school_id}/receipts/`
- Validation: max 2MB, file types: jpg, jpeg, png, pdf
- Automatic cleanup on expense deletion (if soft delete)

## Testing Checklist

### Expense Categories
- ✅ Create root category
- ✅ Create child category
- ✅ View category details with statistics
- ✅ Edit category
- ✅ Deactivate category
- ✅ Delete category (with safety check if has expenses)

### Expenses
- ✅ Create expense with file upload
- ✅ View expense details
- ✅ Edit pending expense
- ✅ Approve expense
- ✅ Reject expense with reason
- ✅ Delete expense (soft delete)
- ✅ Filter expenses by category, status, date range

### Fee Structures
- ✅ Create fee structure for academic year
- ✅ View fee structure with invoice statistics
- ✅ Edit fee structure
- ✅ Deactivate fee structure
- ✅ Delete fee structure (if no invoices)

### Invoices
- ✅ Create invoice for student
- ✅ View invoice with payment history
- ✅ Edit invoice
- ✅ Payment updates invoice balance
- ✅ Invoice status updates automatically (unpaid → partial → paid)
- ✅ Overdue invoice detection

### Payments
- ✅ Record payment against invoice
- ✅ Auto-populate payment amount from invoice balance
- ✅ Generate unique receipt number
- ✅ View payment details
- ✅ Print receipt with school header
- ✅ Invoice paid_amount updates correctly

## Production Deployment

### Steps
1. Run migrations: `php artisan migrate --path=database/migrations/tenants`
2. Seed expense categories (optional): `php artisan tenants:seed-expense-categories`
3. Configure file storage permissions: `chmod -R 775 storage/app`
4. Test on staging environment with sample data
5. Deploy to production
6. Monitor error logs: `storage/logs/laravel.log`

### Configuration
- Ensure `formatMoney()`, `currentCurrency()`, `perPage()` helpers exist
- Configure tenant database connection
- Set up file storage for receipt uploads
- Configure mail settings for invoice/payment notifications (future enhancement)

## Future Enhancements

### Planned Features
- PDF export for invoices and receipts
- CSV export for financial reports
- Email notifications on invoice creation
- SMS notifications on payment receipt
- Budget vs actual expense reports
- Payment reminders for overdue invoices
- Bulk invoice generation for classes
- Payment plan support (installments)
- Expense analytics and charts
- Integration with accounting software (QuickBooks, Xero)

## Troubleshooting

### Common Issues

**Issue:** "No database selected" error
**Solution:** Ensure tenant connection is configured in models: `protected $connection = 'tenant'`

**Issue:** Invoice balance not updating after payment
**Solution:** Check payment controller `store()` method updates invoice `paid_amount`

**Issue:** Receipt not printing
**Solution:** Check browser pop-up blocker, use `target="_blank"` on receipt link

**Issue:** File upload fails
**Solution:** Check storage permissions: `chmod -R 775 storage/app`

**Issue:** Invoice/receipt number duplicates
**Solution:** Ensure `generateInvoiceNumber()` and `generateReceiptNumber()` use database locks

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Review database queries in Laravel Telescope (if installed)
- Test in isolation: Create minimal test case
- Check documentation: This file and inline code comments

---

**Status:** ✅ 100% Production Ready - Fully functional, tested, and immediately deployable

**Last Updated:** January 17, 2025
