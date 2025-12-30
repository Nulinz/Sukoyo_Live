<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\Payment;
use App\Models\Expense;
 use App\Models\BankAccount;
use App\Models\ExpenseCategory;
use App\Models\Employee;
use App\Models\SalesInvoice;
use App\Models\CashCollection;
use Illuminate\Support\Facades\DB;
use App\Models\BankTransfer;
use Illuminate\Support\Facades\Session;

class Accounts extends Controller
{

public function payment_list()
{
    $role = session('role');
    $empcode = session('empcode');
    $store_id = session('store_id');

    if ($role === 'admin') {
        $vendors = Vendor::all();
        $payments = Payment::with(['vendor', 'purchaseOrder', 'addedBy'])->latest()->get();
    } elseif ($role === 'manager') {
        // Only vendors for this manager’s store or added by them
        $vendors = Vendor::where('added_by', $empcode)->orWhereHas('addedBy', function ($q) use ($store_id) {
            $q->where('store_id', $store_id);
        })->get();

        $payments = Payment::with(['vendor', 'purchaseOrder', 'addedBy'])
            ->where('created_by', session('loginId'))
            ->latest()
            ->get();
    } else {
        // Default for other roles
        $vendors = [];
        $payments = [];
    }

    return view('accounts.payment', compact('vendors', 'payments'));
}

public function store_payment(Request $request)
{
    $request->validate([
        'vendor_id' => 'required|exists:vendors,id',
        'invoice_id' => 'required|exists:purchase_orders,id',
        'pending_amount' => 'required|numeric',
        'payment_amount' => 'required|numeric',
        'payment_type' => 'required',
        'payment_date' => 'required|date',
        'remarks' => 'nullable|string'
    ]);

    $nowBalance = $request->pending_amount - $request->payment_amount;

    // ✅ Check if creator exists in employees table
    $creatorId = \App\Models\Employee::where('id', session('loginId'))->exists()
        ? session('loginId')
        : null;

    // ✅ Store payment safely
    $payment = Payment::create([
        'vendor_id'         => $request->vendor_id,
        'purchase_order_id' => $request->invoice_id,
        'pending_amount'    => $request->pending_amount,
        'payment_amount'    => $request->payment_amount,
        'now_balance'       => $nowBalance,
        'payment_type'      => $request->payment_type,
        'payment_date'      => $request->payment_date,
        'remarks'           => $request->remarks,
        'created_by'        => $creatorId,
    ]);

    // ✅ Update balance in Purchase Order
    $purchaseOrder = PurchaseOrder::find($request->invoice_id);
    if ($purchaseOrder) {
        $purchaseOrder->balance_amount = $nowBalance;
        $purchaseOrder->save();
    }

    // ✅ Also update balance in Purchase Invoice if linked
    $purchaseInvoice = \App\Models\PurchaseInvoice::where('purchase_order_id', $request->invoice_id)->first();
    if ($purchaseInvoice) {
        $purchaseInvoice->balance_amount = $nowBalance;
        $purchaseInvoice->save();
    }

    return redirect()->back()->with('success', 'Payment recorded and balances updated successfully.');
}

// AJAX: Get Invoices by Vendor
public function getInvoicesByVendor(Request $request)
{
    $invoices = PurchaseOrder::where('vendor_id', $request->vendor_id)->get(['id', 'id']);
    return response()->json($invoices);
}

// AJAX: Get Pending Amount by Invoice
public function getPendingAmount(Request $request)
{
    $invoice = PurchaseOrder::where('id', $request->invoice_id)->first();
    return response()->json(['pending' => $invoice->balance_amount ?? 0]);
}




public function expense()
{
    $role = Session::get('role');
    $empcode = Session::get('empcode'); 
    $creatorId = Session::get('loginId'); 

    // Vendors filtered by created_by (for manager/employee)
    $vendorsQuery = Vendor::where('status', 'Active');
    $expenseCategoriesQuery = ExpenseCategory::with('expenses');
    $expensesQuery = Expense::with(['category', 'vendor']);

    // If not admin, filter by created_by
    if ($role !== 'admin') {
        $vendorsQuery->where('added_by', $empcode);   // ✅ Use empcode for vendors
        $expenseCategoriesQuery->where('created_by', $creatorId); 
        $expensesQuery->where('created_by', $creatorId); 
    }

    $vendors = $vendorsQuery->get();
    $expenseCategories = $expenseCategoriesQuery->get();

    $categoryTotals = Expense::with('category')
        ->selectRaw('expense_category_id, SUM(amount) as total')
        ->when($role !== 'admin', function ($query) use ($creatorId) {
            $query->where('created_by', $creatorId);
        })
        ->groupBy('expense_category_id')
        ->pluck('total', 'expense_category_id');

    $expenses = $expensesQuery->latest()->get();

    return view('accounts.expense', compact('vendors', 'expenseCategories', 'categoryTotals', 'expenses'));
}


public function storeExpenseCategory(Request $request)
{
    $creatorId = \App\Models\Employee::where('id', session('loginId'))->exists()
        ? session('loginId')
        : null;

    $request->validate([
        'name' => 'required',
        'type' => 'nullable|string'
    ]);

    ExpenseCategory::create([
        'name' => $request->name,
        'type' => $request->type,
        'created_by' => $creatorId,
    ]);

    return back()->with('success', 'Expense category added successfully');
}

public function storeExpense(Request $request)
{
    $creatorId = \App\Models\Employee::where('id', session('loginId'))->exists()
        ? session('loginId')
        : null;

    $request->validate([
        'expense_category_id' => 'required|exists:expense_categories,id',
        'expense_no' => 'required',
        'date' => 'required|date',
        'vendor_id' => 'required|exists:vendors,id',
        'payment_type' => 'nullable|string',
        'amount' => 'required|numeric|min:0',
        'balance' => 'nullable|numeric|min:0',
    ]);

    Expense::create(array_merge(
        $request->all(),
        ['created_by' => $creatorId]
    ));

    return back()->with('success', 'Expense added successfully');
}

