<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Batch;
use App\Models\Item;
use App\Models\Store;
use App\Models\Repacking;
use App\Models\UOM;
use App\Models\PurchaseOrderItem;
use App\Models\SalesInvoiceItem;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\ItemTransfer;
use App\Models\ItemTransferDetail;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Storage;

class Inventory extends Controller
{

    public function brand_list()
    {
        $role = session('role');
        $brandsQuery = Brand::query();

        if ($role === 'manager') {
            $brandsQuery->where('created_by', session('loginId'));
        }

        $brands = $brandsQuery->latest()->get();
        return view('inventory.brand_list', compact('brands'));
    }

public function brand_bulk_upload(Request $request)
{
    /* -----------------------------------------
       1️⃣ Validate upload
    ------------------------------------------*/
    $request->validate([
        'file' => 'required|mimes:csv,xlsx,xls|max:2048'
    ]);

    $file = $request->file('file');

    /* -----------------------------------------
       2️⃣ Read file locally (CSV only logic)
    ------------------------------------------*/
    $data = array_map('str_getcsv', file($file->getRealPath()));

    // Remove header
    unset($data[0]);

    /* -----------------------------------------
       3️⃣ Upload file to S3 (NO url() calls)
    ------------------------------------------*/
    $s3Path = null;

    try {
        $fileName = 'brands_' . time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        // Full S3 key (IMPORTANT)
        $s3Path = 'brand_bulk_uploads/' . $fileName;

        Storage::disk('s3')->putFileAs(
            'brand_bulk_uploads',
            $file,
            $fileName
        );
    } catch (\Exception $e) {
        return back()->with('error', 'S3 Upload Failed: ' . $e->getMessage());
    }

    /* -----------------------------------------
       4️⃣ Insert rows into DB
    ------------------------------------------*/
    foreach ($data as $row) {
        if (empty($row[0])) {
            continue; // skip empty rows
        }

        Brand::create([
            'name'       => trim($row[0]),
            'remarks'    => $row[1] ?? '',
            'status'     => 'Active',
            'created_by' => session('role') === 'manager'
                ? session('loginId')
                : null,

            // OPTIONAL: store uploaded file path
            // 'upload_file' => $s3Path,
        ]);
    }

    return redirect()
        ->back()
        ->with('success', 'Brands uploaded successfully!');
}

    public function store_brand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        Brand::create([
            'name' => $request->name,
            'remarks' => $request->remarks,
            'status' => 'Active',
            'created_by' => session('role') === 'manager' ? session('loginId') : null,
        ]);

        return redirect()->back()->with('success', 'Brand added successfully!');
    }


    public function update_brand(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update([
            'name' => $request->name,
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Brand updated successfully!');
    }

    public function toggle_brand_status($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->status = $brand->status === 'Active' ? 'Inactive' : 'Active';
        $brand->save();

        return redirect()->back()->with('success', 'Status updated!');
    }
    public function category_list()
    {
        $role = session('role');
        $loginId = session('loginId');

        if ($role === 'admin') {
            $categories = Category::all();
        } elseif ($role === 'manager') {
            $categories = Category::where('created_by', $loginId)->get();
        } else {
            abort(403, 'Unauthorized');
        }

        return view('inventory.category_list', compact('categories'));
    }



public function category_bulk_upload(Request $request)
{
    /* -----------------------------------------
       1️⃣ Validate upload
    ------------------------------------------*/
    $request->validate([
        'file' => 'required|mimes:csv,xlsx,xls|max:2048'
    ]);

    $file = $request->file('file');

    /* -----------------------------------------
       2️⃣ Read file locally (CSV logic)
    ------------------------------------------*/
    $rows = array_map('str_getcsv', file($file->getRealPath()));

    // Remove header
    unset($rows[0]);

    /* -----------------------------------------
       3️⃣ Upload file to S3 (storage only – SAFE)
    ------------------------------------------*/
    $s3Path = null;

    try {
        $fileName = 'categories_' . time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        // Full S3 key (important)
        $s3Path = 'category_bulk_uploads/' . $fileName;

        Storage::disk('s3')->putFileAs(
            'category_bulk_uploads',
            $file,
            $fileName
        );
    } catch (\Exception $e) {
        return back()->with('error', 'S3 Upload Failed: ' . $e->getMessage());
    }

    $createdBy = session('role') === 'manager'
        ? session('loginId')
        : null;

    /* -----------------------------------------
       4️⃣ Insert into DB
    ------------------------------------------*/
    foreach ($rows as $row) {
        if (empty($row[0])) {
            continue; // skip empty rows
        }

        Category::create([
            'name'       => trim($row[0]),
            'remarks'    => $row[1] ?? '',
            'status'     => 'Active',
            'created_by' => $createdBy,

            // OPTIONAL: store upload file path
            // 'upload_file' => $s3Path,
        ]);
    }

    return redirect()
        ->back()
        ->with('success', 'Categories uploaded successfully!');
}


    public function store_category(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'remarks' => 'nullable',
        ]);

        $createdBy = null;
        if (session('role') === 'manager') {
            $createdBy = session('loginId');
        }

        Category::create([
            'name' => $request->name,
            'remarks' => $request->remarks,
            'status' => 'Active',
            'created_by' => $createdBy,
        ]);

        return redirect()->back()->with('success', 'Category added!');
    }


    public function update_category(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'remarks' => 'nullable',
        ]);
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'remarks' => $request->remarks,
        ]);
        return redirect()->back()->with('success', 'Category updated!');
    }

    public function toggle_category_status($id)
    {
        $category = Category::findOrFail($id);
        $category->status = $category->status == 'Active' ? 'Inactive' : 'Active';
        $category->save();
        return redirect()->back()->with('success', 'Status updated!');
    }

    public function subcategory_list()
    {
        $role = session('role');
        $loginId = session('loginId');

        if ($role === 'admin') {
            $subcategories = Subcategory::with('category')->get();
            $categories = Category::where('status', 'Active')->get();
        } elseif ($role === 'manager') {
            $subcategories = Subcategory::with('category')->where('created_by', $loginId)->get();
            $categories = Category::where('status', 'Active')->where('created_by', $loginId)->get();
        } else {
            abort(403, 'Unauthorized access.');
        }

        return view('inventory.subcategory_list', compact('subcategories', 'categories'));
    }


public function subcategory_bulk_upload(Request $request)
{
    /* -----------------------------------------
       1️⃣ Validate upload
    ------------------------------------------*/
    $request->validate([
        'file' => 'required|mimes:csv,xlsx,xls|max:2048'
    ]);

    $file = $request->file('file');

    /* -----------------------------------------
       2️⃣ Read file locally (CSV logic)
    ------------------------------------------*/
    $rows = array_map('str_getcsv', file($file->getRealPath()));

    // Remove header
    unset($rows[0]);

    /* -----------------------------------------
       3️⃣ Upload file to S3 (storage only – SAFE)
    ------------------------------------------*/
    $s3Path = null;

    try {
        $fileName = 'subcategories_' . time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        // Full S3 key (important)
        $s3Path = 'subcategory_bulk_uploads/' . $fileName;

        Storage::disk('s3')->putFileAs(
            'subcategory_bulk_uploads',
            $file,
            $fileName
        );
    } catch (\Exception $e) {
        return back()->with('error', 'S3 Upload Failed: ' . $e->getMessage());
    }

    $createdBy = session('role') === 'manager'
        ? session('loginId')
        : null;

    /* -----------------------------------------
       4️⃣ Insert into DB (SAFE + VALIDATED)
    ------------------------------------------*/
    foreach ($rows as $row) {
        // Require both category name & subcategory name
        if (empty($row[0]) || empty($row[1])) {
            continue;
        }

        $category = Category::where('name', trim($row[0]))->first();

        if (!$category) {
            continue; // skip if category does not exist
        }

        SubCategory::create([
            'category_id' => $category->id,
            'name'        => trim($row[1]),
            'remarks'     => $row[2] ?? '',
            'status'      => 'Active',
            'created_by'  => $createdBy,

            // OPTIONAL: store uploaded file path
            // 'upload_file' => $s3Path,
        ]);
    }

    return redirect()
        ->back()
        ->with('success', 'Sub Categories uploaded successfully!');
}


    public function store_subcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $createdBy = session('role') === 'manager' ? session('loginId') : null;

        Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'remarks' => $request->remarks,
            'status' => 'Active',
            'created_by' => $createdBy,
        ]);

        return redirect()->route('inventory.subcategorylist')->with('success', 'Subcategory added successfully');
    }


    public function update_subcategory(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $subcategory = Subcategory::findOrFail($id);
        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('inventory.subcategorylist')->with('success', 'Subcategory updated successfully');
    }

    public function toggle_subcategory($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->status = $subcategory->status === 'Active' ? 'Inactive' : 'Active';
        $subcategory->save();

        return redirect()->route('inventory.subcategorylist')->with('success', 'Status updated successfully');
    }





    public function repacking_list()
    {
        $stores = Store::all();
        $items = Item::all();
        $repackings = Repacking::with(['store', 'item'])->get();
        return view('inventory.repacking_list', compact('stores', 'repackings', 'items'));
    }

    // public function repacking_store(Request $request)
// {
//     try {
//         // Validate the request
//         $validated = $request->validate([
//             'items' => 'required|array|min:1',
//             'items.*.item_id' => 'required|exists:items,id',
//             'items.*.total_bulk_qty' => 'required|numeric|min:0.01',
//             'items.*.bulk_unit' => 'required|string',
//             'items.*.repacking_charge' => 'required|numeric|min:0',
//             'repack_uom' => 'required|string',
//             'repack_qty' => 'required|numeric|min:0.01',
//             'cost_per_pack' => 'required|numeric|min:0',
//             'selling_price' => 'required|numeric|min:0',
//             'variant_name' => 'required|string',
//           'store_id' => ['required', function($attribute, $value, $fail) {
//                         if ($value !== 'Warehouse' && !Store::where('id', $value)->exists()) {
//                             $fail('The selected store is invalid.');
//                         }
//                         }],
//         ]);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
//         return redirect()->back()
//             ->withErrors($e->validator)
//             ->withInput()
//             ->with('error', 'Please fix the validation errors.');
//     }

    //     try {
