<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Models\SalesInvoice;
use App\Models\Batch;
use App\Models\SalesInvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use App\Models\Employee;
use App\Models\PurchaseOrderItem;
use GuzzleHttp\Client;
use App\Models\GstDetail;
use App\Models\ReturnVoucher;
use App\Models\PurchaseInvoiceItem;
class POS extends Controller
{

    
    /**
     * Get client IP address from request
     */
    private function getClientIpAddress(Request $request)
    {
        // Check for various proxy headers first
        $ipSources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ipSources as $source) {
            if (!empty($_SERVER[$source])) {
                $ip = $_SERVER[$source];
                
                // Handle comma-separated IPs (X-Forwarded-For can contain multiple IPs)
                if (strpos($ip, ',') !== false) {
                    $ips = explode(',', $ip);
                    $ip = trim($ips[0]); // Get the first IP
                }
                
                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        // Fallback to Laravel's built-in method
        $laravelIp = $request->ip();
        
        // If it's localhost/private IP, try to get a more meaningful identifier
        if (in_array($laravelIp, ['127.0.0.1', '::1']) || $this->isPrivateIP($laravelIp)) {
            // For local development, you might want to include hostname or other identifier
            $hostname = gethostname();
            return $laravelIp . ($hostname ? " ({$hostname})" : '');
        }
        
        return $laravelIp;
    }
    
    /**
     * Check if IP address is private/local
     */
    private function isPrivateIP($ip)
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    
    

  
private function getStoreItems($store_id)
{
    if (!$store_id) {
        return collect();
    }
    
    // Get store name from store_id
    $store = \App\Models\Store::find($store_id);
    if (!$store) {
        return collect();
    }
    
    $store_name = $store->store_name;
    
    // Get items that have been purchased for this store (warehouse field contains store name)
    $availableItemIds = PurchaseInvoiceItem::join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
        ->where('purchase_invoices.warehouse', $store_name) // Compare with store name
        ->where('purchase_invoice_items.qty', '>', 0) // Only items with quantity > 0
        ->pluck('purchase_invoice_items.item')
        ->unique();
    
    // Get the actual items
    $items = Item::whereIn('id', $availableItemIds)
        ->where('stock_status', '!=', 'out_of_stock') // Optional: filter out out-of-stock items
        ->get();
        
    // Add current stock quantity to each item
    foreach ($items as $item) {
        $item->current_stock = $this->getCurrentStock($item->id, $store_id);
    }
    
    return $items;
}
//      public function pos_bill(Request $request)
// {
//     $items = collect(); // Initialize empty collection
//     $employee_id = null;
//     $store_id = null;

//     // Get logged-in user details
//     $role = Session::get('role');
//     $loginId = Session::get('loginId');

//     if ($role === 'employee' || $role === 'manager') {
//         // Get employee/manager details including store_id
//         $employee = Employee::find($loginId);
//         if ($employee) {
//             $employee_id = $employee->id;
//             $store_id = $employee->store_id;

//             // Fetch regular items and batch items for this store
//             $items = $this->getItemsWithBatches($store_id);
//         }
//     } elseif ($role === 'admin') {
//         // Admin can see all items except inactive and out_of_stock
//         $requested_store = $request->query('store_id');
//         if ($requested_store) {
//             $store_id = $requested_store;
//             $items = $this->getItemsWithBatches($store_id);
//         } else {
//             // For admin viewing all items, get combined items from all stores
//             $items = $this->getAllItemsWithBatches();
//         }
//     }

//     $customer_name = $request->query('name');
//     $customer_contact = $request->query('contact');

//     return view('pos.bill', compact(
//         'items', 
//         'customer_name', 
//         'customer_contact', 
//         'employee_id', 
//         'store_id'
//     ));
// }





public function getItemsForPos(Request $request)
{
    set_time_limit(300);
    ini_set('memory_limit', '512M');
    
    $role = Session::get('role');
    $loginId = Session::get('loginId');
    $store_id = null;
    $chunk = $request->input('chunk', 0);
    $chunkSize = 500;

    if ($role === 'employee' || $role === 'manager') {
        $employee = Employee::find($loginId);
        if ($employee) {
            $store_id = $employee->store_id;
        }
        
        if (!$store_id) {
            return response()->json([
                'status' => false,
                'message' => 'Store not found for employee/manager',
                'data' => [],
                'has_more' => false,
                'total_count' => 0
            ]);
        }
    } elseif ($role === 'admin') {
        $requested_store = $request->input('store_id');
        if ($requested_store) {
            $store_id = $requested_store;
        }
    }

    try {
        if ($role === 'admin' && !$store_id) {
            $result = $this->getAllItemsWithBatchesChunked($chunk, $chunkSize);
        } else {
            $result = $this->getItemsWithBatchesChunked($store_id, $chunk, $chunkSize);
        }

        return response()->json([
            'status' => true,
            'data' => $result['items'],
            'has_more' => $result['has_more'],
            'total_count' => $result['total_count'],
            'current_chunk' => $chunk
        ]);
    } catch (\Exception $e) {
        Log::error('Get Items Error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Error loading items',
            'data' => [],
            'has_more' => false
        ], 500);
    }
}
private function getItemsWithBatchesChunked($store_id, $chunk = 0, $chunkSize = 500)
{
    $offset = $chunk * $chunkSize;
    $allItems = collect();

    // Get total count first
    $totalRegularItems = Item::where('store_id', $store_id)
        ->whereNotIn('stock_status', ['out_of_stock', 'Inactive'])
        ->whereNotIn('item_type',['transfer'])
        ->count();
    
    $totalBatchItems = Batch::whereHas('item', function($query) use ($store_id) {
        $query->where('store_id', $store_id)
              ->whereNotIn('stock_status', ['out_of_stock', 'Inactive']);
    })
    ->where('qty', '>', 0)
    ->count();
    
    $totalCount = $totalRegularItems + $totalBatchItems;

    // 1. Get regular items - chunked
    $regularItems = Item::select('id', 'item_code', 'item_name', 'measure_unit', 'sales_price', 'mrp', 'gst_rate', 'discount', 'store_id', 'opening_stock')
        ->where('store_id', $store_id)
        ->whereNotIn('stock_status', ['out_of_stock', 'Inactive'])
        ->whereNotIn('item_type',['transfer'])
        ->skip($offset)
        ->take($chunkSize)
        ->get();

    // OPTIMIZATION: Bulk calculate stock for all items at once
    $itemIds = $regularItems->pluck('id')->toArray();
    $stockData = $this->getBulkCurrentStock($itemIds, $store_id);

    foreach ($regularItems as $item) {
        $item->current_stock = $stockData[$item->id] ?? 0;
        $item->item_type = 'regular';
        $item->display_name = $item->item_name;
        $item->unique_identifier = 'item_' . $item->id;
        
        if (empty($item->barcode)) {
            $item->barcode = $item->item_code;
        }
        
        $allItems->push($item);
    }

    // 2. Get batch items - only if we have space in chunk
    $remainingSpace = $chunkSize - $regularItems->count();
    if ($remainingSpace > 0) {
        $batchOffset = max(0, $offset - $totalRegularItems);
        
        $batchItems = Batch::select('id', 'item_id', 'item_code', 'batch_no', 'price', 'qty', 'mfg_date', 'exp_date')
            ->with(['item:id,item_name,measure_unit,gst_rate,discount,store_id,mrp'])
            ->whereHas('item', function($query) use ($store_id) {
                $query->where('store_id', $store_id)
                      ->whereNotIn('stock_status', ['out_of_stock', 'Inactive']);
            })
            ->where('qty', '>', 0)
            ->skip($batchOffset)
            ->take($remainingSpace)
            ->get();

        foreach ($batchItems as $batch) {
            $batchAsItem = new \stdClass();
            $batchAsItem->id = $batch->item->id;
            $batchAsItem->batch_id = $batch->id;
            $batchAsItem->item_code = $batch->item_code;
            $batchAsItem->item_name = $batch->item->item_name;
            $batchAsItem->display_name = $batch->item->item_name . ' (Batch: ' . $batch->batch_no . ')';
            $batchAsItem->batch_no = $batch->batch_no;
            $batchAsItem->measure_unit = $batch->item->measure_unit;
            $batchAsItem->sales_price = $batch->price;
            $batchAsItem->mrp = $batch->item->mrp;
            $batchAsItem->gst_rate = $batch->item->gst_rate;
            $batchAsItem->discount = $batch->item->discount;
            $batchAsItem->current_stock = $batch->qty;
            $batchAsItem->item_type = 'batch';
            $batchAsItem->mfg_date = $batch->mfg_date;
            $batchAsItem->exp_date = $batch->exp_date;
            $batchAsItem->unique_identifier = 'batch_' . $batch->id;
            $batchAsItem->barcode = 'BATCH_' . $batch->id . '_' . $batch->batch_no;
            
            $allItems->push($batchAsItem);
        }
    }

    $hasMore = ($offset + $allItems->count()) < $totalCount;

    return [
        'items' => $allItems,
        'has_more' => $hasMore,
        'total_count' => $totalCount
    ];
}
private function getAllItemsWithBatchesChunked($chunk = 0, $chunkSize = 500)
{
    $offset = $chunk * $chunkSize;
    $allItems = collect();

    // Get total count
    $totalRegularItems = Item::whereNotIn('stock_status', ['out_of_stock', 'Inactive'])
                               ->whereNotIn('item_type',['transfer'])
                               ->count();
    $totalBatchItems = Batch::whereHas('item', function($query) {
        $query->whereNotIn('stock_status', ['out_of_stock', 'Inactive']);
    })
    ->where('qty', '>', 0)
    ->count();
    
    $totalCount = $totalRegularItems + $totalBatchItems;

    // 1. Get regular items - chunked
    $regularItems = Item::select('id', 'item_code', 'item_name', 'measure_unit', 'sales_price', 'mrp', 'gst_rate', 'discount', 'opening_stock')
        ->whereNotIn('stock_status', ['out_of_stock', 'Inactive'])
        ->whereNotIn('item_type',['transfer'])
        ->skip($offset)
        ->take($chunkSize)
        ->get();
    
    // OPTIMIZATION: Bulk calculate combined stock for all items at once
    $itemIds = $regularItems->pluck('id')->toArray();
    $stockData = $this->getBulkCombinedStock($itemIds);

    foreach ($regularItems as $item) {
        $item->current_stock = $stockData[$item->id] ?? 0;
        $item->item_type = 'regular';
        $item->display_name = $item->item_name;
        $item->unique_identifier = 'item_' . $item->id;
        
        if (empty($item->barcode)) {
            $item->barcode = $item->item_code;
        }
        
        $allItems->push($item);
    }

    // 2. Get batch items
    $remainingSpace = $chunkSize - $regularItems->count();
    if ($remainingSpace > 0) {
        $batchOffset = max(0, $offset - $totalRegularItems);
        
        $batchItems = Batch::select('id', 'item_id', 'item_code', 'batch_no', 'price', 'qty', 'mfg_date', 'exp_date')
            ->with(['item:id,item_name,measure_unit,gst_rate,discount,mrp'])
            ->whereHas('item', function($query) {
                $query->whereNotIn('stock_status', ['out_of_stock', 'Inactive']);
            })
            ->where('qty', '>', 0)
            ->skip($batchOffset)
            ->take($remainingSpace)
            ->get();

        foreach ($batchItems as $batch) {
            $batchAsItem = new \stdClass();
            $batchAsItem->id = $batch->item->id;
            $batchAsItem->batch_id = $batch->id;
            $batchAsItem->item_code = $batch->item_code;
            $batchAsItem->item_name = $batch->item->item_name;
            $batchAsItem->display_name = $batch->item->item_name . ' (Batch: ' . $batch->batch_no . ')';
            $batchAsItem->batch_no = $batch->batch_no;
            $batchAsItem->measure_unit = $batch->item->measure_unit;
            $batchAsItem->sales_price = $batch->price;
            $batchAsItem->mrp = $batch->item->mrp;
            $batchAsItem->gst_rate = $batch->item->gst_rate;
            $batchAsItem->discount = $batch->item->discount;
            $batchAsItem->current_stock = $batch->qty;
            $batchAsItem->item_type = 'batch';
            $batchAsItem->mfg_date = $batch->mfg_date;
            $batchAsItem->exp_date = $batch->exp_date;
            $batchAsItem->unique_identifier = 'batch_' . $batch->id;
            $batchAsItem->barcode = 'BATCH_' . $batch->id . '_' . $batch->batch_no;
            
            $allItems->push($batchAsItem);
        }
    }

    $hasMore = ($offset + $allItems->count()) < $totalCount;

    return [
        'items' => $allItems,
        'has_more' => $hasMore,
        'total_count' => $totalCount
    ];
}
private function getBulkCurrentStock($itemIds, $store_id)
{
    if (empty($itemIds)) {
        return [];
    }

    // Get opening stock for all items
    $openingStocks = Item::whereIn('id', $itemIds)
        ->pluck('opening_stock', 'id')
        ->toArray();

    // Get store name once
    $store_name = \App\Models\Store::where('id', $store_id)->value('store_name');

    // Get total purchased for all items in ONE query
    $purchasedData = [];
    if ($store_name) {
        $purchasedData = PurchaseInvoiceItem::join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->where('purchase_invoices.warehouse', $store_name)
            ->whereIn('purchase_invoice_items.item', $itemIds)
            ->select('purchase_invoice_items.item', DB::raw('SUM(purchase_invoice_items.qty) as total_qty'))
            ->groupBy('purchase_invoice_items.item')
            ->pluck('total_qty', 'item')
            ->toArray();
    }

    // Get total sold for all items in ONE query
    $soldData = DB::table('sales_invoice_items')
        ->join('sales_invoices', 'sales_invoice_items.salesinvoice_id', '=', 'sales_invoices.id')
        ->whereIn('sales_invoice_items.item_id', $itemIds)
        ->where('sales_invoices.store_id', $store_id)
        ->select('sales_invoice_items.item_id', DB::raw('SUM(sales_invoice_items.qty) as total_qty'))
        ->groupBy('sales_invoice_items.item_id')
        ->pluck('total_qty', 'item_id')
        ->toArray();

    // Calculate stock for all items (SAME CALCULATION AS BEFORE)
    $stockData = [];
    foreach ($itemIds as $itemId) {
        $opening = $openingStocks[$itemId] ?? 0;
        $purchased = $purchasedData[$itemId] ?? 0;
        $sold = $soldData[$itemId] ?? 0;
        
        $stockData[$itemId] = $opening + $purchased - $sold;
    }

    return $stockData;
}
private function getBulkCombinedStock($itemIds)
{
    if (empty($itemIds)) {
        return [];
    }

    // Get opening stock for all items
    $openingStocks = Item::whereIn('id', $itemIds)
        ->pluck('opening_stock', 'id')
        ->toArray();

    // Get total purchased for all items in ONE query (from all stores)
    $purchasedData = PurchaseInvoiceItem::join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
        ->whereIn('purchase_invoice_items.item', $itemIds)
        ->select('purchase_invoice_items.item', DB::raw('SUM(purchase_invoice_items.qty) as total_qty'))
        ->groupBy('purchase_invoice_items.item')
        ->pluck('total_qty', 'item')
        ->toArray();

    // Get total sold for all items in ONE query (from all stores)
    $soldData = DB::table('sales_invoice_items')
        ->join('sales_invoices', 'sales_invoice_items.salesinvoice_id', '=', 'sales_invoices.id')
        ->whereIn('sales_invoice_items.item_id', $itemIds)
        ->select('sales_invoice_items.item_id', DB::raw('SUM(sales_invoice_items.qty) as total_qty'))
        ->groupBy('sales_invoice_items.item_id')
        ->pluck('total_qty', 'item_id')
        ->toArray();

    // Calculate stock for all items (SAME CALCULATION AS BEFORE)
    $stockData = [];
    foreach ($itemIds as $itemId) {
        $opening = $openingStocks[$itemId] ?? 0;
        $purchased = $purchasedData[$itemId] ?? 0;
        $sold = $soldData[$itemId] ?? 0;
        
        $stockData[$itemId] = $opening + $purchased - $sold;
    }

    return $stockData;
}

/**
 * Optimized stock calculation - cached per request
 */
private function getCurrentStockOptimized($item_id, $store_id)
{
    static $stockCache = [];
    $cacheKey = $item_id . '_' . $store_id;
    
    if (isset($stockCache[$cacheKey])) {
        return $stockCache[$cacheKey];
    }
    
    $stock = $this->getCurrentStock($item_id, $store_id);
    $stockCache[$cacheKey] = $stock;
    
    return $stock;
}
public function pos_bill(Request $request)
{
    $employee_id = null;
    $store_id = null;

    // Get logged-in user details
    $role = Session::get('role');
    $loginId = Session::get('loginId');

    if ($role === 'employee' || $role === 'manager') {
        $employee = Employee::find($loginId);
        if ($employee) {
            $employee_id = $employee->id;
            $store_id = $employee->store_id;
        }
    } elseif ($role === 'admin') {
        $requested_store = $request->query('store_id');
        if ($requested_store) {
            $store_id = $requested_store;
        }
    }

    $customer_name = $request->query('name');
    $customer_contact = $request->query('contact');

    // Don't load items here - pass empty collection
    $items = collect();

    return view('pos.bill', compact(
        'items', 
        'customer_name', 
        'customer_contact', 
        'employee_id', 
        'store_id'
    ));
}

private function getCombinedStockOptimized($item_id)
{
    static $stockCache = [];
    
    if (isset($stockCache[$item_id])) {
        return $stockCache[$item_id];
    }
    
    $stock = $this->getCombinedStock($item_id);
    $stockCache[$item_id] = $stock;
    
    return $stock;
}

private function getItemsWithBatches($store_id)
{
    $allItems = collect();

    // 1. Get regular items (existing logic)
    $regularItems = Item::where('store_id', $store_id)
                       ->whereNotIn('stock_status', ['out_of_stock', 'Inactive'])
                       ->get();

    // Add current stock to regular items
    foreach ($regularItems as $item) {
        $item->current_stock = $this->getCurrentStock($item->id, $store_id);
        $item->item_type = 'regular'; // Flag to identify item type
        $item->display_name = $item->item_name;
        $item->unique_identifier = 'item_' . $item->id;
        
        // For regular items, use item_code for barcode scanning
        if (empty($item->barcode)) {
            $item->barcode = $item->item_code;
        }
        
        $allItems->push($item);
    }

    // 2. Get batch items for items in this store
    $batchItems = Batch::with('item')
                      ->whereHas('item', function($query) use ($store_id) {
                          $query->where('store_id', $store_id)
                                ->whereNotIn('stock_status', ['out_of_stock', 'Inactive']);
                      })
                      ->where('qty', '>', 0) // Only batches with remaining quantity
                      ->get();

    // Process batch items
    foreach ($batchItems as $batch) {
        // Create a pseudo-item for each batch
        $batchAsItem = new \stdClass();
        $batchAsItem->id = $batch->item->id;
        $batchAsItem->batch_id = $batch->id;
        $batchAsItem->item_code = $batch->item_code;
        $batchAsItem->item_name = $batch->item->item_name;
        $batchAsItem->display_name = $batch->item->item_name . ' (Batch: ' . $batch->batch_no . ')';
        $batchAsItem->batch_no = $batch->batch_no;
        $batchAsItem->measure_unit = $batch->item->measure_unit;
        $batchAsItem->sales_price = $batch->price; // Use batch price
        $batchAsItem->gst_rate = $batch->item->gst_rate;
        $batchAsItem->discount = $batch->item->discount;
        $batchAsItem->current_stock = $batch->qty; // Use batch quantity
        $batchAsItem->item_type = 'batch'; // Flag to identify item type
        $batchAsItem->mfg_date = $batch->mfg_date;
        $batchAsItem->exp_date = $batch->exp_date;
        $batchAsItem->unique_identifier = 'batch_' . $batch->id;
        
        // For batch items, create a unique barcode pattern
        $batchAsItem->barcode = 'BATCH_' . $batch->id . '_' . $batch->batch_no;
        
        $allItems->push($batchAsItem);
    }

    return $allItems;
}


private function getAllItemsWithBatches()
{
    $allItems = collect();

    // 1. Get all regular items
    $regularItems = Item::whereNotIn('stock_status', ['out_of_stock', 'Inactive'])->get();
    
    foreach ($regularItems as $item) {
        $item->current_stock = $this->getCombinedStock($item->id);
        $item->item_type = 'regular';
        $item->display_name = $item->item_name;
        $item->unique_identifier = 'item_' . $item->id;
        
        if (empty($item->barcode)) {
            $item->barcode = $item->item_code;
        }
        
        $allItems->push($item);
    }

    // 2. Get all batch items
    $batchItems = Batch::with('item')
                      ->whereHas('item', function($query) {
                          $query->whereNotIn('stock_status', ['out_of_stock', 'Inactive']);
                      })
                      ->where('qty', '>', 0)
                      ->get();

    foreach ($batchItems as $batch) {
        $batchAsItem = new \stdClass();
        $batchAsItem->id = $batch->item->id;
        $batchAsItem->batch_id = $batch->id;
        $batchAsItem->item_code = $batch->item_code;
        $batchAsItem->item_name = $batch->item->item_name;
        $batchAsItem->display_name = $batch->item->item_name . ' (Batch: ' . $batch->batch_no . ')';
        $batchAsItem->batch_no = $batch->batch_no;
        $batchAsItem->measure_unit = $batch->item->measure_unit;
        $batchAsItem->sales_price = $batch->price;
        $batchAsItem->gst_rate = $batch->item->gst_rate;
        $batchAsItem->discount = $batch->item->discount;
        $batchAsItem->current_stock = $batch->qty;
        $batchAsItem->item_type = 'batch';
        $batchAsItem->mfg_date = $batch->mfg_date;
        $batchAsItem->exp_date = $batch->exp_date;
        $batchAsItem->unique_identifier = 'batch_' . $batch->id;
        $batchAsItem->barcode = 'BATCH_' . $batch->id . '_' . $batch->batch_no;
        
        $allItems->push($batchAsItem);
    }

    return $allItems;
}


private function getCurrentStock($item_id, $store_id)
{
    $item = Item::find($item_id);
    $openingStock = $item->opening_stock ?? 0;
    
    $store_name = \App\Models\Store::where('id', $store_id)->value('store_name');
    
    $totalPurchased = 0;
    if ($store_name) {
        $totalPurchased = PurchaseInvoiceItem::join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->where('purchase_invoices.warehouse', $store_name)
            ->where('purchase_invoice_items.item', $item_id)
            ->sum('purchase_invoice_items.qty');
    }
    
    $totalSold = DB::table('sales_invoice_items')
        ->join('sales_invoices', 'sales_invoice_items.salesinvoice_id', '=', 'sales_invoices.id')
        ->where('sales_invoice_items.item_id', $item_id)
        ->where('sales_invoices.store_id', $store_id)
        ->sum('sales_invoice_items.qty');
    
    return $openingStock + $totalPurchased - $totalSold;
}

private function getCombinedStock($item_id)
{
    $item = Item::find($item_id);
    $openingStock = $item->opening_stock ?? 0;
    
    $totalPurchased = PurchaseInvoiceItem::join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
        ->where('purchase_invoice_items.item', $item_id)
        ->sum('purchase_invoice_items.qty');
    
    $totalSold = DB::table('sales_invoice_items')
        ->join('sales_invoices', 'sales_invoice_items.salesinvoice_id', '=', 'sales_invoices.id')
        ->where('sales_invoice_items.item_id', $item_id)
        ->sum('sales_invoice_items.qty');
    
    return $openingStock + $totalPurchased - $totalSold;
}

