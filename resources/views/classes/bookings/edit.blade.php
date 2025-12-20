@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Update Bookings & Registration</h4>
    </div>

    <form action="{{ route('class.bookingsupdate', $booking->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="container-fluid form-div">

            <!-- Student Details -->
            <div class="body-head mb-3">
                <h5>Student Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="stdid">Student ID <span>*</span></label>
                    <input type="text" class="form-control" id="stdid" value="STU{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="stdname">Student Name <span>*</span></label>
                    <input type="text" class="form-control" name="stdname" id="stdname" value="{{ $booking->student_name }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="email">Email ID <span>*</span></label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ $booking->email }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="contact">Contact Number <span>*</span></label>
                    <input type="text" class="form-control" name="contact" id="contact" value="{{ $booking->contact_number }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="dob">Date Of Birth <span>*</span></label>
<input type="date" class="form-control" name="dob" id="dob" 
    value="{{ \Carbon\Carbon::parse($booking->date_of_birth)->format('Y-m-d') }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="gender">Gender <span>*</span></label>
                    <select class="form-select" name="gender" id="gender" required>
                        <option value="" disabled>Select Option</option>
                        <option value="Male" {{ $booking->gender == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $booking->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Others" {{ $booking->gender == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="guardian">Guardian Name <span>*</span></label>
                    <input type="text" class="form-control" name="guardian" id="guardian" value="{{ $booking->guardian_name }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="emgcontact">Emergency Contact <span>*</span></label>
                    <input type="text" class="form-control" name="emgcontact" id="emgcontact" value="{{ $booking->emergency_contact }}" required>
                </div>
            </div>

            <hr>

            <!-- Address Details -->
            <div class="body-head mb-3">
                <h5>Address Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="address">Address <span>*</span></label>
                    <input type="text" class="form-control" name="address" id="address" value="{{ $booking->address }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="city">City <span>*</span></label>
                    <input type="text" class="form-control" name="city" id="city" value="{{ $booking->city }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="state">State <span>*</span></label>
                    <input type="text" class="form-control" name="state" id="state" value="{{ $booking->state }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="pincode">Pincode <span>*</span></label>
                    <input type="text" class="form-control" name="pincode" id="pincode" value="{{ $booking->pincode }}" required>
                </div>
            </div>

            <hr>

            <!-- Class Bookings -->
            <div class="body-head mb-3">
                <h5>Class Bookings</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="classtype">Class Type <span>*</span></label>
                    <select class="form-select" name="classtype" id="classtype" required>
                        <option value="" disabled>Select Option</option>
                        @foreach($classTypes as $type)
                            <option value="{{ $type }}" {{ $booking->class_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="classname">Class Name <span>*</span></label>
                    <select class="form-select" name="classname" id="classname" required>
                        <option value="" disabled>Select Option</option>
                        @foreach($classNames as $class)
                            <option value="{{ $class }}" {{ $booking->class_name == $class ? 'selected' : '' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="date">Date <span>*</span></label>
<input type="date" class="form-control" name="date" id="date"
    value="{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="time">Time <span>*</span></label>
                    <input type="time" class="form-control" name="time" id="time" value="{{ $booking->booking_time }}" required>
                </div>
            </div>

            <hr>

            <!-- Pricing & Payment -->
            <div class="body-head mb-3">
                <h5>Pricing & Payment</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="membership">Membership <span>*</span></label>
                    <select class="form-select" name="membership" id="membership" required>
                        <option value="" disabled>Select Option</option>
                        <option value="Yes" {{ $booking->membership == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $booking->membership == 'No' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="price">Price <span>*</span></label>
                    <input type="number" class="form-control" name="price" id="price" value="{{ $booking->price }}" required>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Update Bookings</button>
            </div>

        </div>
    </form>
</div>

@endsection
