<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Tutor;
use App\Models\Booking;
use Illuminate\Support\Facades\Session;

class Classes extends Controller
{
    // Helper method to check if user is admin
    private function isAdmin()
    {
        return Session::get('role') === 'admin';
    }

    // Helper method to get current user's store_id for managers
    private function getCurrentStoreId()
    {
        return Session::get('store_id');
    }

    // Helper method to get current user's empcode for managers
    private function getCurrentEmpcode()
    {
        return Session::get('empcode');
    }

public function class_list()
{
    if ($this->isAdmin()) {
        // Admin can see all classes with tutor data
        $classes = ClassModel::with(['bookings', 'tutor'])->get();
    } else {
        // Manager can only see classes from their store or created by them
        $storeId = $this->getCurrentStoreId();
        $empcode = $this->getCurrentEmpcode();
        
        $classes = ClassModel::with(['bookings', 'tutor'])
            ->where(function($query) use ($storeId, $empcode) {
                $query->where('store_id', $storeId)
                      ->orWhere('created_by', $empcode);
            })
            ->get();
    }
    
    return view('classes.class.list', compact('classes'));
}

    public function class_add()
    {
        if ($this->isAdmin()) {
            // Admin can see all tutors
            $tutors = Tutor::all();
        } else {
            // Manager can only see tutors from their store
            $storeId = $this->getCurrentStoreId();
            $tutors = Tutor::where('store_id', $storeId)->get();
        }
        
        return view('classes.class.add', compact('tutors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'class_type' => 'required|string',
            'max_participants' => 'required|integer',
            'pricing_type' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|string',
            'recurring_one_time' => 'required|string',
            'tutor_id' => 'required|integer',
        ]);

        $data = $request->all();
        
        // Add store_id and created_by for managers
        if (!$this->isAdmin()) {
            $data['store_id'] = $this->getCurrentStoreId();
            $data['created_by'] = $this->getCurrentEmpcode();
        }

        ClassModel::create($data);

        return redirect()->route('class.classlist')->with('success', 'Class added successfully!');
    }

    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        
        // Check if manager can access this class
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            if ($class->store_id != $storeId && $class->created_by != $empcode) {
                abort(403, 'Unauthorized access to this class.');
            }
        }
        
        return view('classes.class.edit', compact('class'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class_name' => 'required|string',
            'class_type' => 'required|string',
            'max_participants' => 'required|integer',
            'pricing_type' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|string',
            'recurring_one_time' => 'required|string',
            'tutor_id' => 'required|integer',
        ]);

        $class = ClassModel::findOrFail($id);
        
        // Check if manager can access this class
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            if ($class->store_id != $storeId && $class->created_by != $empcode) {
                abort(403, 'Unauthorized access to this class.');
            }
        }
        
        $class->update($request->all());

        return redirect()->route('class.classlist')->with('success', 'Class updated successfully!');
    }

    public function class_profile($id)
    {
        $class = ClassModel::findOrFail($id);
        
        // Check if manager can access this class
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            if ($class->store_id != $storeId && $class->created_by != $empcode) {
                abort(403, 'Unauthorized access to this class.');
            }
        }

        // Fetch students who booked this class (match by class_name)
        if ($this->isAdmin()) {
            $students = Booking::where('class_name', $class->class_name)->get();
        } else {
            // Manager can only see bookings from their store
            $storeId = $this->getCurrentStoreId();
            $students = Booking::where('class_name', $class->class_name)
                             ->where('store_id', $storeId)
                             ->get();
        }

        return view('classes.class.profile', compact('class', 'students'));
    }

    public function student_profile()
    {
        return view('classes.class.student_profile');
    }

    public function bookings_list()
    {
        if ($this->isAdmin()) {
            // Admin can see all bookings
            $bookings = Booking::all();
        } else {
            // Manager can only see bookings from their store
            $storeId = $this->getCurrentStoreId();
            $bookings = Booking::where('store_id', $storeId)->get();
        }
        
        return view('classes.bookings.list', compact('bookings'));
    }

    public function bookings_add()
    {
        return view('classes.bookings.add');
    }

    public function store_booking(Request $request)
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

        $data = $request->all();
        
        // Add store_id for managers
        if (!$this->isAdmin()) {
            $data['store_id'] = $this->getCurrentStoreId();
            $data['booked_by'] = $this->getCurrentEmpcode();
        }

        $booking = Booking::create($data);

        return redirect()->route('class.bookingslist')->with('success', 'Booking Added successfully.');
    }
