<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\Enquiry;
use App\Models\Store;
 use App\Models\Employee;
 use App\Models\Booking;
 use App\Models\ClassModel;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\PurchaseOrder;


class POS_DashboardController extends Controller
{
 

public function enquiry_lists()
{
    $employeeId = Session::get('loginId'); // Get the currently logged-in employee ID

    $employee = Employee::with('store')->find($employeeId);

    // Get only the store the employee belongs to
    $stores = [];
    if ($employee && $employee->store) {
        $stores[] = $employee->store;
    }

    // Fetch only the enquiries created by the logged-in employee
    $enquiries = Enquiry::with(['store', 'employee'])
                    ->where('employee_id', $employeeId)
                    ->latest()
                    ->get();

    return view('pos_dashboard.enquiry', compact('enquiries', 'stores'));
}



public function store_enquiry_data(Request $request)
{
    $request->validate([
        'enquiry_no' => 'required',
        'customer_name' => 'required',
        'contact_number' => 'required',
        'item_name' => 'required',
        'store_id' => 'required|exists:stores,id',
    ]);

    $data = $request->only(['enquiry_no', 'customer_name', 'contact_number', 'item_name', 'store_id']);
    $data['employee_id'] = Session::get('loginId');

    Enquiry::create($data);

    return redirect()->route('enquiry.lists')->with('success', 'Enquiry added successfully');
}


public function update_enquiry_data(Request $request, $id)
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

    return redirect()->route('enquiry.lists')->with('success', 'Enquiry updated successfully');
}





public function sales_invoice_list()
{
    $employeeId = Session::get('loginId'); // adjust this key if your session key is different

    $salesInvoices = SalesInvoice::with(['customer', 'items']) // eager load related models
                        ->where('employee_id', $employeeId)
                        ->latest()
                        ->get();

    return view('pos_dashboard.sales_invoice', compact('salesInvoices'));
}


public function sales_profile_details($id)
{
    $invoice = SalesInvoice::with(['customer', 'items.item'])->findOrFail($id);
    return view('pos_dashboard.sales_profile', compact('invoice'));
}



  public function bookings_list_data()
{
    $employeeId = Session::get('loginId'); // Get currently logged-in employee ID

    $bookings = Booking::where('employee_id', $employeeId)->get();

    return view('pos_dashboard.booking_list', compact('bookings'));
}
 public function bookings_add_data()
    {
        return view('pos_dashboard.booking_add');
    }

    // Store booking data
public function store_booking_data(Request $request)
{
    $request->validate([
        'student_id' => 'required|string',
        'student_name' => 'required|string',
        'email' => 'required|email',
        'contact_number' => 'required|string',
        'date_of_birth' => 'required|date',
        'gender' => 'required|in:Male,Female,Others',
        'guardian_name' => 'required|string',
        'emergency_contact' => 'required|string',
        'address' => 'required|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'pincode' => 'required|string',
        'class_type' => 'required|string',
        'class_name' => 'required|string',
        'booking_date' => 'required|date',
        'booking_time' => 'required',
        'membership' => 'required|string',
        'price' => 'required|numeric'
    ]);

    // Only collect fillable data from the request
    $data = $request->only([
        'student_id', 'student_name', 'email', 'contact_number', 
        'date_of_birth', 'gender', 'guardian_name', 'emergency_contact',
        'address', 'city', 'state', 'pincode',
        'class_type', 'class_name', 'booking_date', 'booking_time',
        'membership', 'price',
    ]);

    // Set the employee_id from the session
    $data['employee_id'] = Session::get('loginId');

    Booking::create($data);

    return redirect()->route('class.bookingslist.data')->with('success', 'Booking added successfully!');
}

 // Get classes based on class type
    public function get_classes_by_type(Request $request)
    {
        $classType = $request->class_type;
        
        $classes = ClassModel::where('class_type', $classType)
                            ->select('class_name')
                            ->distinct()
                            ->get();

        return response()->json($classes);
    }

    // Get class details (date, time) based on class name
    public function get_class_details(Request $request)
    {
        $className = $request->class_name;
        
        $classDetails = ClassModel::where('class_name', $className)
                                ->select('date', 'time')
                                ->get();

        return response()->json($classDetails);
    }




  public function bookings_edit_data($id)
{
    $booking = Booking::findOrFail($id); // Load booking by ID

    // Assume class types and class names come from ClassModel table
    $classTypes = ClassModel::distinct()->pluck('class_type')->toArray();
    $classNames = ClassModel::distinct()->pluck('class_name')->toArray();

    return view('pos_dashboard.booking_edit', compact('booking', 'classTypes', 'classNames'));
}
public function bookings_update_data(Request $request, $id)
{
    $booking = Booking::findOrFail($id);

    $request->validate([
        'stdname' => 'required|string|max:255',
        'email' => 'required|email',
        'contact' => 'required|string',
        'dob' => 'required|date',
        'gender' => 'required|string',
        'guardian' => 'required|string',
        'emgcontact' => 'required|string',
        'address' => 'required|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'pincode' => 'required|string',
        'classtype' => 'required|string',
        'classname' => 'required|string',
        'date' => 'required|date',
        'time' => 'required',
        'membership' => 'required|string',
        'price' => 'required|numeric',
    ]);

    $booking->update([
        'student_name' => $request->stdname,
        'email' => $request->email,
        'contact_number' => $request->contact,
        'date_of_birth' => $request->dob,
        'gender' => $request->gender,
        'guardian_name' => $request->guardian,
        'emergency_contact' => $request->emgcontact,
        'address' => $request->address,
        'city' => $request->city,
        'state' => $request->state,
        'pincode' => $request->pincode,
        'class_type' => $request->classtype,
        'class_name' => $request->classname,
        'booking_date' => $request->date,
        'booking_time' => $request->time,
        'membership' => $request->membership,
        'price' => $request->price,
    ]);

    return redirect()->route('class.bookingslist.data')->with('success', 'Booking updated successfully.');
}

    public function bookings_profile_data($id)
{
    $booking = Booking::findOrFail($id);
    return view('pos_dashboard.booking_profile', compact('booking'));
}
public function update_booking_status(Request $request)
{
    $booking = Booking::findOrFail($request->id);

    $booking->status = ($booking->status === 'Active') ? 'Inactive' : 'Active';
    $booking->save();

    return response()->json([
        'status' => $booking->status,
        'icon' => $booking->status === 'Active' 
            ? '<i class="fas fa-circle-xmark text-danger"></i>' 
            : '<i class="fas fa-circle-check text-success"></i>'
    ]);
}

