<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportsController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', ProductController::class);
    Route::get('/products/category/{category}/subcategories', [ProductController::class, 'getSubCategories'])->name('products.get-subcategories');
    
    // Product Management - Categories, Brands, Units
    Route::resource('categories', CategoryController::class);
    Route::resource('subcategories', SubCategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('units', UnitController::class);
    
    // Sales
    Route::get('/sales/search-product', [SaleController::class, 'searchProduct'])->name('sales.search-product');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::get('/sales/{saleId}/resume', [SaleController::class, 'resume'])->name('sales.resume');
    
    // Customers
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/add-credit', [CustomerController::class, 'addCredit'])->name('customers.add-credit');
    Route::post('/customers/{customer}/pay-credit', [CustomerController::class, 'payCredit'])->name('customers.pay-credit');
    Route::post('/customers/{customer}/redeem-points', [CustomerController::class, 'redeemPoints'])->name('customers.redeem-points');
    
    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{product}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['show']);

    // Purchases
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Sales Returns, Exchanges & Refunds
    Route::resource('sales-returns', SalesReturnController::class);
    Route::post('/sales-returns/{salesReturn}/approve', [SalesReturnController::class, 'approve'])->name('sales-returns.approve');
    Route::post('/sales-returns/{salesReturn}/reject', [SalesReturnController::class, 'reject'])->name('sales-returns.reject');
    Route::post('/sales-returns/{salesReturn}/complete', [SalesReturnController::class, 'complete'])->name('sales-returns.complete');
    Route::get('/sales-returns/{saleId}/details', [SalesReturnController::class, 'getSaleDetails'])->name('sales-returns.get-details');

    Route::resource('exchanges', ExchangeController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('/sales-returns/{salesReturn}/exchanges/create', [ExchangeController::class, 'create'])->name('exchanges.create');

    Route::resource('refunds', RefundController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('/refunds/{refund}/process', [RefundController::class, 'process'])->name('refunds.process');
    Route::post('/refunds/{refund}/mark-failed', [RefundController::class, 'markFailed'])->name('refunds.mark-failed');
    Route::get('/refunds-report', [RefundController::class, 'report'])->name('refunds.report');
    Route::get('/sales-returns/{salesReturn}/refund/create', [RefundController::class, 'create'])->name('refunds.create');

    // Reports & Analytics
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily-sales', [ReportsController::class, 'dailySales'])->name('reports.daily-sales');
    Route::get('/reports/daily-sales/download', [ReportsController::class, 'downloadDailySales'])->name('reports.daily-sales.download');
    Route::get('/reports/product-sales', [ReportsController::class, 'productSales'])->name('reports.product-sales');
    Route::get('/reports/product-sales/download', [ReportsController::class, 'downloadProductSales'])->name('reports.product-sales.download');
    Route::get('/reports/profit-loss', [ReportsController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('/reports/profit-loss/download', [ReportsController::class, 'downloadProfitLoss'])->name('reports.profit-loss.download');
    Route::get('/reports/stock', [ReportsController::class, 'stockReport'])->name('reports.stock');
    Route::get('/reports/stock/download', [ReportsController::class, 'downloadStock'])->name('reports.stock.download');
    Route::get('/reports/cash-summary', [ReportsController::class, 'cashSummary'])->name('reports.cash-summary');
    Route::get('/reports/cash-summary/download', [ReportsController::class, 'downloadCashSummary'])->name('reports.cash-summary.download');

    // Cash Register Management
    Route::post('/reports/open-register', [ReportsController::class, 'openRegister'])->name('reports.open-register');
    Route::post('/reports/close-register', [ReportsController::class, 'closeRegister'])->name('reports.close-register');
    Route::post('/reports/add-expense', [ReportsController::class, 'addExpense'])->name('reports.add-expense');
});
