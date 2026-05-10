# Sales Returns, Exchanges & Refunds System

## Overview

This comprehensive module adds complete support for handling sales returns, product exchanges, and refund tracking to your POS system. The system manages the entire lifecycle from return request creation through approval, inventory restoration, and refund processing.

## Features Implemented

### 1. Sales Returns Management
- Create return requests for completed sales
- Track returned items with condition assessment (new, used, damaged)
- Approval workflow (pending → approved → completed)
- Automatic inventory restoration upon approval
- Support for partial and full returns

### 2. Product Exchanges
- Exchange returned items for new products
- Automatic price difference calculation
- Multiple handling methods for price differences:
  - Customer pays cash
  - Credit note issuance
  - Transaction cancellation
- Real-time inventory adjustment

### 3. Refund Processing
- Multiple refund methods: Cash, Card, Credit Note, Wallet
- Automatic processing for cash refunds
- Pending refund tracking
- Failure reason documentation
- Comprehensive refund status tracking

### 4. Refund Reporting
- Generate refund reports by date range
- Analyze refunds by method
- View pending vs. processed refunds
- Summary statistics and totals

## Database Schema

### Tables Created

#### `sales_returns`
Tracks all return requests
```
- id, sale_id, customer_id, user_id
- return_type (return | exchange | refund)
- status (pending | approved | rejected | completed)
- reason, total_returned_amount, refund_amount
- refund_method, approved_by, approved_at, completed_at
- created_at, updated_at
```

#### `return_items`
Individual items being returned
```
- id, sales_return_id, sale_item_id, product_id
- quantity_returned, unit_price, return_amount
- condition (new | used | damaged)
- item_notes, created_at, updated_at
```

#### `exchanges`
Product exchange transactions
```
- id, sales_return_id
- returned_product_id, returned_quantity
- new_product_id, new_quantity
- price_difference, price_difference_method
- notes, created_at, updated_at
```

#### `refunds`
Refund processing records
```
- id, sales_return_id
- refund_amount, method (cash | card | credit_note | wallet)
- status (pending | processed | failed)
- transaction_id, processed_by, processed_at
- failure_reason, created_at, updated_at
```

#### `sales` (Modified)
New columns added to existing sales table:
```
- return_status (none | partial | full)
- returned_amount (decimal)
```

## Models

### SalesReturn
Main model for managing returns
```php
$return->sale()              // Original sale
$return->customer()          // Customer info
$return->items()             // Returned items
$return->exchanges()         // Product exchanges
$return->refund()            // Refund record
$return->approve()           // Approve return
$return->complete()          // Mark as completed
```

### ReturnItem
Individual items in a return
```php
$item->salesReturn()
$item->saleItem()
$item->product()
```

### Exchange
Product exchange transactions
```php
$exchange->salesReturn()
$exchange->returnedProduct()
$exchange->newProduct()
$exchange->calculatePriceDifference()
```

### Refund
Refund processing
```php
$refund->salesReturn()
$refund->processedBy()
$refund->process()           // Mark as processed
$refund->markFailed()        // Mark as failed
```

## Routes

### Sales Returns
```
GET     /sales-returns           # List all returns
GET     /sales-returns/create    # Create return form
POST    /sales-returns           # Store new return
GET     /sales-returns/{id}      # View return details
GET     /sales-returns/{id}/edit # Edit return
PUT     /sales-returns/{id}      # Update return
DELETE  /sales-returns/{id}      # Delete return
POST    /sales-returns/{id}/approve  # Approve return
POST    /sales-returns/{id}/reject   # Reject return
POST    /sales-returns/{id}/complete # Complete return
GET     /sales-returns/{id}/details  # Get sale details (AJAX)
```

### Exchanges
```
GET     /exchanges                           # List exchanges
GET     /sales-returns/{id}/exchanges/create # New exchange form
POST    /exchanges                           # Store exchange
GET     /exchanges/{id}                      # View exchange
GET     /exchanges/{id}/edit                 # Edit exchange
PUT     /exchanges/{id}                      # Update exchange
DELETE  /exchanges/{id}                      # Delete exchange
```

### Refunds
```
GET     /refunds                    # List refunds
GET     /sales-returns/{id}/refund/create  # New refund form
POST    /refunds                    # Store refund
GET     /refunds/{id}               # View refund details
DELETE  /refunds/{id}               # Delete refund
POST    /refunds/{id}/process       # Process refund
POST    /refunds/{id}/mark-failed   # Mark as failed
GET     /refunds-report             # Refund report
```

## Controllers

### SalesReturnController
```php
index()          // List returns (with filtering)
create()         // Return creation form
store()          // Save new return
show()           // View return details
edit()           // Edit pending return
update()         // Update return
approve()        // Approve return
reject()         // Reject return
complete()       // Complete return
destroy()        // Delete pending return
getSaleDetails() // AJAX endpoint
```