//         \DB::beginTransaction();

    //         $stockErrors = [];
//         $totalRepackedUnits = 0;
//         $combinedItemNames = [];
//         $totalRepackingCharge = 0;

    //         // Validate stock availability for all items first
//         foreach ($request->items as $index => $itemData) {
//             $bulkItem = Item::find($itemData['item_id']);
//             if (!$bulkItem) {
//                 $stockErrors[] = "Item #" . ($index + 1) . ": Item not found.";
//                 continue;
//             }

    //             // Total purchased quantity from purchase invoices (warehouse = "Warehouse")
//             $purchaseInvoiceQty = \DB::table('purchase_invoice_items')
//                 ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
//                 ->where('purchase_invoice_items.item', $itemData['item_id'])
//                 ->where('purchase_invoices.warehouse', 'Warehouse')
//                 ->sum('purchase_invoice_items.qty');

    //             // Total sold quantity from sales invoices
//             $salesInvoiceQty = \DB::table('sales_invoice_items')
//                 ->where('item_id', $itemData['item_id'])
//                 ->sum('qty');

    //             // Total available stock
//             $totalAvailableStock = $bulkItem->opening_stock + $purchaseInvoiceQty - $salesInvoiceQty;

    //             if ($itemData['total_bulk_qty'] > $totalAvailableStock) {
//                 $stockErrors[] = "Item #" . ($index + 1) . " ({$bulkItem->item_name}): Cannot process request. " .
//                     "Requested {$itemData['total_bulk_qty']} {$itemData['bulk_unit']}, available {$totalAvailableStock}. " .
//                     "Breakdown: Item Stock: {$bulkItem->opening_stock}, Warehouse PIs: {$purchaseInvoiceQty}, Sales Invoices: {$salesInvoiceQty}.";
//                 continue;
//             }

    //             $combinedItemNames[] = $bulkItem->item_name;
//             $totalRepackedUnits += $itemData['total_bulk_qty'];
//             $totalRepackingCharge += $itemData['repacking_charge'];
//         }

    //         if (!empty($stockErrors)) {
//             \DB::rollback();
//             $errorBag = new \Illuminate\Support\MessageBag();
//             foreach ($stockErrors as $error) $errorBag->add('stock_error', $error);

    //             return redirect()->back()
//                 ->withInput()
//                 ->withErrors($errorBag)
//                 ->with('error', 'Stock validation failed.');
//         }

    //         // Process all items
//         foreach ($request->items as $itemData) {
//             $bulkItem = Item::findOrFail($itemData['item_id']);
//             $qtyToDeduct = $itemData['total_bulk_qty'];

    //             // Deduct from opening stock first
//             if ($bulkItem->opening_stock >= $qtyToDeduct) {
//                 $bulkItem->decrement('opening_stock', $qtyToDeduct);
//                 $qtyToDeduct = 0;
//             } else {
//                 $deductedFromItem = $bulkItem->opening_stock;
//                 $bulkItem->update(['opening_stock' => 0]);
//                 $qtyToDeduct -= $deductedFromItem;
//             }

    //             // Deduct remaining qty from purchase invoices
//             if ($qtyToDeduct > 0) {
//                 $purchaseInvoiceItems = \DB::table('purchase_invoice_items')
//                     ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
//                     ->where('purchase_invoice_items.item', $itemData['item_id'])
//                     ->where('purchase_invoices.warehouse', 'Warehouse')
//                     ->where('purchase_invoice_items.qty', '>', 0)
//                     ->select('purchase_invoice_items.id', 'purchase_invoice_items.qty')
//                     ->get();

    //                 foreach ($purchaseInvoiceItems as $piItem) {
//                     if ($qtyToDeduct <= 0) break;

    //                     if ($piItem->qty >= $qtyToDeduct) {
//                         \DB::table('purchase_invoice_items')
//                             ->where('id', $piItem->id)
//                             ->decrement('qty', $qtyToDeduct);
//                         $qtyToDeduct = 0;
//                     } else {
//                         \DB::table('purchase_invoice_items')
//                             ->where('id', $piItem->id)
//                             ->update(['qty' => 0]);
//                         $qtyToDeduct -= $piItem->qty;
//                     }
//                 }
//             }

    //             // Create individual repacking record
//             Repacking::create([
//                 'item_id' => $itemData['item_id'],
//                 'item_name' => $bulkItem->item_name,
//                 'total_bulk_qty' => $itemData['total_bulk_qty'],
//                 'bulk_unit' => $itemData['bulk_unit'],
//                 'repack_uom' => $request->repack_uom,
//                 'repack_qty' => $request->repack_qty,
//                 'cost_per_pack' => $request->cost_per_pack,
//                 'selling_price' => $request->selling_price,
//                 'repacking_charge' => $itemData['repacking_charge'],
//                 'variant_name' => $request->variant_name,
//                 'store_id' => $request->store_id,
//             ]);
//         }

    //         // Create combined repacked item
//         $combinedItemName = implode(' + ', $combinedItemNames);
//         $repackedItemName = $combinedItemName . ' - ' . $request->variant_name . ' (' . $request->repack_qty . ' ' . $request->repack_uom . ')';

    //         $existingRepackedItem = Item::where('item_name', $repackedItemName)
//                                   ->where('store_id', $request->store_id)
//                                   ->first();

    //         $totalCostPerPack = $request->cost_per_pack + $totalRepackingCharge;

    //         if ($existingRepackedItem) {
//             $existingRepackedItem->increment('opening_stock', $totalRepackedUnits);
//             $existingRepackedItem->update([
//                 'wholesale_price' => $totalCostPerPack,
//                 'purchase_price' => $totalCostPerPack,
//             ]);
//         } else {
//             $firstItem = Item::findOrFail($request->items[0]['item_id']);

    //             Item::create([
//                 'item_type' => 'repacked',
//                 'item_code' => $firstItem->item_code,
//                 'hsn_code' => $firstItem->hsn_code,
//                 'item_name' => $repackedItemName,
//                 'brand_id' => $firstItem->brand_id,
//                 'category_id' => $firstItem->category_id,
//                 'subcategory_id' => $firstItem->subcategory_id,
//                 'discount' => $firstItem->discount,
//                 'sales_price' => $request->selling_price,
//                 'mrp' => $request->selling_price,
//                 'wholesale_price' => $totalCostPerPack,
//                 'measure_unit' => $request->repack_uom,
//                 'opening_stock' => $totalRepackedUnits,
//                 'opening_unit' => $request->repack_uom,
//                 'gst_rate' => $firstItem->gst_rate,
//                 'item_description' => 'Repacked from ' . $combinedItemName,
//                 'stock_status' => 'active',
//                 'min_stock' => 0,
//                 'max_stock' => 1000,
//                 'abc_category' => $firstItem->abc_category,
//                 'purchase_price' => $totalCostPerPack,
//                 'purchase_tax' => $firstItem->purchase_tax,
//                 'purchase_gst' => $firstItem->purchase_gst,
//                  'store_id' => $request->store_id === 'Warehouse' ? 'Warehouse' : $request->store_id,
//             ]);
//         }

    //         \DB::commit();

    //         return redirect()->route('inventory.repackinglist')
//             ->with('success', "Repacking completed successfully! " . count($request->items) . " items combined. Stock deducted from Items and Warehouse Purchase Invoices.");

    //     } catch (\Exception $e) {
//         \DB::rollback();

    //         \Log::error('Repacking Error: ' . $e->getMessage(), [
//             'line' => $e->getLine(),
//             'file' => $e->getFile(),
//             'trace' => $e->getTraceAsString()
//         ]);

    //         return redirect()->back()