    /**
     * Check if customer exists and return customer details
     */
    public function checkCustomer($contact)
    {
        $customer = Customer::where('contact', $contact)->first();
        $loyaltySettings = LoyaltyPoint::first();

        if ($customer) {
            return response()->json([
                'status' => true,
                'name' => $customer->name,
                'loyalty_points' => $customer->loyalty_points ?? 0,
                'redeem_amt' => $loyaltySettings->redeem_amt ?? 1
            ]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    /**
     * Add new customer
     */
    public function addCustomer(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'contact' => 'required|digits_between:6,15|unique:customers,contact',
            ]);

            $customer = Customer::create([
                'name' => $request->name,
                'contact' => $request->contact,
                'loyalty_points' => 0
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Customer added successfully',
                'name' => $customer->name,
                'loyalty_points' => $customer->loyalty_points
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function checkGiftCardBalance(Request $request)
{
    try {
        $request->validate([
            'card_number' => 'required|string'
        ]);

        $cardNumber = $request->input('card_number');

        // First check if it's a gift card
        $giftCard = \App\Models\GiftCard::where('card_code', $cardNumber)->first();
        
        if ($giftCard) {
            // Calculate used amount from sales_invoices
            $usedAmount = \App\Models\SalesInvoice::where('gift_card_code', $cardNumber)
                ->sum('gift_card_amount');
                
            $availableAmount = $giftCard->card_value - $usedAmount;
            
            // Check if card is expired
            $isExpired = $giftCard->expiry_date < now();
            $status = $isExpired ? 'Expired' : 'Active';
            
            if ($isExpired) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gift card has expired'
                ]);
            }
            
            if ($availableAmount <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gift card balance is zero'
                ]);
            }
            
            return response()->json([
                'status' => true,
                'card_type' => 'gift_card',
                'available_amount' => number_format($availableAmount, 2),
                'max_redeemable' => number_format($availableAmount, 2),
                'card_status' => $status,
                'expiry_date' => $giftCard->expiry_date->format('d-m-Y'),
                'applicable_products' => 'Valid for all products'
            ]);
        }

        // Check if it's a voucher
        $voucher = \App\Models\Voucher::where('voucher_code', $cardNumber)->first();
        
        if ($voucher) {
            // Calculate used amount from sales_invoices
            $usedAmount = \App\Models\SalesInvoice::where('voucher_code', $cardNumber)
                ->sum('voucher_amount');
                
            $availableAmount = $voucher->discount_value - $usedAmount;
            
            // Check if voucher is expired
            $isExpired = $voucher->expiry_date < now();
            $status = $isExpired ? 'Expired' : 'Active';
            
            if ($isExpired) {
                return response()->json([
                    'status' => false,
                    'message' => 'Voucher has expired'
                ]);
            }
            
            if ($availableAmount <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Voucher has been fully used'
                ]);
            }
            
