<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance as Attendancemodel;
use App\Models\Store;
use Carbon\Carbon;

class Attendance extends Controller
{
public function create()
{
    $role = session('role');
    $loginId = session('loginId');
    
    if ($role === 'manager') {
        // Get manager's store_id
        $manager = Employee::find($loginId);
        $storeId = $manager->store_id;
        
        // Get employees under this manager's store AND include the manager themselves
        $employees = Employee::where('store_id', $storeId)
                           ->whereIn('designation', ['employee', 'manager'])
                           ->get();
    } else {
        // For admin, show all employees and managers
        $employees = Employee::whereIn('designation', ['employee', 'manager'])->get();
    }
    
    return view('attendance.create', compact('employees'));
}

// Updated index() method
public function index()
{
    $role = session('role');
    $loginId = session('loginId');

    if ($role === 'manager') {
        // Get manager's store_id
        $manager = Employee::find($loginId);
        $storeId = $manager->store_id;

        // Get employees under this manager's store AND include the manager
        $employees = Employee::where('store_id', $storeId)
                             ->whereIn('designation', ['employee', 'manager'])
                             ->get();

        // Get today's attendance for employees in this store (including manager)
        $attendances = Attendancemodel::with('employee')
                            ->whereDate('date', Carbon::today())
                            ->whereIn('employee_id', $employees->pluck('id'))
                            ->get();
    } else {
        $employees = Employee::whereIn('designation', ['employee', 'manager'])->get();
        $attendances = Attendancemodel::with('employee')
                            ->whereDate('date', Carbon::today())
                            ->get();
    }

    return view('attendance.index', compact('employees', 'attendances'));
}

// Updated edit() method
public function edit($id)
{
    $attendance = Attendancemodel::with('employee')->findOrFail($id);
    
    $role = session('role');
    $loginId = session('loginId');
    
    if ($role === 'manager') {
        // Get manager's store_id
        $manager = Employee::find($loginId);
        $storeId = $manager->store_id;
        
        // Check if attendance belongs to employee under this manager's store
        if ($attendance->employee->store_id != $storeId) {
            return redirect()->route('attendance.index')->with('error', 'Unauthorized access.');
        }
        
        $employees = Employee::where('store_id', $storeId)
                           ->whereIn('designation', ['employee', 'manager'])
                           ->get();
    } else {
        $employees = Employee::whereIn('designation', ['employee', 'manager'])->get();
    }
    
    return view('attendance.edit', compact('attendance', 'employees'));
}

// Updated daily() method
public function daily()
{
    $role = session('role');
    $loginId = session('loginId');

    if ($role === 'manager') {
        // Get manager's store_id
        $manager = Employee::find($loginId);
        $storeId = $manager->store_id;

        // Get only the manager's store
        $stores = Store::where('id', $storeId)->get();
                    
        // Get employees under this manager's store (including manager)
        $employees = Employee::where('store_id', $storeId)->get();

        // Get today's attendance for employees in this store
        $attendances = Attendancemodel::with('employee')
                             ->whereDate('date', Carbon::today())
                             ->whereIn('employee_id', $employees->pluck('id'))
                             ->get();
        
        // Get designations for employees in this store
        $designations = Employee::where('store_id', $storeId)
                               ->distinct()
                               ->whereNotNull('designation')
                               ->pluck('designation')
                               ->toArray();
    } else {
        // Admin can see all stores
        $stores = Store::all();
                    
        // Get all employees
        $employees = Employee::all();
                    
        // Get today's attendance for all employees
        $attendances = Attendancemodel::with('employee')
                             ->whereDate('date', Carbon::today())
                             ->get();
        
        // Get all unique designations
        $designations = Employee::distinct()
                               ->whereNotNull('designation')
                               ->pluck('designation')
                               ->toArray();
    }

    return view('attendance.daily', compact('stores', 'employees', 'attendances', 'role', 'designations'));
}
    
    public function store(Request $request)
    {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'in_time' => 'nullable',
                'out_time' => 'nullable',
                'break_out' => 'nullable',
                'break_in' => 'nullable',
                'status' => 'required|in:present,absent'
            ]);
            
            // Check if attendance already exists for this employee on this date
            $existingAttendance = Attendancemodel::where('employee_id', $request->employee_id)
                                        ->whereDate('date', $request->date)
                                        ->first();
            
        if ($existingAttendance) {
        $employee = Employee::find($request->employee_id);
        $formattedDate = \Carbon\Carbon::parse($request->date)->format('d-M-Y');
        return back()->with('alert', "Attendance for {$employee->name} already exists on {$formattedDate}.");
        }

        
        Attendancemodel::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'in_time' => $request->in_time,
            'out_time' => $request->out_time,
            'break_out' => $request->break_out,
            'break_in' => $request->break_in,
            'status' => $request->status,
            'created_by' => session('loginId')
        ]);
        
        return redirect()->route('attendance.index')->with('success', 'Attendance added successfully.');
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'in_time' => 'required',
            'out_time' => 'nullable',
            'break_out' => 'nullable',
            'break_in' => 'nullable',
            'status' => 'required|in:present,absent'
        ]);
        
        $attendance = Attendancemodel::findOrFail($id);
        
        $role = session('role');
        $loginId = session('loginId');
        
        if ($role === 'manager') {
            // Get manager's store_id
            $manager = Employee::find($loginId);
            $storeId = $manager->store_id;
            
            // Check if attendance belongs to employee under this manager's store
            if ($attendance->employee->store_id != $storeId) {
                return redirect()->route('attendance.index')->with('error', 'Unauthorized access.');
            }
        }
        
        $attendance->update([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'in_time' => $request->in_time,
            'out_time' => $request->out_time,
            'break_out' => $request->break_out,
            'break_in' => $request->break_in,
            'status' => $request->status
        ]);
        
        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }


