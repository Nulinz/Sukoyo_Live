<?php

namespace App\Http\Controllers;
   use App\Models\Vendor;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Store;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class Purchase extends Controller
{

public function inv_list(Request $request)
{
    $days = $request->get('days');
    $role = session('role');
    $store_id = session('store_id');

    $invoices = PurchaseInvoice::with('purchaseOrder.vendor');

    if ($days) {
        $fromDate = \Carbon\Carbon::now()->subDays($days)->startOfDay();
        $invoices->where('bill_date', '>=', $fromDate);
    }

    if ($role === 'manager') {
        $storeName = \App\Models\Store::find($store_id)?->store_name;
        $invoices->whereHas('purchaseOrder', function ($query) use ($storeName) {
            $query->where('warehouse', $storeName);
        });
    }

    $invoices = $invoices->get();

    $totalPurchase = $invoices->sum('total');
    $paidAmount = $invoices->sum('paid_amount');
    $unpaidAmount = $invoices->sum('balance_amount');

    return view('purchase.inv_list', compact('invoices', 'totalPurchase', 'paidAmount', 'unpaidAmount', 'days'));
}
public function inv_delete($id)
{
    $invoice = PurchaseInvoice::with('purchaseInvoiceItems')->find($id);

    if (!$invoice) {
        return redirect()->back()->with('error', 'Invoice not found.');
    }

    // Delete related items first
    $invoice->purchaseInvoiceItems()->delete();

    // Then delete the invoice itself
    $invoice->delete();

    return redirect()->back()->with('success', 'Invoice and related items deleted successfully.');
}


public function inv_add()
{
    $role = session('role');
    $store_id = session('store_id');

    if ($role === 'manager') {
        $storeName = \App\Models\Store::find($store_id)?->store_name;

        $purchaseOrders = \App\Models\PurchaseOrder::where('warehouse', $storeName)
            ->get(['id', 'bill_no']);
    } else {
        $purchaseOrders = \App\Models\PurchaseOrder::all(['id', 'bill_no']);
    }

    return view('purchase.inv_add', compact('purchaseOrders'));
}


public function storeInvoice(Request $request)
{
    try {
        // Debug: Log incoming data
        \Log::info('Purchase Invoice Request Data:', [
            'pos_id' => $request->pos_id,
            'items_count' => count($request->items ?? []),
            'all_data' => $request->all()
        ]);

        // Validate that we have required data
        if (!$request->pos_id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Purchase Order ID is required.');
        }

        // Use database transaction for data integrity
        \DB::beginTransaction();

        // 1. Store invoice header
        $invoice = \App\Models\PurchaseInvoice::create([
            'purchase_order_id' => $request->pos_id,
            'contact'          => $request->contact,
            'billaddress'      => $request->billaddress,
            'bill_no'          => $request->billno,
            'due_date'         => $request->due_date,
            'bill_date'        => $request->billdate,
            'transport'        => $request->transport,
            'packaging'        => $request->packaging,
            'warehouse'        => $request->warehouse,
            'payment_type'     => $request->paytype,
            'reference_no'     => $request->refno,
            'description'      => $request->descp,
            'total'            => $request->total ?? 0,
            'paid_amount'      => $request->paidamt ?? 0,
            'balance_amount'   => $request->balanceamt ?? 0,
        ]);

        \Log::info('Invoice created with ID: ' . $invoice->id);

        // 2. Prepare bulk insert data for selected items
        $items = $request->items ?? [];
        $bulkInsertData = [];
        $timestamp = now();

        foreach ($items as $index => $item) {
            // Only process selected items
            if (isset($item['selected']) && $item['selected'] == '1') {
                $bulkInsertData[] = [
                    'purchase_invoice_id' => $invoice->id,
                    'purchase_order_id' => $request->pos_id,
                    'item' => $item['item_id'] ?? null,
                    'unit' => $item['unit'] ?? null,
                    'qty' => $item['qty'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        \Log::info('Selected items count: ' . count($bulkInsertData));

        // Check if any items were selected
        if (empty($bulkInsertData)) {
            \DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one item to create invoice.');
        }

        // 3. Bulk insert all items at once (much faster than loop)
        // Split into chunks of 100 to avoid max_input_vars limit
        $chunks = array_chunk($bulkInsertData, 100);
        
        foreach ($chunks as $chunkIndex => $chunk) {
            \App\Models\PurchaseInvoiceItem::insert($chunk);
            \Log::info('Inserted chunk ' . ($chunkIndex + 1) . ' with ' . count($chunk) . ' items');
        }

        // Commit the transaction
        \DB::commit();

        \Log::info('Transaction committed successfully');

        return redirect()->route('purchase.inv_list')
            ->with('success', 'Purchase invoice with ' . count($bulkInsertData) . ' items stored successfully!');

    } catch (\Illuminate\Database\QueryException $e) {
        // Rollback on error
        \DB::rollBack();
        
        // Log database error with details
        \Log::error('Purchase Invoice Database Error:', [
            'message' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings(),
        ]);
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Database Error: ' . $e->getMessage());
            
    } catch (\Exception $e) {
        // Rollback on error
        \DB::rollBack();
        
        // Log the error with full trace
        \Log::error('Purchase Invoice Store Error:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error: ' . $e->getMessage());
    }
}



    public function get_order_data($id)
    {
        $order = \App\Models\PurchaseOrder::with(['items.item', 'vendor'])->findOrFail($id);

        return response()->json($order);
    }



public function inv_profile($id) 
{
    $invoice = PurchaseInvoice::with('purchaseOrder.vendor')->findOrFail($id);
    
    // Manual join to get item details
    $items = PurchaseInvoiceItem::leftJoin('items', 'purchase_invoice_items.item', '=', 'items.id')
                ->select('purchase_invoice_items.*', 'items.item_name')
                ->where('purchase_invoice_items.purchase_order_id', $invoice->purchase_order_id)
                ->get();
                
    $billDate = Carbon::parse($invoice->bill_date)->format('d-m-Y');
    $dueDate = $invoice->due_date ? Carbon::parse($invoice->due_date)->startOfDay() : null;
    $today = Carbon::now()->startOfDay();

    $dueIn = '-';
    if ($dueDate) {
        $dueIn = $today->gt($dueDate)
            ? 'Overdue by ' . $today->diffInDays($dueDate) . ' Day(s)'
            : $today->diffInDays($dueDate) . ' Day(s) left';
    }

    $status = $invoice->balance_amount == 0 ? 'Paid' : 'Unpaid';

    return view('purchase.inv_profile', compact('invoice', 'items', 'billDate', 'dueIn', 'status'));
}


public function order_list()
{
    $role = session('role');

    if ($role === 'manager') {
        $store_id = session('store_id');

        // Get only orders from the manager's store
        $purchaseOrders = PurchaseOrder::with('vendor')
            ->where('warehouse', Store::find($store_id)->store_name)
            ->orderBy('created_at', 'desc')
            ->get();
    } else {
        // Admin sees all
        $purchaseOrders = PurchaseOrder::with('vendor')->orderBy('created_at', 'desc')->get();
    }

    return view('purchase.order_list', compact('purchaseOrders'));
}
public function order_delete($id)
{
    $order = PurchaseOrder::find($id);

    if (!$order) {
        return redirect()->back()->with('error', 'Purchase order not found.');
    }

    // Optional: Delete related items if any
    // $order->items()->delete();

    $order->delete();

    return redirect()->back()->with('success', 'Purchase order deleted successfully.');
}
    public function toggle_status($id)
    {
        $order = PurchaseOrder::findOrFail($id);
        $order->status = $order->status === 'Active' ? 'Inactive' : 'Active';
        $order->save();

        return response()->json(['status' => $order->status]);
    }



public function order_add()
{
    $role = session('role');
    $empcode = session('empcode');
    $storeId = session('store_id');

    if ($role === 'manager') {
        // Vendors added by this manager
        $vendors = Vendor::where('added_by', $empcode)->get();

        // Only the store assigned to this manager
        $stores = Store::where('id', $storeId)->get();

        // Only items for the manager's store
        $items = Item::where('store_id', $storeId)->get();
    } else {
        // Admin sees all
        $vendors = Vendor::all();
        $stores = Store::all();
        $items = Item::all(); // Admin sees all items
    }

    return view('purchase.order_add', compact('vendors', 'items', 'stores'));
}

public function getVendorDetails($id)
{
    $vendor = Vendor::find($id);
    if ($vendor) {
        return response()->json([
            'contact' => $vendor->contact,
            'billaddress' => $vendor->billaddress,
        ]);
    }
    return response()->json(['message' => 'Vendor not found'], 404);
}


    public function order_store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'contact' => 'required',
            'billaddress' => 'nullable',
            'bill_no' => 'required',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date', 
            'transport' => 'nullable',
            'packaging' => 'nullable',
            'warehouse' => 'required',
            'payment_type' => 'required',
            'reference_no' => 'nullable|numeric',
            'description' => 'nullable',
            'total' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'balance_amount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit' => 'required',
            'items.*.qty' => 'required|numeric',
            'items.*.price' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.tax' => 'nullable|numeric',
            'items.*.amount' => 'required|numeric',
        ]);

        $order = PurchaseOrder::create($validated);

        foreach ($validated['items'] as $item) {
            $order->items()->create($item);
        }
    return redirect()->route('purchase.order_list')->with('success', 'Purchase order added successfully!');

    }



public function order_edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('items.item', 'vendor')->findOrFail($id);
        $items = Item::select('id', 'item_name')->orderBy('item_name')->get();
        
        return view('purchase.order_edit', compact('purchaseOrder', 'items'));
    }

    /**
     * Update purchase order with optimized handling for large datasets
     */
    public function order_update(Request $request, $id)
    {
        // Increase execution time and memory for large datasets
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        // Handle JSON requests (for AJAX submissions)
        if ($request->isJson()) {
            $data = $request->json()->all();
            $request->merge($data);
        }
        
        // Custom validation with better error messages
        $validator = Validator::make($request->all(), [
            'total' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'balance_amount' => 'required|numeric',
            'transport' => 'nullable|numeric|min:0',
            'packaging' => 'nullable|numeric|min:0',
            'reference_no' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'round_off_total_amount' => 'nullable|numeric',
            'round_off_amount' => 'nullable|numeric',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.unit' => 'required|string|in:kg,g,litre,ml,pcs,pack,box,dozen',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.tax' => 'nullable|numeric|min:0|max:100',
            'items.*.amount' => 'required|numeric|min:0',
        ], [
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.item_id.required' => 'Item selection is required.',
            'items.*.item_id.exists' => 'Selected item does not exist.',
            'items.*.unit.required' => 'Unit is required for all items.',
            'items.*.unit.in' => 'Invalid unit selected.',
            'items.*.qty.required' => 'Quantity is required for all items.',
            'items.*.qty.gt' => 'Quantity must be greater than 0.',
            'items.*.price.required' => 'Price is required for all items.',
            'items.*.price.min' => 'Price cannot be negative.',
            'items.*.amount.required' => 'Amount is required for all items.',
            'total.required' => 'Total amount is required.',
            'paid_amount.required' => 'Paid amount is required.',
            'balance_amount.required' => 'Balance amount is required.',
        ]);
        
        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = $errors->first();
            
            // Count how many errors
            $errorCount = $errors->count();
            
            Log::warning('Purchase Order Validation Failed', [
                'purchase_order_id' => $id,
                'error_count' => $errorCount,
                'errors' => $errors->toArray(),
                'items_count' => count($request->input('items', []))
            ]);
            
            // Handle JSON/AJAX response
            if ($request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $firstError,
                    'errors' => $errors->toArray(),
                    'error_count' => $errorCount
                ], 422);
            }
            
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $firstError);
        }
        
        try {
            DB::beginTransaction();
            
            // Find purchase order
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            
            // Update main purchase order fields
            $purchaseOrder->total = $request->input('total');
            $purchaseOrder->paid_amount = $request->input('paid_amount');
            $purchaseOrder->balance_amount = $request->input('balance_amount');
            $purchaseOrder->transport = $request->input('transport', 0);
            $purchaseOrder->packaging = $request->input('packaging', 0);
            $purchaseOrder->reference_no = $request->input('reference_no');
            $purchaseOrder->description = $request->input('description');

            $purchaseOrder->save();
            
            // Delete old items first
            DB::table('purchase_order_items')
                ->where('purchase_order_id', $purchaseOrder->id)
                ->delete();
            
            // Get items data
            $itemsData = $request->input('items', []);
            $totalItems = count($itemsData);
            
            // Process items in chunks (100 at a time)
            $chunkSize = 100;
            $chunks = array_chunk($itemsData, $chunkSize, true);
            $insertedCount = 0;
            
            foreach ($chunks as $chunk) {
                $itemsToInsert = [];
                
                foreach ($chunk as $index => $item) {
                    // Skip items with invalid data
                    if (empty($item['item_id']) || !isset($item['qty']) || !isset($item['price'])) {
                        continue;
                    }
                    
                    $itemsToInsert[] = [
                        'purchase_order_id' => $purchaseOrder->id,
                        'item_id' => (int) $item['item_id'],
                        'unit' => $item['unit'],
                        'qty' => (float) $item['qty'],
                        'price' => (float) $item['price'],
                        'discount' => (float) ($item['discount'] ?? 0),
                        'tax' => (float) ($item['tax'] ?? 0),
                        'amount' => (float) $item['amount'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $insertedCount++;
                }
                
                // Bulk insert
                if (!empty($itemsToInsert)) {
                    DB::table('purchase_order_items')->insert($itemsToInsert);
                }
            }
            
            DB::commit();
            
            // Log success
            Log::info("Purchase Order #{$purchaseOrder->id} updated successfully", [
                'total_items' => $totalItems,
                'inserted_items' => $insertedCount,
                'user_id' => auth()->id()
            ]);
            
            // Handle JSON/AJAX response
            if ($request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Purchase order updated successfully with {$insertedCount} items!",
                    'redirect' => route('purchase.order_list'),
                    'data' => [
                        'id' => $purchaseOrder->id,
                        'total' => $purchaseOrder->total,
                        'items_count' => $insertedCount
                    ]
                ]);
            }
            
            // Regular form submission
            return redirect()
                ->route('purchase.order_list')
                ->with('success', "Purchase order updated successfully with {$insertedCount} items!");
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error
            Log::error('Purchase Order Update Error', [
                'purchase_order_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'items_count' => count($request->input('items', []))
            ]);
            
            $errorMessage = config('app.debug') 
                ? "Error: {$e->getMessage()} (Line: {$e->getLine()})"
                : 'Failed to update purchase order. Please try again.';
            
            // Handle JSON/AJAX response
            if ($request->isJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }
    

    public function order_profile($id)
    {
        // Load purchase order with vendor & items (and each item's item)
        $order = PurchaseOrder::with(['vendor', 'items.item'])->findOrFail($id);

        return view('purchase.order_profile', compact('order'));
    }

}
