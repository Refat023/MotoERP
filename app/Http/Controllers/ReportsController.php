<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\CashRegisterSession;
use App\Models\Expense;
use App\Models\Refund;
use App\Models\SalesReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Daily sales report.
     */
    public function dailySales(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($date);

        // Sales data
        $sales = Sale::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->with(['customer', 'user', 'items'])
                    ->get();

        // Summary statistics
        $summary = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('final_amount'),
            'total_discount' => $sales->sum('discount') + $sales->sum('item_discount_total'),
            'total_tax' => $sales->sum('tax'),
            'cash_sales' => $sales->where('payment_method', 'cash')->sum('final_amount'),
            'card_sales' => $sales->where('payment_method', 'card')->sum('final_amount'),
            'other_sales' => $sales->whereNotIn('payment_method', ['cash', 'card'])->sum('final_amount'),
            'items_sold' => $sales->sum(function($sale) {
                return $sale->items->sum('quantity');
            }),
        ];

        // Hourly breakdown
        $hourlySales = Sale::selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(final_amount) as amount')
                          ->whereDate('created_at', $date)
                          ->where('status', 'completed')
                          ->groupBy('hour')
                          ->orderBy('hour')
                          ->get();

        // Top products
        $topProducts = SaleItem::selectRaw('products.name, SUM(sale_items.quantity) as quantity, SUM(sale_items.subtotal) as amount')
                              ->join('products', 'sale_items.product_id', '=', 'products.id')
                              ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                              ->whereDate('sales.created_at', $date)
                              ->where('sales.status', 'completed')
                              ->groupBy('products.id', 'products.name')
                              ->orderBy('amount', 'desc')
                              ->limit(10)
                              ->get();

        return view('reports.daily-sales', compact('date', 'sales', 'summary', 'hourlySales', 'topProducts'));
    }

    /**
     * Product-wise sales report.
     */
    public function productSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $categoryId = $request->get('category_id');

        $query = SaleItem::selectRaw('
                products.id,
                products.name,
                products.sku,
                categories.name as category_name,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.subtotal) as total_amount,
                AVG(sale_items.price) as avg_price,
                COUNT(DISTINCT sales.id) as sales_count
            ')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereDate('sales.created_at', '>=', $startDate)
            ->whereDate('sales.created_at', '<=', $endDate)
            ->where('sales.status', 'completed');

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        $products = $query->groupBy('products.id', 'products.name', 'products.sku', 'categories.name')
                          ->orderBy('total_amount', 'desc')
                          ->paginate(25);

        $categories = \App\Models\Category::all();

        return view('reports.product-sales', compact('products', 'startDate', 'endDate', 'categories', 'categoryId'));
    }

    /**
     * Profit & Loss report.
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Revenue (Sales)
        $revenue = Sale::whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate)
                      ->where('status', 'completed')
                      ->sum('final_amount');

        // Cost of Goods Sold (COGS)
        $cogs = PurchaseItem::selectRaw('SUM(purchase_items.quantity * purchase_items.cost_price) as total_cost')
                           ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                           ->whereDate('purchases.created_at', '>=', $startDate)
                           ->whereDate('purchases.created_at', '<=', $endDate)
                           ->where('purchases.status', 'completed')
                           ->value('total_cost') ?? 0;

        // Expenses
        $expenses = Expense::whereDate('expense_date', '>=', $startDate)
                          ->whereDate('expense_date', '<=', $endDate)
                          ->sum('amount');

        // Returns & Refunds
        $returns = SalesReturn::whereDate('created_at', '>=', $startDate)
                             ->whereDate('created_at', '<=', $endDate)
                             ->where('status', 'completed')
                             ->sum('total_returned_amount');

        $refunds = Refund::whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate)
                        ->where('status', 'processed')
                        ->sum('refund_amount');

        // Calculate profit/loss
        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses - $returns - $refunds;

        // Monthly breakdown
        $monthlyData = [];
        $period = Carbon::parse($startDate);
        while ($period->lte(Carbon::parse($endDate))) {
            $monthStart = $period->copy()->startOfMonth();
            $monthEnd = $period->copy()->endOfMonth();

            $monthlyData[] = [
                'month' => $period->format('M Y'),
                'revenue' => Sale::whereBetween('created_at', [$monthStart, $monthEnd])
                               ->where('status', 'completed')->sum('final_amount'),
                'cogs' => PurchaseItem::selectRaw('SUM(purchase_items.quantity * purchase_items.cost_price) as total_cost')
                                     ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                                     ->whereBetween('purchases.created_at', [$monthStart, $monthEnd])
                                     ->where('purchases.status', 'completed')
                                     ->value('total_cost') ?? 0,
                'expenses' => Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount'),
                'returns' => SalesReturn::whereBetween('created_at', [$monthStart, $monthEnd])
                                       ->where('status', 'completed')->sum('total_returned_amount'),
            ];

            $period->addMonth();
        }

        return view('reports.profit-loss', compact(
            'startDate', 'endDate', 'revenue', 'cogs', 'expenses',
            'returns', 'refunds', 'grossProfit', 'netProfit', 'monthlyData'
        ));
    }

    /**
     * Stock report.
     */
    public function stockReport(Request $request)
    {
        $categoryId = $request->get('category_id');
        $lowStock = $request->get('low_stock', false);

        $query = Product::with(['category', 'brand', 'unit'])
                       ->select([
                           'id', 'name', 'sku', 'quantity', 'reorder_level',
                           'price', 'mrp', 'wholesale_price', 'active'
                       ]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($lowStock) {
            $query->whereColumn('quantity', '<=', 'reorder_level');
        }

        $products = $query->orderBy('quantity', 'asc')->paginate(25);

        // Stock summary
        $summary = [
            'total_products' => Product::count(),
            'active_products' => Product::where('active', true)->count(),
            'low_stock_products' => Product::whereColumn('quantity', '<=', 'reorder_level')->count(),
            'out_of_stock' => Product::where('quantity', 0)->count(),
            'total_value' => Product::sum(DB::raw('quantity * price')),
        ];

        $categories = \App\Models\Category::all();

        return view('reports.stock', compact('products', 'summary', 'categories', 'categoryId', 'lowStock'));
    }

    /**
     * Cash summary report.
     */
    public function cashSummary(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($date);

        // Get or create cash register session for the day
        $session = CashRegisterSession::where('user_id', auth()->id())
                                    ->whereDate('opened_at', $date)
                                    ->first();

        if (!$session) {
            // Create a new session if none exists
            $session = CashRegisterSession::create([
                'user_id' => auth()->id(),
                'opening_balance' => 0,
                'opened_at' => $date->startOfDay(),
            ]);
        }

        // Calculate actual sales for the day
        $sales = Sale::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->get();

        $actualCashSales = $sales->where('payment_method', 'cash')->sum('final_amount');
        $actualCardSales = $sales->where('payment_method', 'card')->sum('final_amount');
        $actualOtherSales = $sales->whereNotIn('payment_method', ['cash', 'card'])->sum('final_amount');

        // Calculate refunds and returns
        $refunds = Refund::whereDate('created_at', $date)
                        ->where('status', 'processed')
                        ->where('method', 'cash')
                        ->sum('refund_amount');

        $returns = SalesReturn::whereDate('created_at', $date)
                             ->where('status', 'completed')
                             ->where('return_type', '!=', 'exchange')
                             ->sum('total_returned_amount');

        // Expenses
        $expenses = Expense::whereDate('expense_date', $date)
                          ->where('payment_method', 'cash')
                          ->sum('amount');

        // Update session with calculated values
        $session->update([
            'cash_sales' => $actualCashSales,
            'card_sales' => $actualCardSales,
            'other_sales' => $actualOtherSales,
            'refunds' => $refunds,
            'returns' => $returns,
            'expenses' => $expenses,
        ]);

        $expectedBalance = $session->calculateExpectedBalance();

        // Get expenses list
        $expensesList = Expense::whereDate('expense_date', $date)
                              ->with('user')
                              ->orderBy('expense_date', 'desc')
                              ->get();

        return view('reports.cash-summary', compact(
            'session', 'date', 'sales', 'expectedBalance',
            'expensesList', 'actualCashSales', 'actualCardSales', 'actualOtherSales'
        ));
    }

    /**
     * Open cash register.
     */
    public function openRegister(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
        ]);

        // Check if there's already an open session
        $existingSession = CashRegisterSession::where('user_id', auth()->id())
                                            ->where('status', 'open')
                                            ->first();

        if ($existingSession) {
            return back()->with('error', 'You already have an open cash register session.');
        }

        CashRegisterSession::create([
            'user_id' => auth()->id(),
            'opening_balance' => $request->opening_balance,
            'opened_at' => now(),
        ]);

        return redirect()->route('reports.cash-summary')->with('success', 'Cash register opened successfully.');
    }

    /**
     * Close cash register.
     */
    public function closeRegister(Request $request)
    {
        $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = CashRegisterSession::where('user_id', auth()->id())
                                    ->where('status', 'open')
                                    ->first();

        if (!$session) {
            return back()->with('error', 'No open cash register session found.');
        }

        $session->close($request->closing_balance, $request->notes);

        return redirect()->route('reports.cash-summary')->with('success', 'Cash register closed successfully.');
    }

    /**
     * Add expense.
     */
    public function addExpense(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:utilities,supplies,maintenance,marketing,other',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $session = CashRegisterSession::where('user_id', auth()->id())
                                    ->where('status', 'open')
                                    ->first();

        Expense::create([
            'user_id' => auth()->id(),
            'cash_register_session_id' => $session?->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'category' => $request->category,
            'payment_method' => $request->payment_method,
            'expense_date' => $request->expense_date,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Expense added successfully.');
    }

    /**
     * Download daily sales report as PDF.
     */
    public function downloadDailySales(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($date);

        // Get all the same data as the view method
        $sales = Sale::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->with(['customer', 'user', 'items'])
                    ->get();

        $summary = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('final_amount'),
            'total_discount' => $sales->sum('discount') + $sales->sum('item_discount_total'),
            'total_tax' => $sales->sum('tax'),
            'cash_sales' => $sales->where('payment_method', 'cash')->sum('final_amount'),
            'card_sales' => $sales->where('payment_method', 'card')->sum('final_amount'),
            'other_sales' => $sales->whereNotIn('payment_method', ['cash', 'card'])->sum('final_amount'),
            'items_sold' => $sales->sum(function($sale) { return $sale->items->sum('quantity'); }),
        ];

        $hourlySales = Sale::selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(final_amount) as amount')
                          ->whereDate('created_at', $date)
                          ->where('status', 'completed')
                          ->groupByRaw('HOUR(created_at)')
                          ->orderBy('hour')
                          ->get();

        $topProducts = SaleItem::selectRaw('products.name, SUM(sale_items.quantity) as quantity, SUM(sale_items.subtotal) as amount')
                              ->join('products', 'sale_items.product_id', '=', 'products.id')
                              ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                              ->whereDate('sales.created_at', $date)
                              ->where('sales.status', 'completed')
                              ->groupBy('sale_items.product_id', 'products.name')
                              ->orderBy('quantity', 'desc')
                              ->limit(10)
                              ->get();

        $pdf = \PDF::loadView('reports.pdf.daily-sales', compact('date', 'summary', 'hourlySales', 'topProducts', 'sales'));
        return $pdf->download('daily-sales-report-' . $date->format('Y-m-d') . '.pdf');
    }

    /**
     * Download product sales report as PDF.
     */
    public function downloadProductSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $categoryId = $request->get('category_id');

        $query = SaleItem::selectRaw('
                products.name,
                products.sku,
                categories.name as category_name,
                COUNT(DISTINCT sales.id) as sales_count,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.subtotal) as total_amount,
                AVG(sale_items.price) as avg_price
            ')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->groupBy('sale_items.product_id', 'products.name', 'products.sku', 'categories.name')
            ->orderBy('total_quantity', 'desc');

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        $products = $query->paginate(50);
        $categories = \App\Models\Category::all();

        $pdf = \PDF::loadView('reports.pdf.product-sales', compact('products', 'startDate', 'endDate', 'categoryId', 'categories'));
        return $pdf->download('product-sales-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    /**
     * Download profit & loss report as PDF.
     */
    public function downloadProfitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Revenue
        $revenue = Sale::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->sum('final_amount');

        // Cost of Goods Sold
        $cogs = PurchaseItem::join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                           ->whereBetween('purchases.created_at', [$startDate, $endDate])
                           ->where('purchases.status', 'completed')
                           ->sum(DB::raw('purchase_items.quantity * purchase_items.cost_price'));

        // Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                          ->sum('amount');

        // Returns & Refunds
        $returns = SalesReturn::whereBetween('created_at', [$startDate, $endDate])
                             ->where('status', 'approved')
                             ->sum('refund_amount');

        $refunds = Refund::whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 'processed')
                        ->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses - $returns - $refunds;

        // Monthly data for trend
        $monthlyData = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($start->lte($end)) {
            $monthStart = $start->copy()->startOfMonth();
            $monthEnd = $start->copy()->endOfMonth();

            $monthRevenue = Sale::whereBetween('created_at', [$monthStart, $monthEnd])
                               ->where('status', 'completed')
                               ->sum('final_amount');

            $monthCogs = PurchaseItem::join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                                    ->whereBetween('purchases.created_at', [$monthStart, $monthEnd])
                                    ->where('purchases.status', 'completed')
                                    ->sum(DB::raw('purchase_items.quantity * purchase_items.cost_price'));

            $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                                   ->sum('amount');

            $monthReturns = SalesReturn::whereBetween('created_at', [$monthStart, $monthEnd])
                                      ->where('status', 'approved')
                                      ->sum('refund_amount');

            $monthlyData[] = [
                'month' => $start->format('M Y'),
                'revenue' => $monthRevenue,
                'cogs' => $monthCogs,
                'expenses' => $monthExpenses,
                'returns' => $monthReturns,
            ];

            $start->addMonth();
        }

        $pdf = \PDF::loadView('reports.pdf.profit-loss', compact(
            'startDate', 'endDate', 'revenue', 'cogs', 'grossProfit', 'expenses', 'returns', 'refunds', 'netProfit', 'monthlyData'
        ));
        return $pdf->download('profit-loss-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }

    /**
     * Download stock report as PDF.
     */
    public function downloadStock(Request $request)
    {
        $categoryId = $request->get('category_id');
        $lowStock = $request->boolean('low_stock');

        $query = Product::with(['category', 'brand', 'unit'])
                       ->where('active', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($lowStock) {
            $query->whereColumn('quantity', '<=', 'reorder_level');
        }

        $products = $query->orderBy('name')->get();
        $categories = \App\Models\Category::all();

        $summary = [
            'total_products' => Product::where('active', true)->count(),
            'active_products' => Product::where('active', true)->count(),
            'low_stock_products' => Product::where('active', true)->whereColumn('quantity', '<=', 'reorder_level')->count(),
            'out_of_stock' => Product::where('active', true)->where('quantity', 0)->count(),
            'total_value' => Product::where('active', true)->sum(DB::raw('quantity * price')),
        ];

        $pdf = \PDF::loadView('reports.pdf.stock', compact('products', 'categories', 'categoryId', 'lowStock', 'summary'));
        return $pdf->download('stock-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Download cash summary as PDF.
     */
    public function downloadCashSummary(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($date);

        $session = CashRegisterSession::whereDate('opened_at', $date)
                                    ->where('user_id', auth()->id())
                                    ->first();

        if (!$session) {
            // Create a default session if none exists
            $session = (object) [
                'id' => null,
                'status' => 'closed',
                'opening_balance' => 0,
                'closing_balance' => 0,
                'opened_at' => $date,
                'closed_at' => null,
                'user' => auth()->user(),
                'cash_sales' => 0,
                'card_sales' => 0,
                'other_sales' => 0,
                'refunds' => 0,
                'returns' => 0,
                'expenses' => 0,
            ];
        }

        $expectedBalance = $session->opening_balance + $session->cash_sales - $session->refunds - $session->returns - $session->expenses;

        $sales = Sale::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->get();

        $actualCashSales = $sales->where('payment_method', 'cash')->sum('final_amount');
        $actualCardSales = $sales->where('payment_method', 'card')->sum('final_amount');

        $expensesList = Expense::whereDate('expense_date', $date)
                              ->where('user_id', auth()->id())
                              ->with('user')
                              ->get();

        $pdf = \PDF::loadView('reports.pdf.cash-summary', compact('date', 'session', 'expectedBalance', 'sales', 'actualCashSales', 'actualCardSales', 'expensesList'));
        return $pdf->download('cash-summary-' . $date->format('Y-m-d') . '.pdf');
    }
}