            // Build applicable products message
            $applicableProducts = 'Valid for ';
            if ($voucher->redeemable_brand) {
                $applicableProducts .= $voucher->redeemable_brand . ' products';
            } elseif ($voucher->redeemable_category) {
                $applicableProducts .= $voucher->redeemable_category . ' category';
            } elseif ($voucher->redeemable_subcategory) {
                $applicableProducts .= $voucher->redeemable_subcategory . ' subcategory';
            } elseif ($voucher->redeemable_item) {
                $applicableProducts .= 'specific items only';
            } else {
                $applicableProducts = 'Valid for all products';
            }
            
            return response()->json([
                'status' => true,
                'card_type' => 'voucher',
                'available_amount' => number_format($availableAmount, 2),
                'max_redeemable' => number_format($availableAmount, 2),
                'card_status' => $status,
                'expiry_date' => $voucher->expiry_date->format('d-m-Y'),
                'applicable_products' => $applicableProducts
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid gift card or voucher number'
        ]);

    } catch (\Exception $e) {
        Log::error('Check Gift Card Balance Error: ' . $e->getMessage());
        
        return response()->json([
            'status' => false,
            'message' => 'Error checking card balance'
        ], 500);
    }
}

/**
 * Modified Save Bill method to include gift card data
 */

public function saveBill(Request $request)
{
    try {
        // Updated validation rules - customer fields are now optional
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_id' => 'nullable|exists:batches,id',
            'items.*.unit' => 'required|string|max:50',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.tax' => 'nullable|numeric|min:0|max:100',
            'items.*.amount' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'total_discount' => 'required|numeric|min:0',
            'total_tax' => 'required|numeric|min:0',
            'additional_charges' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'received_amount' => 'required|numeric|min:0',
            'mode_of_payment' => 'required|string|in:Cash,Online,Card,UPI,Both',
            'cash_amount' => 'nullable|numeric|min:0',
            'online_amount' => 'nullable|numeric|min:0',
            'loyalty_points_used' => 'nullable|numeric|min:0',
            
            // Gift card fields - UPDATED to include return_voucher
            'applied_gift_cards' => 'nullable|array',
            'applied_gift_cards.*.card_number' => 'required_with:applied_gift_cards|string',
            'applied_gift_cards.*.card_type' => 'required_with:applied_gift_cards|in:gift_card,voucher,return_voucher', // <-- CHANGED
            'applied_gift_cards.*.redeem_amount' => 'required_with:applied_gift_cards|numeric|min:0',
            'total_gift_card_discount' => 'nullable|numeric|min:0',
        ]);

        // Get employee, store information and IP address
        $role = Session::get('role');
        $loginId = Session::get('loginId');
        $employee_id = null;
        $store_id = null;
        $pos_ipaddress = $this->getClientIpAddress($request);

        if (in_array($role, ['employee', 'manager'])) {
            $employee = Employee::find($loginId);
            if ($employee) {
                $employee_id = $employee->id;
                $store_id = $employee->store_id;
            }
        } elseif ($role === 'admin') {
            $store_id = $request->input('store_id');
        }

        DB::beginTransaction();

        // Handle customer - create/find only if customer data is provided
        $customer = null;
        $customer_id = null;
        $usedPoints = 0;
        $earnedPoints = 0;
        
        if (!empty($validated['customer_contact'])) {
            // Customer data provided, find or create customer
            $customer = Customer::where('contact', $validated['customer_contact'])->first();
            
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $validated['customer_name'] ?? 'Walk-in Customer',
                    'contact' => $validated['customer_contact'],
                    'loyalty_points' => 0
                ]);
            } else {
                // Update customer name if provided and different
                if (!empty($validated['customer_name']) && $customer->name !== $validated['customer_name']) {
                    $customer->update(['name' => $validated['customer_name']]);
                }
            }
            
            $customer_id = $customer->id;
            $usedPoints = $validated['loyalty_points_used'] ?? 0;

            // Validate loyalty points
            if ($usedPoints > $customer->loyalty_points) {
                throw new \Exception('Insufficient loyalty points. Customer has only ' . $customer->loyalty_points . ' points.');
            }

            // Calculate earned loyalty points
            $loyaltySettings = LoyaltyPoint::first();
            $earnAmt = $loyaltySettings->earn_amt ?? 100;
            $earnPointsRate = $loyaltySettings->earn_points ?? 1;
            $earnedPoints = floor($validated['grand_total'] / $earnAmt) * $earnPointsRate;
        }

        // Process gift cards/vouchers/return vouchers - UPDATED
        $giftCardCode = null;
        $giftCardAmount = 0;
        $voucherCode = null;
        $voucherAmount = 0;
        $returnVoucherCode = null;
        $returnVoucherAmount = 0;

        if (!empty($validated['applied_gift_cards'])) {
            foreach ($validated['applied_gift_cards'] as $appliedCard) {
                if ($appliedCard['card_type'] === 'gift_card') {
                    $giftCardCode = $appliedCard['card_number'];
                    $giftCardAmount += $appliedCard['redeem_amount'];
                } elseif ($appliedCard['card_type'] === 'voucher') {
                    $voucherCode = $appliedCard['card_number'];
                    $voucherAmount += $appliedCard['redeem_amount'];
                } elseif ($appliedCard['card_type'] === 'return_voucher') {
                    // NEW: Handle return voucher
                    $returnVoucherCode = $appliedCard['card_number'];
                    $returnVoucherAmount += $appliedCard['redeem_amount'];
                    
                    // Mark return voucher as used
                    $returnVoucher = ReturnVoucher::where('voucher_code', $appliedCard['card_number'])->first();
                    if ($returnVoucher && !$returnVoucher->is_used) {
                        $returnVoucher->is_used = true;
                        $returnVoucher->save();
                    }
                }
            }
        }

        // Create sales invoice with optional customer
        $invoice = SalesInvoice::create([
            'customer_id' => $customer_id,
            'employee_id' => $employee_id,
            'store_id' => $store_id,
            'pos_ipaddress' => $pos_ipaddress,
            'sub_total' => $validated['sub_total'],
            'total_discount' => $validated['total_discount'],
            'total_tax' => $validated['total_tax'],
            'additional_charges' => $validated['additional_charges'],
            'grand_total' => $validated['grand_total'],
            'received_amount' => $validated['received_amount'],
            'mode_of_payment' => $validated['mode_of_payment'],
            'cash_amount' => $validated['cash_amount'] ?? null,
            'online_amount' => $validated['online_amount'] ?? null,
            'loyalty_points_used' => $usedPoints,
            'loyalty_points_earned' => $earnedPoints,
            
            // Gift card fields
            'gift_card_code' => $giftCardCode,
            'gift_card_amount' => $giftCardAmount,
            'voucher_code' => $voucherCode,
            'voucher_amount' => $voucherAmount,
            'return_voucher_code' => $returnVoucherCode, // NEW field - add to migration if needed
            'return_voucher_amount' => $returnVoucherAmount, // NEW field - add to migration if needed
            'total_gift_card_discount' => $validated['total_gift_card_discount'] ?? 0,
            
            'invoice_date' => now(),
            'status' => 'completed'
        ]);

        foreach ($validated['items'] as $itemData) {
            // Validate stock before processing
            if (!empty($itemData['batch_id'])) {
                $batch = Batch::find($itemData['batch_id']);
                if (!$batch) {
                    throw new \Exception("Batch not found for item ID: {$itemData['item_id']}");
                }
                if ($batch->qty < $itemData['qty']) {
                    throw new \Exception("Insufficient batch stock for item: {$batch->item->item_name} (Batch: {$batch->batch_no}). Available: {$batch->qty}, Requested: {$itemData['qty']}");
                }
            }

            // Create the sales invoice item
            $salesInvoiceItem = SalesInvoiceItem::create([
                'salesinvoice_id' => $invoice->id,
                'item_id' => $itemData['item_id'],
                'batch_id' => $itemData['batch_id'] ?? null,
                'unit' => $itemData['unit'],
                'qty' => $itemData['qty'],
                'price' => $itemData['price'],
                'discount' => $itemData['discount'] ?? 0,
                'tax' => $itemData['tax'] ?? 0,
                'amount' => $itemData['amount']
            ]);

            // Update stock
            if (!empty($itemData['batch_id'])) {
                $batch = Batch::find($itemData['batch_id']);
                if ($batch) {
                    $batch->qty -= $itemData['qty'];
                    $batch->save();
                    
                    $item = $batch->item;
                    if ($item && method_exists($item, 'updateCurrentStock')) {
                        $item->updateCurrentStock();
                    }
                }
            } else {
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    // Add your regular item stock reduction logic
                }
            }
        }

        // Update customer's loyalty points only if customer exists
        $newLoyaltyPoints = 0;
        if ($customer) {
            $newLoyaltyPoints = $customer->loyalty_points - $usedPoints + $earnedPoints;
            $customer->update(['loyalty_points' => max(0, $newLoyaltyPoints)]);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Bill saved successfully.',
            'data' => [
                'invoice_id' => $invoice->id,
                'invoice_date' => $invoice->invoice_date->format('Y-m-d H:i:s'),
                'customer_name' => $customer ? $customer->name : 'Walk-in Customer',
                'customer_contact' => $customer ? $customer->contact : 'N/A',
                'grand_total' => $invoice->grand_total,
                'loyalty_points_earned' => $earnedPoints,
                'loyalty_points_used' => $usedPoints,
                'new_loyalty_balance' => $newLoyaltyPoints,
                'gift_card_discount' => $validated['total_gift_card_discount'] ?? 0,
                'employee_id' => $employee_id,
                'store_id' => $store_id,
                'pos_ipaddress' => $pos_ipaddress,
                'processed_by' => $role
            ]
        ], 200);

    } catch (ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Save Bill Error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
            'employee_id' => $employee_id ?? 'N/A',
            'store_id' => $store_id ?? 'N/A',
            'pos_ipaddress' => $this->getClientIpAddress($request),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong while saving the bill.',
            'error' => $e->getMessage()
        ], 500);
    }
}




    /**
     * Get all sales invoices with employee, store and IP information
     */
    public function getSalesInvoices()
    {
        $invoices = SalesInvoice::with([
            'customer', 
            'items.item', 
            'employee:id,empname,empcode',  
            'store:id,store_name'          
        ])
        ->select([
            'id', 'customer_id', 'employee_id', 'store_id', 
            'pos_ipaddress', // 🆕 Include IP address in selection
            'sub_total', 'total_discount', 'total_tax', 'additional_charges',
            'grand_total', 'received_amount', 'mode_of_payment',
            'loyalty_points_used', 'loyalty_points_earned', 'invoice_date',
            'status', 'created_at', 'updated_at'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'status' => true,
            'data' => $invoices
        ]);
    }

    /**
     * Get specific invoice details with employee, store and IP information
     */
    public function getInvoiceDetails($id)
    {
        $invoice = SalesInvoice::with([
            'customer', 
            'items.item',
            'employee:id,empname,empcode',  
            'store:id,store_name'          
        ])
        ->select([
            'id', 'customer_id', 'employee_id', 'store_id', 
            'pos_ipaddress', // 🆕 Include IP address
            'sub_total', 'total_discount', 'total_tax', 'additional_charges',
            'grand_total', 'received_amount', 'mode_of_payment',
            'loyalty_points_used', 'loyalty_points_earned', 'invoice_date',
            'status', 'created_at', 'updated_at'
        ])
        ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $invoice
        ]);
    }

    /**
     * Get sales report by employee with IP tracking
     */
    public function getSalesByEmployee(Request $request)
    {
        $employee_id = $request->input('employee_id');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = SalesInvoice::with(['customer', 'employee:id,empname', 'store:id,store_name'])
            ->select([
                'id', 'customer_id', 'employee_id', 'store_id', 
                'pos_ipaddress', // 🆕 Include IP address
                'grand_total', 'mode_of_payment', 'invoice_date',
                'status', 'created_at'
            ]);

        if ($employee_id) {
            $query->where('employee_id', $employee_id);
        }

        if ($date_from) {
            $query->whereDate('invoice_date', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('invoice_date', '<=', $date_to);
        }

        $sales = $query->orderBy('invoice_date', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $sales
        ]);
    }

    /**
     * Get sales report by store with IP tracking
     */
    public function getSalesByStore(Request $request)
    {
        $store_id = $request->input('store_id');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = SalesInvoice::with(['customer', 'employee:id,empname', 'store:id,store_name'])
            ->select([
                'id', 'customer_id', 'employee_id', 'store_id', 
                'pos_ipaddress', // 🆕 Include IP address
                'grand_total', 'mode_of_payment', 'invoice_date',
                'status', 'created_at'
            ]);

        if ($store_id) {
            $query->where('store_id', $store_id);
        }

        if ($date_from) {
            $query->whereDate('invoice_date', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('invoice_date', '<=', $date_to);
        }

        $sales = $query->orderBy('invoice_date', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $sales
        ]);
    }

    /**
     * Get sales report by IP address (new method for IP-based analysis)
     */
    public function getSalesByIP(Request $request)
    {
        $pos_ipaddress = $request->input('pos_ipaddress');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = SalesInvoice::with(['customer', 'employee:id,empname', 'store:id,store_name'])
            ->select([
                'id', 'customer_id', 'employee_id', 'store_id', 
                'pos_ipaddress', 'grand_total', 'mode_of_payment', 
                'invoice_date', 'status', 'created_at'
            ]);

        if ($pos_ipaddress) {
            $query->where('pos_ipaddress', 'LIKE', "%{$pos_ipaddress}%");
        }

        if ($date_from) {
            $query->whereDate('invoice_date', '>=', $date_from);
        }

        if ($date_to) {
            $query->whereDate('invoice_date', '<=', $date_to);
        }

        $sales = $query->orderBy('invoice_date', 'desc')->get();

        // Group by IP address for summary
        $ipSummary = $sales->groupBy('pos_ipaddress')->map(function ($group) {
            return [
                'ip_address' => $group->first()->pos_ipaddress,
                'total_transactions' => $group->count(),
                'total_amount' => $group->sum('grand_total'),
                'transactions' => $group
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $sales,
            'ip_summary' => $ipSummary->values()
        ]);
    }
            public function gst_verify(Request $request)
    {
        $request->validate([
            'gst_no' => 'required'
        ]);
       
        $group_id = 'ec33ab9a-6ebb-46a7-b87d-3966673f1214';
        $task_id = '8abc6431-fc08-4594-bc8d-090df206f15c';
        $gst_number = $request->gst_no;
        $is_details = true;

        $payload = [
            'group_id' => $group_id,
            'task_id' => $task_id,
            'data' => [
                'gstnumber' => $gst_number,
                'isdetails' => $is_details
            ]
        ];

        $client = new Client();

        try {
            // Step 1: Send GST request
            $response = $client->post('https://eve.idfy.com/v3/tasks/async/retrieve/gst_info', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'api-key' => '6f0425e9-63af-4f1f-b96e-1f01bff888f2',
                    'account-id' => 'd46db310a92c/7e109c77-5501-4330-ad0a-2f1274c23374'
                ],
                'json' => $payload
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            $requestId = $responseData['request_id'] ?? null;

            if (!$requestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request ID not found in first response'
                ], 400);
            }

            // Step 2: Poll for GST details
            sleep(7);
            $response2 = $client->get('https://eve.idfy.com/v3/tasks', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'api-key' => '6f0425e9-63af-4f1f-b96e-1f01bff888f2',
                    'account-id' => 'd46db310a92c/7e109c77-5501-4330-ad0a-2f1274c23374'
                ],
                'query' => [
                    'request_id' => $requestId
                ],
            ]);

            $responseData2 = json_decode($response2->getBody()->getContents(), true);

            return response()->json([
                'success' => true,
                'data' => $responseData2,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


public function printCorporateInvoice($id)
{
    try {
        $salesInvoice = SalesInvoice::with(['items.item', 'customer', 'gstDetail', 'employee', 'store'])
            ->where('id', $id)
            ->where('is_corporate_bill', true)
            ->firstOrFail();

        return view('cororate_pdf', compact('salesInvoice'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Invoice not found');
    }
}





        public function saveCorporateBill(Request $request)
        {
            try {
                DB::beginTransaction();

                // Check if this is a without GST request
                $withoutGST = $request->input('without_gst') === 'true';

                // Validation rules - GST fields are optional when without_gst is true
                $validationRules = [
                    // Customer fields are completely optional for corporate bills
                    'customer_name' => 'nullable|string|max:255',
                    'customer_contact' => 'nullable|string|max:20',
                    
                    // Items are always required
                    'items' => 'required|array|min:1',
                    'items.*.item_id' => 'required|exists:items,id',
                    'items.*.unit' => 'nullable|string|max:50',
                    'items.*.qty' => 'required|numeric|min:0.01',
                    'items.*.price' => 'required|numeric|min:0',
                    'items.*.discount' => 'nullable|numeric|min:0|max:100',
                    'items.*.tax' => 'nullable|numeric|min:0|max:100',
                    'items.*.amount' => 'nullable|numeric|min:0',
                    
                    // Financial totals
                    'sub_total' => 'required|numeric|min:0',
                    'total_discount' => 'nullable|numeric|min:0',
                    'total_tax' => 'nullable|numeric|min:0',
                    'additional_charges' => 'nullable|numeric|min:0',
                    'grand_total' => 'required|numeric|min:0',
                    'received_amount' => 'required|numeric|min:0',
                    'mode_of_payment' => 'required|string|in:Cash,Online,Card,UPI,Both',
                    'cash_amount' => 'nullable|numeric|min:0',
                    'online_amount' => 'nullable|numeric|min:0',
                    
                    // Loyalty points only matter if customer exists
                    'loyalty_points_used' => 'nullable|numeric|min:0',
                    'loyalty_points_earned' => 'nullable|numeric|min:0',
                    
                    // Gift card fields
                    'applied_gift_cards' => 'nullable|array',
                    'applied_gift_cards.*.card_number' => 'required_with:applied_gift_cards|string',
                    'applied_gift_cards.*.card_type' => 'required_with:applied_gift_cards|in:gift_card,voucher',
                    'applied_gift_cards.*.redeem_amount' => 'required_with:applied_gift_cards|numeric|min:0',
                    'total_gift_card_discount' => 'nullable|numeric|min:0',
                    
                    'print_bill' => 'nullable|string|in:true,false',
                    'without_gst' => 'nullable|string|in:true,false',
                ];

                // Add GST validation only if not without_gst
                if (!$withoutGST) {
                    $validationRules = array_merge($validationRules, [
                        'gst_number' => 'required|string|min:15|max:15',
                        'name' => 'required|string|max:255',
                        'business_legal' => 'nullable|string',
                        'contact_no' => 'nullable|string',
                        'email_id' => 'nullable|email',
                        'pan_no' => 'nullable|string',
                        'register_date' => 'nullable|date',
                        'gstaddress' => 'required|string',
                        'nature_business' => 'nullable|string',
                        'annual_turnover' => 'nullable|string'
                    ]);
                }

                $validated = $request->validate($validationRules);

                // Get employee, store information and IP address
                $role = Session::get('role');
                $loginId = Session::get('loginId');
                $employee_id = null;
                $store_id = null;
                $pos_ipaddress = $this->getClientIpAddress($request);

                if (in_array($role, ['employee', 'manager'])) {
                    $employee = Employee::find($loginId);
                    if ($employee) {
                        $employee_id = $employee->id;
                        $store_id = $employee->store_id;
                    }
                } elseif ($role === 'admin') {
                    $store_id = $request->input('store_id');
                }

                // Handle customer - only create if both name and contact are provided
                $customer = null;
                $customer_id = null;
                $usedPoints = 0;
                $earnedPoints = 0;
                
                if (!empty($validated['customer_name']) && !empty($validated['customer_contact'])) {
                    $customerName = trim($validated['customer_name']);
                    $customerContact = trim($validated['customer_contact']);
                    
                    if ($customerName !== '' && $customerContact !== '') {
                        $customer = Customer::where('contact', $customerContact)->first();
                        
                        if (!$customer) {
                            $customer = Customer::create([
                                'name' => $customerName,
                                'contact' => $customerContact,
                                'loyalty_points' => 0
                            ]);
                        } else {
                            if ($customer->name !== $customerName) {
                                $customer->update(['name' => $customerName]);
                            }
                        }
                        
                        $customer_id = $customer->id;
                        $usedPoints = $validated['loyalty_points_used'] ?? 0;

                        if ($usedPoints > $customer->loyalty_points) {
                            throw new \Exception('Insufficient loyalty points. Customer has only ' . $customer->loyalty_points . ' points.');
                        }

                        if ($customer_id) {
                            $loyaltySettings = LoyaltyPoint::first();
                            if ($loyaltySettings) {
                                $earnAmt = $loyaltySettings->earn_amt ?? 100;
                                $earnPointsRate = $loyaltySettings->earn_points ?? 1;
                                $earnedPoints = floor($validated['grand_total'] / $earnAmt) * $earnPointsRate;
                            }
                        }
                    }
                }

                // Create GST details only if not without GST
                $gst_detail_id = null;
                if (!$withoutGST) {
                    $gstDetail = GstDetail::create([
                        'gst_number' => $validated['gst_number'],
                        'name' => $validated['name'],
                        'business_legal' => $validated['business_legal'] ?? null,
                        'contact_no' => $validated['contact_no'] ?? null,
                        'email_id' => $validated['email_id'] ?? null,
                        'pan_no' => $validated['pan_no'] ?? null,
                        'register_date' => $validated['register_date'] ?? null,
                        'gstaddress' => $validated['gstaddress'],
                        'nature_business' => $validated['nature_business'] ?? null,
                        'annual_turnover' => $validated['annual_turnover'] ?? null
                    ]);
                    $gst_detail_id = $gstDetail->id;
                }

                // Process gift cards/vouchers
                $giftCardCode = null;
                $giftCardAmount = 0;
                $voucherCode = null;
                $voucherAmount = 0;

                if (!empty($validated['applied_gift_cards'])) {
                    foreach ($validated['applied_gift_cards'] as $appliedCard) {
                        if ($appliedCard['card_type'] === 'gift_card') {
                            $giftCardCode = $appliedCard['card_number'];
                            $giftCardAmount += $appliedCard['redeem_amount'];
                        } else {
                            $voucherCode = $appliedCard['card_number'];
                            $voucherAmount += $appliedCard['redeem_amount'];
                        }
                    }
                }

                // Create the sales invoice
                $salesInvoice = SalesInvoice::create([
                    'customer_id' => $customer_id,
                    'employee_id' => $employee_id,
                    'store_id' => $store_id,
                    'pos_ipaddress' => $pos_ipaddress,
                    'sub_total' => $validated['sub_total'],
                    'total_discount' => $validated['total_discount'] ?? 0,
                    'total_tax' => $validated['total_tax'] ?? 0,
                    'additional_charges' => $validated['additional_charges'] ?? 0,
                    'grand_total' => $validated['grand_total'],
                    'received_amount' => $validated['received_amount'],
                    'mode_of_payment' => $validated['mode_of_payment'],
                    'cash_amount' => $validated['cash_amount'] ?? null,
                    'online_amount' => $validated['online_amount'] ?? null,
                    'loyalty_points_used' => $usedPoints,
                    'loyalty_points_earned' => $earnedPoints,
                    
                    // Gift card fields
                    'gift_card_code' => $giftCardCode,
                    'gift_card_amount' => $giftCardAmount,
                    'voucher_code' => $voucherCode,
                    'voucher_amount' => $voucherAmount,
                    'total_gift_card_discount' => $validated['total_gift_card_discount'] ?? 0,
                    
                    'invoice_date' => now(),
                    'status' => 'completed',
                    'gst_detail_id' => $gst_detail_id, // Will be null for without GST bills
                    'is_corporate_bill' => true
                ]);

                // Create sales invoice items
                foreach ($validated['items'] as $itemData) {
                    SalesInvoiceItem::create([
                        'salesinvoice_id' => $salesInvoice->id,
                        'item_id' => $itemData['item_id'],
                        'unit' => $itemData['unit'] ?? 'pcs',
                        'qty' => $itemData['qty'],
                        'price' => $itemData['price'],
                        'discount' => $itemData['discount'] ?? 0,
                        'tax' => $itemData['tax'] ?? 0,
                        'amount' => $itemData['amount'] ?? ($itemData['qty'] * $itemData['price'])
                    ]);

                    // Update item stock if available
                    $item = Item::find($itemData['item_id']);
                    if ($item && isset($item->stock)) {
                        if ($item->stock >= $itemData['qty']) {
                            $item->decrement('stock', $itemData['qty']);
                        } else {
                            Log::warning("Insufficient stock for item {$item->name}. Available: {$item->stock}, Requested: {$itemData['qty']}");
                        }
                    }
                }

                // Update customer's loyalty points only if customer exists
                $newLoyaltyPoints = 0;
                if ($customer && $earnedPoints > 0) {
                    $newLoyaltyPoints = $customer->loyalty_points - $usedPoints + $earnedPoints;
                    $customer->update(['loyalty_points' => max(0, $newLoyaltyPoints)]);
                }

                DB::commit();

                $response = [
                    'success' => true,
                    'status' => true,
                    'message' => $withoutGST ? 'Corporate bill (without GST) saved successfully' : 'Corporate bill saved successfully',
                    'data' => [
                        'invoice_id' => $salesInvoice->id,
                        'invoice_date' => $salesInvoice->invoice_date->format('Y-m-d H:i:s'),
                        'customer_name' => $customer ? $customer->name : null,
                        'customer_contact' => $customer ? $customer->contact : null,
                        'gst_number' => !$withoutGST ? ($gstDetail->gst_number ?? null) : null,
                        'business_name' => !$withoutGST ? ($gstDetail->business_legal ?? null) : null,
                        'grand_total' => $salesInvoice->grand_total,
                        'loyalty_points_earned' => $earnedPoints,
                        'loyalty_points_used' => $usedPoints,
                        'new_loyalty_balance' => $newLoyaltyPoints,
                        'gift_card_discount' => $validated['total_gift_card_discount'] ?? 0,
                        'employee_id' => $employee_id,
                        'store_id' => $store_id,
                        'pos_ipaddress' => $pos_ipaddress,
                        'processed_by' => $role,
                        'without_gst' => $withoutGST
                    ]
                ];

                // Add print URL if print is requested
                if (($validated['print_bill'] ?? '') === 'true') {
                    $response['print_url'] = route('corporate.invoice.print', $salesInvoice->id);
                }

                return response()->json($response, 200);

            } catch (ValidationException $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
                
            } catch (\Exception $e) {
                DB::rollback();
                
                Log::error('Save Corporate Bill Error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all(),
                    'employee_id' => $employee_id ?? 'N/A',
                    'store_id' => $store_id ?? 'N/A',
                    'pos_ipaddress' => $this->getClientIpAddress($request),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()
                ]);
                
                return response()->json([
                    'success' => false,
                    'status' => false,
                    'message' => 'Something went wrong while saving the corporate bill.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        
        
        
        
        
        public function getBillDetails(Request $request)
{
    try {
        $request->validate([
            'bill_number' => 'required'
        ]);

        $billNumber = $request->input('bill_number');
        
        // Extract invoice ID from bill number (format: BILL-123)
        $invoiceId = null;
        if (preg_match('/BILL-(\d+)/', $billNumber, $matches)) {
            $invoiceId = $matches[1];
        } else {
            // If just a number was entered
            $invoiceId = $billNumber;
        }

        // Get the sales invoice with all related data
        $invoice = SalesInvoice::with([
            'items.item',
            'items.batch',
            'customer',
            'employee',
            'store'
        ])->find($invoiceId);

        if (!$invoice) {
            return response()->json([
                'status' => false,
                'message' => 'Bill not found. Please check the bill number.'
            ], 404);
        }

        // Check if invoice has items with remaining quantity
        $availableItems = $invoice->items->filter(function ($item) {
            return $item->qty > 0;
        });

        if ($availableItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'All items from this bill have already been returned. No items available for return.'
            ], 400);
        }

        // Format items for return - only include items with remaining quantity
        $items = $availableItems->map(function ($item) {
            return [
                'id' => $item->id,
                'item_id' => $item->item_id,
                'batch_id' => $item->batch_id,
                'item_name' => $item->item->item_name ?? 'Unknown Item',
                'batch_no' => $item->batch ? $item->batch->batch_no : null,
                'unit' => $item->unit,
                'qty' => $item->qty,
                'price' => $item->price,
                'discount' => $item->discount,
                'tax' => $item->tax,
                'amount' => $item->amount,
                'return_qty' => $item->qty, // Default to remaining quantity
                'is_batch_item' => !is_null($item->batch_id)
            ];
        })->values(); // Re-index the collection

        return response()->json([
            'status' => true,
            'message' => 'Bill details retrieved successfully',
            'data' => [
                'invoice_id' => $invoice->id,
                'bill_number' => 'BILL-' . $invoice->id,
                'invoice_date' => $invoice->invoice_date->format('d-m-Y H:i:s'),
                'customer_name' => $invoice->customer->name ?? 'Walk-in Customer',
                'customer_contact' => $invoice->customer->contact ?? 'N/A',
                'sub_total' => $invoice->sub_total,
                'total_discount' => $invoice->total_discount,
                'total_tax' => $invoice->total_tax,
                'grand_total' => $invoice->grand_total,
                'received_amount' => $invoice->received_amount,
                'mode_of_payment' => $invoice->mode_of_payment,
                'items' => $items,
                'store_name' => $invoice->store->store_name ?? 'N/A',
                'employee_name' => $invoice->employee->empname ?? 'N/A'
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Get bill details error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Failed to retrieve bill details: ' . $e->getMessage()
        ], 500);
    }
}
public function processReturn(Request $request)
{
    try {
        $request->validate([
            'invoice_id' => 'required|exists:sales_invoices,id',
            'items' => 'required|array|min:1',
            'items.*.sales_invoice_item_id' => 'required|exists:sales_invoice_items,id',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.return_qty' => 'required|numeric|min:0.01',

        ]);

        \DB::beginTransaction();

        $invoice = SalesInvoice::with('items')->findOrFail($request->invoice_id);
        $totalRefundAmount = 0;

        // Process each returned item
        foreach ($request->items as $returnItem) {
            $salesInvoiceItem = SalesInvoiceItem::findOrFail($returnItem['sales_invoice_item_id']);
            
            // Validate return quantity
            if ($returnItem['return_qty'] > $salesInvoiceItem->qty) {
                throw new \Exception("Return quantity cannot exceed original quantity for item: {$salesInvoiceItem->item->item_name}");
            }

            // Calculate refund amount for this item
            $itemAmount = $returnItem['return_qty'] * $returnItem['price'];
           
            $totalRefundAmount += $itemAmount;

            // // Update item stock
            // $item = Item::findOrFail($returnItem['item_id']);
            
            // if ($returnItem['batch_id']) {
            //     // Update batch stock
            //     $batch = Batch::findOrFail($returnItem['batch_id']);
            //     $batch->qty += $returnItem['return_qty'];
            //     $batch->save();
            // } else {
            //     // Update regular item stock
            //     $item->opening_stock += $returnItem['return_qty'];
            // }
            
            // $item->save();

            // Reduce quantity in sales invoice item
            $salesInvoiceItem->qty -= $returnItem['return_qty'];
            
            // If quantity becomes zero, you might want to delete or mark it
            if ($salesInvoiceItem->qty <= 0) {
                $salesInvoiceItem->delete();
            } else {
                $salesInvoiceItem->amount = $salesInvoiceItem->qty * $salesInvoiceItem->price;
                $salesInvoiceItem->save();
            }
        }

        // Generate return voucher
        $voucherCode = ReturnVoucher::generateVoucherCode();
        $expiryDate = now()->addDays(30)->toDateString();

        $returnVoucher = ReturnVoucher::create([
            'voucher_code' => $voucherCode,
            'amount' => $totalRefundAmount,
            'expiry_date' => $expiryDate,
            'sales_invoice_id' => $invoice->id,
            'is_used' => false
        ]);

        // Update invoice totals
        // $invoice->grand_total -= $totalRefundAmount;
        $invoice->save();

        \DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Return processed successfully',
            'data' => [
                'refund_amount' => $totalRefundAmount,
                'voucher_code' => $voucherCode,
                'expiry_date' => $expiryDate,
                'voucher_message' => "Your return voucher code is: {$voucherCode}. Valid until: {$expiryDate}"
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        \DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Return processing error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Error processing return: ' . $e->getMessage()
        ], 500);
    }
}





public function checkReturnVoucherBalance(Request $request)
{
    try {
        $request->validate([
            'card_number' => 'required|string'
        ]);

        $voucherCode = $request->input('card_number');

        // Find the return voucher
        $returnVoucher = ReturnVoucher::where('voucher_code', $voucherCode)->first();

        if (!$returnVoucher) {
            return response()->json([
                'status' => false,
                'message' => 'Return voucher not found'
            ], 404);
        }

        // Check if voucher is valid
        if ($returnVoucher->is_used) {
            return response()->json([
                'status' => false,
                'message' => 'This return voucher has already been used'
            ], 400);
        }

        if ($returnVoucher->isExpired()) {
            return response()->json([
                'status' => false,
                'message' => 'This return voucher has expired'
            ], 400);
        }

        // Return voucher details
        return response()->json([
            'status' => true,
            'message' => 'Return voucher is valid',
            'available_amount' => $returnVoucher->amount,
            'max_redeemable' => $returnVoucher->amount,
            'card_status' => 'Valid',
            'expiry_date' => $returnVoucher->expiry_date->format('d-m-Y'),
            'applicable_products' => 'Applicable for all products'
        ]);

    } catch (\Exception $e) {
        \Log::error('Return voucher check error: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Error checking return voucher: ' . $e->getMessage()
        ], 500);
    }
}
}