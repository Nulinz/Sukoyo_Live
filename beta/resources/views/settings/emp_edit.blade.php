@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head mb-3">
            <h4>Update Employee</h4>
        </div>
<form action="{{ route('settings.emp_update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="container-fluid form-div">
        <div class="body-head mb-3">
            <h5>Employee Details</h5>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="empcode">Employee Code <span>*</span></label>
                <input type="text" class="form-control" name="empcode" id="empcode" value="{{ old('empcode', $employee->empcode) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="empname">Employee Name <span>*</span></label>
                <input type="text" class="form-control" name="empname" id="empname" value="{{ old('empname', $employee->empname) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="gender">Gender <span>*</span></label>
                <select class="form-select" name="gender" id="gender" required>
                    <option value="" disabled>Select Option</option>
                    <option value="Male" {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Others" {{ old('gender', $employee->gender) == 'Others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="marital">Marital Status <span>*</span></label>
                <select class="form-select" name="marital" id="marital" required>
                    <option value="" disabled>Select Option</option>
                    <option value="Single" {{ old('marital', $employee->marital) == 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ old('marital', $employee->marital) == 'Married' ? 'selected' : '' }}>Married</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="dob">Date Of Birth <span>*</span></label>
                <input type="date" class="form-control" name="dob" id="dob" value="{{ old('dob', $employee->dob) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="contact">Contact Number <span>*</span></label>
                <input type="number" class="form-control" name="contact" id="contact" value="{{ old('contact', $employee->contact) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="altcontact">Alternate Contact Number <span>*</span></label>
                <input type="number" class="form-control" name="altcontact" id="altcontact" value="{{ old('altcontact', $employee->altcontact) }}">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="email">Email ID <span>*</span></label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $employee->email) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="designation">Designation <span>*</span></label>
                <select class="form-select" name="designation" id="designation" required>
                    <option value="" disabled {{ old('designation', $employee->designation) == '' ? 'selected' : '' }}>Select Designation</option>
                    <option value="Manager" {{ old('designation', $employee->designation) == 'Manager' ? 'selected' : '' }}>Manager</option>
                    <option value="Employee" {{ old('designation', $employee->designation) == 'Employee' ? 'selected' : '' }}>Employee</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="emp_password">Password (leave blank to keep existing)</label>
                <input type="password" class="form-control" name="emp_password" id="emp_password">
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="joindate">Joining Date <span>*</span></label>
                <input type="date" class="form-control" name="joindate" id="joindate" value="{{ old('joindate', $employee->joindate) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="pfimg">Profile Image</label>
                <input type="file" class="form-control" name="pfimg" id="pfimg" accept="image/*">
            </div>
            @if($employee->pfimg)
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Current Image</label>
                    <img src="{{ asset('uploads/'.$employee->pfimg) }}" style="width:150px;height:150px;object-fit:cover;">
                </div>
            @endif
        </div>
        <div class="body-head my-3">
            <h5 class="m-0">Address Details</h5>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label>Address Line 1 <span>*</span></label>
                <input type="text" class="form-control" name="ad_1" value="{{ old('ad_1', $employee->ad_1) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label>Address Line 2 <span>*</span></label>
                <input type="text" class="form-control" name="ad_2" value="{{ old('ad_2', $employee->ad_2) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label>District <span>*</span></label>
                <input type="text" class="form-control" name="dis" value="{{ old('dis', $employee->dis) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label>State <span>*</span></label>
                <input type="text" class="form-control" name="state" value="{{ old('state', $employee->state) }}" required>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label>Pincode <span>*</span></label>
                <input type="number" class="form-control" name="pin" value="{{ old('pin', $employee->pin) }}" required>
            </div>
        </div>
        <div class="col-sm-12 mt-3 d-flex justify-content-center">
            <button type="submit" class="formbtn">Update Employee</button>
        </div>
    </div>
</form>

    </div>

    <script>
        const profileImg = document.getElementById('pfimg');
        profileImg.addEventListener('change', function (event) {
            const file = event.target.files[0];
            const previewImg = document.getElementById('preview-img');
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
        });
    </script>

@endsection