public function manager()
{
    // Get manager details from session
    $managerId = Session::get('loginId');
    
    if (!$managerId) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    
    // Get manager and store information with relationship
    $manager = Employee::with('store')->find($managerId);
    
    if (!$manager) {
        return redirect()->route('login')->with('error', 'Manager not found.');
    }
    
    // Get store ID from manager's record
    $managerStoreId = $manager->store_id;
    
    // Validate manager has a store assigned
    if (!$managerStoreId || !$manager->store) {
        return redirect()->route('login')->with('error', 'Manager is not assigned to any store.');
    }
    
    // Update session with store_id for future use
    Session::put('store_id', $managerStoreId);
    
    $today = Carbon::today();
    $yesterday = Carbon::yesterday();
    $pastWeek = Carbon::today()->subWeek();
    
    // Store information
    $storeName = $manager->store->store_name;
    $storeCode = $manager->store->store_code ?? 'N/A';
    
    // Today's Sales for the manager's store ONLY
    $todaySales = SalesInvoice::where('store_id', $managerStoreId)
        ->whereDate('invoice_date', $today)
        ->sum('grand_total');

    // Yesterday's Sales for comparison (same store)
    $yesterdaySales = SalesInvoice::where('store_id', $managerStoreId)
        ->whereDate('invoice_date', $yesterday)
        ->sum('grand_total');

    // Calculate sales percentage change
    $salesPercentageChange = 0;
    $salesTrend = 'up';
    if ($yesterdaySales > 0) {
        $salesPercentageChange = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100;
        $salesTrend = $salesPercentageChange >= 0 ? 'up' : 'down';
    } elseif ($todaySales > 0) {
        $salesPercentageChange = 100; // 100% increase if yesterday was 0 but today has sales
    }

    // Today's Purchase for the manager's store ONLY
    // Using warehouse field to match store name - try multiple variations
    $storeVariations = [
        $storeName,
        strtolower($storeName),
        ucfirst(strtolower($storeName)),
        str_replace(' ', '', $storeName), // Remove spaces
        'store' . $managerStoreId, // store1, store2, etc.
    ];
    
    $todayPurchase = PurchaseOrder::whereIn('warehouse', $storeVariations)
        ->whereDate('bill_date', $today)
        ->sum('total');

    // If no purchase found with store variations, try exact match with what we see in database
    if ($todayPurchase == 0) {
        // Get all unique warehouse values for debugging
        $availableWarehouses = PurchaseOrder::distinct()->pluck('warehouse')->toArray();
        
        // Try to find a match (case-insensitive)
        $matchingWarehouse = collect($availableWarehouses)->first(function ($warehouse) use ($storeName) {
            return strtolower($warehouse) == strtolower($storeName) || 
                   str_contains(strtolower($warehouse), strtolower($storeName)) ||
                   str_contains(strtolower($storeName), strtolower($warehouse));
        });
        
        if ($matchingWarehouse) {
            $todayPurchase = PurchaseOrder::where('warehouse', $matchingWarehouse)
                ->whereDate('bill_date', $today)
                ->sum('total');
                
            $pastWeekPurchase = PurchaseOrder::where('warehouse', $matchingWarehouse)
                ->whereBetween('bill_date', [$pastWeek, $today->copy()->subDay()])
                ->sum('total');
        } else {
            $pastWeekPurchase = 0;
        }
    } else {
        // Past week's Purchase for comparison (same store)
        $pastWeekPurchase = PurchaseOrder::whereIn('warehouse', $storeVariations)
            ->whereBetween('bill_date', [$pastWeek, $today->copy()->subDay()])
            ->sum('total');
    }

    // Calculate purchase percentage change
    $purchasePercentageChange = 0;
    $purchaseTrend = 'up';
    if ($pastWeekPurchase > 0) {
        $averagePastWeekPurchase = $pastWeekPurchase / 7;
        $purchasePercentageChange = (($todayPurchase - $averagePastWeekPurchase) / $averagePastWeekPurchase) * 100;
        $purchaseTrend = $purchasePercentageChange >= 0 ? 'up' : 'down';
    } elseif ($todayPurchase > 0) {
        $purchasePercentageChange = 100;
    }

    // Today's Bills count for the manager's store ONLY
    $todayBills = SalesInvoice::where('store_id', $managerStoreId)
        ->whereDate('invoice_date', $today)
        ->count();

    // Yesterday's Bills for comparison (same store)
    $yesterdayBills = SalesInvoice::where('store_id', $managerStoreId)
        ->whereDate('invoice_date', $yesterday)
        ->count();

    // Calculate bills percentage change
    $billsPercentageChange = 0;
    $billsTrend = 'up';
    if ($yesterdayBills > 0) {
        $billsPercentageChange = (($todayBills - $yesterdayBills) / $yesterdayBills) * 100;
        $billsTrend = $billsPercentageChange >= 0 ? 'up' : 'down';
    } elseif ($todayBills > 0) {
        $billsPercentageChange = 100;
    }

    // ENHANCED DYNAMIC POS DATA - STORE SPECIFIC
    // Get POS data by IP address for the specific store
    $posByIP = SalesInvoice::where('store_id', $managerStoreId) // Store specific
        ->whereDate('invoice_date', $today)
        ->whereNotNull('pos_ipaddress')
        ->selectRaw('
            pos_ipaddress,
            employee_id,
            COUNT(*) as transaction_count,
            SUM(grand_total) as total_sales,
            SUM(CASE WHEN mode_of_payment = "cash" THEN grand_total ELSE 0 END) as cash_sales,
            SUM(CASE WHEN mode_of_payment IN ("online", "card", "upi", "gpay", "phonepe", "paytm") THEN grand_total ELSE 0 END) as online_sales,
            SUM(loyalty_points_used) as loyalty_points_used,
            MIN(created_at) as first_transaction,
            MAX(created_at) as last_transaction
        ')
        ->with('employee:id,empname,empcode,store_id')
        ->groupBy('pos_ipaddress', 'employee_id')
        ->orderBy('pos_ipaddress')
        ->get();

    // If no IP-based data, fall back to employee-based grouping for the specific store
    if ($posByIP->isEmpty()) {
        $posData = Employee::where('store_id', $managerStoreId) // Store specific employees
            ->where('designation', 'employee')
            ->select('id', 'empname', 'empcode', 'store_id')
            ->get()
            ->map(function ($employee, $index) use ($today, $managerStoreId) {
                $todaySalesData = SalesInvoice::where('employee_id', $employee->id)
                    ->where('store_id', $managerStoreId) // Double check store
                    ->whereDate('invoice_date', $today)
                    ->selectRaw('
                        COUNT(*) as transaction_count,
                        SUM(grand_total) as total_sales,
                        SUM(CASE WHEN mode_of_payment = "cash" THEN grand_total ELSE 0 END) as cash_sales,
                        SUM(CASE WHEN mode_of_payment IN ("online", "card", "upi", "gpay", "phonepe", "paytm") THEN grand_total ELSE 0 END) as online_sales,
                        SUM(loyalty_points_used) as loyalty_points_used
                    ')
                    ->first();

                return [
                    'pos_number' => $index + 1,
                    'employee_name' => $employee->empname,
                    'employee_code' => $employee->empcode,
                    'pos_ip' => 'N/A',
                    'transaction_count' => $todaySalesData->transaction_count ?? 0,
                    'total_sales' => $todaySalesData->total_sales ?? 0,
                    'cash_sales' => $todaySalesData->cash_sales ?? 0,
                    'online_sales' => $todaySalesData->online_sales ?? 0,
                    'loyalty_points' => $todaySalesData->loyalty_points_used ?? 0,
                    'is_active' => ($todaySalesData->transaction_count ?? 0) > 0,
                ];
            });
    } else {
        // Use IP-based data (already filtered by store)
        $posData = $posByIP->map(function ($pos, $index) {
            return [
                'pos_number' => $index + 1,
                'employee_name' => $pos->employee ? $pos->employee->empname : 'Unknown',
                'employee_code' => $pos->employee ? $pos->employee->empcode : 'N/A',
                'pos_ip' => $pos->pos_ipaddress,
                'transaction_count' => $pos->transaction_count,
                'total_sales' => $pos->total_sales,
                'cash_sales' => $pos->cash_sales,
                'online_sales' => $pos->online_sales,
                'loyalty_points' => $pos->loyalty_points_used,
                'is_active' => $pos->transaction_count > 0,
                'first_transaction' => $pos->first_transaction,
                'last_transaction' => $pos->last_transaction,
            ];
        });
    }

    // If no POS data found, create a placeholder
    if ($posData->isEmpty()) {
        $posData = collect([
            [
                'pos_number' => 1,
                'employee_name' => 'No Active POS',
                'employee_code' => 'N/A',
                'pos_ip' => 'N/A',
                'transaction_count' => 0,
                'total_sales' => 0,
                'cash_sales' => 0,
                'online_sales' => 0,
                'loyalty_points' => 0,
                'is_active' => false,
            ]
        ]);
    }

    // Additional analytics for the specific store
    $totalActivePOS = $posData->where('is_active', true)->count();
    $averageSalesPerPOS = $totalActivePOS > 0 ? $todaySales / $totalActivePOS : 0;

    // Payment method breakdown for the manager's store ONLY
    $paymentBreakdown = SalesInvoice::where('store_id', $managerStoreId) // Store specific
        ->whereDate('invoice_date', $today)
        ->selectRaw('
            mode_of_payment,
            COUNT(*) as transaction_count,
            SUM(grand_total) as total_amount
        ')
        ->groupBy('mode_of_payment')
        ->orderBy('total_amount', 'desc')
        ->get();

    // Additional store-specific metrics
    $storeMetrics = [
        'total_employees' => Employee::where('store_id', $managerStoreId)->count(),
        'active_employees_today' => Employee::where('store_id', $managerStoreId)
            ->whereHas('salesInvoices', function($query) use ($today) {
                $query->whereDate('invoice_date', $today);
            })->count(),
        'top_selling_employee' => $this->getTopSellingEmployee($managerStoreId, $today),
    ];

    // Debug information for warehouse matching (remove in production)
    $debugInfo = [
        'store_name' => $storeName,
        'store_variations_tried' => $storeVariations,
        'matched_warehouse' => $matchingWarehouse ?? 'Direct match used',
        'available_warehouses' => $availableWarehouses ?? [],
    ];

    $data = [
        'manager' => $manager,
        'storeName' => $storeName,
        'storeCode' => $storeCode,
        'storeId' => $managerStoreId,
        'todaySales' => $todaySales,
        'salesPercentageChange' => abs(round($salesPercentageChange, 1)),
        'salesTrend' => $salesTrend,
        'todayPurchase' => $todayPurchase,
        'purchasePercentageChange' => abs(round($purchasePercentageChange, 1)),
        'purchaseTrend' => $purchaseTrend,
        'todayBills' => $todayBills,
        'billsPercentageChange' => abs(round($billsPercentageChange, 1)),
        'billsTrend' => $billsTrend,
        'posData' => $posData,
        'totalActivePOS' => $totalActivePOS,
        'averageSalesPerPOS' => $averageSalesPerPOS,
        'paymentBreakdown' => $paymentBreakdown,
        'storeMetrics' => $storeMetrics,
        // 'debugInfo' => $debugInfo, // Uncomment for debugging
    ];

    return view('manger_dashboard', $data);
}

/**
 * Get top selling employee for the store today
 */
private function getTopSellingEmployee($storeId, $today)
{
    return Employee::where('store_id', $storeId)
        ->withSum(['salesInvoices' => function($query) use ($today) {
            $query->whereDate('invoice_date', $today);
        }], 'grand_total')
        ->orderBy('sales_invoices_sum_grand_total', 'desc')
        ->first();
}



}