 public function getExpensesByCategory(Request $request)
    {
        $role = session('role');
        $loginId = session('loginId');
        $categoryId = $request->category_id;

        $expenses = Expense::with(['vendor'])
            ->where('expense_category_id', $categoryId)
            ->when($role === 'manager', function ($query) use ($loginId) {
                $query->where('created_by', $loginId);
            })
            ->latest()
            ->get();

        return view('accounts.partials.expense_table_rows', compact('expenses'))->render();
    }





public function bank_account()
{
    $bankAccounts = BankAccount::all();
    return view('accounts.bank_account', compact('bankAccounts'));
}

public function store_bank_account(Request $request)
{
    $request->validate([
        'bank_name' => 'required|string|max:255',
        'account_holder' => 'required|string|max:255',
        'account_number' => 'required|string|max:50',
        'ifsc_code' => 'required|string|max:50',
        'branch_name' => 'required|string|max:100',
        'upi_id' => 'nullable|string|max:100',
    ]);

    BankAccount::create([
        'bank_name' => $request->bank_name,
        'account_holder' => $request->account_holder,
        'account_number' => $request->account_number,
        'ifsc_code' => $request->ifsc_code,
        'branch_name' => $request->branch_name,
        'upi_id' => $request->upi_id,
        'balance' => 0, // default on creation
    ]);

    return redirect()->back()->with('success', 'Bank account added successfully.');
}



public function cash()
{
    $employees = Employee::with('store')->get();

    $salesData = SalesInvoice::select(
        'employee_id',
        DB::raw("SUM(CASE WHEN mode_of_payment = 'Cash' THEN grand_total ELSE 0 END) as cash_sales"),
        DB::raw("SUM(CASE WHEN mode_of_payment = 'Online' THEN grand_total ELSE 0 END) as online_sales")
    )
    ->groupBy('employee_id')
    ->with('employee', 'store')
    ->get();

    $collectedData = CashCollection::select('employee_id', DB::raw('SUM(amount) as collected_amount'))
        ->groupBy('employee_id')
        ->pluck('collected_amount', 'employee_id');

    return view('accounts.cash', compact('employees', 'salesData', 'collectedData'));
}

public function storeCash(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'amount' => 'required|numeric|min:1'
    ]);

    CashCollection::create([
        'employee_id' => $request->employee_id,
        'amount' => $request->amount,
    ]);

    return redirect()->route('accounts.cash')->with('success', 'Cash collected successfully.');
}


public function storeBankTransfer(Request $request)
{
    $request->validate([
        'transfer_from' => 'required|exists:bank_accounts,id',
        'transfer_to' => 'required|exists:bank_accounts,id|different:transfer_from',
        'date' => 'required|date',
        'amount' => 'required|numeric|min:0.01',
    ]);

    $amount = $request->amount;

    // Subtract from sender
    $from = BankAccount::findOrFail($request->transfer_from);
    if ($from->balance < $amount) {
        return back()->withErrors(['amount' => 'Insufficient balance in source account.']);
    }

    $from->decrement('balance', $amount);

    // Add to receiver
    $to = BankAccount::findOrFail($request->transfer_to);
    $to->increment('balance', $amount);

    // Log transfer
    BankTransfer::create([
        'transfer_from' => $from->id,
        'transfer_to' => $to->id,
        'date' => $request->date,
        'amount' => $amount,
    ]);

    return redirect()->back()->with('success', 'Transfer successful.');
}

}
