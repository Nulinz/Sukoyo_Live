<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Item;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseInvoiceItem;
class Stock extends Controller
{


public function lowstock_list()
{
    $categories = Category::with('subcategories')->get();
    return view('stock.lowstock_list', compact('categories'));
}

public function getLowStockItems(Request $request)
{
    $role = session('role');
    $storeId = session('store_id');

    // Fetch all items first
    $items = Item::with(['brand', 'category', 'subcategory'])
        ->select('items.*')
        ->get()
        ->filter(function ($item) use ($role, $storeId) {
            // Sum purchasedQty from store-specific purchase orders if manager
            $purchasedQuery = PurchaseInvoiceItem::where('item', $item->id);
            if ($role === 'manager') {
                $purchasedQuery->whereHas('purchaseInvoice', function ($q) use ($storeId) {
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
    $result = $items->values()->map(function ($item, $index) {
        $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');
        $purchasedQty = PurchaseInvoiceItem::where('item', $item->id)->sum('qty');
        $currentStock = $item->opening_stock + $purchasedQty - $soldQty;

        return [
            'index' => $index + 1,
            'brand' => optional($item->brand)->name ?? '-',
            'item_code' => $item->item_code,
            'item_name' => $item->item_name,
            'category' => optional($item->category)->name ?? '-',
            'subcategory' => optional($item->subcategory)->name ?? '-',
            'current_stock' => $currentStock,
            'status' => $currentStock <= 0 ? 'Out of Stock' : 'Low Stock',
        ];
    });

    return response()->json($result);
}



public function overstock_list(Request $request)
{
    $role = session('role');
    $storeId = session('store_id');
    $categories = Category::all();

    $items = Item::with(['brand', 'category', 'subcategory'])
        ->get()
        ->map(function ($item) use ($role, $storeId) {
            $purchasedQuery = PurchaseInvoiceItem::where('item', $item->id);

            if ($role === 'manager') {
                $purchasedQuery->whereHas('purchaseInvoice', function ($q) use ($storeId) {
                    $q->where('warehouse', $storeId);
                });
            }

            $purchasedQty = $purchasedQuery->sum('qty');
            $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');
            $currentStock = $item->opening_stock + $purchasedQty - $soldQty;

            $item->current_stock = $currentStock;
            $item->is_overstocked = $currentStock > $item->max_stock;
            return $item;
        })
        ->filter(function ($item) use ($request) {
            return $item->is_overstocked &&
                (!$request->category || $item->category_id == $request->category) &&
                (!$request->subcategory || $item->subcategory_id == $request->subcategory);
        });

    return view('stock.overstock_list', compact('categories', 'items'));
}


    public function getSubcategories($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }



public function zero_list(Request $request)
{
    $role = session('role');
    $storeId = session('store_id');

    $days = $request->get('days', 10);
    $categoryId = $request->get('category');
    $subcategoryId = $request->get('subcategory');

    $cutoffDate = Carbon::now()->subDays($days);

    $soldItemIds = SalesInvoiceItem::whereHas('salesInvoice', function ($q) use ($cutoffDate) {
        $q->whereDate('created_at', '>=', $cutoffDate);
    })->pluck('item_id')->unique();

    $itemsQuery = Item::with(['brand', 'category', 'subcategory'])
        ->whereNotIn('id', $soldItemIds);

    if ($categoryId) {
        $itemsQuery->where('category_id', $categoryId);
    }

    if ($subcategoryId) {
        $itemsQuery->where('subcategory_id', $subcategoryId);
    }

    $items = $itemsQuery->get()->map(function ($item) use ($role, $storeId) {
        $purchasedQuery = PurchaseInvoiceItem::where('item', $item->id);

        if ($role === 'manager') {
            $purchasedQuery->whereHas('purchaseInvoice', function ($q) use ($storeId) {
                $q->where('warehouse', $storeId);
            });
        }

        $purchasedQty = $purchasedQuery->sum('qty');
        $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');
        $item->current_stock = $item->opening_stock + $purchasedQty - $soldQty;
        return $item;
    });

    $categories = Category::all();

    return view('stock.zero_list', compact('items', 'categories', 'days', 'categoryId', 'subcategoryId'));
}

    
}