### ExchangeController
```php
index()  // List exchanges
create() // Exchange form
store()  // Save exchange
show()   // View exchange
edit()   // Edit exchange
update() // Update exchange
destroy()// Delete exchange
```

### RefundController
```php
index()      // List refunds
create()     // Refund form
store()      // Save refund
show()       // View refund
process()    // Process refund
markFailed() // Mark failed
report()     // Generate report
destroy()    // Delete refund
```

## Blade Views

### Returns
- `returns/index.blade.php` - Return listing with filters
- `returns/create.blade.php` - Create return with dynamic item selection
- `returns/edit.blade.php` - Edit pending returns
- `returns/show.blade.php` - Return details with approval workflow

### Exchanges
- `exchanges/index.blade.php` - Exchange listing
- `exchanges/create.blade.php` - Create exchange with price calculation
- `exchanges/edit.blade.php` - Edit exchange
- `exchanges/show.blade.php` - Exchange details

### Refunds
- `refunds/index.blade.php` - Refund listing with filters
- `refunds/create.blade.php` - Refund creation form
- `refunds/show.blade.php` - Refund details with processing options
- `refunds/report.blade.php` - Comprehensive refund report

## Service Classes

### RefundService
Utility class for refund business logic:
```php
calculateRefundAmount()           // Calculate refund total
processRefund()                   // Process refund
canReturnSale()                   // Check return eligibility
getRefundEligibilityMessage()    // Eligibility message
getInventoryImpact()             // Show inventory changes
getRefundStatusSummary()         // Get summary statistics
validateReturnRequest()          // Validate return data
```

## Usage Examples

### Creating a Return
```
1. Navigate to Sales Returns → New Return
2. Select a completed sale
3. Choose return type (Return/Exchange/Refund)
4. Provide reason
5. Select items to return and their condition
6. Submit for approval
```

### Processing a Return
```
1. View pending return
2. Review items and reason
3. Approve return (items automatically restocked)
4. If exchange: Add new product and quantity
5. If refund: Process refund through selected method
6. Mark as completed
```

### Generating Refund Report
```
1. Navigate to Refunds → Report
2. Set date range (optional)
3. View summary by method
4. Analyze processed vs pending refunds
```

## Business Rules Enforced

1. **Return Window**: Sales can only be returned within 30 days (configurable)
2. **Completed Sales Only**: Only completed sales can be returned
3. **Quantity Validation**: Cannot return more than purchased
4. **Inventory Management**: Automatic inventory restoration on approval
5. **Approval Workflow**: Returns must be approved before completion
6. **Status Tracking**: Sales track partial/full return status
7. **Exchange Matching**: New product must have sufficient stock

## Key Features

### Approval Workflow
- Pending → Review for approval
- Approve → Items restocked, ready for exchange/refund
- Reject → Provide rejection reason
- Complete → Final status

### Automatic Calculations
- Return amounts: Quantity × Unit Price
- Price differences: New value - Returned value
- Inventory updates: Automatic on approval

### Status Tracking
- Track return status on original sale
- Track refund method and status
- Track exchange details and price differences

## Security Considerations

- All operations require authentication
- Approval by designated user
- Audit trail through user_id and timestamps
- Non-destructive (soft-delete where applicable)
- Proper validation of quantities and amounts

## Configuration

Return window can be configured in `RefundService.php`:
```php
// Default: 30 days
const RETURN_WINDOW_DAYS = 30;
```

Refund methods can be extended in models and controllers.

## Testing Scenarios

1. **Full Return**: Return all items from a sale
2. **Partial Return**: Return some items from a sale
3. **Exchange**: Return items + receive different items
4. **Price Difference**: Handle positive/negative differences
5. **Refund Methods**: Test each refund method
6. **Rejection**: Test return rejection workflow

## Future Enhancements

- Damaged item credit/deduction percentage
- Return authorization numbers (RMA)
- Customer communication (email notifications)
- Automatic credit note generation
- Multi-level approval workflows
- Return analytics dashboard
- API endpoints for mobile app
- Batch refund processing
- Integration with payment gateways

## Troubleshooting

### Returns not showing items
- Check if sale.status === 'completed'
- Verify sale items exist in sale_items table

### Inventory not updating
- Check if approval() method was called
- Verify product quantity is correct

### Refund processing fails
- Check refund.status is 'pending'
- Verify refund_amount is greater than 0

## Support & Maintenance

- Database backups recommended before bulk return operations
- Monitor refund report for processing delays
- Regular cleanup of rejected returns (optional)
- Monitor inventory accuracy after returns

---

**Version**: 1.0
**Last Updated**: 2026-04-06
**Status**: Production Ready