//             ->withInput()
//             ->with('error', 'Error: ' . $e->getMessage());
//     }
// }

    public function repacking_store(Request $request)
    {
        try {
            // Dynamic validation based on item type
            $rules = [
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.total_bulk_qty' => 'required|numeric|min:0.01',
                'items.*.bulk_unit' => 'required|string',
                'items.*.repacking_charge' => 'required|numeric|min:0',
                'repack_qty' => 'required|numeric|min:0.01',
                'item_type' => 'required|in:new,existing',
            ];

            // Add conditional validation based on item type
            if ($request->item_type === 'new') {
                $rules['repack_uom'] = 'required|string';
                $rules['cost_per_pack'] = 'required|numeric|min:0';
                $rules['selling_price'] = 'required|numeric|min:0';
                $rules['store_id'] = [
                    'required',
                    function ($attribute, $value, $fail) {
                        if ($value !== 'Warehouse' && !Store::where('id', $value)->exists()) {
                            $fail('The selected store is invalid.');
                        }
                    }
                ];
                $rules['variant_name'] = 'required|string';
            } else {
                $rules['existing_item_id'] = 'required|exists:items,id';
            }

            $validated = $request->validate($rules);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            \DB::beginTransaction();

            $stockErrors = [];
            $combinedItemNames = [];
            $totalRepackingCharge = 0;

            // Validate stock availability for all items first
            foreach ($request->items as $index => $itemData) {
                $bulkItem = Item::find($itemData['item_id']);
                if (!$bulkItem) {
                    $stockErrors[] = "Item #" . ($index + 1) . ": Item not found.";
                    continue;
                }

                // Total purchased quantity from purchase invoices (warehouse = "Warehouse")
                $purchaseInvoiceQty = \DB::table('purchase_invoice_items')
                    ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                    ->where('purchase_invoice_items.item', $itemData['item_id'])
                    ->where('purchase_invoices.warehouse', 'Warehouse')
                    ->sum('purchase_invoice_items.qty');

                // Total sold quantity from sales invoices
                $salesInvoiceQty = \DB::table('sales_invoice_items')
                    ->where('item_id', $itemData['item_id'])
                    ->sum('qty');

                // Total available stock
                $totalAvailableStock = $bulkItem->opening_stock + $purchaseInvoiceQty - $salesInvoiceQty;

                if ($itemData['total_bulk_qty'] > $totalAvailableStock) {
                    $stockErrors[] = "Item #" . ($index + 1) . " ({$bulkItem->item_name}): Cannot process request. " .
                        "Requested {$itemData['total_bulk_qty']} {$itemData['bulk_unit']}, available {$totalAvailableStock}. " .
                        "Breakdown: Item Stock: {$bulkItem->opening_stock}, Warehouse PIs: {$purchaseInvoiceQty}, Sales Invoices: {$salesInvoiceQty}.";
                    continue;
                }

                $combinedItemNames[] = $bulkItem->item_name;
                $totalRepackingCharge += $itemData['repacking_charge'];
            }

            if (!empty($stockErrors)) {
                \DB::rollback();
                $errorBag = new \Illuminate\Support\MessageBag();
                foreach ($stockErrors as $error)
                    $errorBag->add('stock_error', $error);

                return redirect()->back()
                    ->withInput()
                    ->withErrors($errorBag)
                    ->with('error', 'Stock validation failed.');
            }

            // Calculate total bulk qty to deduct
            $totalBulkQtyToDeduct = 0;
            foreach ($request->items as $itemData) {
                $totalBulkQtyToDeduct += $itemData['total_bulk_qty'];
            }

            // Process all items - Deduct stock
            foreach ($request->items as $itemData) {
                $bulkItem = Item::findOrFail($itemData['item_id']);
                $qtyToDeduct = $itemData['total_bulk_qty'];

                // Deduct from opening stock first
                if ($bulkItem->opening_stock >= $qtyToDeduct) {
                    $bulkItem->decrement('opening_stock', $qtyToDeduct);
                    $qtyToDeduct = 0;
                } else {
                    $deductedFromItem = $bulkItem->opening_stock;
                    $bulkItem->update(['opening_stock' => 0]);
                    $qtyToDeduct -= $deductedFromItem;
                }

                // Deduct remaining qty from purchase invoices
                if ($qtyToDeduct > 0) {
                    $purchaseInvoiceItems = \DB::table('purchase_invoice_items')
                        ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                        ->where('purchase_invoice_items.item', $itemData['item_id'])
                        ->where('purchase_invoices.warehouse', 'Warehouse')
                        ->where('purchase_invoice_items.qty', '>', 0)
                        ->select('purchase_invoice_items.id', 'purchase_invoice_items.qty')
                        ->get();

                    foreach ($purchaseInvoiceItems as $piItem) {
                        if ($qtyToDeduct <= 0)
                            break;

                        if ($piItem->qty >= $qtyToDeduct) {
                            \DB::table('purchase_invoice_items')
                                ->where('id', $piItem->id)
                                ->decrement('qty', $qtyToDeduct);
                            $qtyToDeduct = 0;
                        } else {
                            \DB::table('purchase_invoice_items')
                                ->where('id', $piItem->id)
                                ->update(['qty' => 0]);
                            $qtyToDeduct -= $piItem->qty;
                        }
                    }
                }

                // Create individual repacking record
                Repacking::create([
                    'item_id' => $itemData['item_id'],
                    'item_name' => $bulkItem->item_name,
                    'total_bulk_qty' => $itemData['total_bulk_qty'],
                    'bulk_unit' => $itemData['bulk_unit'],
                    'repack_uom' => $request->item_type === 'existing' ? $bulkItem->measure_unit : $request->repack_uom,
                    'repack_qty' => $request->repack_qty,
                    'cost_per_pack' => $request->item_type === 'existing' ? 0 : $request->cost_per_pack,
                    'selling_price' => $request->item_type === 'existing' ? 0 : $request->selling_price,
                    'repacking_charge' => $itemData['repacking_charge'],
                    'variant_name' => $request->item_type === 'existing' ? 'Added to Existing' : $request->variant_name,
                    'store_id' => $request->item_type === 'existing' ? $bulkItem->store_id : $request->store_id,
                ]);
            }

            // Handle based on item type selection
            if ($request->item_type === 'existing') {
                // Add to existing item stock - ONLY UPDATE OPENING STOCK
                $existingItem = Item::findOrFail($request->existing_item_id);

                // New opening stock = current opening stock - total bulk qty + repack qty
                $existingItem->increment('opening_stock', $request->repack_qty);

                $successMessage = "Repacking completed successfully! Stock updated for item: {$existingItem->item_name}. Opening stock updated: +{$request->repack_qty} (after deducting {$totalBulkQtyToDeduct} bulk qty)";

            } else {
                // Create new repacked item (existing logic)
                $totalCostPerPack = $request->cost_per_pack + $totalRepackingCharge;
                $combinedItemName = implode(' + ', $combinedItemNames);
                $repackedItemName = $combinedItemName . ' - ' . $request->variant_name . ' (' . $request->repack_qty . ' ' . $request->repack_uom . ')';

                $existingRepackedItem = Item::where('item_name', $repackedItemName)
                    ->where('store_id', $request->store_id)
                    ->first();

                if ($existingRepackedItem) {
                    $existingRepackedItem->increment('opening_stock', $request->repack_qty);
                    $existingRepackedItem->update([
                        'wholesale_price' => $totalCostPerPack,
                        'purchase_price' => $totalCostPerPack,
                    ]);
                    $successMessage = "Repacking completed successfully! Stock added to existing repacked item: {$repackedItemName}";
                } else {
                    $firstItem = Item::findOrFail($request->items[0]['item_id']);

                    Item::create([
                        'item_type' => 'repacked',
                        'item_code' => $firstItem->item_code,
                        'hsn_code' => $firstItem->hsn_code,
                        'item_name' => $repackedItemName,
                        'brand_id' => $firstItem->brand_id,
                        'category_id' => $firstItem->category_id,
                        'subcategory_id' => $firstItem->subcategory_id,
                        'discount' => $firstItem->discount,
                        'sales_price' => $request->selling_price,
                        'mrp' => $request->selling_price,
                        'wholesale_price' => $firstItem->wholesale_price,
                        'measure_unit' => $request->repack_uom,
                        'opening_stock' => $request->repack_qty,
                        'opening_unit' => $request->repack_uom,
                        'gst_rate' => $firstItem->gst_rate,
                        'item_description' => 'Repacked from ' . $combinedItemName,
                        'stock_status' => 'Active',
                        'min_stock' => 0,
                        'max_stock' => 1000,
                        'abc_category' => $firstItem->abc_category,
                        'purchase_price' => $totalCostPerPack,
                        'purchase_tax' => $firstItem->purchase_tax,
                        'purchase_gst' => $firstItem->purchase_gst,
                        'store_id' => $request->store_id === 'Warehouse' ? 'Warehouse' : $request->store_id,
                    ]);
                    $successMessage = "Repacking completed successfully! " . count($request->items) . " items combined. New repacked item created: {$repackedItemName}";
                }
            }

            \DB::commit();

            return redirect()->route('inventory.repackinglist')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Repacking Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function repacking_update(Request $request, $id = null)
    {
        // Validate the request
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'total_bulk_qty' => 'required|numeric|min:0.01',
            'bulk_unit' => 'required|string',
            'repack_uom' => 'required|string',
            'repack_qty' => 'required|numeric|min:0.01',
            'cost_per_pack' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'repacking_charge' => 'required|numeric|min:0',
            'variant_name' => 'required|string',
            'store_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value !== 'Warehouse' && !Store::where('id', $value)->exists()) {
                        $fail('The selected store is invalid.');
                    }
                }
            ],
        ]);

        $id = $request->form_repack_id;

        $repacking = Repacking::findOrFail($id);

        // dd($id);
        $newBulkItem = Item::findOrFail($request->item_id);
        $oldBulkItem = Item::findOrFail($repacking->item_id);

        try {
            \DB::beginTransaction();

            // Determine if this was "Added to Existing" type
            $isExistingType = ($repacking->variant_name === 'Added to Existing');

            // Calculate differences
            $oldBulkQty = $repacking->total_bulk_qty;
            $newBulkQty = $request->total_bulk_qty;

            $oldRepackQty = $repacking->repack_qty;
            $newRepackQty = $request->repack_qty;

            // ========== STEP 1: RESTORE OLD BULK ITEM STOCK ==========
            $oldBulkItem->increment('opening_stock', $oldBulkQty);

            // ========== STEP 2: VALIDATE AND DEDUCT NEW BULK ITEM STOCK ==========
            $purchaseInvoiceQty = \DB::table('purchase_invoice_items')
                ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                ->where('purchase_invoice_items.item', $request->item_id)
                ->where('purchase_invoices.warehouse', 'Warehouse')
                ->sum('purchase_invoice_items.qty');

            $salesInvoiceQty = \DB::table('sales_invoice_items')
                ->where('item_id', $request->item_id)
                ->sum('qty');

            $totalAvailableStock = $newBulkItem->opening_stock + $purchaseInvoiceQty - $salesInvoiceQty;

            if ($newBulkQty > $totalAvailableStock) {
                throw new \Exception("Insufficient stock for {$newBulkItem->item_name}. Requested: {$newBulkQty}, Available: {$totalAvailableStock}");
            }

            // Deduct from new bulk item
            $qtyToDeduct = $newBulkQty;

            if ($newBulkItem->opening_stock >= $qtyToDeduct) {
                $newBulkItem->decrement('opening_stock', $qtyToDeduct);
                $qtyToDeduct = 0;
            } else {
                $deductedFromItem = $newBulkItem->opening_stock;
                $newBulkItem->update(['opening_stock' => 0]);
                $qtyToDeduct -= $deductedFromItem;
            }

            // Deduct remaining from purchase invoices if needed
            if ($qtyToDeduct > 0) {
                $purchaseInvoiceItems = \DB::table('purchase_invoice_items')
                    ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                    ->where('purchase_invoice_items.item', $request->item_id)
                    ->where('purchase_invoices.warehouse', 'Warehouse')
                    ->where('purchase_invoice_items.qty', '>', 0)
                    ->select('purchase_invoice_items.id', 'purchase_invoice_items.qty')
                    ->orderBy('purchase_invoice_items.id', 'asc')
                    ->get();

                foreach ($purchaseInvoiceItems as $piItem) {
                    if ($qtyToDeduct <= 0)
                        break;

                    if ($piItem->qty >= $qtyToDeduct) {
                        \DB::table('purchase_invoice_items')
                            ->where('id', $piItem->id)
                            ->decrement('qty', $qtyToDeduct);
                        $qtyToDeduct = 0;
                    } else {
                        \DB::table('purchase_invoice_items')
                            ->where('id', $piItem->id)
                            ->update(['qty' => 0]);
                        $qtyToDeduct -= $piItem->qty;
                    }
                }
            }

            // ========== STEP 3: HANDLE REPACKED ITEM (DESTINATION) ==========
            $totalCostPerPack = $request->cost_per_pack + $request->repacking_charge;

            if ($isExistingType) {
                // ========== FOR "ADDED TO EXISTING" TYPE ==========

                // Remove old repacked quantity from the old bulk item
                if ($oldBulkItem->opening_stock >= $oldRepackQty) {
                    $oldBulkItem->decrement('opening_stock', $oldRepackQty);
                } else {
                    $oldBulkItem->update(['opening_stock' => 0]);
                }

                // Add new repacked quantity to the new bulk item
                $newBulkItem->increment('opening_stock', $newRepackQty);

                // Update prices
                $newBulkItem->update([
                    'wholesale_price' => $totalCostPerPack,
                    'purchase_price' => $totalCostPerPack,
                ]);

            } else {
                // ========== FOR "NEW REPACKED ITEM" TYPE ==========

                // Build the old repacked item name
                $oldRepackedItemName = $oldBulkItem->item_name . ' - ' . $repacking->variant_name .
                    ' (' . $oldRepackQty . ' ' . $repacking->repack_uom . ')';

                // Find the original repacked item
                $originalRepackedItem = Item::where('item_type', 'repacked')
                    ->where('item_name', $oldRepackedItemName)
                    ->where('store_id', $repacking->store_id)
                    ->first();

                // If not found, try finding by description (fallback)
                if (!$originalRepackedItem) {
                    $originalRepackedItem = Item::where('item_type', 'repacked')
                        ->where('item_description', 'LIKE', '%Repacked from ' . $oldBulkItem->item_name . '%')
                        ->where('store_id', $repacking->store_id)
                        ->where('measure_unit', $repacking->repack_uom)
                        ->orderBy('id', 'desc')
                        ->first();
                }

                if (!$originalRepackedItem) {
                    throw new \Exception("Original repacked item not found. Cannot update.");
                }

                // Build new repacked item name
                $newRepackedItemName = $newBulkItem->item_name . ' - ' . $request->variant_name .
                    ' (' . $newRepackQty . ' ' . $request->repack_uom . ')';

                // Check if repacked item details are changing
                $itemDetailsChanged = ($oldRepackedItemName !== $newRepackedItemName) ||
                    ($repacking->store_id != $request->store_id);

                // Calculate stock difference
                $repackQtyDifference = $newRepackQty - $oldRepackQty;

                if ($itemDetailsChanged) {
                    // Item details changed - update ALL details including name
                    $originalRepackedItem->update([
                        'item_code' => $newBulkItem->item_code,
                        'hsn_code' => $newBulkItem->hsn_code,
                        'item_name' => $newRepackedItemName,
                        'brand_id' => $newBulkItem->brand_id,
                        'category_id' => $newBulkItem->category_id,
                        'subcategory_id' => $newBulkItem->subcategory_id,
                        'sales_price' => $request->selling_price,
                        'mrp' => $request->selling_price,
                        'wholesale_price' => $totalCostPerPack,
                        'measure_unit' => $request->repack_uom,
                        'opening_unit' => $request->repack_uom,
                        'gst_rate' => $newBulkItem->gst_rate,
                        'item_description' => 'Repacked from ' . $newBulkItem->item_name,
                        'purchase_price' => $totalCostPerPack,
                        'purchase_tax' => $newBulkItem->purchase_tax,
                        'purchase_gst' => $newBulkItem->purchase_gst,
                        'store_id' => $request->store_id === 'Warehouse' ? 'Warehouse' : $request->store_id,
                    ]);
                } else {
                    // Only prices changed - update prices
                    $originalRepackedItem->update([
                        'sales_price' => $request->selling_price,
                        'mrp' => $request->selling_price,
                        'wholesale_price' => $totalCostPerPack,
                        'purchase_price' => $totalCostPerPack,
                    ]);
                }

                // Adjust stock based on the difference (always do this)
                if ($repackQtyDifference > 0) {
                    $originalRepackedItem->increment('opening_stock', $repackQtyDifference);
                } else if ($repackQtyDifference < 0) {
                    $decreaseAmount = abs($repackQtyDifference);
                    if ($originalRepackedItem->opening_stock >= $decreaseAmount) {
                        $originalRepackedItem->decrement('opening_stock', $decreaseAmount);
                    } else {
                        $originalRepackedItem->update(['opening_stock' => 0]);
                    }
                }
            }

            // ========== STEP 4: UPDATE THE REPACKING RECORD ==========
            $repacking->update([
                'item_id' => $request->item_id,
                'item_name' => $newBulkItem->item_name,
                'total_bulk_qty' => $request->total_bulk_qty,
                'bulk_unit' => $request->bulk_unit,
                'repack_uom' => $request->repack_uom,
                'repack_qty' => $request->repack_qty,
                'cost_per_pack' => $request->cost_per_pack,
                'selling_price' => $request->selling_price,
                'repacking_charge' => $request->repacking_charge,
                'variant_name' => $request->variant_name,
                'store_id' => $request->store_id,
            ]);

            \DB::commit();

            return redirect()->route('inventory.repackinglist')
                ->with('success', 'Repacking updated successfully! All stock adjustments completed.');

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Repacking Update Error: ' . $e->getMessage(), [
                'repacking_id' => $id,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error updating repacking: ' . $e->getMessage()]);
        }
    }


    public function checkItemCode(Request $request)
    {
        $exists = \App\Models\Item::where('item_code', $request->item_code)->exists();
        return response()->json(['exists' => $exists]);
    }

    // Add this new method to get variant names dynamically
    public function getVariantNames()
    {
        $variantNames = Item::distinct()->pluck('item_name')->toArray();
        return response()->json($variantNames);
    }


    // public function item_list()
