<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Login as LoginModel;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Login extends Controller
{
    public function login()
    {
        // If already logged in, redirect to appropriate dashboard
        if (Session::has('loginId')) {
            $role = Session::get('role');
            if ($role === 'admin') {
                return redirect()->route('dashboard.admin');
            } elseif ($role === 'employee') {
                return redirect()->route('dashboard.bill');
            } elseif ($role === 'manager') {
                return redirect()->route('dashboard.manager');
            }
        }
        
        return view('index');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'empcode' => 'required',
            'password' => 'required'
        ]);

        // Get the submitted employee code (case-sensitive)
        $empcode = $request->empcode;

        // First: Check if user is admin (logins table) - Case sensitive comparison
        $user = LoginModel::where(DB::raw('BINARY empcode'), $empcode)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Session::put('loginId', $user->id);
            Session::put('role', $user->role);
            Session::put('empname', $user->empname ?? 'Admin');
            Session::put('empcode', $user->empcode);

            if ($user->role === 'admin') {
                return redirect()->route('dashboard.admin');
            }
        }

        // Second: Check if user is manager (employees table) - Case sensitive comparison
        $manager = Employee::where(DB::raw('BINARY empcode'), $empcode)
            ->where('designation', 'manager')
            ->first();

        if ($manager && Hash::check($request->password, $manager->password)) {
            Session::put('loginId', $manager->id);
            Session::put('role', 'manager');
            Session::put('empname', $manager->empname);
            Session::put('empcode', $manager->empcode);
            Session::put('store_id', $manager->store_id);

            return redirect()->route('dashboard.manager');
        }

        // Third: Check if user is accounts (employees table) - Case sensitive comparison
        $accounts = Employee::where(DB::raw('BINARY empcode'), $empcode)
            ->where('designation', 'admin')
            ->first();

        if ($accounts && Hash::check($request->password, $accounts->password)) {
            Session::put('loginId', $accounts->id);
            Session::put('role', 'admin');
            Session::put('empname', $accounts->empname);
            Session::put('empcode', $accounts->empcode);
            Session::put('store_id', $accounts->store_id);

            return redirect()->route('dashboard.admin');
        }

        // Fourth: Check if user is employee (employees table) - Case sensitive comparison
        $employee = Employee::where(DB::raw('BINARY empcode'), $empcode)
            ->where('designation', 'employee')
            ->first();

        if ($employee && Hash::check($request->password, $employee->password)) {
            Session::put('loginId', $employee->id);
            Session::put('role', 'employee');
            Session::put('empname', $employee->empname);
            Session::put('empcode', $employee->empcode);

            return redirect()->route('dashboard.bill');
        }

        // If no match found, return with error for popup display
        return back()->with('error', 'Invalid employee code or password! Please check your credentials and try again.');
    }


    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'oldpassword' => 'required',
            'newpassword' => 'required|min:6',
            'confirmpassword' => 'required|same:newpassword',
        ]);

        $role = Session::get('role');
        $loginId = Session::get('loginId');

        if ($role === 'admin') {
            $user = LoginModel::find($loginId);
        } elseif ($role === 'employee' || $role === 'manager') {
            $user = Employee::find($loginId);
        } else {
            return back()->with('error', 'Unauthorized access.');
        }

        if (!$user || !Hash::check($request->oldpassword, $user->password)) {
            return back()->with('error', 'Old password is incorrect.');
        }

        $user->password = Hash::make($request->newpassword);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}