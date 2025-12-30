<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\PurchaseOrderItem;
use App\Models\Store;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class Dashboard extends Controller
{
    public function admin()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $weekAgo = Carbon::today()->subDays(7);

        // 1. Today Sales
        $todaySales = SalesInvoice::whereDate('invoice_date', $today)
            ->where('status', 'completed')
            ->sum('grand_total');

        // 2. Yesterday Sales & % change
        $yesterdaySales = SalesInvoice::whereDate('invoice_date', $yesterday)
            ->where('status', 'completed')
            ->sum('grand_total');
        $salesDifference = $yesterdaySales > 0
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 2)
            : 0;

        // 3. Today Purchase
        $todayPurchase = PurchaseOrder::whereDate('bill_date', $today)->sum('total');

        // 4. Last week purchase (excluding today) & % change
        $pastWeekPurchase = PurchaseOrder::whereBetween('bill_date', [$weekAgo, $today->copy()->subDay()])->sum('total');
        $purchaseDifference = $pastWeekPurchase > 0
            ? round((($todayPurchase - $pastWeekPurchase) / $pastWeekPurchase) * 100, 2)
            : 0;

        // 5. Today Bills count & % change from yesterday
        $todayBills = SalesInvoice::whereDate('invoice_date', $today)->count();
        $yesterdayBills = SalesInvoice::whereDate('invoice_date', $yesterday)->count();
        $billDifference = $yesterdayBills > 0
            ? round((($todayBills - $yesterdayBills) / $yesterdayBills) * 100, 2)
            : 0;

        // 6. To Pay
        $toPayAmount = PurchaseOrder::where('balance_amount', '>', 0)->sum('balance_amount');

        // 7. Vendors to pay count
        $vendorsToPay = PurchaseOrder::where('balance_amount', '>', 0)
            ->distinct('vendor_id')
            ->count('vendor_id');

        // 8. Customers with Loyalty Points
        $customersWithLoyalty = Customer::where('loyalty_points', '>', 0)->count();

        // 9. Today's New Loyalty Customers
        $todayNewCustomersWithLoyalty = Customer::whereDate('created_at', $today)
            ->where('loyalty_points', '>', 0)
            ->count();

        // 10. Returning Customers
        $returningCustomerIds = SalesInvoice::select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('customer_id');

        $returningCustomersWithLoyalty = Customer::whereIn('id', $returningCustomerIds)
            ->where('loyalty_points', '>', 0)
            ->count();

        // 11. Circle %
        $loyaltyTargetPercent = $customersWithLoyalty > 0
            ? round(($todayNewCustomersWithLoyalty / $customersWithLoyalty) * 100)
            : 0;

        $returningCustomersPercent = $customersWithLoyalty > 0
            ? round(($returningCustomersWithLoyalty / $customersWithLoyalty) * 100)
            : 0;

        return view('dashboard.admin', compact(
            'todaySales',
            'salesDifference',
            'todayPurchase',
            'purchaseDifference',
            'todayBills',
            'billDifference',
            'toPayAmount',
            'vendorsToPay',
            'customersWithLoyalty',
            'todayNewCustomersWithLoyalty',
            'returningCustomersWithLoyalty',
            'loyaltyTargetPercent',
            'returningCustomersPercent'
        ));
    }

    public function getTopSellingProducts($month)
    {
        $topSelling = SalesInvoiceItem::select(
                'items.item_name as product',
                DB::raw('SUM(sales_invoice_items.qty) as total_qty')
            )
            ->join('items', 'sales_invoice_items.item_id', '=', 'items.id')
            ->join('sales_invoices', 'sales_invoice_items.salesinvoice_id', '=', 'sales_invoices.id')
            ->whereMonth('sales_invoices.invoice_date', $month)
            ->whereYear('sales_invoices.invoice_date', date('Y'))
            ->where('sales_invoices.status', 'completed')
            ->groupBy('items.item_name')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        $productNames = $topSelling->pluck('product');
        $quantities = $topSelling->pluck('total_qty');

        return response()->json([
            'products' => $productNames,
            'quantities' => $quantities
        ]);
    }
