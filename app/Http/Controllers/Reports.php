<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\Employee;
use App\Models\Store;
use App\Models\SalesInvoiceItem;
use App\Models\SalesInvoice;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\GstDetail;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;



class Reports extends Controller
{
    public function reports_list()
    {
        return view('reports.list');
    }

public function profit_loss(Request $request)
{
    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get profit & loss data
    $profitLossData = $this->getProfitLossData($selectedStore, $startDate, $endDate);
    
    return view('reports.profit_loss', compact(
        'profitLossData', 
        'stores',
        'selectedStore', 
        'dateRange',
        'startDate',
        'endDate'
    ));
}

private function getProfitLossData($selectedStore, $startDate, $endDate)
{
    $role = session('role');
    $storeId = session('store_id');

    // Apply store filter logic
    $storeFilter = null;
    if ($role === 'manager') {
        $storeFilter = $storeId;
    } elseif ($selectedStore !== 'all') {
        $storeFilter = $selectedStore;
    }

    // Get Sales Data
    $salesQuery = SalesInvoice::whereBetween('invoice_date', [$startDate, $endDate])
        ->where('status', 'completed');
    
    if ($storeFilter) {
        $salesQuery->where('store_id', $storeFilter);
    }
    
    $salesData = $salesQuery->get();
    $totalSales = $salesData->sum('grand_total');
    $totalSalesTax = $salesData->sum('total_tax');
    $totalSalesDiscount = $salesData->sum('total_discount');

    // Get Purchase Data
    $purchaseQuery = DB::table('purchase_orders')
        ->whereBetween('bill_date', [$startDate, $endDate]);
    
    if ($storeFilter) {
        $purchaseQuery->where('warehouse', $storeFilter);
    }
    
    $purchaseData = $purchaseQuery->get();
    $totalPurchases = $purchaseData->sum('total');
    $totalPurchaseTax = $purchaseData->sum('total'); // Assuming tax is included in total

    // Get Cost of Goods Sold (COGS)
    $cogsQuery = DB::table('sales_invoice_items as sii')
        ->join('sales_invoices as si', 'sii.salesinvoice_id', '=', 'si.id')
        ->join('items as i', 'sii.item_id', '=', 'i.id')
        ->whereBetween('si.invoice_date', [$startDate, $endDate])
        ->where('si.status', 'completed');
    
    if ($storeFilter) {
        $cogsQuery->where('si.store_id', $storeFilter);
    }
    
    $cogsSold = $cogsQuery->sum(DB::raw('sii.qty * COALESCE(i.purchase_price, 0)'));

    // Calculate Opening and Closing Stock Values
    $openingStockQuery = Item::query();
    if ($storeFilter) {
        $openingStockQuery->where('store_id', $storeFilter);
    }
    $openingStock = $openingStockQuery->sum(DB::raw('opening_stock * COALESCE(purchase_price, 0)'));

    // For closing stock, calculate current stock and multiply by purchase price
    $items = $openingStockQuery->get();
    $closingStock = 0;
    
    foreach ($items as $item) {
        // Calculate current stock
        $purchasedQty = PurchaseOrderItem::where('item_id', $item->id)
            ->when($storeFilter, function ($q) use ($storeFilter) {
                $q->whereHas('purchaseOrder', function ($pq) use ($storeFilter) {
                    $pq->where('warehouse', $storeFilter);
                });
            })
            ->sum('qty');
        
        $soldQty = SalesInvoiceItem::where('item_id', $item->id)
            ->when($storeFilter, function ($q) use ($storeFilter) {
                $q->whereHas('salesInvoice', function ($sq) use ($storeFilter) {
                    $sq->where('store_id', $storeFilter);
                });
            })
            ->sum('qty');
        
        $currentStock = $item->opening_stock + $purchasedQty - $soldQty;
        $closingStock += $currentStock * ($item->purchase_price ?? 0);
    }

    // Calculate other income/expenses (you can customize these based on your business logic)
    $otherIncome = 0; // Add logic for other income sources
    $indirectExpenses = 0; // Add logic for indirect expenses

    // Calculate Gross Profit
    $grossProfit = $totalSales - $cogsSold;
    
    // Calculate Net Profit
    $netProfit = $grossProfit + $otherIncome - $indirectExpenses;

    return [
        'sales' => $totalSales,
        'credit_note' => 0, // Add logic if you have credit notes
        'purchases' => $totalPurchases,
        'debit_note' => 0, // Add logic if you have debit notes
        'tax_payable' => $totalSalesTax - $totalPurchaseTax,
        'tax_receivable' => 0, // Calculate based on your tax logic
        'opening_stock' => $openingStock,
        'closing_stock' => $closingStock,
        'gross_profit' => $grossProfit,
        'other_income' => $otherIncome,
        'indirect_expenses' => $indirectExpenses,
        'net_profit' => $netProfit,
        'cogs' => $cogsSold
    ];
}

    // Add this new method for Low Stock Summary
    public function low_stock_summary(Request $request)
    {
        // Handle download request
        if ($request->has('download')) {
            return $this->download_low_stock_summary($request);
        }

        // Get all stores for filter dropdown
        $stores = Store::all();
        
        // Get filter values
        $selectedStore = $request->get('store_id', session('store_id'));
        $dateRange = $request->get('date_range', 'last_7_days');
        
        // Get low stock items
        $items = $this->getLowStockItemsData($request);
        
        // Calculate totals
        $totalItems = $items->count();
        $totalStockValue = $items->sum('stock_value');

        return view('reports.low_stock', compact(
            'items', 
            'stores', 
            'selectedStore', 
            'dateRange',
            'totalItems',
            'totalStockValue'
        ));
    }

    public function getLowStockItems(Request $request)
    {
        $items = $this->getLowStockItemsData($request);
        return response()->json($items->values());
    }

    private function getLowStockItemsData(Request $request)
    {
        $role = session('role');
        $storeId = session('store_id');

        // Fetch all items first
        $items = Item::with(['brand', 'category', 'subcategory'])
            ->select('items.*')
            ->get()
            ->filter(function ($item) use ($role, $storeId) {
                // Sum purchasedQty from store-specific purchase orders if manager
                $purchasedQuery = PurchaseOrderItem::where('item_id', $item->id);
                if ($role === 'manager') {
                    $purchasedQuery->whereHas('purchaseOrder', function ($q) use ($storeId) {
                        $q->where('warehouse', $storeId);
                    });
                }

                $purchasedQty = $purchasedQuery->sum('qty');
                $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');
                $currentStock = $item->opening_stock + $purchasedQty - $soldQty;

                return $currentStock < $item->min_stock;
            });

        // Filter by category/subcategory
        if ($request->category_id) {
            $items = $items->where('category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $items = $items->where('subcategory_id', $request->subcategory_id);
        }

        // Return formatted result
        $result = $items->values()->map(function ($item, $index) use ($role, $storeId) {
            // Recalculate stock for consistent data
            $purchasedQuery = PurchaseOrderItem::where('item_id', $item->id);
            if ($role === 'manager') {
                $purchasedQuery->whereHas('purchaseOrder', function ($q) use ($storeId) {
                    $q->where('warehouse', $storeId);
                });
            }
            
            $purchasedQty = $purchasedQuery->sum('qty');
            $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');
            $currentStock = $item->opening_stock + $purchasedQty - $soldQty;
            
            // Calculate stock value (assuming you have a price field)
            $stockValue = $currentStock * ($item->selling_price ?? $item->purchase_price ?? 0);

            return [
                'index' => $index + 1,
                'brand' => optional($item->brand)->name ?? '-',
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'category' => optional($item->category)->name ?? '-',
                'subcategory' => optional($item->subcategory)->name ?? '-',
                'current_stock' => $currentStock,
                'min_stock' => $item->min_stock,
                'stock_value' => $stockValue,
                'status' => $currentStock <= 0 ? 'Out of Stock' : 'Low Stock',
            ];
        });

        return $result;
    }


    public function item_sales_purchase_summary(Request $request)
    {
        // Handle download request
        if ($request->has('download')) {
            return $this->download_item_summary($request);
        }

        // Get all stores for filter dropdown
        $stores = Store::all();
        
        // Get filter values
        $selectedStore = $request->get('store_id', 'all');
        $dateRange = $request->get('date_range', 'last_7_days');
        
        // Calculate date range
        $dateRanges = $this->getDateRange($dateRange);
        $startDate = $dateRanges['start'];
        $endDate = $dateRanges['end'];
        
        // Get items with their sales and purchase quantities
        $items = $this->getItemSummaryData($selectedStore, $startDate, $endDate);
        
        // Calculate totals
        $totalSalesQty = $items->sum('sales_quantity');
        $totalPurchaseQty = $items->sum('purchase_quantity');

        return view('reports.item', compact(
            'items', 
            'stores', 
            'selectedStore', 
            'dateRange',
            'totalSalesQty',
            'totalPurchaseQty',
            'startDate',
            'endDate'
        ));
    }

    private function getItemSummaryData($selectedStore, $startDate, $endDate)
    {
        // Get sales data
        $salesData = DB::table('sales_invoice_items as sii')
            ->join('sales_invoices as si', 'sii.salesinvoice_id', '=', 'si.id')
            ->join('items as i', 'sii.item_id', '=', 'i.id')
            ->whereBetween('si.invoice_date', [$startDate, $endDate])
            ->when($selectedStore !== 'all', function ($query) use ($selectedStore) {
                return $query->where('si.store_id', $selectedStore);
            })
            ->select('i.id', 'i.item_name', 'i.item_code', DB::raw('SUM(sii.qty) as sales_qty'))
            ->groupBy('i.id', 'i.item_name', 'i.item_code')
            ->get()
            ->keyBy('id');

        // Get purchase data
        $purchaseData = DB::table('purchase_order_items as poi')
            ->join('purchase_orders as po', 'poi.purchase_order_id', '=', 'po.id')
            ->join('items as i', 'poi.item_id', '=', 'i.id')
            ->whereBetween('po.bill_date', [$startDate, $endDate])
            ->select('i.id', 'i.item_name', 'i.item_code', DB::raw('SUM(poi.qty) as purchase_qty'))
            ->groupBy('i.id', 'i.item_name', 'i.item_code')
            ->get()
            ->keyBy('id');

        // Combine sales and purchase data
        $allItemIds = collect(array_merge($salesData->keys()->toArray(), $purchaseData->keys()->toArray()))->unique();
        
        $combinedData = collect();
        
        foreach ($allItemIds as $itemId) {
            $salesItem = $salesData->get($itemId);
            $purchaseItem = $purchaseData->get($itemId);
            
            $item = (object) [
                'id' => $itemId,
                'item_name' => $salesItem ? $salesItem->item_name : $purchaseItem->item_name,
                'item_code' => $salesItem ? $salesItem->item_code : $purchaseItem->item_code,
                'sales_quantity' => $salesItem ? (float) $salesItem->sales_qty : 0,
                'purchase_quantity' => $purchaseItem ? (float) $purchaseItem->purchase_qty : 0,
            ];
            
            // Only include items that have either sales or purchase activity
            if ($item->sales_quantity > 0 || $item->purchase_quantity > 0) {
                $combinedData->push($item);
            }
        }
        
        return $combinedData->sortBy('item_name');
    }

    private function getDateRange($range)
    {
        $endDate = Carbon::now()->endOfDay();
        
        switch ($range) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                break;
            default:
                $startDate = Carbon::now()->subDays(6)->startOfDay();
        }
        
        return [
            'start' => $startDate,
            'end' => $endDate
        ];
    }

