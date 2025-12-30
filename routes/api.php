<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemApiController;


Route::get('/demo-api', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working!',
    ]);
});
Route::get('items/barcode-list', [ItemApiController::class, 'barcodeList']);


Route::get('/items/barcode/{barcode}', [ItemApiController::class, 'getByBarcode']);

Route::get('items/barcode', [ItemApiController::class, 'index']);

Route::delete('/generated-barcodes/{store_id}/{item_code}', [ItemApiController::class, 'destroyByStoreAndItem']);