public function getStudentByContact(Request $request)
{
    $request->validate([
        'contact_number' => 'required|string'
    ]);

    $query = Booking::where('contact_number', $request->contact_number);

    // ✅ Filter by store_id / created_by if user is not admin
    if (!$this->isAdmin()) {
        $query->where('store_id', $this->getCurrentStoreId());
        $query->where('booked_by', $this->getCurrentEmpcode());
    }

    $student = $query->latest()->first();

    if ($student) {
        return response()->json([
            'status' => 'found',
            'data' => $student
        ]);
    }

    return response()->json(['status' => 'not_found']);
}
    public function get_classes_by_type(Request $request)
    {
        $classType = $request->class_type;
        
        if ($this->isAdmin()) {
            $classes = ClassModel::where('class_type', $classType)
                                ->select('class_name')
                                ->distinct()
                                ->get();
        } else {
            // Manager can only see classes from their store
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            $classes = ClassModel::where('class_type', $classType)
                                ->where(function($query) use ($storeId, $empcode) {
                                    $query->where('store_id', $storeId)
                                          ->orWhere('created_by', $empcode);
                                })
                                ->select('class_name')
                                ->distinct()
                                ->get();
        }

        return response()->json($classes);
    }

    public function get_class_details(Request $request)
    {
        $className = $request->class_name;
        
        if ($this->isAdmin()) {
            $classDetails = ClassModel::where('class_name', $className)
                                    ->select('date', 'time')
                                    ->get();
        } else {
            // Manager can only see class details from their store
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            $classDetails = ClassModel::where('class_name', $className)
                                    ->where(function($query) use ($storeId, $empcode) {
                                        $query->where('store_id', $storeId)
                                              ->orWhere('created_by', $empcode);
                                    })
                                    ->select('date', 'time')
                                    ->get();
        }

        return response()->json($classDetails);
    }

    public function bookings_edit($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check if manager can access this booking
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($booking->store_id != $storeId) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }

        if ($this->isAdmin()) {
            $classTypes = ClassModel::distinct()->pluck('class_type')->toArray();
            $classNames = ClassModel::distinct()->pluck('class_name')->toArray();
        } else {
            // Manager can only see class types and names from their store
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            $classTypes = ClassModel::where(function($query) use ($storeId, $empcode) {
                                $query->where('store_id', $storeId)
                                      ->orWhere('created_by', $empcode);
                            })
                            ->distinct()
                            ->pluck('class_type')
                            ->toArray();
            
            $classNames = ClassModel::where(function($query) use ($storeId, $empcode) {
                                $query->where('store_id', $storeId)
                                      ->orWhere('created_by', $empcode);
                            })
                            ->distinct()
                            ->pluck('class_name')
                            ->toArray();
        }

        return view('classes.bookings.edit', compact('booking', 'classTypes', 'classNames'));
    }

    public function bookings_update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check if manager can access this booking
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($booking->store_id != $storeId) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }

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

        return redirect()->route('class.bookingslist')->with('success', 'Booking updated successfully.');
    }

    public function bookings_profile($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check if manager can access this booking
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($booking->store_id != $storeId) {
                abort(403, 'Unauthorized access to this booking.');
            }
        }
        
        return view('classes.bookings.profile', compact('booking'));
    }

    public function update_booking_status(Request $request)
    {
        $booking = Booking::findOrFail($request->id);
        
        // Check if manager can access this booking
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($booking->store_id != $storeId) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
        }

        $booking->status = ($booking->status === 'Active') ? 'Inactive' : 'Active';
        $booking->save();

        return response()->json([
            'status' => $booking->status,
            'icon' => $booking->status === 'Active' 
                ? '<i class="fas fa-circle-xmark text-danger"></i>' 
                : '<i class="fas fa-circle-check text-success"></i>'
        ]);
    }

    public function tutor_list()
    {
        if ($this->isAdmin()) {
            // Admin can see all tutors
            $tutors = \App\Models\Tutor::with('class')->orderBy('id')->get();
        } else {
            // Manager can only see tutors from their store
            $storeId = $this->getCurrentStoreId();
            $tutors = \App\Models\Tutor::with('class')
                                     ->where('store_id', $storeId)
                                     ->orderBy('id')
                                     ->get();
        }
        
        return view('classes.tutor.list', compact('tutors'));
    }

    public function tutor_toggle_status($id)
    {
        $tutor = \App\Models\Tutor::findOrFail($id);
        
        // Check if manager can access this tutor
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($tutor->store_id != $storeId) {
                abort(403, 'Unauthorized access to this tutor.');
            }
        }
        
        $tutor->status = ($tutor->status === 'Active' ? 'Inactive' : 'Active');
        $tutor->save();

        return redirect()->route('class.tutorlist')->with('status', 'Tutor status updated.');
    }

    public function tutor_add()
    {
        if ($this->isAdmin()) {
            $classes = ClassModel::all();
        } else {
            // Manager can only see classes from their store
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            $classes = ClassModel::where(function($query) use ($storeId, $empcode) {
                $query->where('store_id', $storeId)
                      ->orWhere('created_by', $empcode);
            })->get();
        }
        
        $expertiseList = ['Packing', 'Speaking', 'Coding'];
        return view('classes.tutor.add', compact('classes', 'expertiseList'));
    }

    public function tutor_store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'expertise' => 'required',
            'email' => 'required|email',
            'contact' => 'required',
            'internal_external' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required',
        ]);

        // Add store_id for managers
        if (!$this->isAdmin()) {
            $validated['store_id'] = $this->getCurrentStoreId();
            $validated['added_by'] = $this->getCurrentEmpcode();
        }

        \App\Models\Tutor::create($validated);

        return redirect()->route('class.tutorlist')->with('success', 'Tutor added successfully');
    }

    public function tutor_edit($id)
    {
        $tutor = Tutor::findOrFail($id);
        
        // Check if manager can access this tutor
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($tutor->store_id != $storeId) {
                abort(403, 'Unauthorized access to this tutor.');
            }
        }

        if ($this->isAdmin()) {
            $classes = ClassModel::all();
        } else {
            // Manager can only see classes from their store
            $storeId = $this->getCurrentStoreId();
            $empcode = $this->getCurrentEmpcode();
            
            $classes = ClassModel::where(function($query) use ($storeId, $empcode) {
                $query->where('store_id', $storeId)
                      ->orWhere('created_by', $empcode);
            })->get();
        }
        
        $expertiseList = ['Packing', 'Drawing', 'Painting'];

        return view('classes.tutor.edit', compact('tutor', 'classes', 'expertiseList'));
    }

    public function tutor_update(Request $request, $id)
    {
        $tutor = Tutor::findOrFail($id);
        
        // Check if manager can access this tutor
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($tutor->store_id != $storeId) {
                abort(403, 'Unauthorized access to this tutor.');
            }
        }

        $request->validate([
            'name' => 'required|string',
            'expertise' => 'required',
            'email' => 'required|email|unique:tutors,email,' . $id,
            'contact' => 'required',
            'internal_external' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required',
        ]);

        $tutor->update([
            'name' => $request->name,
            'expertise' => $request->expertise,
            'email' => $request->email,
            'contact' => $request->contact,
            'internal_external' => $request->internal_external,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
        ]);

        return redirect()->route('class.tutorlist')->with('success', 'Tutor updated successfully');
    }

    public function tutor_profile($id)
    {
        $tutor = Tutor::findOrFail($id);
        
        // Check if manager can access this tutor
        if (!$this->isAdmin()) {
            $storeId = $this->getCurrentStoreId();
            
            if ($tutor->store_id != $storeId) {
                abort(403, 'Unauthorized access to this tutor.');
            }
        }
        
        return view('classes.tutor.profile', compact('tutor'));
    }
}