// {
//     $role = Session::get('role');
//     $storeId = Session::get('store_id');

    //     // Admin sees all items
//     if ($role === 'admin') {
//         $items = Item::with('brand', 'category', 'subcategory')
//                 ->whereIn('item_type', ['Product', 'repacked'])
//                 ->get();
//     }
//     // Manager sees only items added to their store
//     elseif ($role === 'manager') {
//         $items = Item::with('brand', 'category', 'subcategory')
//                     ->where('store_id', $storeId)
//                     ->get();
//     } else {
//         // Optional: restrict others (like employees)
//         return redirect()->back()->with('error', 'Unauthorized access');
//     }

    //     // Calculate current stock for each item
//     foreach ($items as $item) {
//         $openingStock = $item->opening_stock ?? 0;

    //         // Total Purchased Qty
//         $totalPurchasedQty = PurchaseInvoiceItem::where('item', $item->id)->sum('qty');

    //         // Total Sold Qty
//         $totalSoldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');

    //         // Current Stock = Opening + Purchases - Sales
//         $item->current_stock = $openingStock + $totalPurchasedQty - $totalSoldQty;
//     }

    //     // Optional: if you use items1 for dropdowns etc.
//     $items1 = Item::all();

    //     return view('inventory.item_list', compact('items', 'items1'));
