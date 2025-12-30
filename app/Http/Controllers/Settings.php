<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use App\Models\PosSystem;
use App\Models\Company;
use App\Models\Store;
use Illuminate\Support\Facades\Session;

class Settings extends Controller
{

public function company_profile()
{
    $company = Company::first(); // Assuming there's only one company
    return view('settings.comp_profile', compact('company'));
}

    
   public function company_edit()
{
    $company = Company::first(); // Fetch first company record
    return view('settings.comp_edit', compact('company'));
}
public function company_update(Request $request)
{
    $company = Company::first();

    if (!$company) {
        $company = new Company();
    }

    $company->fill($request->except('company_logo'));

    if ($request->hasFile('company_logo')) {
        $file = $request->file('company_logo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/logos'), $filename);
        $company->company_logo = $filename;
    }

    $company->save();

    return redirect()->route('settings.companyprofile')->with('success', 'Company updated successfully.');
}
public function employee_list()
{
    $role = Session::get('role');
    $empId = Session::get('loginId');

    if ($role === 'admin') {
        $employees = Employee::with('store')->get();
    } else {
        $employees = Employee::with('store')->where('created_by', $empId)->get();
    }

    return view('settings.emp_list', compact('employees'));
}

public function employee_add()
{
    $role = Session::get('role');

    if ($role === 'admin') {
        $stores = \App\Models\Store::all();
    } else {
        // Get only the manager's store
        $employee = Employee::find(Session::get('loginId'));
        $stores = \App\Models\Store::where('id', $employee->store_id)->get();
    }

    return view('settings.emp_add', compact('stores', 'role'));
}

public function employee_store(Request $request)
{
    $role = Session::get('role');
    $loginId = Session::get('loginId');

    $validated = $request->validate([
        'empcode' => 'required',
        'empname' => 'required',
        'gender' => 'required',
        'marital' => 'required',
        'dob' => 'required|date',
        'contact' => 'required',
        'altcontact' => 'nullable',
        'email' => 'required|email|unique:employees',
        'designation' => 'required|in:Manager,Employee,Admin',
        'joindate' => 'required|date',
        'pfimg' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'ad_1' => 'required',
        'ad_2' => 'nullable',
        'dis' => 'required',
        'state' => 'required',
        'pin' => 'required|digits:6',
        'emp_password' => 'required|min:6',
        'store_id' => 'required|exists:stores,id',
    ]);

    // Enforce role-based restrictions
    if ($role !== 'admin') {
        $manager = \App\Models\Employee::findOrFail($loginId);
        $validated['store_id'] = $manager->store_id;
        $validated['designation'] = 'Employee';
    }

    // Handle profile image upload
    if ($request->hasFile('pfimg')) {
        $imageName = time().'.'.$request->pfimg->extension();
        $request->pfimg->move(public_path('uploads/employees'), $imageName);
    } else {
        $imageName = null;
    }

    // Check if the logged-in user exists in the employees table (to prevent FK violation)
    $creatorId = \App\Models\Employee::where('id', $loginId)->exists() ? $loginId : null;

    // Store the employee
    \App\Models\Employee::create([
        'empcode' => $validated['empcode'],
        'empname' => $validated['empname'],
        'gender' => $validated['gender'],
        'marital' => $validated['marital'],
        'dob' => $validated['dob'],
        'contact' => $validated['contact'],
        'altcontact' => $validated['altcontact'],
        'email' => $validated['email'],
        'designation' => $validated['designation'],
        'store_id' => $validated['store_id'],
        'password' => Hash::make($validated['emp_password']),
        'joindate' => $validated['joindate'],
        'pfimg' => $imageName,
        'ad_1' => $validated['ad_1'],
        'ad_2' => $validated['ad_2'],
        'dis' => $validated['dis'],
        'state' => $validated['state'],
        'pin' => $validated['pin'],
        'created_by' => $creatorId,
    ]);

    return redirect()->route('settings.emp_list')->with('success', 'Employee added successfully!');
}


    public function employee_toggle_status($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->status = $employee->status == 'Active' ? 'Inactive' : 'Active';
        $employee->save();
        return redirect()->route('settings.emp_list')->with('success', 'Status updated!');
    }
