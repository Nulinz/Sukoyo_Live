<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store as StoreModel; // Use the Store model
 use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;

class Store extends Controller
{
   public function store_list()
{
    $stores = StoreModel::all(); // fetch all stores from DB

    // Define column list dynamically (key = database field, label = human-readable)
    $columns = [
        'store_id'       => 'Store ID',
        'store_name'     => 'Store Name',
        'email'          => 'Email ID',
        'contact_number' => 'Contact Number',
        'city'           => 'City',
        // You can add more if needed: 'state' => 'State', etc.
    ];

    return view('store.list', compact('stores', 'columns'));
}
    public function toggle_status($id)
    {
    $store = StoreModel::findOrFail($id);
        $store->status = ($store->status === 'Active') ? 'Inactive' : 'Active';
        $store->save();

        return redirect()->route('store.list')->with('success', 'Store status updated successfully.');
    }



public function storeProfile($id)
{
    $store = StoreModel::findOrFail($id);

    // Get all purchase invoices for this store
    $purchaseInvoices = PurchaseInvoice::where('warehouse', $store->store_name)->pluck('id');

    // Total product count (distinct items in purchase invoice items)
    $totalProductCount = PurchaseInvoiceItem::whereIn('purchase_invoice_id', $purchaseInvoices)
        ->distinct('item')
        ->count('item');

    // Total value of all purchase invoices
    $totalValue = PurchaseInvoice::where('warehouse', $store->store_name)->sum('total');

    // Get item-wise details under this store
    $items = PurchaseInvoiceItem::with('itemDetails')
        ->whereIn('purchase_invoice_id', $purchaseInvoices)
        ->get();

    return view('store.profile', compact('store', 'totalProductCount', 'totalValue', 'items'));
}

public function warehouse_list()
{
    // Get purchase invoice items
    $purchaseItems = DB::table('purchase_invoice_items as pii')
        ->join('purchase_invoices as pi', 'pii.purchase_invoice_id', '=', 'pi.id')
        ->join('items as i', 'pii.item', '=', 'i.id')
        ->join('brands as b', 'i.brand_id', '=', 'b.id')
        ->leftJoin(DB::raw("
            (SELECT item_id, SUM(qty) as sold_qty 
            FROM sales_invoice_items 
            GROUP BY item_id
            ) as si
        "), 'pii.item', '=', 'si.item_id')
        ->select(
            'i.item_code',
            'pii.item as item_id',
            'i.item_name',
            'b.name as brand_name',
            'pii.unit',
            DB::raw('SUM(pii.qty) as total_purchased_qty'),
            DB::raw('COALESCE(SUM(si.sold_qty), 0) as total_sold_qty'),
            DB::raw('SUM(pii.qty) - COALESCE(SUM(si.sold_qty), 0) as available_qty'),
            DB::raw('AVG(pii.price) as unit_price'),
            DB::raw('(SUM(pii.qty) - COALESCE(SUM(si.sold_qty), 0)) * AVG(pii.price) as total_value')
        )
        ->where('pi.warehouse', 'Warehouse')
        ->groupBy('i.item_code', 'pii.item', 'i.item_name', 'b.name', 'pii.unit');

    // Get repacked items
    $repackedItems = DB::table('items as i')
        ->join('brands as b', 'i.brand_id', '=', 'b.id')
        ->leftJoin(DB::raw("
            (SELECT item_id, SUM(qty) as sold_qty 
            FROM sales_invoice_items 
            GROUP BY item_id
            ) as si
        "), 'i.id', '=', 'si.item_id')
        ->select(
            'i.item_code',
            'i.id as item_id',
            'i.item_name',
            'b.name as brand_name',
            'i.opening_unit as unit',
            DB::raw('0 as total_purchased_qty'),
            DB::raw('COALESCE(SUM(si.sold_qty), 0) as total_sold_qty'),
            DB::raw('i.opening_stock - COALESCE(SUM(si.sold_qty), 0) as available_qty'),
            DB::raw('i.purchase_price as unit_price'),
            DB::raw('(i.opening_stock - COALESCE(SUM(si.sold_qty), 0)) * i.purchase_price as total_value')
        )
        ->where('i.item_type', 'repacked')
        ->where('i.store_id', 'Warehouse')
        ->groupBy('i.item_code', 'i.id', 'i.item_name', 'b.name', 'i.opening_unit', 'i.opening_stock', 'i.purchase_price');

    // Union both queries
    $items = $purchaseItems->union($repackedItems)
        ->orderBy('item_name')
        ->get();

    return view('warehouse.list', compact('items'));
}


    // Store new store data
    public function store_add(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|unique:stores',
            'store_name' => 'required',
            'email' => 'required|email|unique:stores',
            'contact_number' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required',
            'geo_location' => 'nullable',
        ]);

        StoreModel::create($validated);

        return redirect()->route('store.list')->with('success', 'Store added successfully!');
    }

    // Update store data
    public function store_update(Request $request, $id)
    {
        $store = StoreModel::findOrFail($id);

        $validated = $request->validate([
            'store_id' => 'required|unique:stores,store_id,' . $store->id,
            'store_name' => 'required',
            'email' => 'required|email|unique:stores,email,' . $store->id,
            'contact_number' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required',
            'geo_location' => 'nullable',
        ]);

        $store->update($validated);

        return redirect()->route('store.list')->with('success', 'Store updated successfully!');
    }
}
