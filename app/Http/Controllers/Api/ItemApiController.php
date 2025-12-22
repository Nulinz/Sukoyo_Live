<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\GeneratedBarcode;

class ItemApiController extends Controller
{
    public function barcodeList()
    {
        $items = Item::select('item_code as barcode', 'item_name', 'mrp', 'sales_price')->get();

        return response()->json([
            'status' => true,
            'data' => $items
        ]);
    }

    public function getByBarcode($barcode)
    {
        $item = Item::where('item_code', $barcode)
                    ->select('item_code as barcode', 'item_name', 'mrp', 'sales_price')
                    ->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $item
        ]);
    }

public function index()
{
    $barcodes = GeneratedBarcode::with('item:id,item_code,item_name')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($barcode) {
            return [
                'id' => $barcode->id,
                'item_id' => $barcode->item_id,
                'item_code' => $barcode->item->item_code ?? null,
                'item_name' => $barcode->item->item_name ?? null,
                'mrp' => $barcode->mrp,
                'net_price' => $barcode->net_price,
                'barcode_count' => $barcode->barcode_count,
                 'store_id'=> $barcode->store_id,
                 'user_id'=> $barcode->user_id,
                'created_at' => $barcode->created_at,
            ];
        });

    return response()->json([
        'status' => 'success',
        'data' => $barcodes,
    ]);
}

public function destroyByStoreAndItem($store_id, $item_code)
{
    $item = Item::where('item_code', $item_code)->first();

    if (!$item) {
        return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
    }

    $deleted = GeneratedBarcode::where('store_id', $store_id)
        ->where('item_id', $item->id)
        ->delete();

    if ($deleted > 0) {
        return response()->json([
            'status' => 'success',
            'message' => "$deleted barcode record(s) deleted.",
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'No matching records found.',
    ], 404);
}



}
