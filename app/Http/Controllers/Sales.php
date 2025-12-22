<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\ReturnVoucher;

class Sales extends Controller
{
// public function sales_list()
// {
//     $role = session('role');
//     $salesQuery = SalesInvoice::with(['customer', 'store', 'items']);

//     if ($role === 'manager') {
//         // Manager's store and their own POS bills
//         $storeId = session('store_id');
//         $employeeId = session('employee_id'); // make sure this is stored in session

//         $salesQuery->where(function ($query) use ($storeId, $employeeId) {
//             $query->where('store_id', $storeId)
//                   ->orWhere('employee_id', $employeeId);
//         });
//     }

//     // Admin: gets all data
//     $sales = $salesQuery->latest()->get();

//     return view('sales.list', compact('sales'));
// }

public function sales_list()
{
    $role = session('role');
    $salesQuery = SalesInvoice::with(['customer', 'store', 'items']);

    if ($role === 'manager') {
        $storeId = session('store_id'); // this should match stores.id
        $employeeId = session('employee_id');

        // Manager can see ONLY sales from their store
        $salesQuery->where('store_id', $storeId);

        // If you want to filter even further to show ONLY their own bills, uncomment below:
        // $salesQuery->where('employee_id', $employeeId);
    }

    $sales = $salesQuery->latest()->get();

    return view('sales.list', compact('sales'));
}



    public function sales_profile(Request $request)
    {
        $id = $request->get('id');
        $sale = SalesInvoice::with(['customer', 'store', 'employee', 'items.item'])->findOrFail($id);
        return view('sales.profile', compact('sale'));
    }
    
          public function return_vouchers_list()
    {
        $role = session('role');
        $vouchersQuery = ReturnVoucher::with(['salesInvoice.customer', 'salesInvoice.store', 'salesInvoice.employee']);

        if ($role === 'manager') {
            $storeId = session('store_id');
            
            // Manager can see ONLY vouchers from their store
            $vouchersQuery->whereHas('salesInvoice', function($query) use ($storeId) {
                $query->where('store_id', $storeId);
            });
        }

        $vouchers = $vouchersQuery->latest()->get();

        return view('sales.return', compact('vouchers'));
    }
}