    public function download_item_summary(Request $request)
    {
        // Get filter values
        $selectedStore = $request->get('store_id', 'all');
        $dateRange = $request->get('date_range', 'last_7_days');
        
        // Calculate date range
        $dateRanges = $this->getDateRange($dateRange);
        $startDate = $dateRanges['start'];
        $endDate = $dateRanges['end'];
        
        // Get the same data as the main report
        $items = $this->getItemSummaryData($selectedStore, $startDate, $endDate);

        // Create CSV content
        $csvData = [];
        $csvData[] = ['#', 'Item Name', 'Item Code', 'Sales Quantity', 'Purchase Quantity'];
        
        foreach ($items as $index => $item) {
            $csvData[] = [
                $index + 1,
                $item->item_name,
                $item->item_code ?: '',
                number_format($item->sales_quantity, 0),
                number_format($item->purchase_quantity, 0)
            ];
        }

        // Add totals row
        $csvData[] = ['', 'Total', '', $items->sum('sales_quantity'), $items->sum('purchase_quantity')];

        $filename = 'item-sales-purchase-summary-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


// Add this method to your Reports controller
public function stock_summary(Request $request)
{
    $role = session('role');
    $storeId = session('store_id');
    
    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get all categories for filter dropdown
    $categories = Category::all();
    
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $selectedCategory = $request->get('category_id');
    $selectedSubCategory = $request->get('subcategory_id');
    $dateRange = $request->get('date_range', 'last_7_days');
    $stockFilter = $request->get('stock_filter', 'all'); // all, low_stock, out_of_stock, in_stock
    
    // Handle download request
    if ($request->has('download')) {
        return $this->download_stock_summary($request);
    }
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get stock data
    $items = $this->getStockSummaryData($selectedStore, $selectedCategory, $selectedSubCategory, $stockFilter, $role, $storeId);
    
    // Calculate totals
    $totalItems = $items->count();
    $totalStockValue = $items->sum('stock_value');
    $lowStockItems = $items->where('status', 'Low Stock')->count();
    $outOfStockItems = $items->where('status', 'Out of Stock')->count();

    return view('reports.stock_summary', compact(
        'items', 
        'stores', 
        'categories',
        'selectedStore', 
        'selectedCategory',
        'selectedSubCategory',
        'dateRange',
        'stockFilter',
        'totalItems',
        'totalStockValue',
        'lowStockItems',
        'outOfStockItems',
        'startDate',
        'endDate'
    ));
}

private function getStockSummaryData($selectedStore, $selectedCategory, $selectedSubCategory, $stockFilter, $role, $storeId)
{
    // Start with base query
    $query = Item::with(['brand', 'category', 'subcategory']);
    
    // Filter by store for managers
    if ($role === 'manager') {
        $query->where('store_id', $storeId);
    } elseif ($selectedStore && $selectedStore !== 'all') {
        $query->where('store_id', $selectedStore);
    }
    
    // Filter by category
    if ($selectedCategory) {
        $query->where('category_id', $selectedCategory);
    }
    
    // Filter by subcategory
    if ($selectedSubCategory) {
        $query->where('subcategory_id', $selectedSubCategory);
    }
    
    $items = $query->get();
    
    // Calculate stock for each item and apply stock filters
    $result = collect();
    
    foreach ($items as $index => $item) {
        // Calculate current stock
        $purchasedQuery = PurchaseOrderItem::where('item_id', $item->id);
        if ($role === 'manager') {
            $purchasedQuery->whereHas('purchaseOrder', function ($q) use ($storeId) {
                $q->where('warehouse', $storeId);
            });
        }
        
        $purchasedQty = $purchasedQuery->sum('qty');
        $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');
        $currentStock = $item->opening_stock + $purchasedQty - $soldQty;
        
        // Determine status
        $status = 'In Stock';
        if ($currentStock <= 0) {
            $status = 'Out of Stock';
        } elseif ($currentStock < $item->min_stock) {
            $status = 'Low Stock';
        }
        
        // Apply stock filter
        $includeItem = true;
        switch ($stockFilter) {
            case 'low_stock':
                $includeItem = $status === 'Low Stock';
                break;
            case 'out_of_stock':
                $includeItem = $status === 'Out of Stock';
                break;
            case 'in_stock':
                $includeItem = $status === 'In Stock';
                break;
            case 'all':
            default:
                $includeItem = true;
                break;
        }
        
        if ($includeItem) {
            // Calculate stock value
            $stockValue = $currentStock * ($item->selling_price ?? $item->purchase_price ?? 0);
            
            $result->push((object) [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'brand' => optional($item->brand)->name ?? '-',
                'category' => optional($item->category)->name ?? '-',
                'subcategory' => optional($item->subcategory)->name ?? '-',
                'current_stock' => $currentStock,
                'min_stock' => $item->min_stock,
                'purchase_price' => $item->purchase_price ?? 0,
                'selling_price' => $item->selling_price ?? 0,
                'stock_value' => $stockValue,
                'opening_unit' => $item->opening_unit ?? 'PCS',
                'status' => $status,
            ]);
        }
    }
    
    return $result->sortBy('item_name');
}

public function get_subcategories_by_category(Request $request)
{
    $categoryId = $request->get('category_id');
    $subcategories = SubCategory::where('category_id', $categoryId)->get();
    
    return response()->json($subcategories);
}

public function download_stock_summary(Request $request)
{
    $role = session('role');
    $storeId = session('store_id');
    
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $selectedCategory = $request->get('category_id');
    $selectedSubCategory = $request->get('subcategory_id');
    $stockFilter = $request->get('stock_filter', 'all');
    
    // Get the same data as the main report
    $items = $this->getStockSummaryData($selectedStore, $selectedCategory, $selectedSubCategory, $stockFilter, $role, $storeId);

    // Create CSV content
    $csvData = [];
    $csvData[] = ['#', 'Item Name', 'Item Code', 'Brand', 'Category', 'Sub Category', 'Purchase Price', 'Selling Price', 'Stock Quantity', 'Stock Value', 'Status'];
    
    foreach ($items as $index => $item) {
        $csvData[] = [
            $index + 1,
            $item->item_name,
            $item->item_code ?: '',
            $item->brand,
            $item->category,
            $item->subcategory,
            'Rs.' . number_format($item->purchase_price, 2),
            'Rs.' . number_format($item->selling_price, 2),
            $item->current_stock . ' ' . strtoupper($item->opening_unit),
            'Rs.' . number_format($item->stock_value, 2),
            $item->status
        ];
    }

    // Add summary row
    $csvData[] = [''];
    $csvData[] = ['Summary'];
    $csvData[] = ['Total Items', $items->count()];
    $csvData[] = ['Total Stock Value', 'Rs.' . number_format($items->sum('stock_value'), 2)];

    $filename = 'stock-summary-' . date('Y-m-d-H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($csvData) {
        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function item_report_by_party(Request $request)
{
    // Handle download request
    if ($request->has('download')) {
        return $this->download_item_party_report($request);
    }

    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get all vendors for filter dropdown
    $vendors = Vendor::where('status', 'active')->orderBy('vendorname')->get();
    
    // Get filter values
    $selectedStore = $request->get('store_id', 'all');
    $selectedVendor = $request->get('vendor_id', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get items with their purchase details by party
    $items = $this->getItemPartyData($selectedStore, $selectedVendor, $startDate, $endDate);
    
    // Calculate totals
    $totalQuantity = $items->sum('total_quantity');
    $totalAmount = $items->sum('total_amount');
    $totalOrders = $items->sum('total_orders');

    return view('reports.item_party', compact(
        'items', 
        'stores', 
        'vendors',
        'selectedStore', 
        'selectedVendor',
        'dateRange',
        'totalQuantity',
        'totalAmount',
        'totalOrders',
        'startDate',
        'endDate'
    ));
}

public function getItemPartyItems(Request $request)
{
    // Calculate date range
    $dateRanges = $this->getDateRange($request->get('date_range', 'last_7_days'));
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    $items = $this->getItemPartyData(
        $request->get('store_id', 'all'),
        $request->get('vendor_id', 'all'),
        $startDate,
        $endDate
    );
    
    return response()->json($items->values());
}

private function getItemPartyData($selectedStore, $selectedVendor, $startDate, $endDate)
{
    $role = session('role');
    $storeId = session('store_id');

    // Build the query
    $query = DB::table('purchase_order_items as poi')
        ->join('purchase_orders as po', 'poi.purchase_order_id', '=', 'po.id')
        ->join('vendors as v', 'po.vendor_id', '=', 'v.id')
        ->join('items as i', 'poi.item_id', '=', 'i.id')
        ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
        ->whereBetween('po.bill_date', [$startDate, $endDate])
        ->where('v.status', 'active');

    // Apply store filter
    if ($role === 'manager') {
        $query->where('po.warehouse', $storeId);
    } elseif ($selectedStore !== 'all') {
        $query->where('po.warehouse', $selectedStore);
    }

    // Apply vendor filter
    if ($selectedVendor !== 'all') {
        $query->where('po.vendor_id', $selectedVendor);
    }

    $purchaseData = $query
        ->select(
            'i.id as item_id',
            'i.item_name',
            'i.item_code',
            'v.id as vendor_id',
            'v.vendorname',
            's.store_name',
            DB::raw('COUNT(DISTINCT po.id) as total_orders'),
            DB::raw('SUM(poi.qty) as total_quantity'),
            DB::raw('SUM(poi.amount) as total_amount'),
            DB::raw('AVG(poi.price) as avg_price'),
            DB::raw('MIN(po.bill_date) as first_purchase'),
            DB::raw('MAX(po.bill_date) as last_purchase')
        )
        ->groupBy('i.id', 'i.item_name', 'i.item_code', 'v.id', 'v.vendorname', 's.store_name')
        ->orderBy('v.vendorname')
        ->orderBy('i.item_name')
        ->get();

    // Format the data
    $result = collect();
    
    foreach ($purchaseData as $index => $item) {
        $result->push((object) [
            'index' => $index + 1,
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'item_code' => $item->item_code ?: '-',
            'vendor_id' => $item->vendor_id,
            'vendor_name' => $item->vendorname,
            'store_name' => $item->store_name ?: '-',
            'total_orders' => (int) $item->total_orders,
            'total_quantity' => (float) $item->total_quantity,
            'total_amount' => (float) $item->total_amount,
            'avg_price' => (float) $item->avg_price,
            'first_purchase' => $item->first_purchase,
            'last_purchase' => $item->last_purchase,
        ]);
    }
    
    return $result;
}

public function download_item_party_report(Request $request)
{
    // Get filter values
    $selectedStore = $request->get('store_id', 'all');
    $selectedVendor = $request->get('vendor_id', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get the same data as the main report
    $items = $this->getItemPartyData($selectedStore, $selectedVendor, $startDate, $endDate);

    // Create CSV content
    $csvData = [];
    $csvData[] = ['#', 'Item Name', 'Item Code', 'Vendor Name', 'Store/Warehouse', 'Total Orders', 'Total Quantity', 'Total Amount', 'Average Price', 'First Purchase', 'Last Purchase'];
    
    foreach ($items as $item) {
        $csvData[] = [
            $item->index,
            $item->item_name,
            $item->item_code,
            $item->vendor_name,
            $item->store_name,
            $item->total_orders,
            number_format($item->total_quantity, 2),
            'Rs.' . number_format($item->total_amount, 2),
            'Rs.' . number_format($item->avg_price, 2),
            date('d-m-Y', strtotime($item->first_purchase)),
            date('d-m-Y', strtotime($item->last_purchase))
        ];
    }

    // Add totals row
    $csvData[] = [''];
    $csvData[] = ['Summary'];
    $csvData[] = ['Total Items', $items->count()];
    $csvData[] = ['Total Orders', $items->sum('total_orders')];
    $csvData[] = ['Total Quantity', number_format($items->sum('total_quantity'), 2)];
    $csvData[] = ['Total Amount', 'Rs.' . number_format($items->sum('total_amount'), 2)];

    $filename = 'item-report-by-party-' . date('Y-m-d-H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($csvData) {
        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function sales_summary(Request $request)
{
    // Handle download request
    if ($request->has('download')) {
        return $this->download_sales_summary($request);
    }

    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get all employees for filter dropdown (optional)
    $employees = Employee::where('status', 'active')->orderBy('empname')->get();
    
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $selectedEmployee = $request->get('employee_id', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    $paymentMode = $request->get('payment_mode', 'all');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get sales data
    $sales = $this->getSalesSummaryData($selectedStore, $selectedEmployee, $paymentMode, $startDate, $endDate);
    
    // Calculate totals
    $totalSales = $sales->count();
    $totalAmount = $sales->sum('grand_total');
    $totalDiscount = $sales->sum('total_discount_with_gift_cards');
    $cashSales = $sales->where('mode_of_payment', 'cash')->sum('grand_total');
    $onlineSales = $sales->whereIn('mode_of_payment', ['card', 'upi', 'online'])->sum('grand_total');

    return view('reports.sales_summary', compact(
        'sales', 
        'stores', 
        'employees',
        'selectedStore', 
        'selectedEmployee',
        'paymentMode',
        'dateRange',
        'totalSales',
        'totalAmount',
        'totalDiscount',
        'cashSales',
        'onlineSales',
        'startDate',
        'endDate'
    ));
}

public function getSalesSummaryItems(Request $request)
{
    // Calculate date range
    $dateRanges = $this->getDateRange($request->get('date_range', 'last_7_days'));
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    $sales = $this->getSalesSummaryData(
        $request->get('store_id', session('store_id')),
        $request->get('employee_id', 'all'),
        $request->get('payment_mode', 'all'),
        $startDate,
        $endDate
    );
    
    return response()->json($sales->values());
}

private function getSalesSummaryData($selectedStore, $selectedEmployee, $paymentMode, $startDate, $endDate)
{
    $role = session('role');
    $storeId = session('store_id');

    // Build the query
    $query = SalesInvoice::with(['customer', 'employee', 'store'])
        ->whereBetween('invoice_date', [$startDate, $endDate])
        ->where('status', 'completed');

    // Apply store filter
    if ($role === 'manager') {
        $query->where('store_id', $storeId);
    } elseif ($selectedStore !== 'all') {
        $query->where('store_id', $selectedStore);
    }

    // Apply employee filter
    if ($selectedEmployee !== 'all') {
        $query->where('employee_id', $selectedEmployee);
    }

    // Apply payment mode filter
    if ($paymentMode !== 'all') {
        if ($paymentMode === 'online') {
            $query->whereIn('mode_of_payment', ['card', 'upi', 'online']);
        } else {
            $query->where('mode_of_payment', $paymentMode);
        }
    }

    $salesData = $query->orderBy('invoice_date', 'desc')->get();

    // Format the data
    $result = collect();
    
    foreach ($salesData as $index => $sale) {
        $result->push((object) [
            'index' => $index + 1,
            'id' => $sale->id,
            'invoice_date' => $sale->invoice_date,
            'customer_name' => optional($sale->customer)->customer_name ?? 'Walk-in Customer',
            'employee_name' => optional($sale->employee)->empname ?? 'N/A',
            'store_name' => optional($sale->store)->store_name ?? 'N/A',
            'mode_of_payment' => ucfirst($sale->mode_of_payment),
            'sub_total' => (float) $sale->sub_total,
            'total_discount' => (float) $sale->total_discount,
            'total_tax' => (float) $sale->total_tax,
            'grand_total' => (float) $sale->grand_total,
            'received_amount' => (float) $sale->received_amount,
            'gift_card_amount' => (float) $sale->gift_card_amount,
            'voucher_amount' => (float) $sale->voucher_amount,
            'total_gift_card_discount' => (float) $sale->total_gift_card_discount,
            'total_discount_with_gift_cards' => (float) $sale->total_discount + (float) $sale->total_gift_card_discount,
            'loyalty_points_used' => (int) $sale->loyalty_points_used,
            'loyalty_points_earned' => (int) $sale->loyalty_points_earned,
            'pos_ipaddress' => $sale->pos_ipaddress ?? 'N/A',
            'status' => ucfirst($sale->status),
        ]);
    }
    
    return $result;
}

public function download_sales_summary(Request $request)
{
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $selectedEmployee = $request->get('employee_id', 'all');
    $paymentMode = $request->get('payment_mode', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get the same data as the main report
    $sales = $this->getSalesSummaryData($selectedStore, $selectedEmployee, $paymentMode, $startDate, $endDate);

    // Create CSV content
    $csvData = [];
    $csvData[] = ['#', 'Date', 'Invoice No', 'Customer Name', 'Employee', 'Store', 'Payment Mode', 'Sub Total', 'Discount', 'Tax', 'Grand Total', 'Status'];
    
    foreach ($sales as $sale) {
        $csvData[] = [
            $sale->index,
            date('d-m-Y', strtotime($sale->invoice_date)),
            $sale->id,
            $sale->customer_name,
            $sale->employee_name,
            $sale->store_name,
            $sale->mode_of_payment,
            'Rs.' . number_format($sale->sub_total, 2),
            'Rs.' . number_format($sale->total_discount_with_gift_cards, 2),
            'Rs.' . number_format($sale->total_tax, 2),
            'Rs.' . number_format($sale->grand_total, 2),
            $sale->status
        ];
    }

    // Add totals row
    $csvData[] = [''];
    $csvData[] = ['Summary'];
    $csvData[] = ['Total Sales', $sales->count()];
    $csvData[] = ['Total Amount', 'Rs.' . number_format($sales->sum('grand_total'), 2)];
    $csvData[] = ['Total Discount', 'Rs.' . number_format($sales->sum('total_discount_with_gift_cards'), 2)];

    $filename = 'sales-summary-' . date('Y-m-d-H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($csvData) {
        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function vendor_report(Request $request)
{
    // Handle download request
    if ($request->has('download')) {
        return $this->download_vendor_report($request);
    }

    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get all vendors for filter dropdown
    $vendors = Vendor::where('status', 'active')->orderBy('vendorname')->get();
    
    // Get filter values
    $selectedStore = $request->get('store_id', 'all');
    $selectedVendor = $request->get('vendor_id', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get vendor purchase data
    $vendorData = $this->getVendorReportData($selectedStore, $selectedVendor, $startDate, $endDate);
    
    // Calculate totals
    $totalVendors = $vendorData->count();
    $totalQuantity = $vendorData->sum('total_quantity');
    $totalAmount = $vendorData->sum('total_amount');
    $totalOrders = $vendorData->sum('total_orders');

    return view('reports.vendor_report', compact(
        'vendorData', 
        'stores', 
        'vendors',
        'selectedStore', 
        'selectedVendor',
        'dateRange',
        'totalVendors',
        'totalQuantity',
        'totalAmount',
        'totalOrders',
        'startDate',
        'endDate'
    ));
}

public function getVendorReportItems(Request $request)
{
    // Calculate date range
    $dateRanges = $this->getDateRange($request->get('date_range', 'last_7_days'));
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    $vendorData = $this->getVendorReportData(
        $request->get('store_id', 'all'),
        $request->get('vendor_id', 'all'),
        $startDate,
        $endDate
    );
    
    return response()->json($vendorData->values());
}

private function getVendorReportData($selectedStore, $selectedVendor, $startDate, $endDate)
{
    $role = session('role');
    $storeId = session('store_id');

    // Build the query to get vendor purchase summary
    $query = DB::table('purchase_order_items as poi')
        ->join('purchase_orders as po', 'poi.purchase_order_id', '=', 'po.id')
        ->join('vendors as v', 'po.vendor_id', '=', 'v.id')
        ->join('items as i', 'poi.item_id', '=', 'i.id')
        ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
        ->whereBetween('po.bill_date', [$startDate, $endDate])
        ->where('v.status', 'active');

    // Apply store filter
    if ($role === 'manager') {
        $query->where('po.warehouse', $storeId);
    } elseif ($selectedStore !== 'all') {
        $query->where('po.warehouse', $selectedStore);
    }

    // Apply vendor filter
    if ($selectedVendor !== 'all') {
        $query->where('po.vendor_id', $selectedVendor);
    }

    $purchaseData = $query
        ->select(
            'v.id as vendor_id',
            'v.vendorname as vendor_name',
            'v.contact as vendor_contact',
            'v.email as vendor_email',
            's.store_name',
            DB::raw('COUNT(DISTINCT po.id) as total_orders'),
            DB::raw('COUNT(DISTINCT poi.item_id) as total_items'),
            DB::raw('SUM(poi.qty) as total_quantity'),
            DB::raw('SUM(poi.amount) as total_amount'),
            DB::raw('AVG(poi.price) as avg_price'),
            DB::raw('MIN(po.bill_date) as first_purchase'),
            DB::raw('MAX(po.bill_date) as last_purchase')
        )
        ->groupBy('v.id', 'v.vendorname', 'v.contact', 'v.email', 's.store_name')
        ->orderBy('total_amount', 'desc')
        ->get();

    // Format the data
    $result = collect();
    
    foreach ($purchaseData as $index => $vendor) {
        $result->push((object) [
            'index' => $index + 1,
            'vendor_id' => $vendor->vendor_id,
            'vendor_name' => $vendor->vendor_name,
            'vendor_contact' => $vendor->vendor_contact ?? 'N/A',
            'vendor_email' => $vendor->vendor_email ?? 'N/A',
            'store_name' => $vendor->store_name ?? '-',
            'total_orders' => (int) $vendor->total_orders,
            'total_items' => (int) $vendor->total_items,
            'total_quantity' => (float) $vendor->total_quantity,
            'total_amount' => (float) $vendor->total_amount,
            'avg_price' => (float) $vendor->avg_price,
            'first_purchase' => $vendor->first_purchase,
            'last_purchase' => $vendor->last_purchase,
        ]);
    }
    
    return $result;
}

public function download_vendor_report(Request $request)
{
    // Get filter values
    $selectedStore = $request->get('store_id', 'all');
    $selectedVendor = $request->get('vendor_id', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get the same data as the main report
    $vendorData = $this->getVendorReportData($selectedStore, $selectedVendor, $startDate, $endDate);

    // Create CSV content
    $csvData = [];
    $csvData[] = ['#', 'Vendor Name', 'Contact', 'Email', 'Store/Warehouse', 'Total Orders', 'Total Items', 'Total Quantity', 'Total Amount', 'Average Price', 'First Purchase', 'Last Purchase'];
    
    foreach ($vendorData as $vendor) {
        $csvData[] = [
            $vendor->index,
            $vendor->vendor_name,
            $vendor->vendor_contact,
            $vendor->vendor_email,
            $vendor->store_name,
            $vendor->total_orders,
            $vendor->total_items,
            number_format($vendor->total_quantity, 2),
            'Rs.' . number_format($vendor->total_amount, 2),
            'Rs.' . number_format($vendor->avg_price, 2),
            date('d-m-Y', strtotime($vendor->first_purchase)),
            date('d-m-Y', strtotime($vendor->last_purchase))
        ];
    }

    // Add totals row
    $csvData[] = [''];
    $csvData[] = ['Summary'];
    $csvData[] = ['Total Vendors', $vendorData->count()];
    $csvData[] = ['Total Orders', $vendorData->sum('total_orders')];
    $csvData[] = ['Total Items', $vendorData->sum('total_items')];
    $csvData[] = ['Total Quantity', number_format($vendorData->sum('total_quantity'), 2)];
    $csvData[] = ['Total Amount', 'Rs.' . number_format($vendorData->sum('total_amount'), 2)];

    $filename = 'vendor-report-' . date('Y-m-d-H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($csvData) {
        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function purchase_summary(Request $request)
{
    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get all vendors for filter dropdown
    $vendors = Vendor::where('status', 'active')->orderBy('vendorname')->get();
    
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $selectedVendor = $request->get('vendor_id', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get purchase summary data
    $purchases = $this->getPurchaseSummaryData($selectedStore, $selectedVendor, $startDate, $endDate);
    
    // Calculate totals
    $totalPurchases = $purchases->count();
    $totalAmount = $purchases->sum('total');
    $totalQuantity = $purchases->sum('total_quantity');
    $totalPaidAmount = $purchases->sum('paid_amount');
    $totalBalance = $purchases->sum('balance_amount');

    return view('reports.purchase_summary', compact(
        'purchases', 
        'stores', 
        'vendors',
        'selectedStore', 
        'selectedVendor',
        'dateRange',
        'totalPurchases',
        'totalAmount',
        'totalQuantity',
        'totalPaidAmount',
        'totalBalance',
        'startDate',
        'endDate'
    ));
}

public function getPurchaseSummaryItems(Request $request)
{
    // Calculate date range
    $dateRanges = $this->getDateRange($request->get('date_range', 'last_7_days'));
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    $purchases = $this->getPurchaseSummaryData(
        $request->get('store_id', session('store_id')),
        $request->get('vendor_id', 'all'),
        $startDate,
        $endDate
    );
    
    return response()->json($purchases->values());
}

private function getPurchaseSummaryData($selectedStore, $selectedVendor, $startDate, $endDate)
{
    $role = session('role');
    $storeId = session('store_id');

    // Build the query for purchase orders
    $query = DB::table('purchase_orders as po')
        ->join('vendors as v', 'po.vendor_id', '=', 'v.id')
        ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
        ->leftJoin('purchase_order_items as poi', 'po.id', '=', 'poi.purchase_order_id')
        ->whereBetween('po.bill_date', [$startDate, $endDate])
        ->where('v.status', 'active');

    // Apply store filter
    if ($role === 'manager') {
        $query->where('po.warehouse', $storeId);
    } elseif ($selectedStore !== 'all') {
        $query->where('po.warehouse', $selectedStore);
    }

    // Apply vendor filter
    if ($selectedVendor !== 'all') {
        $query->where('po.vendor_id', $selectedVendor);
    }

    $purchaseData = $query
        ->select(
            'po.id',
            'po.bill_date as purchase_date',
            'po.bill_no as purchase_no',
            'v.vendorname as party_name',
            'po.total',
            'po.paid_amount',
            'po.balance_amount',
            's.store_name',
            DB::raw('SUM(poi.qty) as total_quantity'),
            DB::raw('COUNT(poi.id) as total_items'),
            DB::raw('SUM(poi.tax) as total_tax'),
            DB::raw('SUM(poi.discount) as total_discount'),
            DB::raw('SUM(poi.amount) as sub_total')
        )
        ->groupBy('po.id', 'po.bill_date', 'po.bill_no', 'v.vendorname', 'po.total', 'po.paid_amount', 'po.balance_amount', 's.store_name')
        ->orderBy('po.bill_date', 'desc')
        ->get();

    // Format the data
    $result = collect();
    
    foreach ($purchaseData as $index => $purchase) {
        $result->push((object) [
            'index' => $index + 1,
            'id' => $purchase->id,
            'purchase_date' => $purchase->purchase_date,
            'purchase_no' => $purchase->purchase_no ?: $purchase->id,
            'party_name' => $purchase->party_name,
            'store_name' => $purchase->store_name ?? 'N/A',
            'total_items' => (int) $purchase->total_items,
            'total_quantity' => (float) $purchase->total_quantity,
            'sub_total' => (float) ($purchase->sub_total ?? 0),
            'total_discount' => (float) ($purchase->total_discount ?? 0),
            'total_tax' => (float) ($purchase->total_tax ?? 0),
            'total' => (float) $purchase->total,
            'paid_amount' => (float) $purchase->paid_amount,
            'balance_amount' => (float) $purchase->balance_amount,
        ]);
    }
    
    return $result;
}
public function bill_wise_profit(Request $request)
{
    // Handle download request
    if ($request->has('download')) {
        return $this->download_bill_wise_profit($request);
    }

    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get all employees for filter dropdown
    $employees = Employee::where('status', 'active')->orderBy('empname')->get();
    
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $selectedEmployee = $request->get('employee_id', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    $profitFilter = $request->get('profit_filter', 'all'); // all, profit, loss, break_even
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get bill wise profit data
    $bills = $this->getBillWiseProfitData($selectedStore, $selectedEmployee, $profitFilter, $startDate, $endDate);
    
    // Calculate totals
    $totalBills = $bills->count();
    $totalRevenue = $bills->sum('grand_total');
    $totalCostPrice = $bills->sum('total_cost_price');
    $totalProfit = $bills->sum('profit_amount');
    $avgProfitMargin = $totalBills > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
    $profitableBills = $bills->where('profit_amount', '>', 0)->count();
    $lossBills = $bills->where('profit_amount', '<', 0)->count();

    return view('reports.bill_wise_profit', compact(
        'bills', 
        'stores', 
        'employees',
        'selectedStore', 
        'selectedEmployee',
        'profitFilter',
        'dateRange',
        'totalBills',
        'totalRevenue',
        'totalCostPrice',
        'totalProfit',
        'avgProfitMargin',
        'profitableBills',
        'lossBills',
        'startDate',
        'endDate'
    ));
}

public function getBillWiseProfitItems(Request $request)
{
    // Calculate date range
    $dateRanges = $this->getDateRange($request->get('date_range', 'last_7_days'));
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    $bills = $this->getBillWiseProfitData(
        $request->get('store_id', session('store_id')),
        $request->get('employee_id', 'all'),
        $request->get('profit_filter', 'all'),
        $startDate,
        $endDate
    );
    
    return response()->json($bills->values());
}

private function getBillWiseProfitData($selectedStore, $selectedEmployee, $profitFilter, $startDate, $endDate)
{
    $role = session('role');
    $storeId = session('store_id');

    // Build the query for sales invoices
    $query = SalesInvoice::with(['customer', 'employee', 'store', 'items.item'])
        ->whereBetween('invoice_date', [$startDate, $endDate])
        ->where('status', 'completed');

    // Apply store filter
    if ($role === 'manager') {
        $query->where('store_id', $storeId);
    } elseif ($selectedStore !== 'all') {
        $query->where('store_id', $selectedStore);
    }

    // Apply employee filter
    if ($selectedEmployee !== 'all') {
        $query->where('employee_id', $selectedEmployee);
    }

    $salesData = $query->orderBy('invoice_date', 'desc')->get();

    // Calculate profit for each bill
    $result = collect();
    
    foreach ($salesData as $index => $sale) {
        $totalCostPrice = 0;
        $totalSellingPrice = 0;
        $itemDetails = [];
        
        // Calculate cost and selling price for each item in the bill
        foreach ($sale->items as $saleItem) {
            $item = $saleItem->item;
            if ($item) {
                $costPrice = ($item->purchase_price ?? 0) * $saleItem->qty;
                $sellingPrice = $saleItem->amount; // This is qty * price after discount
                
                $totalCostPrice += $costPrice;
                $totalSellingPrice += $sellingPrice;
                
                $itemDetails[] = [
                    'item_name' => $item->item_name,
                    'qty' => $saleItem->qty,
                    'unit_cost' => $item->purchase_price ?? 0,
                    'unit_selling' => $saleItem->price,
                    'total_cost' => $costPrice,
                    'total_selling' => $sellingPrice,
                    'item_profit' => $sellingPrice - $costPrice
                ];
            }
        }
        
        // Calculate profit metrics
        $profitAmount = $totalSellingPrice - $totalCostPrice;
        $profitPercentage = $totalSellingPrice > 0 ? ($profitAmount / $totalSellingPrice) * 100 : 0;
        
        // Determine status
        $status = 'Break Even';
        if ($profitAmount > 0) {
            $status = 'Profit';
        } elseif ($profitAmount < 0) {
            $status = 'Loss';
        }
        
        // Apply profit filter
        $includeItem = true;
        switch ($profitFilter) {
            case 'profit':
                $includeItem = $profitAmount > 0;
                break;
            case 'loss':
                $includeItem = $profitAmount < 0;
                break;
            case 'break_even':
                $includeItem = $profitAmount == 0;
                break;
            case 'all':
            default:
                $includeItem = true;
                break;
        }
        
        if ($includeItem) {
            $result->push((object) [
                'index' => $index + 1,
                'id' => $sale->id,
                'invoice_date' => $sale->invoice_date,
                'customer_name' => optional($sale->customer)->customer_name ?? 'Walk-in Customer',
                'employee_name' => optional($sale->employee)->empname ?? 'N/A',
                'store_name' => optional($sale->store)->store_name ?? 'N/A',
                'mode_of_payment' => ucfirst($sale->mode_of_payment),
                'total_items' => $sale->items->count(),
                'total_quantity' => $sale->items->sum('qty'),
                'sub_total' => (float) $sale->sub_total,
                'total_discount' => (float) $sale->total_discount + (float) $sale->total_gift_card_discount,
                'total_tax' => (float) $sale->total_tax,
                'grand_total' => (float) $sale->grand_total,
                'total_cost_price' => (float) $totalCostPrice,
                'total_selling_price' => (float) $totalSellingPrice,
                'profit_amount' => (float) $profitAmount,
                'profit_percentage' => (float) $profitPercentage,
                'status' => $status,
                'item_details' => $itemDetails,
                'pos_ipaddress' => $sale->pos_ipaddress ?? 'N/A',
            ]);
        }
    }
    
    return $result;
}

public function download_bill_wise_profit(Request $request)
{
    // Get filter values
    $selectedStore = $request->get('store_id', session('store_id'));
    $selectedEmployee = $request->get('employee_id', 'all');
    $profitFilter = $request->get('profit_filter', 'all');
    $dateRange = $request->get('date_range', 'last_7_days');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get the same data as the main report
    $bills = $this->getBillWiseProfitData($selectedStore, $selectedEmployee, $profitFilter, $startDate, $endDate);

    // Create CSV content
    $csvData = [];
    $csvData[] = [
        '#', 
        'Date', 
        'Invoice No', 
        'Customer Name', 
        'Employee', 
        'Store', 
        'Payment Mode', 
        'Total Items',
        'Total Qty',
        'Sub Total', 
        'Discount', 
        'Tax', 
        'Grand Total',
        'Cost Price',
        'Selling Price',
        'Profit Amount',
        'Profit %',
        'Status'
    ];
    
    foreach ($bills as $bill) {
        $csvData[] = [
            $bill->index,
            date('d-m-Y', strtotime($bill->invoice_date)),
            $bill->id,
            $bill->customer_name,
            $bill->employee_name,
            $bill->store_name,
            $bill->mode_of_payment,
            $bill->total_items,
            number_format($bill->total_quantity, 2),
            'Rs.' . number_format($bill->sub_total, 2),
            'Rs.' . number_format($bill->total_discount, 2),
            'Rs.' . number_format($bill->total_tax, 2),
            'Rs.' . number_format($bill->grand_total, 2),
            'Rs.' . number_format($bill->total_cost_price, 2),
            'Rs.' . number_format($bill->total_selling_price, 2),
            'Rs.' . number_format($bill->profit_amount, 2),
            number_format($bill->profit_percentage, 2) . '%',
            $bill->status
        ];
    }

    // Add summary rows
    $csvData[] = [''];
    $csvData[] = ['Summary'];
    $csvData[] = ['Total Bills', $bills->count()];
    $csvData[] = ['Total Revenue', 'Rs.' . number_format($bills->sum('grand_total'), 2)];
    $csvData[] = ['Total Cost', 'Rs.' . number_format($bills->sum('total_cost_price'), 2)];
    $csvData[] = ['Total Profit', 'Rs.' . number_format($bills->sum('profit_amount'), 2)];
    $csvData[] = ['Average Profit %', number_format($bills->avg('profit_percentage'), 2) . '%'];
    $csvData[] = ['Profitable Bills', $bills->where('profit_amount', '>', 0)->count()];
    $csvData[] = ['Loss Bills', $bills->where('profit_amount', '<', 0)->count()];

    $filename = 'bill-wise-profit-report-' . date('Y-m-d-H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($csvData) {
        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

// Method to get detailed item-wise profit for a specific bill
public function getBillItemDetails(Request $request, $billId)
{
    $bill = SalesInvoice::with(['items.item'])->findOrFail($billId);
    
    $itemDetails = [];
    foreach ($bill->items as $saleItem) {
        $item = $saleItem->item;
        if ($item) {
            $costPrice = ($item->purchase_price ?? 0) * $saleItem->qty;
            $sellingPrice = $saleItem->amount;
            $itemProfit = $sellingPrice - $costPrice;
            $itemProfitPercentage = $sellingPrice > 0 ? ($itemProfit / $sellingPrice) * 100 : 0;
            
            $itemDetails[] = [
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'qty' => $saleItem->qty,
                'unit' => $saleItem->unit,
                'unit_cost_price' => $item->purchase_price ?? 0,
                'unit_selling_price' => $saleItem->price,
                'discount' => $saleItem->discount,
                'tax' => $saleItem->tax,
                'total_cost_price' => $costPrice,
                'total_selling_price' => $sellingPrice,
                'item_profit' => $itemProfit,
                'item_profit_percentage' => $itemProfitPercentage
            ];
        }
    }
    
    return response()->json($itemDetails);
}

// Add these methods to your Reports controller class

public function vendor_outstanding_report(Request $request)
{
    // Handle download request
    if ($request->has('download')) {
        return $this->download_vendor_outstanding_report($request);
    }

    // Get all stores for filter dropdown
    $stores = Store::all();
    
    // Get all vendors for filter dropdown
    $vendors = Vendor::where('status', 'active')->orderBy('vendorname')->get();
    
    // Get filter values
    $selectedStore = $request->get('store_id', 'all');
    $selectedVendor = $request->get('vendor_id', 'all');
    $dateRange = $request->get('date_range', 'all_time');
    $outstandingFilter = $request->get('outstanding_filter', 'all'); // all, outstanding_only, paid_only
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get vendor outstanding data
    $vendorOutstanding = $this->getVendorOutstandingData($selectedStore, $selectedVendor, $startDate, $endDate, $outstandingFilter);
    
    // Calculate totals
    $totalVendors = $vendorOutstanding->count();
    $totalPurchaseAmount = $vendorOutstanding->sum('total_purchase_amount');
    $totalPaidAmount = $vendorOutstanding->sum('total_paid_amount');
    $totalOutstandingAmount = $vendorOutstanding->sum('total_outstanding_amount');
    $vendorsWithOutstanding = $vendorOutstanding->where('total_outstanding_amount', '>', 0)->count();

    return view('reports.vendor_outstanding', compact(
        'vendorOutstanding', 
        'stores', 
        'vendors',
        'selectedStore', 
        'selectedVendor',
        'dateRange',
        'outstandingFilter',
        'totalVendors',
        'totalPurchaseAmount',
        'totalPaidAmount',
        'totalOutstandingAmount',
        'vendorsWithOutstanding',
        'startDate',
        'endDate'
    ));
}

public function getVendorOutstandingItems(Request $request)
{
    // Calculate date range
    $dateRanges = $this->getDateRange($request->get('date_range', 'all_time'));
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    $vendorOutstanding = $this->getVendorOutstandingData(
        $request->get('store_id', 'all'),
        $request->get('vendor_id', 'all'),
        $startDate,
        $endDate,
        $request->get('outstanding_filter', 'all')
    );
    
    return response()->json($vendorOutstanding->values());
}

private function getVendorOutstandingData($selectedStore, $selectedVendor, $startDate, $endDate, $outstandingFilter)
{
    $role = session('role');
    $storeId = session('store_id');

    // Build the query to get vendor purchase summary with outstanding amounts
    $query = DB::table('purchase_orders as po')
        ->join('vendors as v', 'po.vendor_id', '=', 'v.id')
        ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
        ->where('v.status', 'active');

    // Apply date range filter only if not all_time
    if ($startDate && $endDate) {
        $query->whereBetween('po.bill_date', [$startDate, $endDate]);
    }

    // Apply store filter
    if ($role === 'manager') {
        $query->where('po.warehouse', $storeId);
    } elseif ($selectedStore !== 'all') {
        $query->where('po.warehouse', $selectedStore);
    }

    // Apply vendor filter
    if ($selectedVendor !== 'all') {
        $query->where('po.vendor_id', $selectedVendor);
    }

    $vendorData = $query
        ->select(
            'v.id as vendor_id',
            'v.vendorname as vendor_name',
            'v.contact as vendor_contact',
            'v.email as vendor_email',
            'v.billaddress as vendor_address',
            's.store_name',
            DB::raw('COUNT(po.id) as total_transactions'),
            DB::raw('SUM(po.total) as total_purchase_amount'),
            DB::raw('SUM(po.paid_amount) as total_paid_amount'),
            DB::raw('SUM(po.balance_amount) as total_outstanding_amount'),
            DB::raw('MIN(po.bill_date) as first_transaction_date'),
            DB::raw('MAX(po.bill_date) as last_transaction_date'),
            DB::raw('COUNT(CASE WHEN po.balance_amount > 0 THEN 1 END) as outstanding_transactions'),
            DB::raw('COUNT(CASE WHEN po.balance_amount = 0 THEN 1 END) as paid_transactions')
        )
        ->groupBy('v.id', 'v.vendorname', 'v.contact', 'v.email', 'v.billaddress', 's.store_name')
        ->orderBy('total_outstanding_amount', 'desc')
        ->get();

    // Apply outstanding filter
    $filteredData = collect();
    
    foreach ($vendorData as $vendor) {
        $includeVendor = true;
        
        switch ($outstandingFilter) {
            case 'outstanding_only':
                $includeVendor = $vendor->total_outstanding_amount > 0;
                break;
            case 'paid_only':
                $includeVendor = $vendor->total_outstanding_amount == 0;
                break;
            case 'all':
            default:
                $includeVendor = true;
                break;
        }
        
        if ($includeVendor) {
            // Calculate payment percentage
            $paymentPercentage = $vendor->total_purchase_amount > 0 
                ? ($vendor->total_paid_amount / $vendor->total_purchase_amount) * 100 
                : 0;
            
            // Determine status
            $status = 'Fully Paid';
            if ($vendor->total_outstanding_amount > 0) {
                if ($vendor->total_paid_amount == 0) {
                    $status = 'Unpaid';
                } else {
                    $status = 'Partially Paid';
                }
            }
            
            // Calculate aging (days since last transaction)
            $daysSinceLastTransaction = $vendor->last_transaction_date 
                ? Carbon::now()->diffInDays(Carbon::parse($vendor->last_transaction_date))
                : 0;
            
            $filteredData->push((object) [
                'vendor_id' => $vendor->vendor_id,
                'vendor_name' => $vendor->vendor_name,
                'vendor_contact' => $vendor->vendor_contact ?? 'N/A',
                'vendor_email' => $vendor->vendor_email ?? 'N/A',
                'vendor_address' => $vendor->vendor_address ?? 'N/A',
                'store_name' => $vendor->store_name ?? 'All Stores',
                'total_transactions' => (int) $vendor->total_transactions,
                'outstanding_transactions' => (int) $vendor->outstanding_transactions,
                'paid_transactions' => (int) $vendor->paid_transactions,
                'total_purchase_amount' => (float) $vendor->total_purchase_amount,
                'total_paid_amount' => (float) $vendor->total_paid_amount,
                'total_outstanding_amount' => (float) $vendor->total_outstanding_amount,
                'payment_percentage' => (float) $paymentPercentage,
                'first_transaction_date' => $vendor->first_transaction_date,
                'last_transaction_date' => $vendor->last_transaction_date,
                'days_since_last_transaction' => $daysSinceLastTransaction,
                'status' => $status,
            ]);
        }
    }
    
    return $filteredData;
}

public function getVendorTransactionDetails(Request $request, $vendorId) 
{
    try {
        $role = session('role');
        $storeId = session('store_id');
        
        $query = DB::table('purchase_orders as po')
            ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
            ->leftJoin('vendors as v', 'po.vendor_id', '=', 'v.id')
            ->where('po.vendor_id', $vendorId);
        
        // Apply store filter based on role
        if ($role === 'manager' && $storeId) {
            $query->where('po.warehouse', $storeId);
        }
        
        $transactions = $query
            ->select(
                'po.id',
                'po.bill_no',
                'po.bill_date',
                'po.due_date', 
                'po.total',
                'po.paid_amount',
                'po.balance_amount',
                'po.payment_type',
                's.store_name',
                'v.vendorname as vendor_name'
            )
            ->orderBy('po.bill_date', 'desc')
            ->get();
        
        $formattedTransactions = [];
        
        foreach ($transactions as $index => $transaction) {
            // Calculate days past due
            $daysPastDue = 0;
            if ($transaction->due_date) {
                $dueDate = Carbon::parse($transaction->due_date);
                $today = Carbon::now();
                
                if ($today->gt($dueDate)) {
                    $daysPastDue = $today->diffInDays($dueDate);
                }
            }
            
            // Determine status
            $status = 'Fully Paid';
            if ($transaction->balance_amount > 0) {
                if ($transaction->paid_amount == 0) {
                    $status = 'Unpaid';
                } else {
                    $status = 'Partially Paid';
                }
                
                if ($daysPastDue > 0) {
                    $status .= ' (Overdue)';
                }
            }
            
            $formattedTransactions[] = [
                'index' => $index + 1,
                'id' => $transaction->id,
                'bill_no' => $transaction->bill_no ?: 'PO-' . $transaction->id,
                'bill_date' => $transaction->bill_date,
                'due_date' => $transaction->due_date,
                'store_name' => $transaction->store_name ?? 'N/A',
                'total_amount' => (float) $transaction->total,
                'paid_amount' => (float) $transaction->paid_amount,
                'balance_amount' => (float) $transaction->balance_amount,
                'payment_type' => ucfirst($transaction->payment_type ?? 'cash'),
                'days_past_due' => $daysPastDue,
                'status' => $status,
            ];
        }
        
        return response()->json($formattedTransactions);
        
    } catch (\Exception $e) {
        \Log::error('Vendor Transaction Details Error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Unable to load transaction details',
            'message' => $e->getMessage()
        ], 500);
    }
}
public function download_vendor_outstanding_report(Request $request)
{
    // Get filter values
    $selectedStore = $request->get('store_id', 'all');
    $selectedVendor = $request->get('vendor_id', 'all');
    $dateRange = $request->get('date_range', 'all_time');
    $outstandingFilter = $request->get('outstanding_filter', 'all');
    
    // Calculate date range
    $dateRanges = $this->getDateRange($dateRange);
    $startDate = $dateRanges['start'];
    $endDate = $dateRanges['end'];
    
    // Get the same data as the main report
    $vendorOutstanding = $this->getVendorOutstandingData($selectedStore, $selectedVendor, $startDate, $endDate, $outstandingFilter);

    // Create CSV content
    $csvData = [];
    $csvData[] = [
        '#', 
        'Vendor Name', 
        'Contact', 
        'Email', 
        'Address',
        'Store/Warehouse', 
        'Total Transactions',
        'Outstanding Transactions',
        'Paid Transactions',
        'Total Purchase Amount', 
        'Total Paid Amount',
        'Total Outstanding Amount',
        'Payment %',
        'First Transaction',
        'Last Transaction',
        'Days Since Last Transaction',
        'Status'
    ];
    
    $index = 1;
    foreach ($vendorOutstanding as $vendor) {
        $csvData[] = [
            $index++,
            $vendor->vendor_name,
            $vendor->vendor_contact,
            $vendor->vendor_email,
            $vendor->vendor_address,
            $vendor->store_name,
            $vendor->total_transactions,
            $vendor->outstanding_transactions,
            $vendor->paid_transactions,
            'Rs.' . number_format($vendor->total_purchase_amount, 2),
            'Rs.' . number_format($vendor->total_paid_amount, 2),
            'Rs.' . number_format($vendor->total_outstanding_amount, 2),
            number_format($vendor->payment_percentage, 2) . '%',
            $vendor->first_transaction_date ? date('d-m-Y', strtotime($vendor->first_transaction_date)) : 'N/A',
            $vendor->last_transaction_date ? date('d-m-Y', strtotime($vendor->last_transaction_date)) : 'N/A',
            $vendor->days_since_last_transaction,
            $vendor->status
        ];
    }

    // Add summary rows
    $csvData[] = [''];
    $csvData[] = ['Summary'];
    $csvData[] = ['Total Vendors', $vendorOutstanding->count()];
    $csvData[] = ['Vendors with Outstanding', $vendorOutstanding->where('total_outstanding_amount', '>', 0)->count()];
    $csvData[] = ['Total Purchase Amount', 'Rs.' . number_format($vendorOutstanding->sum('total_purchase_amount'), 2)];
    $csvData[] = ['Total Paid Amount', 'Rs.' . number_format($vendorOutstanding->sum('total_paid_amount'), 2)];
    $csvData[] = ['Total Outstanding Amount', 'Rs.' . number_format($vendorOutstanding->sum('total_outstanding_amount'), 2)];
    $csvData[] = ['Overall Payment %', number_format(($vendorOutstanding->sum('total_paid_amount') / $vendorOutstanding->sum('total_purchase_amount')) * 100, 2) . '%'];

    $filename = 'vendor-outstanding-report-' . date('Y-m-d-H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($csvData) {
        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

// Method to get aging analysis for vendor outstanding amounts
public function vendor_aging_analysis(Request $request)
{
    $selectedStore = $request->get('store_id', 'all');
    $selectedVendor = $request->get('vendor_id', 'all');
    
    $role = session('role');
    $storeId = session('store_id');

    // Build query for aging analysis
    $query = DB::table('purchase_orders as po')
        ->join('vendors as v', 'po.vendor_id', '=', 'v.id')
        ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
        ->where('v.status', 'active')
        ->where('po.balance_amount', '>', 0); // Only outstanding amounts

    // Apply store filter
    if ($role === 'manager') {
        $query->where('po.warehouse', $storeId);
    } elseif ($selectedStore !== 'all') {
        $query->where('po.warehouse', $selectedStore);
    }

    // Apply vendor filter
    if ($selectedVendor !== 'all') {
        $query->where('po.vendor_id', $selectedVendor);
    }

    $outstandingTransactions = $query
        ->select(
            'v.vendorname',
            'po.bill_date',
            'po.due_date',
            'po.balance_amount',
            'po.bill_no',
            's.store_name'
        )
        ->get();

    // Categorize by aging periods
    $agingAnalysis = [
        '0-30' => ['count' => 0, 'amount' => 0],
        '31-60' => ['count' => 0, 'amount' => 0],
        '61-90' => ['count' => 0, 'amount' => 0],
        '91-180' => ['count' => 0, 'amount' => 0],
        '180+' => ['count' => 0, 'amount' => 0],
    ];

    foreach ($outstandingTransactions as $transaction) {
        $dueDate = $transaction->due_date ? Carbon::parse($transaction->due_date) : Carbon::parse($transaction->bill_date)->addDays(30);
        $daysPastDue = Carbon::now()->diffInDays($dueDate, false);
        
        if ($daysPastDue <= 30) {
            $agingAnalysis['0-30']['count']++;
            $agingAnalysis['0-30']['amount'] += $transaction->balance_amount;
        } elseif ($daysPastDue <= 60) {
            $agingAnalysis['31-60']['count']++;
            $agingAnalysis['31-60']['amount'] += $transaction->balance_amount;
        } elseif ($daysPastDue <= 90) {
            $agingAnalysis['61-90']['count']++;
            $agingAnalysis['61-90']['amount'] += $transaction->balance_amount;
        } elseif ($daysPastDue <= 180) {
            $agingAnalysis['91-180']['count']++;
            $agingAnalysis['91-180']['amount'] += $transaction->balance_amount;
        } else {
            $agingAnalysis['180+']['count']++;
            $agingAnalysis['180+']['amount'] += $transaction->balance_amount;
        }
    }

    return response()->json($agingAnalysis);
}
public function vendor_statement(Request $request)
{
    $role = session('role');
    $storeId = session('store_id');
    
    // Get filter parameters
    $selectedVendor = $request->get('vendor_id', 'all');
    $selectedStore = $request->get('store_id', $role === 'manager' ? $storeId : 'all');
    $dateRange = $request->get('date_range', 'this_month');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    $transactionType = $request->get('transaction_type', 'all'); // all, purchase, payment
    
    // Get stores for filter
    $storesQuery = DB::table('stores')->where('status', 'Active');
    if ($role === 'manager') {
        $storesQuery->where('id', $storeId);
    }
    $stores = $storesQuery->get();
    
    // Get vendors for filter
    $vendors = DB::table('vendors')->where('status', 'Active')->get();
    
    // Calculate date range
    $dateConditions = $this->calculateDateRange($dateRange, $startDate, $endDate);
    
    // Build the main query for statement
    $statementData = [];
    $vendorTotals = [];
    
    if ($selectedVendor !== 'all') {
        $vendorIds = [$selectedVendor];
    } else {
        $vendorQuery = DB::table('vendors')->where('status', 'Active');
        $vendorIds = $vendorQuery->pluck('id')->toArray();
    }
    
    foreach ($vendorIds as $vendorId) {
        $vendor = DB::table('vendors')->where('id', $vendorId)->first();
        if (!$vendor) continue;
        
        $transactions = [];
        
        // Get Purchase Orders (Debit entries)
        if ($transactionType === 'all' || $transactionType === 'purchase') {
            $purchaseQuery = DB::table('purchase_orders as po')
                ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
                ->where('po.vendor_id', $vendorId);
                
            if ($selectedStore !== 'all') {
                $purchaseQuery->where('po.warehouse', $selectedStore);
            }
            
            if ($dateConditions['start_date'] && $dateConditions['end_date']) {
                $purchaseQuery->whereBetween('po.bill_date', [$dateConditions['start_date'], $dateConditions['end_date']]);
            }
            
            $purchases = $purchaseQuery
                ->select(
                    'po.id',
                    'po.bill_no',
                    'po.bill_date as transaction_date',
                    'po.due_date',
                    'po.total',
                    'po.paid_amount',
                    'po.balance_amount',
                    'po.payment_type',
                    'po.description',
                    's.store_name'
                )
                ->get();
                
            foreach ($purchases as $purchase) {
                $transactions[] = [
                    'type' => 'purchase',
                    'id' => $purchase->id,
                    'reference' => $purchase->bill_no ?: 'PO-' . $purchase->id,
                    'date' => $purchase->transaction_date,
                    'due_date' => $purchase->due_date,
                    'description' => $purchase->description ?: 'Purchase Order',
                    'store' => $purchase->store_name,
                    'debit' => (float) $purchase->total,
                    'credit' => 0,
                    'balance' => (float) $purchase->balance_amount,
                    'payment_type' => $purchase->payment_type,
                    'status' => $purchase->balance_amount > 0 ? 'Outstanding' : 'Paid'
                ];
            }
        }
        
        // Get Payments (Credit entries)
        if ($transactionType === 'all' || $transactionType === 'payment') {
            $paymentQuery = DB::table('payments as p')
                ->leftJoin('purchase_orders as po', 'p.purchase_order_id', '=', 'po.id')
                ->leftJoin('stores as s', 'po.warehouse', '=', 's.id')
                ->where('p.vendor_id', $vendorId);
                
            if ($selectedStore !== 'all') {
                $paymentQuery->where('po.warehouse', $selectedStore);
            }
            
            if ($dateConditions['start_date'] && $dateConditions['end_date']) {
                $paymentQuery->whereBetween('p.payment_date', [$dateConditions['start_date'], $dateConditions['end_date']]);
            }
            
            $payments = $paymentQuery
                ->select(
                    'p.id',
                    'p.payment_date as transaction_date',
                    'p.payment_amount',
                    'p.payment_type',
                    'p.remarks',
                    'po.bill_no',
                    'po.id as po_id',
                    's.store_name'
                )
                ->get();
                
            foreach ($payments as $payment) {
                $transactions[] = [
                    'type' => 'payment',
                    'id' => $payment->id,
                    'reference' => 'PMT-' . $payment->id . ($payment->bill_no ? ' (Ref: ' . $payment->bill_no . ')' : ''),
                    'date' => $payment->transaction_date,
                    'due_date' => null,
                    'description' => $payment->remarks ?: 'Payment Received',
                    'store' => $payment->store_name,
                    'debit' => 0,
                    'credit' => (float) $payment->payment_amount,
                    'balance' => 0,
                    'payment_type' => $payment->payment_type,
                    'status' => 'Completed'
                ];
            }
        }
        
        // Sort transactions by date
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        // Calculate running balance
        $runningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($transactions as &$transaction) {
            if ($transaction['type'] === 'purchase') {
                $runningBalance += $transaction['debit'];
                $totalDebit += $transaction['debit'];
            } else {
                $runningBalance -= $transaction['credit'];
                $totalCredit += $transaction['credit'];
            }
            $transaction['running_balance'] = $runningBalance;
        }
        
        if (!empty($transactions)) {
            $statementData[$vendorId] = [
                'vendor' => $vendor,
                'transactions' => $transactions,
                'summary' => [
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'balance' => $runningBalance
                ]
            ];
        }
    }
    
    // Handle download
    if ($request->get('download') === 'csv') {
        return $this->downloadVendorStatementCSV($statementData, $request);
    }
    
    return view('reports.vendor_statement', compact(
        'statementData',
        'vendors',
        'stores',
        'selectedVendor',
        'selectedStore',
        'dateRange',
        'startDate',
        'endDate',
        'transactionType'
    ));
}

/**
 * Download Vendor Statement as CSV
 */
public function vendor_statement_download(Request $request)
{
    // Get the same data as the main report
    $data = $this->vendor_statement($request);
    $statementData = $data->getData()['statementData'];
    
    $filename = 'vendor_statement_' . date('Y-m-d_H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];
    
    return response()->stream(function() use ($statementData) {
        $file = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($file, [
            'Vendor Name',
            'Date',
            'Reference',
            'Description',
            'Store',
            'Debit Amount',
            'Credit Amount',
            'Running Balance',
            'Payment Type',
            'Status'
        ]);
        
        foreach ($statementData as $vendorData) {
            foreach ($vendorData['transactions'] as $transaction) {
                fputcsv($file, [
                    $vendorData['vendor']->vendorname,
                    date('d-m-Y', strtotime($transaction['date'])),
                    $transaction['reference'],
                    $transaction['description'],
                    $transaction['store'],
                    $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '',
                    $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '',
                    number_format($transaction['running_balance'], 2),
                    $transaction['payment_type'],
                    $transaction['status']
                ]);
            }
        }
        
        fclose($file);
    }, 200, $headers);
}

/**
 * Calculate date range based on selection
 */
private function calculateDateRange($dateRange, $startDate = null, $endDate = null)
{
    $start = null;
    $end = null;
    
    switch ($dateRange) {
        case 'today':
            $start = $end = date('Y-m-d');
            break;
        case 'yesterday':
            $start = $end = date('Y-m-d', strtotime('-1 day'));
            break;
        case 'last_7_days':
            $start = date('Y-m-d', strtotime('-7 days'));
            $end = date('Y-m-d');
            break;
        case 'last_30_days':
            $start = date('Y-m-d', strtotime('-30 days'));
            $end = date('Y-m-d');
            break;
        case 'this_month':
            $start = date('Y-m-01');
            $end = date('Y-m-t');
            break;
        case 'last_month':
            $start = date('Y-m-01', strtotime('first day of last month'));
            $end = date('Y-m-t', strtotime('last day of last month'));
            break;
        case 'this_year':
            $start = date('Y-01-01');
            $end = date('Y-12-31');
            break;
        case 'custom':
            $start = $startDate;
            $end = $endDate;
            break;
        case 'all_time':
        default:
            // No date restriction
            break;
    }
    
    return [
        'start_date' => $start,
        'end_date' => $end
    ];
}






public function index(Request $request)
{
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
    $taxRate = $request->input('tax_rate', '18'); // Default to 18%
    
    // Get company GST details (assuming first record is company details)
    $companyGst = GstDetail::first();
    
    // Get purchase data with GST details filtered by tax rate
    $purchases = $this->getPurchaseData($startDate, $endDate, $taxRate);
    
    // Get available tax rates for dropdown
    $availableTaxRates = $this->getAvailableTaxRates();
    
    return view('reports.gstr3b', compact('purchases', 'startDate', 'endDate', 'companyGst', 'taxRate', 'availableTaxRates'));
}

private function getPurchaseData($startDate, $endDate, $taxRate = '18')
{
    return PurchaseInvoice::with(['purchaseOrder.vendor', 'purchaseInvoiceItems.itemDetails'])
        ->whereBetween('bill_date', [$startDate, $endDate])
        ->whereHas('purchaseOrder.vendor', function($query) {
            $query->whereNotNull('gst');
        })
        ->whereHas('purchaseInvoiceItems', function($query) use ($taxRate) {
            $query->where('tax', 'LIKE', '%' . $taxRate . '%');
        })
        ->get()
        ->map(function($invoice) use ($taxRate) {
            $vendor = $invoice->purchaseOrder->vendor ?? null;
            
            // Get items with the specific tax rate
            $itemsWithTax = $invoice->purchaseInvoiceItems->filter(function($item) use ($taxRate) {
                return strpos($item->tax, $taxRate) !== false;
            });
            
            // Calculate totals for items with specific tax rate only
            $itemTotal = $itemsWithTax->sum('amount');
            $taxRateDecimal = floatval($taxRate) / 100;
            
            // Calculate GST breakdown
            $taxableAmount = $itemTotal / (1 + $taxRateDecimal);
            $gstAmount = $itemTotal - $taxableAmount;
            $cgst = $gstAmount / 2;
            $sgst = $gstAmount / 2;
            
            return [
                'date' => Carbon::parse($invoice->bill_date)->format('d-M-Y'),
                'particulars' => $vendor->vendorname ?? 'Unknown Vendor',
                'party_gstin' => $vendor->gst ?? '',
                'vch_type' => 'GST Purchase',
                'vch_no' => $invoice->bill_no ?? '',
                'doc_no' => $invoice->reference_no ?? $invoice->bill_no ?? '',
                'doc_date' => Carbon::parse($invoice->bill_date)->format('d-M-Y'),
                'taxable_amount' => $taxableAmount,
                'igst' => 0, // For inter-state transactions
                'cgst' => $cgst,
                'sgst' => $sgst,
                'cess' => 0,
                'tax_amount' => $gstAmount,
                'total_amount' => $itemTotal,
                'tax_rate' => $taxRate . '%'
            ];
        })
        ->filter(function($item) {
            // Remove items with zero amounts
            return $item['total_amount'] > 0;
        });
}

private function getAvailableTaxRates()
{
    // Get distinct tax rates from purchase invoice items
    $taxRates = PurchaseInvoiceItem::distinct()
        ->whereNotNull('tax')
        ->where('tax', '!=', '')
        ->pluck('tax')
        ->map(function($tax) {
            // Extract numeric value from tax string (e.g., "GST-18%" -> "18")
            preg_match('/(\d+)/', $tax, $matches);
            return isset($matches[1]) ? $matches[1] : null;
        })
        ->filter()
        ->unique()
        ->sort()
        ->values();
    
    return $taxRates;
}

public function exportExcel(Request $request)
{
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
    $taxRate = $request->input('tax_rate', '18');
    
    $companyGst = GstDetail::first();
    $purchases = $this->getPurchaseData($startDate, $endDate, $taxRate);
    
    return response()->json([
        'company' => $companyGst,
        'period' => [
            'start' => Carbon::parse($startDate)->format('d-M-Y'),
            'end' => Carbon::parse($endDate)->format('d-M-Y')
        ],
        'tax_rate' => $taxRate,
        'data' => $purchases->toArray()
    ]);
}
}