<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoyaltyPoint;
use App\Models\GiftCard;
use App\Models\Voucher;
use App\Models\Brand;
use App\Models\SalesInvoice;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Item;

class Offers extends Controller
{
public function lp_list()
{
    $role = session('role');
    $empcode = session('empcode');
    $store_id = session('store_id');
    $loginId = session('loginId');

    if ($role === 'admin') {
        // Admin sees all loyalty points
        $loyaltyPoints = LoyaltyPoint::all();
    } elseif ($role === 'manager') {
        // Manager sees loyalty points only for their store
        $loyaltyPoints = LoyaltyPoint::where('store_id', $store_id)
            ->where('created_by', $loginId) // loyalty rules created by this manager
            ->get();
    } else {
        // Other roles - restrict or empty
        $loyaltyPoints = collect(); // empty collection
    }

    return view('offers.lp_list', compact('loyaltyPoints'));
}



    public function lp_edit()
    {
        return view('offers.lp_edit');
    }

public function lp_store(Request $request)
{
    $validated = $request->validate([
        'earn_amt' => 'required|numeric',
        'earn_points' => 'required|integer',
        'min_invoice_for_earning' => 'required|numeric',
        'redeem_amt' => 'required|numeric',
        'redeem_points' => 'required|integer',
        'max_percent_invoice' => 'required|numeric',
        'min_invoice_for_redeem' => 'required|numeric',
    ]);

    $validated['store_id'] = session('store_id');
    $validated['created_by'] = session('loginId');

    LoyaltyPoint::create($validated);

    return redirect()->route('offers.lplist')
        ->with('success', 'Loyalty Points Updated Successfully');
}


public function gift_list()
{
    $role = session('role');
    $empcode = session('empcode');
    $store_id = session('store_id');
    $loginId = session('loginId');

    if ($role === 'admin') {
        // Admin sees all
        $giftcards = GiftCard::all();
    } elseif ($role === 'manager') {
        // Manager sees only gift cards created by them for their store
        $giftcards = GiftCard::where('store_id', $store_id)
            ->where('created_by', $loginId)
            ->get();
    } else {
        // Other roles - nothing (or customize if needed)
        $giftcards = collect();
    }

    return view('offers.gift_list', compact('giftcards'));
}


    public function gift_add()
    {
        return view('offers.gift_add');
    }
public function gift_store(Request $request)
{
    $validated = $request->validate([
        'card_code'   => 'required|string',
        'no_of_cards' => 'required|integer',
        'card_type'   => 'required|string',
        'card_value'  => 'required|numeric',
        'issue_date'  => 'required|date',
        'expiry_date' => 'required|date|after_or_equal:issue_date',
        'reloadable'  => 'required|boolean',
    ]);

    $validated['store_id'] = session('store_id');
    $validated['created_by'] = session('loginId');

    GiftCard::create($validated);

    return redirect()->route('offers.giftlist')
        ->with('success', 'Gift card added successfully');
}


public function gift_profile($id)
{
    try {
        // Find the gift card by ID
        $giftCard = GiftCard::with(['customer', 'store', 'employee'])->findOrFail($id);
        
        // Get all sales invoices that used this gift card
        $transactions = SalesInvoice::with(['customer', 'employee', 'store'])
            ->where('gift_card_code', $giftCard->card_code)
            ->orderBy('invoice_date', 'desc')
            ->get();
        
        // Calculate total used amount
        $totalUsed = $transactions->sum('gift_card_amount');
        
        // Calculate balance
        $balance = $giftCard->value - $totalUsed;
        
        // Prepare data for the view
        $data = [
            'giftCard' => $giftCard,
            'transactions' => $transactions,
            'totalUsed' => $totalUsed,
            'balance' => max(0, $balance), // Ensure balance is never negative
            'status' => $balance > 0 ? 'Active' : 'Expired'
        ];
        
        return view('offers.gift_profile', $data);
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gift card not found.');
    }
}

public function voucher_list()
{
    $role = session('role');
    $store_id = session('store_id');
    $loginId = session('loginId');

    if ($role === 'admin') {
        // Admin sees all vouchers
        $vouchers = Voucher::all();
    } elseif ($role === 'manager') {
        // Manager sees only vouchers created by them for their store
        $vouchers = Voucher::where('store_id', $store_id)
            ->where('employee_id', $loginId)
            ->get();
    } else {
        // Other roles - nothing
        $vouchers = collect();
    }

    return view('offers.voucher_list', compact('vouchers'));
}

