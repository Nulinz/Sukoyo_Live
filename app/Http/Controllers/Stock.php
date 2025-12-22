<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Item;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseInvoiceItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Stock extends Controller
{
    // Show low stock view
    public function lowstock_list()
    {
        $categories = Category::with('subcategories')->get();
        return view('stock.lowstock_list', compact('categories'));
    }

    // Return low stock items as JSON for DataTables
    // public function getLowStockItems(Request $request)
    // {
    //     $role = session('role');
    //     $storeId = session('store_id');

    //     // Base query for items
    //     $itemsQuery = Item::with(['brand', 'category', 'subcategory']);

    //     // Optional category/subcategory filter
    //     if ($request->category_id) {
    //         $itemsQuery->where('category_id', $request->category_id);
    //     }
    //     if ($request->subcategory_id) {
    //         $itemsQuery->where('subcategory_id', $request->subcategory_id);
    //     }

    //     $items = $itemsQuery->get();

    //     $result = $items->map(function ($item, $index) use ($role, $storeId) {

    //         // Calculate purchased quantity
    //         $purchasedQuery = PurchaseInvoiceItem::where('item', $item->id);
    //         if ($role === 'manager') {
    //             $purchasedQuery->whereHas('purchaseInvoice', function ($q) use ($storeId) {
    //                 $q->where('warehouse', $storeId);
    //             });
    //         }
    //         $purchasedQty = $purchasedQuery->sum('qty');

    //         // Calculate sold quantity
    //         $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');

    //         $currentStock = $item->opening_stock + $purchasedQty - $soldQty;

    //         // Only include items with stock below minimum
    //         if ($currentStock >= $item->min_stock) {
    //             return null;
    //         }

    //         return [
    //             'index' => $index + 1,
    //             'brand' => optional($item->brand)->name ?? '-',
    //             'item_code' => $item->item_code,
    //             'item_name' => $item->item_name,
    //             'category' => optional($item->category)->name ?? '-',
    //             'subcategory' => optional($item->subcategory)->name ?? '-',
    //             'current_stock' => $currentStock,
    //             'min_stock' => $item->min_stock,
    //             'status' => $currentStock <= 0 ? 'Out of Stock' : 'Low Stock',
    //         ];
    //     });

    //     // Remove nulls from map
    //     $result = $result->filter()->values();

    //     return response()->json($result);
    // }

    public function getLowStockItems(Request $request)
    {
        $query = Item::query()
            ->leftJoin('brands', 'brands.id', '=', 'items.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->leftJoin('subcategories', 'subcategories.id', '=', 'items.subcategory_id')

            // Purchased quantity per item
            ->leftJoinSub(
                DB::table('purchase_invoice_items')
                    ->select('item', DB::raw('SUM(qty) as purchased_qty'))
                    ->groupBy('item'),
                'purchases',
                'purchases.item',
                '=',
                'items.id'
            )

            // Sold quantity per item
            ->leftJoinSub(
                DB::table('sales_invoice_items')
                    ->select('item_id', DB::raw('SUM(qty) as sold_qty'))
                    ->groupBy('item_id'),
                'sales',
                'sales.item_id',
                '=',
                'items.id'
            )

            ->select(
                'items.id',
                'items.item_code',
                'items.item_name',
                'items.min_stock',
                'brands.name as brand',
                'categories.name as category',
                'subcategories.name as subcategory',

                // Calculate current stock
                DB::raw('
                (items.opening_stock 
                + COALESCE(purchases.purchased_qty,0) 
                - COALESCE(sales.sold_qty,0)
                ) as current_stock
            ')
            )

            // Low stock condition
            ->whereRaw('
    (items.opening_stock 
     + COALESCE(purchases.purchased_qty,0) 
     - COALESCE(sales.sold_qty,0)
    ) <= items.min_stock
');


        // Filters
        if ($request->category_id) {
            $query->where('items.category_id', $request->category_id);
        }

        if ($request->subcategory_id) {
            $query->where('items.subcategory_id', $request->subcategory_id);
        }

        $items = $query->limit(500)->get();

        // DataTables format
        $data = $items->map(function ($item, $index) {
            return [
                'index' => $index + 1,
                'brand' => $item->brand ?? '-',
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'category' => $item->category ?? '-',
                'subcategory' => $item->subcategory ?? '-',
                'current_stock' => (int) $item->current_stock,
                'min_stock' => (int) $item->min_stock,
                'status' => $item->current_stock <= 0 ? 'Out of Stock' : 'Low Stock',
            ];
        });

        return response()->json(['data' => $data]);
    }
    // Return overstock items as JSON for DataTables
    public function overstock_list(Request $request)
    {
        $role = session('role');
        $storeId = session('store_id');

        $items = Item::with(['brand', 'category', 'subcategory'])
            ->get()
            ->map(function ($item) use ($role, $storeId) {
                $purchasedQty = PurchaseInvoiceItem::where('item', $item->id)
                    ->when(
                        $role === 'manager',
                        fn($q) =>
                        $q->whereHas('purchaseInvoice', fn($q2) => $q2->where('warehouse', $storeId))
                    )->sum('qty');

                $soldQty = SalesInvoiceItem::where('item_id', $item->id)->sum('qty');
                $currentStock = $item->opening_stock + $purchasedQty - $soldQty;

                return [
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'brand' => optional($item->brand)->name ?? '-',
                    'category' => optional($item->category)->name ?? '-',
                    'subcategory' => optional($item->subcategory)->name ?? '-',
                    'current_stock' => $currentStock,
                    'max_stock' => $item->max_stock,
                    'is_overstocked' => $currentStock > $item->max_stock
                ];
            })
            ->filter(fn($item) => $item['is_overstocked']);

        return response()->json($items);
    }

    // Return zero stock / unsold items as JSON for DataTables
    public function zero_list(Request $request)
    {
        $days = $request->get('days', 10);
        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');

        $cutoffDate = Carbon::now()->subDays($days);

        $query = Item::query()
            ->leftJoin('brands', 'brands.id', '=', 'items.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'items.category_id')
            ->leftJoin('subcategories', 'subcategories.id', '=', 'items.subcategory_id')

            // Purchased qty
            ->leftJoinSub(
                DB::table('purchase_invoice_items')
                    ->select('item', DB::raw('SUM(qty) as purchased_qty'))
                    ->groupBy('item'),
                'purchases',
                'purchases.item',
                '=',
                'items.id'
            )

            // Sold qty
            ->leftJoinSub(
                DB::table('sales_invoice_items')
                    ->select('item_id', DB::raw('SUM(qty) as sold_qty'))
                    ->groupBy('item_id'),
                'sales',
                'sales.item_id',
                '=',
                'items.id'
            )

            // Items NOT sold in last X days
            ->whereNotIn('items.id', function ($q) use ($cutoffDate) {
                $q->select('item_id')
                    ->from('sales_invoice_items')
                    ->whereDate('created_at', '>=', $cutoffDate);
            })

            ->when($categoryId, fn($q) => $q->where('items.category_id', $categoryId))
            ->when($subcategoryId, fn($q) => $q->where('items.subcategory_id', $subcategoryId))

            ->select(
                'items.item_code',
                'items.item_name',
                'brands.name as brand',
                'categories.name as category',
                'subcategories.name as subcategory',
                DB::raw('
                (items.opening_stock
                + COALESCE(purchases.purchased_qty,0)
                - COALESCE(sales.sold_qty,0)
                ) as current_stock
            ')
            )

            ->limit(1000); // safety limit

        $items = $query->get();

        return response()->json([
            'data' => $items
        ]);
    }
    public function zeroMovementPage(Request $request)
    {
        $categories = Category::all();

        // default values for Blade
        $days = 10;
        $categoryId = null;
        $subcategoryId = null;

        return view('stock.zero_list', compact(
            'categories',
            'days',
            'categoryId',
            'subcategoryId'
        ));
    }

    // Return subcategories for a category
    public function getSubcategories($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->get();
        return response()->json($subcategories);
    }
}
