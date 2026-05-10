# Super Shop POS System - Setup Guide

## Overview
This is a complete Point of Sale (POS) system built with Laravel and Bootstrap 5, featuring inventory management and customer tracking for Super Shop.

## Features Included

### 1. **Dashboard**
   - Total Revenue Overview
   - Sales Count
   - Customer Count
   - Product Inventory Count
   - Low Stock Alerts
   - Recent Sales Overview

### 2. **Sales Management**
   - Create new sales transactions
   - Add multiple products to a single sale
   - Apply discounts and taxes
   - Multiple payment methods (Cash, Card, Check)
   - Customer tracking (walk-in or registered)
   - View detailed sale history
   - Print receipts

### 3. **Product Management**
   - Add new products with SKU, price, category
   - Edit product details
   - Track stock quantities
   - Set reorder levels for low stock alerts
   - Delete products

### 4. **Inventory Management**
   - Real-time stock tracking
   - Low stock alerts
   - Inventory adjustments with reasons
   - Stock history tracking
   - Automatic inventory updates on sales

### 5. **Customer Management**
   - Add and manage customers
   - Track customer purchase history
   - Store contact information
   - Monitor total customer purchases

## Database Setup

### 1. Update Database Configuration (.env file)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_db
DB_USERNAME=root
DB_PASSWORD=root
```

### 2. Create Database
```bash
# Login to MySQL
mysql -u root -p

# Create the database
CREATE DATABASE pos_db;
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Create Test User (Optional)
```bash
php artisan tinker
# Then run:
App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
]);
# Type 'exit' to quit
```

## Running the Application

### 1. Start the Development Server
```bash
php artisan serve
```

### 2. Access the Application
- Open your browser and navigate to: `http://localhost:8000`
- Login with your credentials

## File Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── ProductController.php
│   ├── SaleController.php
│   ├── CustomerController.php
│   └── InventoryController.php
├── Models/
│   ├── Product.php
│   ├── Sale.php
│   ├── SaleItem.php
│   ├── Customer.php
│   └── InventoryTransaction.php

database/
├── migrations/
│   ├── 2024_01_01_000001_create_products_table.php
│   ├── 2024_01_01_000002_create_customers_table.php
│   ├── 2024_01_01_000003_create_sales_table.php
│   ├── 2024_01_01_000004_create_sale_items_table.php
│   └── 2024_01_01_000005_create_inventory_transactions_table.php

resources/views/
├── layouts/app.blade.php
├── dashboard.blade.php
├── products/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── sales/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── show.blade.php
├── customers/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── inventory/
    └── index.blade.php

routes/web.php
```

## Routes

All routes are protected with authentication middleware:

- `GET /dashboard` - Dashboard
- `GET /products` - View all products
- `GET /products/create` - Create product form
- `POST /products` - Store product
- `GET /products/{id}/edit` - Edit product form
- `PUT /products/{id}` - Update product
- `DELETE /products/{id}` - Delete product
- `GET /sales` - View all sales
- `GET /sales/create` - Create sale form
- `POST /sales` - Store sale
- `GET /sales/{id}` - View sale details
- `GET /customers` - View all customers
- `GET /customers/create` - Create customer form
- `POST /customers` - Store customer
- `GET /customers/{id}/edit` - Edit customer form
- `PUT /customers/{id}` - Update customer
- `DELETE /customers/{id}` - Delete customer
- `GET /inventory` - View inventory
- `POST /inventory/{product}/adjust` - Adjust stock

## Database Tables

### Products Table
- id, name, description, price, quantity, reorder_level, sku, category, image, active, timestamps

### Customers Table
- id, name, phone, email, address, total_purchases, timestamps

### Sales Table
- id, customer_id (nullable), user_id, total_amount, discount, tax, final_amount, payment_method, status, notes, timestamps

### Sale Items Table
- id, sale_id, product_id, quantity, price, subtotal, timestamps

### Inventory Transactions Table
- id, product_id, type (in/out/adjustment), quantity, reason, user_id, timestamps

## Key Features

✅ Responsive Bootstrap 5 Design
✅ Real-time stock management
✅ Automatic inventory updates
✅ Low stock alerts
✅ Customer tracking
✅ Sales history with detailed receipts
✅ Payment method recording
✅ Discount and tax calculation
✅ User-based transaction logging

## Support

For issues or bug reports, please check the Laravel and Bootstrap documentation:
- Laravel: https://laravel.com/docs
- Bootstrap: https://getbootstrap.com/docs

---
Created with ❤️ for Super Shop POS System
