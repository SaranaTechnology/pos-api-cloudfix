# Cashier POS Service

Point of Sale - cash transactions, sales, receipts.

## Features

- POS transactions
- Cash management
- Receipt printing
- Daily sales reports
- Cash drawer management
- Payment methods
- Discounts & promotions

## Key Endpoints

```
# Transactions
GET/POST /api/v1/transactions
GET /api/v1/transactions/{id}
POST /api/v1/transactions/{id}/void

# Cash Drawer
POST /api/v1/cash-drawer/open
POST /api/v1/cash-drawer/close
GET /api/v1/cash-drawer/status

# Sales
GET /api/v1/sales/daily
GET /api/v1/sales/summary

# Products (sync from WMS)
GET /api/v1/products
GET /api/v1/products/{barcode}

# Payments
POST /api/v1/payments/cash
POST /api/v1/payments/card
POST /api/v1/payments/qris
```

## Database Tables

- `pos_transactions` - POS transaction headers
- `pos_transaction_items` - Transaction line items
- `cash_drawers` - Cash drawer sessions
- `cash_movements` - Cash in/out movements
- `payment_methods` - Available payment types
- `discounts` - Discount rules
- `receipts` - Receipt data

## Key Actions

| Action | Description |
|--------|-------------|
| `CreateTransactionAction` | Create POS sale |
| `VoidTransactionAction` | Void/cancel transaction |
| `OpenCashDrawerAction` | Start cash session |
| `CloseCashDrawerAction` | End session, reconcile |
| `ProcessPaymentAction` | Handle payment |
| `GetDailySalesAction` | Daily sales report |

## Transaction Flow

```
Scan Items → Apply Discounts → Select Payment → Process → Print Receipt
```

## Cash Drawer Flow

```
Open Drawer (float) → Sales → Cash In/Out → Close Drawer (count) → Reconcile
```

## Payment Methods

| Method | Integration |
|--------|-------------|
| Cash | Direct |
| Card | Payment gateway |
| QRIS | Xendit/other |
| Transfer | Manual verify |

## Shared Database

- Products synced from WMS service
- Shared Redis cache with all services
- Can create journals via Core for sales revenue