public function getLeastSellingProducts($month)
{
    $leastSelling = SalesInvoiceItem::select(
            'items.item_name as product',
            DB::raw('SUM(sales_invoice_items.qty) as total_qty')
        )
        ->join('items', 'sales_invoice_items.item_id', '=', 'items.id')
        ->join('sales_invoices', 'sales_invoice_items.salesinvoice_id', '=', 'sales_invoices.id')
        ->whereMonth('sales_invoices.invoice_date', $month)
        ->whereYear('sales_invoices.invoice_date', date('Y'))
        ->where('sales_invoices.status', 'completed')
        ->groupBy('items.item_name')
        ->orderBy('total_qty', 'asc') // ASC for least
        ->limit(6)
        ->get();

    return response()->json([
        'products' => $leastSelling->pluck('product'),
        'quantities' => $leastSelling->pluck('total_qty')
    ]);
}
public function getGrossAvenueData()
{
    $currentYear = now()->year;
    $lastYear = now()->subYear()->year;

    // Get monthly sales totals for current year
    $thisYearData = SalesInvoice::selectRaw('MONTH(invoice_date) as month, SUM(grand_total) as total')
        ->whereYear('invoice_date', $currentYear)
        ->where('status', 'completed')
        ->groupBy('month')
        ->pluck('total', 'month');

    // Get monthly sales totals for last year
    $lastYearData = SalesInvoice::selectRaw('MONTH(invoice_date) as month, SUM(grand_total) as total')
        ->whereYear('invoice_date', $lastYear)
        ->where('status', 'completed')
        ->groupBy('month')
        ->pluck('total', 'month');

    // Create complete monthly data (fill 0 if no value)
    $months = range(1, 12);
    $thisYearSales = [];
    $lastYearSales = [];

    foreach ($months as $month) {
        $thisYearSales[] = round($thisYearData[$month] ?? 0, 2);
        $lastYearSales[] = round($lastYearData[$month] ?? 0, 2);
    }

    return response()->json([
        'this_year' => $thisYearSales,
        'last_year' => $lastYearSales,
    ]);
}
public function getLoyaltyDiscountData()
{
    $year = now()->year;

    $pointsRedeemed = SalesInvoice::selectRaw('MONTH(invoice_date) as month, SUM(loyalty_points_used) as total')
        ->whereYear('invoice_date', $year)
        ->where('status', 'completed')
        ->groupBy('month')
        ->pluck('total', 'month');

    $pointsNotRedeemed = SalesInvoice::selectRaw('MONTH(invoice_date) as month, SUM(loyalty_points_earned - loyalty_points_used) as total')
        ->whereYear('invoice_date', $year)
        ->where('status', 'completed')
        ->groupBy('month')
        ->pluck('total', 'month');

    // Ensure data for all 12 months
    $months = range(1, 12);
    $redeemed = [];
    $notRedeemed = [];

    foreach ($months as $month) {
        $redeemed[] = round($pointsRedeemed[$month] ?? 0);
        $notRedeemed[] = round($pointsNotRedeemed[$month] ?? 0);
    }

    return response()->json([
        'redeemed' => $redeemed,
        'not_redeemed' => $notRedeemed,
    ]);
}

        public function manager()
    {
        return view('dashboard.manager');
    }
