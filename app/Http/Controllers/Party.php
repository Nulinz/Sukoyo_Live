<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use App\Models\Enquiry;
use App\Models\Store;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use App\Models\PurchaseInvoice;
class Party extends Controller
{
    
public function vendor_list()
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        if ($role === 'admin') {
            // Admin can see all vendors
            $vendors = Vendor::all();
        } elseif ($role === 'manager') {
            // Manager can only see vendors they added
            $vendors = Vendor::where('added_by', $empcode)->get();
        } else {
            // For other roles, you might want to restrict access
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        return view('party.vendor_list', compact('vendors'));
    }

    public function vendor_toggle_status($id)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        $vendor = Vendor::findOrFail($id);
        
        // Check if manager is trying to modify vendor they didn't add
        if ($role === 'manager' && $vendor->added_by !== $empcode) {
            return redirect()->back()->with('error', 'You can only modify vendors you added.');
        }
        
        $vendor->status = ($vendor->status == 'Active') ? 'Inactive' : 'Active';
        $vendor->save();

        return redirect()->back()->with('success', 'Vendor status updated successfully!');
    }

    public function vendor_add()
    {
        return view('party.vendor_add');
    }

public function vendor_store(Request $request) {
    $validated = $request->validate([
        'vendorname' => 'required|string|max:255',
        'contact' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'openbalance' => 'nullable|numeric|min:0',
        'tax' => 'nullable|string',
        'gst' => 'nullable|string|max:50',
        'panno' => 'nullable|string|max:50',
        'creditperiod' => 'required|integer|min:0',
        'creditlimit' => 'nullable|numeric|min:0',
        'billaddress' => 'nullable|string',
        'shipaddress' => 'nullable|string',
    ]);

    $empcode = Session::get('empcode');
    $empname = Session::get('empname');

    $vendor = Vendor::create([
        'vendorname' => $request->vendorname,
        'contact' => $request->contact,
        'email' => $request->email,
        'openbalance' => $request->openbalance ?? 0, // Default to 0 if not provided
        'tax' => $request->tax ?? 'Without Tax', // Default value
        'topay' => $request->has('topay'),
        'tocollect' => $request->has('tocollect'),
        'gst' => $request->gst,
        'panno' => $request->panno,
        'creditperiod' => $request->creditperiod ?? 0, // Default to 0 if not provided
        'creditlimit' => $request->creditlimit ?? 0, // Default to 0 if not provided
        'billaddress' => $request->billaddress,
        'shipaddress' => $request->shipaddress,
        'added_by' => $empcode,
        'added_by_name' => $empname,
    ]);

    return redirect()->route('party.vendorlist')->with('success', 'Vendor added successfully!');
}

    public function vendor_edit($id)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        $vendor = Vendor::findOrFail($id);
        
        // Check if manager is trying to edit vendor they didn't add
        if ($role === 'manager' && $vendor->added_by !== $empcode) {
            return redirect()->back()->with('error', 'You can only edit vendors you added.');
        }
        
        return view('party.vendor_edit', compact('vendor'));
    }

    public function vendor_update(Request $request, $id)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        $vendor = Vendor::findOrFail($id);
        
        // Check if manager is trying to update vendor they didn't add
        if ($role === 'manager' && $vendor->added_by !== $empcode) {
            return redirect()->back()->with('error', 'You can only update vendors you added.');
        }
        
        $vendor->update($request->all());

        return redirect()->route('party.vendorlist')->with('success', 'Vendor updated successfully!');
    }