// NEW METHOD: Monthly Attendance
public function monthly(Request $request)
{
    $role = session('role');
    $loginId = session('loginId');
    $monthlyData = [];

    if ($role === 'manager') {
        // Get manager's store_id
        $manager = Employee::find($loginId);
        $storeId = $manager->store_id;
        $stores = Store::where('id', $storeId)->get();
    } else {
        // Admin can see all stores
        $stores = Store::all();
    }

    // Get all unique designations for the dropdown
    $designations = Employee::distinct()
                           ->whereNotNull('designation')
                           ->pluck('designation')
                           ->toArray();

    // If form is submitted, get monthly data
    if ($request->has('store_id') && $request->has('month')) {
        $monthlyData = $this->getMonthlyAttendanceData($request, $role, $loginId);
    }

    return view('attendance.monthly', compact('stores', 'monthlyData', 'role', 'designations'));
}

// Helper method to calculate monthly attendance data
private function getMonthlyAttendanceData($request, $role, $loginId)
{
    $storeId = $request->store_id;
    $month = $request->month; // Format: 2024-01
    $employeeId = $request->employee_id ?? null;
    $designation = $request->designation ?? null;

    // Parse the month to get start and end dates
    $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
    
    // Calculate total working days (excluding Sundays)
    $totalWorkingDays = 0;
    $currentDate = $startDate->copy();
    while ($currentDate <= $endDate) {
        if ($currentDate->dayOfWeek != Carbon::SUNDAY) {
            $totalWorkingDays++;
        }
        $currentDate->addDay();
    }

    // Get employees based on role and filters
    $employeesQuery = Employee::query();
    
    if ($role === 'manager') {
        $manager = Employee::find($loginId);
        $managerStoreId = $manager->store_id;
        $employeesQuery->where('store_id', $managerStoreId);
    } else {
        if ($storeId) {
            $employeesQuery->where('store_id', $storeId);
        }
    }

    // Filter by designation if provided
    if ($designation) {
        $employeesQuery->where('designation', $designation);
    }

    // Filter by specific employee if provided
    if ($employeeId) {
        $employeesQuery->where('id', $employeeId);
    }

    $employees = $employeesQuery->get();

    $monthlyData = [];
    foreach ($employees as $employee) {
        // Get attendance records for this employee in the selected month
        $attendanceRecords = Attendancemodel::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $presentDays = $attendanceRecords->where('status', 'present')->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        
        // Calculate days not marked (neither present nor absent)
        $totalMarkedDays = $presentDays + $absentDays;
        $unmarkedDays = $totalWorkingDays - $totalMarkedDays;

        $monthlyData[] = [
            'employee' => $employee,
            'total_working_days' => $totalWorkingDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'unmarked_days' => $unmarkedDays,
            'attendance_percentage' => $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100, 2) : 0
        ];
    }

    return $monthlyData;
}

// AJAX method to get employees by store (updated to include designation filter)
public function getEmployeesByStore(Request $request)
{
    try {
        $storeId = $request->store_id;
        $designation = $request->designation ?? null;
        
        if (!$storeId) {
            return response()->json(['error' => 'Store ID is required'], 400);
        }
        
        $employeesQuery = Employee::where('store_id', $storeId);
        
        // Filter by designation if provided
        if ($designation) {
            $employeesQuery->where('designation', $designation);
        }
        
        $employees = $employeesQuery->get(['id', 'empname as name', 'empcode as employee_code']);
        
        return response()->json($employees);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error fetching employees'], 500);
    }
}