public function pos()
{
    $today = today();

    // Get all sales for today, eager load store relation
    $todaySales = SalesInvoice::with('store')
        ->whereDate('invoice_date', $today)
        ->get();

    // Group by store_id
    $salesByStore = $todaySales->groupBy('store_id');

    // Get all stores with POS count and employees
    $stores = Store::with(['posSystems', 'employees'])->get();

    $storeData = $stores->map(function ($store) use ($salesByStore) {
        $storeSales = $salesByStore->get($store->id, collect());

        $cashTotal = $storeSales->filter(function ($sale) {
            return strtolower($sale->mode_of_payment) === 'cash';
        })->sum('grand_total');

        $onlineTotal = $storeSales->filter(function ($sale) {
            return in_array(strtolower($sale->mode_of_payment), ['online', 'card', 'upi', 'digital']);
        })->sum('grand_total');

        $redeemedTotal = $storeSales->sum('loyalty_points_used');
        $grandTotal = $storeSales->sum('grand_total');

        return [
            'store' => $store,
            'pos_count' => $store->posSystems->count(),
            'cash_total' => $cashTotal,
            'online_total' => $onlineTotal,
            'redeemed_total' => $redeemedTotal,
            'grand_total' => $grandTotal
        ];
    });

    // Get all employees with store
    $employees = Employee::with('store')->get();

    // Group today's sales by employee
    $salesByEmployee = $todaySales->groupBy('employee_id');

    $topPerformers = $employees->map(function ($employee) use ($salesByEmployee) {
        $empSales = $salesByEmployee->get($employee->id, collect());

        return [
            'employee' => $employee,
            'total_bills' => $empSales->count(),
            'total_sales' => $empSales->sum('grand_total'),
            'store_name' => $employee->store ? $employee->store->store_name : 'N/A'
        ];
    })
    ->filter(fn($performer) => $performer['total_sales'] > 0)
    ->sortByDesc('total_sales')
    ->values()
    ->take(10);

    return view('dashboard.pos', compact('storeData', 'topPerformers'));
}

       public function abc()
    {
        // Get all unique stores from purchase orders
        $stores = PurchaseOrder::select('warehouse')
            ->whereNotNull('warehouse')
            ->distinct()
            ->pluck('warehouse');

        return view('dashboard.abc', compact('stores'));
    }

    public function getAbcData(Request $request)
    {
        $storeName = $request->input('store');
        
        if (!$storeName) {
            return response()->json(['error' => 'Store name is required'], 400);
        }

        // Get ABC category data for the selected store
        $abcData = $this->calculateAbcData($storeName);
        
        // Get chart data
        $chartData = $this->getChartData($storeName);

        return response()->json([
            'abc_data' => $abcData,
            'chart_data' => $chartData,
            'overall_inventory' => $abcData['total_value']
        ]);
    }

    private function calculateAbcData($storeName)
    {
        // Get items from purchase orders for the specific store
        $itemsData = DB::table('purchase_orders')
            ->join('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('items', 'purchase_order_items.item_id', '=', 'items.id')
            ->where('purchase_orders.warehouse', $storeName)
            ->select(
                'items.abc_category',
                'purchase_order_items.qty',
                'purchase_order_items.amount',
                'items.id'
            )
            ->get();

        // Group by ABC category and calculate totals
        $abcSummary = [
            'A' => ['items' => 0, 'value' => 0],
            'B' => ['items' => 0, 'value' => 0],
            'C' => ['items' => 0, 'value' => 0]
        ];

        $totalValue = 0;
        $processedItems = [];

        foreach ($itemsData as $item) {
            $category = $item->abc_category ?? 'C'; // Default to C if null
            
            // Count unique items only
            if (!in_array($item->id, $processedItems)) {
                $abcSummary[$category]['items']++;
                $processedItems[] = $item->id;
            }
            
            $abcSummary[$category]['value'] += $item->amount;
            $totalValue += $item->amount;
        }

        // Calculate percentages
        foreach ($abcSummary as $category => &$data) {
            $data['percentage'] = $totalValue > 0 ? round(($data['value'] / $totalValue) * 100, 1) : 0;
            $data['value'] = round($data['value'], 2);
        }

        return [
            'categories' => $abcSummary,
            'total_value' => round($totalValue, 2)
        ];
    }

    private function getChartData($storeName)
    {
        // Get data for donut chart (items by ABC category)
        $donutData = DB::table('purchase_orders')
            ->join('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('items', 'purchase_order_items.item_id', '=', 'items.id')
            ->where('purchase_orders.warehouse', $storeName)
            ->select('items.abc_category', DB::raw('COUNT(DISTINCT items.id) as item_count'))
            ->groupBy('items.abc_category')
            ->get();

        $donutSeries = [];
        $donutLabels = [];
        foreach ($donutData as $data) {
            $category = $data->abc_category ?? 'C';
            $donutSeries[] = (int)$data->item_count;
            $donutLabels[] = "Category {$category}";
        }

        // Get data for area chart (monthly stock movement)
        $areaData = $this->getMonthlyStockMovement($storeName);

        return [
            'donut' => [
                'series' => $donutSeries,
                'labels' => $donutLabels
            ],
            'area' => $areaData
        ];
    }

    private function getMonthlyStockMovement($storeName)
    {
        // Get monthly data for the last 12 months
        $monthlyData = DB::table('purchase_orders')
            ->join('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('items', 'purchase_order_items.item_id', '=', 'items.id')
            ->where('purchase_orders.warehouse', $storeName)
            ->select(
                DB::raw('MONTH(purchase_orders.bill_date) as month'),
                DB::raw('YEAR(purchase_orders.bill_date) as year'),
                'items.abc_category',
                DB::raw('SUM(purchase_order_items.qty) as total_qty')
            )
            ->where('purchase_orders.bill_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month', 'items.abc_category')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Initialize data structure for 12 months
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $seriesData = [
            'A' => array_fill(0, 12, 0),
            'B' => array_fill(0, 12, 0),
            'C' => array_fill(0, 12, 0)
        ];

        foreach ($monthlyData as $data) {
            $monthIndex = $data->month - 1; // Convert to 0-based index
            $category = $data->abc_category ?? 'C';
            $seriesData[$category][$monthIndex] = (int)$data->total_qty;
        }

        return [
            'categories' => $months,
            'series' => [
                ['name' => 'Category A', 'data' => $seriesData['A']],
                ['name' => 'Category B', 'data' => $seriesData['B']],
                ['name' => 'Category C', 'data' => $seriesData['C']]
            ]
        ];
    }


    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }


public function bill()
{
    if (!Session::has('loginId') || Session::get('role') !== 'employee') {
        return redirect()->route('login')->with('error', 'Unauthorized access. Only employees can access this page.');
    }

    $employee_id = Session::get('loginId');
    $empname = Session::get('empname');
    $empcode = Session::get('empcode');

    $today = Carbon::today();

    // Today's sales for this employee
    $sales = SalesInvoice::where('employee_id', $employee_id)
        ->whereDate('invoice_date', $today)
        ->get();

    // Dashboard metrics
    $todayBillCount = $sales->count();
    $todaySalesTotal = $sales->sum('grand_total');
    $todayLoyaltyRedeemed = $sales->sum('loyalty_points_used');

    // Recent 10 bills
    $recentBills = SalesInvoice::with('customer', 'items')
        ->where('employee_id', $employee_id)
        ->whereDate('invoice_date', $today)
        ->latest()
        ->take(10)
        ->get();

    return view('dashboard.bill', compact(
        'empname',
        'empcode',
        'todayBillCount',
        'todaySalesTotal',
        'todayLoyaltyRedeemed',
        'recentBills'
    ));
}
public function invoiceDetails($id)
{
    $invoice = SalesInvoice::with(['items.item', 'customer'])->findOrFail($id);

    return view('dashboard.invoice_details', compact('invoice'));
}
}