   public function voucher_add()
{
    $brands = Brand::all();
    return view('offers.voucher_add', compact('brands'));
}


public function voucher_store(Request $request)
{
    $validated = $request->validate([
        'voucher_code' => 'required|string',
        'voucher_name' => 'required|string',
        'no_of_cards' => 'required|integer|min:1',
        'discount_value' => 'required|numeric|min:0',
        'redeemable_brand' => 'nullable|string',
        'redeemable_category' => 'nullable|string',
        'redeemable_subcategory' => 'nullable|string',
        'redeemable_item' => 'nullable|string',
        'issue_date' => 'required|date',
        'expiry_date' => 'required|date|after_or_equal:issue_date',
    ]);

    $voucher = new Voucher();
    $voucher->voucher_code = $validated['voucher_code'];
    $voucher->voucher_name = $validated['voucher_name'];
    $voucher->no_of_cards = $validated['no_of_cards'];
    $voucher->discount_value = $validated['discount_value'];
    $voucher->redeemable_brand = $validated['redeemable_brand'] ?? null;
    $voucher->redeemable_category = $validated['redeemable_category'] ?? null;
    $voucher->redeemable_subcategory = $validated['redeemable_subcategory'] ?? null;
    $voucher->redeemable_item = $validated['redeemable_item'] ?? null;
    $voucher->issue_date = $validated['issue_date'];
    $voucher->expiry_date = $validated['expiry_date'];

    // ✅ Attach store and creator details
    $voucher->store_id = session('store_id');
    $voucher->employee_id = session('loginId');

    $voucher->save();

    return redirect()->route('offers.voucherlist')
        ->with('success', 'Voucher added successfully');
}




public function voucher_profile($id)
{
    try {
        // Find the voucher by ID
        $voucher = Voucher::with(['customer', 'store', 'employee'])->findOrFail($id);
        
        // Get all sales invoices that used this voucher
        $transactions = SalesInvoice::with(['customer', 'employee', 'store'])
            ->where('voucher_code', $voucher->voucher_code)
            ->orderBy('invoice_date', 'desc')
            ->get();
        
        // Calculate total used amount
        $totalUsed = $transactions->sum('voucher_amount');
        
        // Calculate usage count
        $usageCount = $transactions->count();
        
        // Calculate balance based on voucher type
        $balance = 0;
        if ($voucher->discount_type == 'fixed' && $voucher->usage_limit > 0) {
            $remainingUses = max(0, $voucher->usage_limit - $usageCount);
            $balance = $remainingUses * $voucher->discount_value;
        } elseif ($voucher->discount_type == 'percentage') {
            // For percentage vouchers, show remaining uses
            $balance = max(0, $voucher->usage_limit - $usageCount);
        }
        
        // Determine status
        $status = 'Active';
        if ($voucher->expiry_date && $voucher->expiry_date->isPast()) {
            $status = 'Expired';
        } elseif ($voucher->usage_limit > 0 && $usageCount >= $voucher->usage_limit) {
            $status = 'Exhausted';
        } elseif ($voucher->is_active == 0) {
            $status = 'Inactive';
        }
        
        // Prepare data for the view
        $data = [
            'voucher' => $voucher,
            'transactions' => $transactions,
            'totalUsed' => $totalUsed,
            'usageCount' => $usageCount,
            'balance' => $balance,
            'status' => $status
        ];
        
        return view('offers.voucher_profile', $data);
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Voucher not found.');
    }
}


public function getCategories($brandId)
{
    $categories = Category::whereHas('items', function($q) use ($brandId) {
        $q->where('brand_id', $brandId);
    })->distinct()->get();

    return response()->json($categories);
}

public function getSubcategories($categoryId)
{
    $subcategories = SubCategory::where('category_id', $categoryId)->get();
    return response()->json($subcategories);
}

public function getItems($brandId, $categoryId, $subcatId)
{
    $items = Item::where('brand_id', $brandId)
                ->where('category_id', $categoryId)
                ->where('subcategory_id', $subcatId)
                ->get();
    return response()->json($items);
}

}
