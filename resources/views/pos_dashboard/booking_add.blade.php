@extends('layouts.app_pos')

@section('content')

    <div class="body-div p-3">
        <div class="body-head mb-3">
            <h4>Add Bookings & Registration</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('class.bookingsstore.data') }}" method="POST">
            @csrf
            <div class="container-fluid form-div">

                <div class="body-head mb-3">
                    <h5>Student Details</h5>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="student_id">Student ID <span>*</span></label>
                        <input type="text" class="form-control" name="student_id" id="student_id" autofocus required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="student_name">Student Name <span>*</span></label>
                        <input type="text" class="form-control" name="student_name" id="student_name" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="email">Email ID <span>*</span></label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="contact_number">Contact Number <span>*</span></label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" 
                            oninput="validate_contact(this)" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="date_of_birth">Date Of Birth <span>*</span></label>
                        <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="gender">Gender <span>*</span></label>
                        <select class="form-select" name="gender" id="gender" required>
                            <option value="" selected disabled>Select Option</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="guardian_name">Parent's Name <span>*</span></label>
                        <input type="text" class="form-control" name="guardian_name" id="guardian_name" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="emergency_contact">Emergency Contact <span>*</span></label>
                        <input type="text" class="form-control" name="emergency_contact" id="emergency_contact" required>
                    </div>
                </div>
             
                <hr>

                <div class="body-head mb-3">
                    <h5>Address Details</h5>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="address">Address <span>*</span></label>
                        <input type="text" class="form-control" name="address" id="address" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="city">City <span>*</span></label>
                        <input type="text" class="form-control" name="city" id="city" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="state">State <span>*</span></label>
                        <input type="text" class="form-control" name="state" id="state" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="pincode">Pincode <span>*</span></label>
                        <input type="text" class="form-control" name="pincode" id="pincode" 
                            oninput="validate_pincode(this)" required>
                    </div>
                </div>

                <hr>

                <div class="body-head mb-3">
                    <h5>Class Bookings</h5>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="class_type">Class Type <span>*</span></label>
                        <select class="form-select" name="class_type" id="class_type" required>
                            <option value="" selected disabled>Select Option</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="class_name">Class Name <span>*</span></label>
                        <select class="form-select" name="class_name" id="class_name" required>
                            <option value="" selected disabled>Select Option</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="booking_date">Date <span>*</span></label>
                        <select class="form-select" name="booking_date" id="booking_date" required>
                            <option value="" selected disabled>Select Date</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="booking_time">Time <span>*</span></label>
                        <select class="form-select" name="booking_time" id="booking_time" required>
                            <option value="" selected disabled>Select Time</option>
                        </select>
                    </div>
                </div>

                <hr>

                <div class="body-head mb-3">
                    <h5>Pricing & Payment</h5>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="membership">Membership <span>*</span></label>
                        <select class="form-select" name="membership" id="membership" required>
                            <option value="" selected disabled>Select Option</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label for="price">Price <span>*</span></label>
                        <input type="number" class="form-control" name="price" id="price" step="0.01" required>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                    <button type="submit" class="formbtn">Add Bookings</button>
                </div>
            </div>

        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle class type change
            $('#class_type').change(function() {
                var classType = $(this).val();
                
                // Clear dependent dropdowns
                $('#class_name').html('<option value="" selected disabled>Select Option</option>');
                $('#booking_date').html('<option value="" selected disabled>Select Date</option>');
                $('#booking_time').html('<option value="" selected disabled>Select Time</option>');
                
                if (classType) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    
                    $.ajax({
                        url: "{{ route('class.getbytype') }}",
                        type: "POST",
                        data: {
                            class_type: classType
                        },
                        success: function(data) {
                            $.each(data, function(index, item) {
                                $('#class_name').append('<option value="' + item.class_name + '">' + item.class_name + '</option>');
                            });
                        },
                        error: function() {
                            alert('Error fetching classes');
                        }
                    });
                }
            });

            // Handle class name change
            $('#class_name').change(function() {
                var className = $(this).val();
                
                // Clear dependent dropdowns
                $('#booking_date').html('<option value="" selected disabled>Select Date</option>');
                $('#booking_time').html('<option value="" selected disabled>Select Time</option>');
                
                if (className) {
                    $.ajax({
                        url: "{{ route('class.getdetails') }}",
                        type: "POST",
                        data: {
                            class_name: className
                        },
                        success: function(data) {
                            var dates = [];
                            var times = [];
                            
                            $.each(data, function(index, item) {
                                if (dates.indexOf(item.date) === -1) {
                                    dates.push(item.date);
                                    $('#booking_date').append('<option value="' + item.date + '">' + item.date + '</option>');
                                }
                                if (times.indexOf(item.time) === -1) {
                                    times.push(item.time);
                                    $('#booking_time').append('<option value="' + item.time + '">' + item.time + '</option>');
                                }
                            });
                        },
                        error: function() {
                            alert('Error fetching class details');
                        }
                    });
                }
            });
        });

        function validate_contact(input) {
            // Remove non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');
            
            // Limit to 10 digits
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }

        function validate_pincode(input) {
            // Remove non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');
            
            // Limit to 6 digits
            if (input.value.length > 6) {
                input.value = input.value.slice(0, 6);
            }
        }
    </script>

@endsection