public function employee_edit($id)
{
    $role = Session::get('role');
    $loginId = Session::get('loginId');

    // Fetch the employee to be edited
    $employee = Employee::findOrFail($id);

    // If manager is logged in, restrict access to only those they created
    if ($role !== 'admin') {
        if ($employee->created_by != $loginId) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Get manager's own store
        $manager = Employee::find($loginId);
        if (!$manager) {
            return redirect()->back()->with('error', 'Manager not found');
        }

        // Only manager's own store available
        $stores = \App\Models\Store::where('id', $manager->store_id)->get();
    } else {
        // Admin can access all stores
        $stores = \App\Models\Store::all();
    }

    return view('settings.emp_edit', compact('employee', 'stores', 'role'));
}


    public function employee_update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'empcode' => 'required',
            'empname' => 'required',
            'gender' => 'required',
            'marital' => 'required',
            'dob' => 'required|date',
            'contact' => 'required',
            'altcontact' => 'nullable',
            'email' => 'required|email',
            'designation' => 'required',
            'emp_password' => 'nullable',
            'joindate' => 'required|date',
            'pfimg' => 'nullable|image',
            'ad_1' => 'required',
            'ad_2' => 'required',
            'dis' => 'required',
            'state' => 'required',
            'pin' => 'required|digits:6',
        ]);

        // Update basic fields
        $employee->empcode = $validated['empcode'];
        $employee->empname = $validated['empname'];
        $employee->gender = $validated['gender'];
        $employee->marital = $validated['marital'];
        $employee->dob = $validated['dob'];
        $employee->contact = $validated['contact'];
        $employee->altcontact = $validated['altcontact'];
        $employee->email = $validated['email'];
        $employee->designation = $validated['designation'];
        if($validated['emp_password']){
            $employee->emp_password = bcrypt($validated['emp_password']);
        }
        $employee->joindate = $validated['joindate'];

        // Update address fields
        $employee->ad_1 = $validated['ad_1'];
        $employee->ad_2 = $validated['ad_2'];
        $employee->dis = $validated['dis'];
        $employee->state = $validated['state'];
        $employee->pin = $validated['pin'];

        // Update profile image if uploaded
        if($request->hasFile('pfimg')){
            $file = $request->file('pfimg');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $employee->pfimg = $filename;
        }

        $employee->save();

        return redirect()->route('settings.emp_list')->with('success', 'Employee updated successfully!');
    }


        // public function employee_edit()
        // {
        //     return view('settings.emp_edit');
        // }

public function pos_list()
{
    $role = Session::get('role');
    $loginId = Session::get('loginId');

    if ($role === 'admin') {
        $posSystems = PosSystem::with('store')->get();
        $stores = Store::all();
    } else {
        $employee = \App\Models\Employee::find($loginId);
        $stores = Store::where('id', $employee->store_id)->get();

        $posSystems = PosSystem::with('store')
            ->where(function ($query) use ($loginId) {
                $query->where('created_by', $loginId)
                      ->orWhere('store_id', 1); // Include all POS under store 1
            })
            ->get();
    }

    return view('settings.pos', compact('posSystems', 'stores', 'role'));
}


public function store_pos(Request $request)
{
    $role = Session::get('role');
    $loginId = Session::get('loginId');

    $request->validate([
        'system_no' => 'required|unique:pos_systems',
        'system_type' => 'required|array',
        'store_id' => 'required|exists:stores,id',
        'remarks' => 'nullable'
    ]);

    if ($role !== 'admin') {
        $employee = \App\Models\Employee::find($loginId);
        if ($employee) {
            $request->merge(['store_id' => $employee->store_id]);
        }
    }

    // ✅ Check if employee exists for FK constraint
    $creatorId = \App\Models\Employee::where('id', $loginId)->exists()
        ? $loginId
        : null;

    PosSystem::create([
        'system_no' => $request->system_no,
        'system_type' => implode(',', $request->system_type),
        'store_id' => $request->store_id,
        'remarks' => $request->remarks,
        'status' => true,
        'created_by' => $creatorId,
    ]);

    return redirect()->route('settings.pos')->with('success', 'POS System added successfully!');
}

public function update_pos(Request $request, $id)
{
    $role = Session::get('role');
    $loginId = Session::get('loginId');

    $request->validate([
        'system_no' => 'required|unique:pos_systems,system_no,' . $id,
        'system_type' => 'required|array',
        'store_id' => 'required|exists:stores,id',
        'remarks' => 'nullable'
    ]);

    $pos = PosSystem::findOrFail($id);

    if ($role !== 'admin' && $pos->created_by != $loginId) {
        return redirect()->back()->with('error', 'Unauthorized access.');
    }

    if ($role !== 'admin') {
        $employee = \App\Models\Employee::find($loginId);
        $request->merge([
            'store_id' => $employee->store_id
        ]);
    }

    $pos->update([
        'system_no' => $request->system_no,
        'system_type' => implode(',', $request->system_type),
        'store_id' => $request->store_id,
        'remarks' => $request->remarks,
    ]);

    return redirect()->route('settings.pos')->with('success', 'POS System updated successfully!');
}

}