// }

    public function item_list()
    {
        $role = Session::get('role');
        $storeId = Session::get('store_id');

        // Base query
        $query = Item::with('brand', 'category', 'subcategory')
            ->whereIn('item_type', ['Product', 'repacked']);

        if ($role === 'manager') {
            $query->where('store_id', $storeId);
        } elseif ($role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $items = $query->get();

        // Get purchased qty grouped
        $purchased = PurchaseInvoiceItem::select('item', DB::raw('SUM(qty) as total'))
            ->groupBy('item')
            ->pluck('total', 'item');

        // Get sold qty grouped
        $sold = SalesInvoiceItem::select('item_id', DB::raw('SUM(qty) as total'))
            ->groupBy('item_id')
            ->pluck('total', 'item_id');

        foreach ($items as $item) {
            $opening = $item->opening_stock ?? 0;
            $purchaseQty = $purchased[$item->id] ?? 0;
            $soldQty = $sold[$item->id] ?? 0;

            $item->current_stock = $opening + $purchaseQty - $soldQty;
        }

        $items1 = Item::all();

        return view('inventory.item_list', compact('items', 'items1'));
    }

    public function item_add()
    {
        $brands = Brand::where('status', 'Active')->get();
        $categories = Category::where('status', 'Active')->get();
        $subcategories = SubCategory::where('status', 'Active')->get();

        return view('inventory.item_add', compact('brands', 'categories', 'subcategories'));
    }
    public function getSubcategories(Request $request)
    {
        $categoryId = $request->category_id;

        $subcategories = SubCategory::where('category_id', $categoryId)
            ->where('status', 'Active')
            ->get();

        if ($subcategories->count()) {
            return response()->json([
                'status' => 'success',
                'data' => $subcategories
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No subcategories found.'
            ]);
        }
    }
    public function store_item(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required',
            'item_code' => 'required|unique:items,item_code',
            'hsn_code' => 'required|string|max:255', // ← Required now
            'item_name' => 'required',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'discount' => 'required',
            'sales_price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'wholesale_price' => 'required|numeric',
            'measure_unit' => 'required',
            'opening_stock' => 'required|numeric',
            'opening_unit' => 'required',
            'gst_rate' => 'required',
            'item_description' => 'nullable|string',
            'stock_status' => 'required',
            'min_stock' => 'required|numeric',
            'max_stock' => 'required|numeric',
            'abc_category' => 'required',
            'purchase_price' => 'required|numeric',
            'purchase_tax' => 'required',
            'purchase_gst' => 'required',
        ]);

        if (Session::get('role') === 'manager') {
            $validated['store_id'] = Session::get('store_id');
        }

        Item::create($validated);


        return redirect()->route('inventory.itemlist')->with('success', 'Item added successfully');
    }





    // app/Http/Controllers/InventoryController.php

    public function item_edit($id)
    {
        $item = Item::findOrFail($id);
        $brands = Brand::where('status', 'Active')->get();
        $categories = Category::where('status', 'Active')->get();
        $subcategories = SubCategory::where('status', 'Active')->get();

        return view('inventory.item_edit', compact('item', 'brands', 'categories', 'subcategories'));
    }


    public function item_update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $item->item_type = $request->item_type;
        $item->item_code = $request->item_code;
        $item->item_name = $request->item_name;
        $item->brand_id = $request->brand_id;
        $item->category_id = $request->category_id;
        $item->subcategory_id = $request->subcategory_id;
        $item->discount = $request->discount; // Discount (%) stored as department
        $item->sales_price = $request->sales_price;
        $item->wholesale_price = $request->wholesale_price;
        $item->mrp = $request->mrp;
        $item->measure_unit = $request->measuring_unit;
        $item->opening_stock = $request->opening_stock;
        $item->gst_rate = $request->gst_rate;
        $item->item_description = $request->description;
        $item->stock_status = $request->status;
        $item->min_stock = $request->min_stock;
        $item->max_stock = $request->max_stock;
        $item->abc_category = $request->abc_category;
        $item->purchase_price = $request->purchase_price;
        $item->purchase_gst = $request->purchase_gst;

        $item->save();

        return redirect()->route('inventory.itemlist')->with('success', 'Item updated successfully!');
    }
    public function item_bulk_upload(Request $request)
    {
        // 1️⃣ Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048'
        ]);

    $file = $request->file('file');
    $extension = strtolower($file->getClientOriginalExtension());

        // 2️⃣ Read CSV / Excel
        if ($extension === 'csv') {
            $data = array_map('str_getcsv', file($file->getRealPath()));
        } else {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $data = $spreadsheet->getActiveSheet()->toArray();
        }

        // Remove header row and reset indexes
        unset($data[0]);
        $data = array_values($data);

        // 3️⃣ Upload file to S3 (optional)
        try {
            $fileName = 'items_' . time() . '_' . $file->getClientOriginalName();

        Storage::disk('s3')->putFileAs(
            'item_bulk_uploads',
            $file,
            $fileName
        );
    } catch (\Exception $e) {
        return back()->with('error', 'S3 Upload Failed: ' . $e->getMessage());
    }

        // 4️⃣ Process data
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $rowIndex => $row) {
            try {
                if (empty(array_filter($row))) {
                    continue; // skip empty rows
                }

                $getValue = fn($index, $default = '') => isset($row[$index]) && trim($row[$index]) !== '' ? trim($row[$index]) : $default;
                $numericValue = fn($index, $default = 0) => is_numeric($getValue($index, $default)) ? (float) $getValue($index, $default) : $default;

                // Check mandatory field
                if (empty($getValue(3))) {
                    $errors[] = "Row " . ($rowIndex + 1) . ": Item name is required";
                    $errorCount++;
                    continue;
                }

                // Create or get brand, category, subcategory
                $brand = Brand::firstOrCreate(
                    ['name' => $getValue(4, 'Default')],
                    ['status' => 'Active']
                );

            $category = Category::firstOrCreate(
                ['name' => $getValue(5, 'Default')],
                ['status' => 'Active']
            );

            $subcategory = SubCategory::firstOrCreate(
                [
                    'name' => $getValue(6, 'Default'),
                    'category_id' => $category->id
                ],
                ['status' => 'Active']
            );

                // Insert or update Item
                Item::updateOrCreate(
                    ['item_code' => $getValue(1)], // unique field
                    [
                        'item_type' => $getValue(0, 'Product'),
                        'hsn_code' => $getValue(2),
                        'item_name' => $getValue(3),
                        'brand_id' => $brand->id,
                        'category_id' => $category->id,
                        'subcategory_id' => $subcategory->id,
                        'discount' => $numericValue(7),
                        'sales_price' => $numericValue(8),
                        'mrp' => $numericValue(9),
                        'wholesale_price' => $numericValue(10),
                        'measure_unit' => $getValue(11, 'PCS'),
                        'opening_stock' => $numericValue(12),
                        'opening_unit' => $getValue(13, 'PCS'),
                        'gst_rate' => $numericValue(14),
                        'item_description' => $getValue(15),
                        'stock_status' => $getValue(16, 'Active'),
                        'min_stock' => $numericValue(17),
                        'max_stock' => $numericValue(18),
                        'abc_category' => $getValue(19, 'C'),
                        'purchase_price' => $numericValue(20),
                        'purchase_tax' => $getValue(21, 'With Tax'),
                        'purchase_gst' => $numericValue(22),
                    ]
                );

            $successCount++;

        } catch (\Exception $e) {
            $errorCount++;
            $errors[] = "Row " . ($rowIndex + 1) . ": " . $e->getMessage();
            \Log::error("Item import failed at row {$rowIndex}", [
                'error' => $e->getMessage()
            ]);
        }
        return redirect()
        ->back()
        ->with('success', 'Items uploaded successfully!');
    }

        // 5️⃣ Response
        $message = "Upload complete! Successfully imported: {$successCount} items.";
        if ($errorCount > 0) {
            $message .= " Failed: {$errorCount} items.";
        }

        return redirect()
            ->back()
            ->with([
                'success' => $message,
                'errors_list' => $errors
            ]);
    }


    public function item_profile($id)
    {
        try {
            // Load item with relationships including batches
            $item = Item::with(['brand', 'category', 'subcategory', 'uoms', 'batches'])->findOrFail($id);

            $uoms = $item->uoms ?? collect();
            $batches = $item->batches ?? collect();
            $openingStock = $item->opening_stock ?? 0;

            // Fetch all purchase records with purchase invoice and vendor info
            $purchaseOrderItems = PurchaseInvoiceItem::with([
                'purchaseInvoice.purchaseOrder.vendor' // Eager load vendor through purchase order
            ])
                ->where('item', $id)
                ->whereHas('purchaseInvoice') // Only get items with valid purchase invoices
                ->get();

            // Create a unified transactions array with opening stock as first transaction
            $transactions = collect();

            // Add Opening Stock as first transaction using item's created_at
            $transactions->push((object) [
                'date' => $item->created_at ? $item->created_at->format('Y-m-d') : '2000-01-01',
                'created_at' => $item->created_at,
                'transaction_type' => 'Opening Stock',
                'invoice_no' => '-',
                'bill_date' => '-',
                'vendor_name' => '-',
                'qty' => $openingStock,
                'closing_stock' => 0,
            ]);

            // Add purchase items as transactions
            foreach ($purchaseOrderItems as $itemEntry) {
                // Additional null check for purchaseInvoice
                if ($itemEntry->purchaseInvoice) {
                    $vendorName = '-';

                    // Get vendor name ONLY from purchase order
                    if (
                        $itemEntry->purchaseInvoice->purchaseOrder &&
                        $itemEntry->purchaseInvoice->purchaseOrder->vendor
                    ) {
                        $vendor = $itemEntry->purchaseInvoice->purchaseOrder->vendor;
                        $vendorName = $vendor->vendorname ?? 'Vendor ID: ' . $vendor->id;
                    }

                    // Everything else from purchase invoice
                    $transactions->push((object) [
                        'date' => $itemEntry->purchaseInvoice->created_at ? $itemEntry->purchaseInvoice->created_at->format('Y-m-d') : '-',
                        'created_at' => $itemEntry->purchaseInvoice->created_at, // From purchase_invoices table
                        'transaction_type' => 'Add Stock',
                        'invoice_no' => $itemEntry->purchaseInvoice->bill_no ?? '-', // From purchase_invoices table
                        'bill_date' => $itemEntry->purchaseInvoice->bill_date ?? '-', // From purchase_invoices table
                        'vendor_name' => $vendorName, // From vendors table (through purchase_orders)
                        'qty' => $itemEntry->qty ?? 0,
                        'closing_stock' => 0,
                    ]);
                }
            }

            // Sort transactions by created_at (purchase invoice created_at)
            $transactions = $transactions->sortBy('created_at')->values();

            // Calculate current stock after each transaction
            $stockRunningTotal = 0;
            foreach ($transactions as $txn) {
                $stockRunningTotal += $txn->qty;
                $txn->closing_stock = $stockRunningTotal;
            }

            // Total Purchased Qty
            $totalPurchasedQty = $purchaseOrderItems->sum('qty');

            // Total Sold Qty
            $totalSoldQty = SalesInvoiceItem::where('item_id', $id)->sum('qty');

            // Current Stock = Opening Stock + Purchases - Sales
            $currentStock = $openingStock + $totalPurchasedQty - $totalSoldQty;

            // Latest Purchase Price
            $latestPurchase = PurchaseInvoiceItem::where('item', $id)
                ->whereHas('purchaseInvoice')
                ->latest('created_at')
                ->first();

            $purchasePrice = $latestPurchase ? ($latestPurchase->price ?? 0) : 0;
            $stockValue = $currentStock * $purchasePrice;

            return view('inventory.item_profile', compact(
                'item',
                'uoms',
                'batches',
                'transactions',
                'currentStock',
                'purchasePrice',
                'stockValue'
            ));

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Item Profile Error for ID: ' . $id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return user-friendly error
            return back()->with('error', 'Unable to load item profile. Please contact support.');
        }
    }

    // Batch CRUD Methods
    public function storeBatch(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'item_code' => 'required|string|max:255',
            'batch_no' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'mfg_date' => 'required|date',
            'exp_date' => 'required|date|after:mfg_date',
        ]);

        try {
            Batch::create([
                'item_id' => $request->item_id,
                'item_code' => $request->item_code,
                'batch_no' => $request->batch_no,
                'price' => $request->price,
                'qty' => $request->qty,
                'mfg_date' => $request->mfg_date,
                'exp_date' => $request->exp_date,
            ]);

            return redirect()->back()->with('success', 'Batch added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding batch: ' . $e->getMessage());
        }
    }

    public function editBatch($id)
    {
        try {
            $batch = Batch::findOrFail($id);
            return response()->json($batch);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Batch not found'], 404);
        }
    }

    public function updateBatch(Request $request, $id)
    {
        $request->validate([
            'item_code' => 'required|string|max:255',
            'batch_no' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'mfg_date' => 'required|date',
            'exp_date' => 'required|date|after:mfg_date',
        ]);

        try {
            $batch = Batch::findOrFail($id);
            $batch->update([
                'item_code' => $request->item_code,
                'batch_no' => $request->batch_no,
                'price' => $request->price,
                'qty' => $request->qty,
                'mfg_date' => $request->mfg_date,
                'exp_date' => $request->exp_date,
            ]);

            return redirect()->back()->with('success', 'Batch updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating batch: ' . $e->getMessage());
        }
    }

    public function deleteBatch($id)
    {
        try {
            $batch = Batch::findOrFail($id);
            $batch->delete();

            return response()->json(['success' => true, 'message' => 'Batch deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting batch: ' . $e->getMessage()], 500);
        }
    }



    public function item_toggle_status($id)
    {
        $item = Item::findOrFail($id);
        $item->stock_status = ($item->stock_status == 'Active') ? 'Inactive' : 'Active';
        $item->save();
        return redirect()->back()->with('success', 'Item status updated!');
    }

    public function getItemDetails($id)
    {
        $item = Item::find($id);
        if ($item) {
            return response()->json([
                'item_name' => $item->item_name,
                'sales_price' => $item->sales_price,
                'purchase_price' => $item->purchase_price,
            ]);
        }
        return response()->json([], 404);
    }

    public function generateBarcode(Request $request)
    {
        $item = Item::findOrFail($request->item_id);

        $barcodeData = [
            'item_code' => $item->item_code,
            'item_name' => $item->item_name,
            'mrp' => $item->mrp, // Fetched from DB
            'net_price' => $item->sales_price, // Fetched from DB
            'barcode_count' => $request->barcode_count,
        ];

        return view('inventory.print_barcode_js', compact('barcodeData'));
    }

    public function generate(Request $request)
    {
        // Validate form inputs
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'mrp' => 'required|numeric|min:0',
            'net_price' => 'required|numeric|min:0',
            'barcode_count' => 'required|integer|min:1',
        ]);

        // Get current session values
        $loginId = Session::get('loginId');
        $role = Session::get('role');
        $store_id = Session::get('store_id'); // Set for employee or manager during login

        // Fallback store_id (if admin, or if store_id not available)
        if ($role === 'admin' || !$store_id) {
            $store_id = 1; // Default store_id for admin
        }

        // Save to database
        DB::table('generated_barcodes')->insert([
            'item_id' => $validated['item_id'],
            'store_id' => $store_id,
            'user_id' => $loginId,
            'mrp' => $validated['mrp'],
            'net_price' => $validated['net_price'],
            'barcode_count' => $validated['barcode_count'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('inventory.itemlist')->with('success', 'Barcode data saved successfully.');
    }
    public function index($itemId)
    {
        $uoms = UOM::where('item_id', $itemId)->get();
        return response()->json($uoms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'uom_type' => 'required',
            'qty' => 'required|integer|min:1',
            'rate_per_box' => 'required|numeric|min:0',
            'closing_stock' => 'required|integer|min:0'
        ]);

        $uom = UOM::create($validated);
        return redirect()->route('inventory.itemprofile', ['id' => $validated['item_id']])
            ->with('success', 'UOM added successfully.');
    }

    public function update(Request $request, $id)
    {
        $uom = UOM::findOrFail($id);

        $validated = $request->validate([
            'uom_type' => 'required',
            'qty' => 'required|integer|min:1',
            'rate_per_box' => 'required|numeric|min:0',
            'closing_stock' => 'required|integer|min:0'
        ]);

        $uom->update($validated);

        // Redirect using item_id from the existing UOM record
        return redirect()->route('inventory.itemprofile', ['id' => $uom->item_id])
            ->with('success', 'UOM updated successfully.');
    }


    public function show($id)
    {
        return response()->json(UOM::findOrFail($id));
    }






    public function transfer_items()
    {
        $stores = Store::where('status', 'Active')->get();
        $items = Item::where('stock_status', 'Active')->get();

        return view('inventory.transfer_items', compact('stores', 'items'));
    }

    public function getItemStock($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // Calculate available stock
        $openingStock = $item->opening_stock ?? 0;

        // Total Purchased Qty from warehouse using purchase_invoice_items
        $purchaseInvoiceQty = \DB::table('purchase_invoice_items')
            ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->where('purchase_invoice_items.item', $id)
            ->where('purchase_invoices.warehouse', 'Warehouse')
            ->sum('purchase_invoice_items.qty');

        // Total Sold Qty
        $salesInvoiceQty = \DB::table('sales_invoice_items')
            ->where('item_id', $id)
            ->sum('qty');

        // Calculate total available stock
        $availableStock = $openingStock + $purchaseInvoiceQty - $salesInvoiceQty;

        return response()->json([
            'item_name' => $item->item_name,
            'item_code' => $item->item_code,
            'available_stock' => $availableStock,
            'unit' => $item->opening_unit,
            'sales_price' => $item->sales_price,
            'purchase_price' => $item->purchase_price
        ]);
    }

    public function store_transfer(Request $request)
    {
        try {
            $validated = $request->validate([
                'store_id' => 'required|exists:stores,id',
                'transfer_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.transfer_qty' => 'nullable|numeric',
                'remarks' => 'nullable|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            \DB::beginTransaction();

            $stockErrors = [];
            $transferredItems = [];

            // Validate stock availability for all items first
            foreach ($request->items as $index => $itemData) {
                $item = Item::find($itemData['item_id']);

                if (!$item) {
                    $stockErrors[] = "Item #" . ($index + 1) . ": Item not found.";
                    continue;
                }

                // Calculate available stock
                $openingStock = $item->opening_stock ?? 0;

                $purchaseInvoiceQty = \DB::table('purchase_invoice_items')
                    ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                    ->where('purchase_invoice_items.item', $itemData['item_id'])
                    ->where('purchase_invoices.warehouse', 'Warehouse')
                    ->sum('purchase_invoice_items.qty');

                $salesInvoiceQty = \DB::table('sales_invoice_items')
                    ->where('item_id', $itemData['item_id'])
                    ->sum('qty');

                $totalAvailableStock = $openingStock + $purchaseInvoiceQty - $salesInvoiceQty;

                // Check if requested quantity exceeds available stock
                $requestedQty = floatval($itemData['transfer_qty'] ?? 0);
                if ($requestedQty > $totalAvailableStock) {
                    $stockErrors[] = "Item {$item->item_name}: Insufficient stock. Available: {$totalAvailableStock}, Requested: {$requestedQty}";
                }
            }

            // If there are stock errors, return them
            if (!empty($stockErrors)) {
                \DB::rollback();

                $errorBag = new \Illuminate\Support\MessageBag();
                foreach ($stockErrors as $error) {
                    $errorBag->add('stock_error', $error);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($errorBag)
                    ->with('error', 'Stock validation failed.');
            }

            // Create main transfer record
            $itemTransfer = ItemTransfer::create([
                'store_id' => $request->store_id,
                'transfer_item_count' => count($request->items),
                'remarks' => $request->remarks,
                'transfer_date' => $request->transfer_date,
                'transferred_by' => session('loginId')
            ]);

            // Process all items and deduct stock
            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                $qtyToDeduct = $itemData['transfer_qty'] ?? 0;

                // First, deduct from Item's opening_stock
                if ($item->opening_stock >= $qtyToDeduct) {
                    $item->decrement('opening_stock', $qtyToDeduct);
                    $qtyToDeduct = 0;
                } else {
                    $deductedFromItem = $item->opening_stock;
                    $item->update(['opening_stock' => 0]);
                    $qtyToDeduct -= $deductedFromItem;
                }

                // If still need to deduct, deduct from PurchaseInvoiceItems
                if ($qtyToDeduct > 0) {
                    $purchaseInvoiceItems = \DB::table('purchase_invoice_items')
                        ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                        ->where('purchase_invoice_items.item', $itemData['item_id'])
                        ->where('purchase_invoices.warehouse', 'Warehouse')
                        ->where('purchase_invoice_items.qty', '>', 0)
                        ->select('purchase_invoice_items.id', 'purchase_invoice_items.qty')
                        ->get();

                    foreach ($purchaseInvoiceItems as $piItem) {
                        if ($qtyToDeduct <= 0)
                            break;

                        if ($piItem->qty >= $qtyToDeduct) {
                            \DB::table('purchase_invoice_items')
                                ->where('id', $piItem->id)
                                ->decrement('qty', $qtyToDeduct);
                            $qtyToDeduct = 0;
                        } else {
                            \DB::table('purchase_invoice_items')
                                ->where('id', $piItem->id)
                                ->update(['qty' => 0]);
                            $qtyToDeduct -= $piItem->qty;
                        }
                    }
                }

                // Create transfer detail record
                ItemTransferDetail::create([
                    'item_transfer_id' => $itemTransfer->id,
                    'item_id' => $itemData['item_id'],
                    'item_name' => $item->item_name,
                    'transfer_qty' => $itemData['transfer_qty'] ?? '0',
                    'unit' => $item->opening_unit
                ]);

                // Check if item already exists in target store
                $existingItem = Item::where('item_code', $item->item_code)
                    ->where('store_id', $request->store_id)
                    ->where('item_type', 'transfer')
                    ->first();

                if ($existingItem) {
                    // Update existing item stock
                    $existingItem->increment('opening_stock', $itemData['transfer_qty'] ?? 0);
                } else {
                    // Create new item entry in the target store with item_type = 'transfer'
                    Item::create([
                        'item_type' => 'transfer',
                        'item_code' => $item->item_code,
                        'hsn_code' => $item->hsn_code,
                        'item_name' => $item->item_name,
                        'brand_id' => $item->brand_id,
                        'category_id' => $item->category_id,
                        'subcategory_id' => $item->subcategory_id,
                        'discount' => $item->discount,
                        'sales_price' => $item->sales_price,
                        'mrp' => $item->mrp,
                        'wholesale_price' => $item->wholesale_price,
                        'measure_unit' => $item->measure_unit,
                        'opening_stock' => $itemData['transfer_qty'] ?? '0',
                        'opening_unit' => $item->opening_unit,
                        'gst_rate' => $item->gst_rate,
                        'item_description' => 'Transferred from Warehouse on ' . $request->transfer_date,
                        'stock_status' => 'Active',
                        'min_stock' => $item->min_stock,
                        'max_stock' => $item->max_stock,
                        'abc_category' => $item->abc_category,
                        'purchase_price' => $item->purchase_price,
                        'purchase_tax' => $item->purchase_tax,
                        'purchase_gst' => $item->purchase_gst,
                        'store_id' => $request->store_id
                    ]);
                }

                $transferredItems[] = $item->item_name;
            }

            \DB::commit();

            return redirect()->route('inventory.transferlist')
                ->with('success', 'Transfer completed successfully! ' . count($transferredItems) . ' items transferred.');

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Transfer Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function store_transfer_batch(Request $request)
    {
        // Increase execution time and memory
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        // Log the incoming request
        \Log::info('Batch transfer request received:', [
            'batch_number' => $request->input('batch_number'),
            'total_batches' => $request->input('total_batches'),
            'items_count' => count($request->input('items', [])),
            'transfer_id' => $request->input('transfer_id')
        ]);

        try {
            // Custom validation with detailed error messages
            $validator = \Validator::make($request->all(), [
                'transfer_id' => 'nullable|integer|exists:item_transfers,id',
                'store_id' => 'required|integer|exists:stores,id',
                'transfer_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|integer|exists:items,id',
                'items.*.transfer_qty' => 'required|numeric|min:0.01',
                'remarks' => 'nullable|string|max:500',
                'batch_number' => 'required|integer|min:1',
                'total_batches' => 'required|integer|min:1'
            ], [
                'batch_number.required' => 'Batch number is missing',
                'total_batches.required' => 'Total batches is missing',
                'items.*.transfer_qty.required' => 'Transfer quantity is required for all items',
                'items.*.transfer_qty.numeric' => 'Transfer quantity must be a number',
                'items.*.transfer_qty.min' => 'Transfer quantity must be at least 0.01'
            ]);

            if ($validator->fails()) {
                \Log::error('Batch Validation Error:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

        } catch (\Exception $e) {
            \Log::error('Validation Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ], 422);
        }

        try {
            \DB::beginTransaction();

            // Get or create transfer record
            if ($request->transfer_id) {
                $itemTransfer = ItemTransfer::findOrFail($request->transfer_id);
                \Log::info('Using existing transfer:', ['id' => $itemTransfer->id]);
            } else {
                $itemTransfer = ItemTransfer::create([
                    'store_id' => $request->store_id,
                    'transfer_item_count' => 0,
                    'remarks' => $request->remarks ?? '',
                    'transfer_date' => $request->transfer_date,
                    'transferred_by' => session('loginId')
                ]);
                \Log::info('Created new transfer:', ['id' => $itemTransfer->id]);
            }

            $transferredCount = 0;
            $skippedCount = 0;
            $errors = [];

            // Process items in this batch
            foreach ($request->items as $index => $itemData) {
                try {
                    // Validate item data
                    if (!isset($itemData['item_id']) || !isset($itemData['transfer_qty'])) {
                        $errors[] = "Item at index {$index}: Missing item_id or transfer_qty";
                        $skippedCount++;
                        continue;
                    }

                    $item = Item::where('id', $itemData['item_id'])
                        ->where('stock_status', 'Active')
                        ->where('item_type', 'Product')
                        ->first();

                    if (!$item) {
                        $errors[] = "Item ID {$itemData['item_id']}: Not found or inactive";
                        $skippedCount++;
                        continue;
                    }

                    $qtyToTransfer = floatval($itemData['transfer_qty']);

                    if ($qtyToTransfer <= 0) {
                        $errors[] = "Item {$item->item_name}: Invalid quantity ({$qtyToTransfer})";
                        $skippedCount++;
                        continue;
                    }

                    // Calculate available stock - USING PURCHASE INVOICES
                    $openingStock = floatval($item->opening_stock ?? 0);

                    $purchaseQty = \DB::table('purchase_invoice_items')
                        ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                        ->where('purchase_invoice_items.item', $item->id)
                        ->where('purchase_invoices.warehouse', 'Warehouse')
                        ->sum('purchase_invoice_items.qty');

                    $salesQty = \DB::table('sales_invoice_items')
                        ->where('item_id', $item->id)
                        ->sum('qty');

                    // CORRECT CALCULATION: Opening + Purchase - Sales
                    $availableStock = $openingStock + floatval($purchaseQty) - floatval($salesQty);

                    // Check if enough stock
                    if ($availableStock < $qtyToTransfer) {
                        $errors[] = "Item {$item->item_name}: Insufficient stock. Available: {$availableStock}, Requested: {$qtyToTransfer}";
                        $skippedCount++;
                        continue;
                    }

                    $qtyToDeduct = $qtyToTransfer;

                    // Deduct from Item's opening_stock
                    if ($item->opening_stock >= $qtyToDeduct) {
                        $item->decrement('opening_stock', $qtyToDeduct);
                        $qtyToDeduct = 0;
                    } else {
                        $deductedFromItem = $item->opening_stock;
                        $item->update(['opening_stock' => 0]);
                        $qtyToDeduct -= $deductedFromItem;
                    }

                    // Deduct from PurchaseInvoiceItems if needed
                    if ($qtyToDeduct > 0) {
                        $purchaseInvoiceItems = \DB::table('purchase_invoice_items')
                            ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                            ->where('purchase_invoice_items.item', $item->id)
                            ->where('purchase_invoices.warehouse', 'Warehouse')
                            ->where('purchase_invoice_items.qty', '>', 0)
                            ->select('purchase_invoice_items.id', 'purchase_invoice_items.qty')
                            ->orderBy('purchase_invoice_items.id')
                            ->get();

                        foreach ($purchaseInvoiceItems as $piItem) {
                            if ($qtyToDeduct <= 0)
                                break;

                            if ($piItem->qty >= $qtyToDeduct) {
                                \DB::table('purchase_invoice_items')
                                    ->where('id', $piItem->id)
                                    ->decrement('qty', $qtyToDeduct);
                                $qtyToDeduct = 0;
                            } else {
                                \DB::table('purchase_invoice_items')
                                    ->where('id', $piItem->id)
                                    ->update(['qty' => 0]);
                                $qtyToDeduct -= $piItem->qty;
                            }
                        }
                    }

                    // Create transfer detail
                    ItemTransferDetail::create([
                        'item_transfer_id' => $itemTransfer->id,
                        'item_id' => $item->id,
                        'item_name' => $item->item_name,
                        'transfer_qty' => $qtyToTransfer,
                        'unit' => $item->opening_unit
                    ]);

                    // Check if item already exists in target store
                    $existingItem = Item::where('item_code', $item->item_code)
                        ->where('store_id', $request->store_id)
                        ->where('item_type', 'transfer')
                        ->first();

                    if ($existingItem) {
                        // Update existing item stock
                        $existingItem->increment('opening_stock', $qtyToTransfer);
                        \Log::info("Updated existing item in store:", [
                            'item_code' => $item->item_code,
                            'added_qty' => $qtyToTransfer
                        ]);
                    } else {
                        // Create new item in target store
                        Item::create([
                            'item_type' => 'transfer',
                            'item_code' => $item->item_code,
                            'hsn_code' => $item->hsn_code,
                            'item_name' => $item->item_name,
                            'brand_id' => $item->brand_id,
                            'category_id' => $item->category_id,
                            'subcategory_id' => $item->subcategory_id,
                            'discount' => $item->discount,
                            'sales_price' => $item->sales_price,
                            'mrp' => $item->mrp,
                            'wholesale_price' => $item->wholesale_price,
                            'measure_unit' => $item->measure_unit,
                            'opening_stock' => $qtyToTransfer,
                            'opening_unit' => $item->opening_unit,
                            'gst_rate' => $item->gst_rate,
                            'item_description' => 'Transferred from Warehouse on ' . $request->transfer_date,
                            'stock_status' => 'Active',
                            'min_stock' => $item->min_stock,
                            'max_stock' => $item->max_stock,
                            'abc_category' => $item->abc_category,
                            'purchase_price' => $item->purchase_price,
                            'purchase_tax' => $item->purchase_tax,
                            'purchase_gst' => $item->purchase_gst,
                            'store_id' => $request->store_id
                        ]);
                        \Log::info("Created new item in store:", [
                            'item_code' => $item->item_code,
                            'qty' => $qtyToTransfer
                        ]);
                    }

                    $transferredCount++;

                } catch (\Exception $e) {
                    \Log::error("Item processing error:", [
                        'item_id' => $itemData['item_id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine()
                    ]);
                    $errors[] = "Error processing item {$itemData['item_id']}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            // Update transfer item count
            if ($transferredCount > 0) {
                $itemTransfer->increment('transfer_item_count', $transferredCount);
            }

            \DB::commit();

            \Log::info("Batch {$request->batch_number} completed:", [
                'transferred' => $transferredCount,
                'skipped' => $skippedCount,
                'errors_count' => count($errors)
            ]);

            return response()->json([
                'success' => true,
                'transfer_id' => $itemTransfer->id,
                'batch_number' => $request->batch_number,
                'processed_count' => $transferredCount,
                'skipped_count' => $skippedCount,
                'total_items' => count($request->items),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Batch Transfer Error:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'batch' => $request->batch_number ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    public function getAllItemsWithStock(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $itemIds = $request->input('item_ids');

        if (!$itemIds || !is_array($itemIds) || count($itemIds) === 0) {
            return response()->json(['items' => []]);
        }

        try {
            // Get items
            $items = Item::where('stock_status', 'Active')
                ->where('item_type', 'Product')
                ->whereIn('id', $itemIds)
                ->select('id', 'item_code', 'item_name', 'opening_unit', 'opening_stock')
                ->get();

            if ($items->isEmpty()) {
                return response()->json(['items' => []]);
            }

            $itemIdsArray = $items->pluck('id')->toArray();

            // Get purchase quantities from purchase_invoice_items
            $purchaseQties = \DB::table('purchase_invoice_items')
                ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
                ->where('purchase_invoices.warehouse', 'Warehouse')
                ->whereIn('purchase_invoice_items.item', $itemIdsArray)
                ->select('purchase_invoice_items.item', \DB::raw('COALESCE(SUM(purchase_invoice_items.qty), 0) as total_qty'))
                ->groupBy('purchase_invoice_items.item')
                ->pluck('total_qty', 'item');

            // Get sales quantities
            $salesQties = \DB::table('sales_invoice_items')
                ->whereIn('item_id', $itemIdsArray)
                ->select('item_id', \DB::raw('COALESCE(SUM(qty), 0) as total_qty'))
                ->groupBy('item_id')
                ->pluck('total_qty', 'item_id');

            $itemsWithStock = [];

            foreach ($items as $item) {
                $openingStock = floatval($item->opening_stock ?? 0);
                $purchaseQty = floatval($purchaseQties[$item->id] ?? 0);
                $salesQty = floatval($salesQties[$item->id] ?? 0);

                // CORRECT CALCULATION: Opening + Purchase - Sales
                $availableStock = $openingStock + $purchaseQty - $salesQty;

                if ($availableStock > 0) {
                    $itemsWithStock[] = [
                        'id' => $item->id,
                        'item_code' => $item->item_code,
                        'item_name' => $item->item_name,
                        'opening_unit' => $item->opening_unit,
                        'available_stock' => round($availableStock, 2)
                    ];
                }
            }

            return response()->json([
                'items' => $itemsWithStock,
                'count' => count($itemsWithStock)
            ]);

        } catch (\Exception $e) {
            \Log::error('getAllItemsWithStock Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch items',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function transfer_list()
    {
        $transfers = ItemTransfer::with(['store', 'transferDetails'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inventory.transfer_list', compact('transfers'));
    }

    public function getAllItems()
    {
        // Only return essential fields for better performance
        $items = Item::where('stock_status', 'Active')
            ->where('item_type', 'Product')
            ->select('id', 'item_code', 'item_name', 'opening_unit', 'opening_stock')
            ->orderBy('item_name')
            ->get();

        // Get all purchase invoice quantities in one query
        $purchaseQties = \DB::table('purchase_invoice_items')
            ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->where('purchase_invoices.warehouse', 'Warehouse')
            ->whereIn('purchase_invoice_items.item', $items->pluck('id'))
            ->select('purchase_invoice_items.item', \DB::raw('SUM(purchase_invoice_items.qty) as total_qty'))
            ->groupBy('purchase_invoice_items.item')
            ->pluck('total_qty', 'item');

        // Get all sales quantities in one query
        $salesQties = \DB::table('sales_invoice_items')
            ->whereIn('item_id', $items->pluck('id'))
            ->select('item_id', \DB::raw('SUM(qty) as total_qty'))
            ->groupBy('item_id')
            ->pluck('total_qty', 'item_id');

        // Filter items with available stock > 0
        $filteredItems = $items->filter(function ($item) use ($purchaseQties, $salesQties) {
            $openingStock = $item->opening_stock ?? 0;
            $purchaseQty = $purchaseQties[$item->id] ?? 0;
            $salesQty = $salesQties[$item->id] ?? 0;

            $availableStock = $openingStock + $purchaseQty - $salesQty;

            return $availableStock > 0;
        })->map(function ($item) {
            return [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'opening_unit' => $item->opening_unit
            ];
        })->values();

        return response()->json([
            'items' => $filteredItems
        ]);
    }

    public function transfer_profile($id)
    {
        $transfer = ItemTransfer::with(['store', 'transferDetails.item'])
            ->findOrFail($id);

        return view('inventory.transfer_profile', compact('transfer'));
    }
    // Show add more items form with pre-filled data
    public function add_more_transfer($id)
    {
        $transfer = ItemTransfer::with('store')->findOrFail($id);
        $stores = Store::where('status', 'Active')->get();
        $items = Item::where('stock_status', 'Active')->get();

        return view('inventory.add_more_transfer', compact('transfer', 'stores', 'items'));
    }

    // Store additional items to existing transfer
    public function store_more_transfer(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.transfer_qty' => 'nullable|numeric|min:0.01',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            \DB::beginTransaction();

            $transfer = ItemTransfer::findOrFail($id);
            $stockErrors = [];
            $transferredItems = [];

            // Validate stock availability for all items first
            foreach ($request->items as $index => $itemData) {
                $item = Item::find($itemData['item_id']);

                if (!$item) {
                    $stockErrors[] = "Item #" . ($index + 1) . ": Item not found.";
                    continue;
                }

                // Calculate available stock
                $openingStock = $item->opening_stock ?? 0;

                $purchaseOrderQty = \DB::table('purchase_order_items')
                    ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                    ->where('purchase_order_items.item_id', $itemData['item_id'])
                    ->where('purchase_orders.warehouse', 'Warehouse')
                    ->sum('purchase_order_items.qty');

                $salesInvoiceQty = \DB::table('sales_invoice_items')
                    ->where('item_id', $itemData['item_id'])
                    ->sum('qty');

                $totalAvailableStock = $openingStock + $purchaseOrderQty + $salesInvoiceQty;
            }

            // If there are stock errors, return them
            if (!empty($stockErrors)) {
                \DB::rollback();

                $errorBag = new \Illuminate\Support\MessageBag();
                foreach ($stockErrors as $error) {
                    $errorBag->add('stock_error', $error);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($errorBag)
                    ->with('error', 'Stock validation failed.');
            }

            $addedItemCount = 0;

            // Process all items and deduct stock
            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                $qtyToDeduct = $itemData['transfer_qty'] ?? 0;

                // First, deduct from Item's opening_stock
                if ($item->opening_stock >= $qtyToDeduct) {
                    $item->decrement('opening_stock', $qtyToDeduct);
                    $qtyToDeduct = 0;
                } else {
                    $deductedFromItem = $item->opening_stock;
                    $item->update(['opening_stock' => 0]);
                    $qtyToDeduct -= $deductedFromItem;
                }

                // If still need to deduct, deduct from PurchaseOrderItems
                if ($qtyToDeduct > 0) {
                    $purchaseOrderItems = \DB::table('purchase_order_items')
                        ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
                        ->where('purchase_order_items.item_id', $itemData['item_id'])
                        ->where('purchase_orders.warehouse', 'Warehouse')
                        ->where('purchase_order_items.qty', '>', 0)
                        ->select('purchase_order_items.id', 'purchase_order_items.qty')
                        ->get();

                    foreach ($purchaseOrderItems as $poItem) {
                        if ($qtyToDeduct <= 0)
                            break;

                        if ($poItem->qty >= $qtyToDeduct) {
                            \DB::table('purchase_order_items')
                                ->where('id', $poItem->id)
                                ->decrement('qty', $qtyToDeduct);
                            $qtyToDeduct = 0;
                        } else {
                            \DB::table('purchase_order_items')
                                ->where('id', $poItem->id)
                                ->update(['qty' => 0]);
                            $qtyToDeduct -= $poItem->qty;
                        }
                    }
                }

                // Create transfer detail record
                ItemTransferDetail::create([
                    'item_transfer_id' => $transfer->id,
                    'item_id' => $itemData['item_id'],
                    'item_name' => $item->item_name,
                    'transfer_qty' => $itemData['transfer_qty'] ?? '0',
                    'unit' => $item->opening_unit
                ]);

                // Create new item entry in the target store with item_type = 'transfer'
                Item::create([
                    'item_type' => 'transfer',
                    'item_code' => $item->item_code,
                    'hsn_code' => $item->hsn_code,
                    'item_name' => $item->item_name,
                    'brand_id' => $item->brand_id,
                    'category_id' => $item->category_id,
                    'subcategory_id' => $item->subcategory_id,
                    'discount' => $item->discount,
                    'sales_price' => $item->sales_price,
                    'mrp' => $item->mrp,
                    'wholesale_price' => $item->wholesale_price,
                    'measure_unit' => $item->measure_unit,
                    'opening_stock' => $itemData['transfer_qty'] ?? '0',
                    'opening_unit' => $item->opening_unit,
                    'gst_rate' => $item->gst_rate,
                    'item_description' => 'Transferred from Warehouse on ' . $transfer->transfer_date,
                    'stock_status' => 'Active',
                    'min_stock' => $item->min_stock,
                    'max_stock' => $item->max_stock,
                    'abc_category' => $item->abc_category,
                    'purchase_price' => $item->purchase_price,
                    'purchase_tax' => $item->purchase_tax,
                    'purchase_gst' => $item->purchase_gst,
                    'store_id' => $transfer->store_id
                ]);

                $transferredItems[] = $item->item_name;
                $addedItemCount++;
            }

            // Update transfer item count
            $transfer->increment('transfer_item_count', $addedItemCount);

            \DB::commit();

            return redirect()->route('inventory.transferprofile', $transfer->id)
                ->with('success', $addedItemCount . ' more items added to transfer successfully!');

        } catch (\Exception $e) {
            \DB::rollback();

            \Log::error('Add More Transfer Error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

}