// AJAX method to get attendance by filters (updated to include designation filter)
public function getAttendanceByFilters(Request $request)
{
    try {
        $storeId = $request->store_id;
        $employeeId = $request->employee_id;
        $designation = $request->designation ?? null;
        $date = $request->date ?? Carbon::today()->toDateString();
        
        $role = session('role');
        $loginId = session('loginId');
        
        $query = Attendancemodel::with('employee')->whereDate('date', $date);
        
        if ($role === 'manager') {
            // Manager can only see their store's attendance
            $manager = Employee::find($loginId);
            if (!$manager) {
                return response()->json(['error' => 'Manager not found'], 404);
            }
            
            $managerStoreId = $manager->store_id;
            
            $employeeQuery = Employee::where('store_id', $managerStoreId);
            
            // Filter by designation if provided
            if ($designation) {
                $employeeQuery->where('designation', $designation);
            }
            
            $employeeIds = $employeeQuery->pluck('id');
            $query->whereIn('employee_id', $employeeIds);
        } else {
            // Admin filters
            if ($storeId || $designation) {
                $employeeQuery = Employee::query();
                
                if ($storeId) {
                    $employeeQuery->where('store_id', $storeId);
                }
                
                if ($designation) {
                    $employeeQuery->where('designation', $designation);
                }
                
                $employeeIds = $employeeQuery->pluck('id');
                $query->whereIn('employee_id', $employeeIds);
            }
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        $attendances = $query->get();
        
        return response()->json($attendances);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error fetching attendance data'], 500);
    }
}

// AJAX method for monthly attendance
public function getMonthlyAttendance(Request $request)
{
    try {
        $role = session('role');
        $loginId = session('loginId');
        
        $monthlyData = $this->getMonthlyAttendanceData($request, $role, $loginId);
        
        return response()->json(['data' => $monthlyData]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error fetching monthly attendance data'], 500);
    }
}

public function individual(Request $request)
{
    $role = session('role');
    $loginId = session('loginId');
    $individualData = [];

    if ($role === 'manager') {
        // Get manager's store_id
        $manager = Employee::find($loginId);
        $storeId = $manager->store_id;
        $stores = Store::where('id', $storeId)->get();
    } else {
        // Admin can see all stores
        $stores = Store::all();
    }

    // Get all unique designations for the dropdown
    $designations = Employee::distinct()
                           ->whereNotNull('designation')
                           ->pluck('designation')
                           ->toArray();

    // If form is submitted, get individual attendance data
    if ($request->has('store_id') && $request->has('employee_id') && $request->has('month')) {
        $individualData = $this->getIndividualAttendanceData($request, $role, $loginId);
    }

    return view('attendance.individual', compact('stores', 'individualData', 'role', 'designations'));
}

// Helper method to get individual attendance data
private function getIndividualAttendanceData($request, $role, $loginId)
{
    $storeId = $request->store_id;
    $employeeId = $request->employee_id;
    $designation = $request->designation ?? null;
    $month = $request->month; // Format: 2024-01

    // Parse the month to get start and end dates
    $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    // Validate employee access based on role
    if ($role === 'manager') {
        $manager = Employee::find($loginId);
        $managerStoreId = $manager->store_id;
        
        $employeeQuery = Employee::where('id', $employeeId)
                              ->where('store_id', $managerStoreId);
        
        // Apply designation filter if provided
        if ($designation) {
            $employeeQuery->where('designation', $designation);
        }
        
        $employee = $employeeQuery->with('store')->first();
    } else {
        $employeeQuery = Employee::where('id', $employeeId);
        
        // Apply designation filter if provided
        if ($designation) {
            $employeeQuery->where('designation', $designation);
        }
        
        // Apply store filter if provided
        if ($storeId) {
            $employeeQuery->where('store_id', $storeId);
        }
        
        $employee = $employeeQuery->with('store')->first();
    }

    if (!$employee) {
        return ['error' => 'Employee not found or unauthorized access'];
    }

    // Get attendance records for this employee in the selected month
    $attendanceRecords = Attendancemodel::where('employee_id', $employeeId)
        ->whereBetween('date', [$startDate, $endDate])
        ->orderBy('date', 'asc')
        ->get();

    // Create a complete list of dates for the month
    $dateRange = [];
    $currentDate = $startDate->copy();
    
    while ($currentDate <= $endDate) {
        $dateString = $currentDate->format('Y-m-d');
        
        // Find attendance record for this date
        $attendanceRecord = $attendanceRecords->where('date', $dateString)->first();
        
        $dateRange[] = [
            'date' => $currentDate->format('d/m/Y'),
            'day_name' => $currentDate->format('l'),
            'is_sunday' => $currentDate->dayOfWeek == Carbon::SUNDAY,
            'attendance' => $attendanceRecord,
            'status' => $attendanceRecord ? $attendanceRecord->status : 'unmarked'
        ];
        
        $currentDate->addDay();
    }

    return [
        'employee' => $employee,
        'month_year' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
        'date_range' => $dateRange,
        'summary' => [
            'total_days' => count($dateRange),
            'working_days' => collect($dateRange)->where('is_sunday', false)->count(),
            'present_days' => $attendanceRecords->where('status', 'present')->count(),
            'absent_days' => $attendanceRecords->where('status', 'absent')->count(),
            'unmarked_days' => collect($dateRange)->where('is_sunday', false)->where('status', 'unmarked')->count(),
            'sundays' => collect($dateRange)->where('is_sunday', true)->count()
        ]
    ];
}

// AJAX method for individual attendance
public function getIndividualAttendance(Request $request)
{
    try {
        $role = session('role');
        $loginId = session('loginId');
        
        $individualData = $this->getIndividualAttendanceData($request, $role, $loginId);
        
        return response()->json(['data' => $individualData]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error fetching individual attendance data'], 500);
    }
}
}