public function vendor_profile(Request $request, $id)
{
    $role = Session::get('role');
    $empcode = Session::get('empcode');
    
    $vendor = Vendor::findOrFail($id);
    
    // Check if manager is trying to view vendor they didn't add
    if ($role === 'manager' && $vendor->added_by !== $empcode) {
        return redirect()->back()->with('error', 'You can only view vendors you added.');
    }
    
    // Handle date filter from dropdown
    $range = $request->input('range', '7days'); // default to last 7 days
    $today = Carbon::today();
    
    switch ($range) {
        case '30days':
            $from = $today->copy()->subDays(30);
            break;
        case 'month':
            $from = $today->copy()->startOfMonth();
            break;
        case '3months':
            $from = $today->copy()->subMonths(3);
            break;
        case '6months':
            $from = $today->copy()->subMonths(6);
            break;
        case '9months':
            $from = $today->copy()->subMonths(9);
            break;
        case '12months':
            $from = $today->copy()->subMonths(12);
            break;
        case '18months':
            $from = $today->copy()->subMonths(18);
            break;
        case '1year':
            $from = $today->copy()->subYear();
            break;
        case '2years':
            $from = $today->copy()->subYears(2);
            break;
        default: // '7days'
            $from = $today->copy()->subDays(7);
            break;
    }

    $to = $today;
    
    // Initialize transactions array
    $transactions = collect();
    
    // Add opening balance as first transaction (if exists)
    if ($vendor->openbalance && $vendor->openbalance > 0) {
        $transactions->push([
            'date' => $from->format('Y-m-d'), // Show at start of period
            'voucher' => 'Opening Balance',
            'credit' => $vendor->openbalance, // Pending amount
            'debit' => null, // Paid amount
            'balance' => $vendor->openbalance,
            'sort_order' => 0 // To ensure it appears first
        ]);
    }
    
    // Get Purchase Invoices in date range (instead of Purchase Orders)
    $purchaseInvoices = PurchaseInvoice::with(['purchaseInvoiceItems.itemDetails'])
        ->where('contact', $vendor->contact)
        ->whereBetween('bill_date', [$from, $to])
        ->orderBy('bill_date')
        ->get();
    
    $runningBalance = $vendor->openbalance ?? 0;
    
    // Process Purchase Invoices - create two transactions for each invoice
    foreach ($purchaseInvoices as $invoice) {
        // First transaction: Total amount (Pending)
        $runningBalance += $invoice->total;
        $transactions->push([
            'date' => $invoice->bill_date,
            'voucher' => 'Purchase Invoice - ' . $invoice->bill_no,
            'credit' => $invoice->total, // Total amount in pending
            'debit' => null,
            'balance' => $runningBalance,
            'sort_order' => 1
        ]);
        
        // Second transaction: Paid amount (if any)
        if ($invoice->paid_amount && $invoice->paid_amount > 0) {
            $runningBalance -= $invoice->paid_amount;
            $transactions->push([
                'date' => $invoice->bill_date,
                'voucher' => 'Payment for Invoice - ' . $invoice->bill_no,
                'credit' => null,
                'debit' => $invoice->paid_amount, // Paid amount
                'balance' => $runningBalance,
                'sort_order' => 2
            ]);
        }
    }
    
    // Get separate payments in date range
    $payments = Payment::where('vendor_id', $id)
        ->whereBetween('payment_date', [$from, $to])
        ->orderBy('payment_date')
        ->get();
    
    // Process separate payments
    foreach ($payments as $payment) {
        $runningBalance -= $payment->payment_amount;
        $transactions->push([
            'date' => $payment->payment_date,
            'voucher' => 'Payment - ' . ($payment->purchaseInvoice->bill_no ?? 'Direct'),
            'credit' => null,
            'debit' => $payment->payment_amount,
            'balance' => $runningBalance,
            'sort_order' => 3
        ]);
    }
    
    // Sort transactions by date DESC, then by sort_order DESC (bottom to top - most recent first)
    $transactions = $transactions->sortBy([
        ['date', 'desc'],
        ['sort_order', 'desc']
    ])->values();
    
    // Get the last balance amount (final balance)
    $lastBalance = $transactions->isNotEmpty() ? $transactions->first()['balance'] : ($vendor->openbalance ?? 0);
    
    return view('party.vendor_profile', compact(
        'vendor',
        'purchaseInvoices',
        'transactions',
        'lastBalance',
        'range',
        'from',
        'to'
    ));
}

    public function vendor_bulk_upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $empcode = Session::get('empcode');
        $empname = Session::get('empname');
        
        $file = $request->file('csv_file');
        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        
        // Remove header row
        $header = array_shift($rows);
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 13) { // Ensure we have enough columns
                try {
                    Vendor::create([
                        'vendorname' => $row[0],
                        'contact' => $row[1],
                        'email' => $row[2],
                        'openbalance' => $row[3],
                        'tax' => $row[4],
                        'topay' => filter_var($row[5], FILTER_VALIDATE_BOOLEAN),
                        'tocollect' => filter_var($row[6], FILTER_VALIDATE_BOOLEAN),
                        'gst' => $row[7],
                        'panno' => $row[8],
                        'creditperiod' => $row[9],
                        'creditlimit' => $row[10],
                        'billaddress' => $row[11],
                        'shipaddress' => $row[12],
                        'added_by' => $empcode, // Track who uploaded this vendor
                        'added_by_name' => $empname,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }
        }
        
        $message = "Bulk upload completed. Success: {$successCount}, Errors: {$errorCount}";
        return redirect()->route('party.vendorlist')->with('success', $message);
    }



public function customer_list()
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        if ($role === 'admin') {
            // Admin can see all customers
            $customers = Customer::all();
        } elseif ($role === 'manager') {
            // Manager can only see customers they added
            $customers = Customer::where('added_by', $empcode)->get();
        } else {
            // For other roles, you might want to restrict access
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        return view('party.customer_list', compact('customers'));
    }

    public function store_customer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'contact' => 'required|unique:customers,contact',
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'pincode' => 'nullable',
        ]);

        $empcode = Session::get('empcode');
        $empname = Session::get('empname');

        // Add role tracking fields to validated data
        $validated['added_by'] = $empcode;
        $validated['added_by_name'] = $empname;
        $validated['status'] = 'Active'; // Set default status

        Customer::create($validated);

        return redirect()->route('party.customerlist')->with('success', 'Customer added!');
    }

    public function toggleCustomerStatus($id)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        $customer = Customer::findOrFail($id);
        
        // Check if manager is trying to modify customer they didn't add
        if ($role === 'manager' && $customer->added_by !== $empcode) {
            return redirect()->back()->with('error', 'You can only modify customers you added.');
        }
        
        $customer->status = ($customer->status == 'Active') ? 'Inactive' : 'Active';
        $customer->save();

        return redirect()->route('party.customerlist')->with('success', 'Customer status updated successfully!');
    }

    public function customer_add()
    {
        return view('party.customer_add');
    }

    public function customer_edit($id)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        $customer = Customer::findOrFail($id);
        
        // Check if manager is trying to edit customer they didn't add
        if ($role === 'manager' && $customer->added_by !== $empcode) {
            return redirect()->back()->with('error', 'You can only edit customers you added.');
        }
        
        return view('party.customer_edit', compact('customer'));
    }

    public function update_customer(Request $request, $id)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        $customer = Customer::findOrFail($id);
        
        // Check if manager is trying to update customer they didn't add
        if ($role === 'manager' && $customer->added_by !== $empcode) {
            return redirect()->back()->with('error', 'You can only update customers you added.');
        }

        $validated = $request->validate([
            'name' => 'required',
            'contact' => 'required|unique:customers,contact,' . $id,
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'pincode' => 'nullable',
        ]);

        $customer->update($validated);

        return redirect()->route('party.customerlist')->with('success', 'Customer updated!');
    }

    public function customer_profile($id)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        
        $customer = Customer::findOrFail($id);
        
        // Check if manager is trying to view customer they didn't add
        if ($role === 'manager' && $customer->added_by !== $empcode) {
            return redirect()->back()->with('error', 'You can only view customers you added.');
        }
        
        // Get sales invoices - also filter by role if needed
        if ($role === 'admin') {
            $sales = SalesInvoice::where('customer_id', $id)->get();
        } elseif ($role === 'manager') {
            // Only show sales created by this manager or their store
            $store_id = Session::get('store_id');
            $sales = SalesInvoice::where('customer_id', $id)
                ->where(function($query) use ($empcode, $store_id) {
                    $query->where('created_by', $empcode)
                          ->orWhere('store_id', $store_id);
                })
                ->get();
        } else {
            $sales = collect(); // Empty collection for other roles
        }

        return view('party.customer_profile', compact('customer', 'sales'));
    }

    public function customer_bulk_upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $empcode = Session::get('empcode');
        $empname = Session::get('empname');
        
        $file = $request->file('csv_file');
        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        
        // Remove header row
        $header = array_shift($rows);
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($rows as $row) {
            if (count($row) >= 6) { // Ensure we have enough columns
                try {
                    // Check if contact already exists
                    $existingCustomer = Customer::where('contact', $row[1])->first();
                    if (!$existingCustomer) {
                        Customer::create([
                            'name' => $row[0],
                            'contact' => $row[1],
                            'address' => $row[2] ?? null,
                            'city' => $row[3] ?? null,
                            'state' => $row[4] ?? null,
                            'pincode' => $row[5] ?? null,
                            'added_by' => $empcode,
                            'added_by_name' => $empname,
                            'status' => 'Active',
                        ]);
                        $successCount++;
                    } else {
                        $errorCount++; // Skip duplicate contact
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }
        }
        
        $message = "Bulk upload completed. Success: {$successCount}, Errors/Duplicates: {$errorCount}";
        return redirect()->route('party.customerlist')->with('success', $message);
    }

    public function customer_search(Request $request)
    {
        $role = Session::get('role');
        $empcode = Session::get('empcode');
        $searchTerm = $request->get('search');
        
        $query = Customer::query();
        
        if ($role === 'manager') {
            $query->where('added_by', $empcode);
        }
        
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('contact', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('city', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        $customers = $query->get();
        
        return view('party.customer_list', compact('customers'));
    }

      public function enquiry_list()
{
    $enquiries = Enquiry::with('store')->latest()->get();
    $stores = Store::all();
    return view('enquiry.list', compact('enquiries', 'stores'));
}

public function store_enquiry(Request $request)
{
    $request->validate([
        'enquiry_no' => 'required',
        'customer_name' => 'required',
        'contact_number' => 'required',
        'item_name' => 'required',
        'store_id' => 'required|exists:stores,id',
    ]);

    Enquiry::create($request->all());

    return redirect()->route('enquiry.list')->with('success', 'Enquiry added successfully');
}
public function update_enquiry(Request $request, $id)
{
    $request->validate([
        'enquiry_no' => 'required',
        'customer_name' => 'required',
        'contact_number' => 'required',
        'item_name' => 'required',
        'store_id' => 'required|exists:stores,id',
    ]);

    $enquiry = Enquiry::findOrFail($id);
    $enquiry->update($request->all());

    return redirect()->route('enquiry.list')->with('success', 'Enquiry updated successfully');
}
public function update_status(Request $request, $id)
{
    $request->validate([
        'status' => 'required|string|max:255'
    ]);

    $enquiry = Enquiry::findOrFail($id);
    $enquiry->update(['status' => $request->status]);

    return redirect()->route('enquiry.list')->with('success', 'Enquiry status updated successfully');
}



}
