@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Employee</h4>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.employee_store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="container-fluid form-div">
            <div class="body-head mb-3">
                <h5>Employee Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="empcode">Employee Code <span>*</span></label>
                    <input type="text" class="form-control" name="empcode" id="empcode" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="empname">Employee Name <span>*</span></label>
                    <input type="text" class="form-control" name="empname" id="empname" required>
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
                    <label for="marital">Marital Status <span>*</span></label>
                    <select class="form-select" name="marital" id="marital" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="dob">Date Of Birth <span>*</span></label>
                    <input type="date" class="form-control" name="dob" id="dob" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="contact">Contact Number <span>*</span></label>
                    <input type="number" class="form-control" name="contact" id="contact" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="altcontact">Alternate Contact Number</label>
                    <input type="number" class="form-control" name="altcontact" id="altcontact">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="email">Email ID <span>*</span></label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label for="designation">Designation <span>*</span></label>
                <select class="form-select" name="designation" id="designation" required>
                    <option value="" disabled {{ old('designation') ? '' : 'selected' }}>Select Designation</option>
                    @if($role === 'admin')
                        <option value="Manager" {{ old('designation') == 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="Admin" {{ old('designation') == 'Admin' ? 'selected' : '' }}>Accounts</option>
                    @endif
                    <option value="Employee" {{ old('designation') == 'Employee' ? 'selected' : '' }}>Employee</option>
                </select>
            </div>
            @if($role === 'admin')
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="store_id">Store <span>*</span></label>
                    <select class="form-select" name="store_id" id="store_id" required>
                        <option value="" disabled {{ old('store_id') ? '' : 'selected' }}>Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                {{ $store->store_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="store_id">Store</label>
                    <input type="text" class="form-control" value="{{ $stores[0]->store_name }}" readonly>
                    <input type="hidden" name="store_id" value="{{ $stores[0]->id }}">
                </div>
            @endif
            

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="emp_password">Password <span>*</span></label>
                    <div class="inpflex">
                        <input type="password" class="form-control border-0" name="emp_password" id="emp_password" required>
                        <i class="fa-solid fa-eye-slash" id="emp_passHide"
                            onclick="togglePasswordVisibility('emp_password', 'emp_passShow', 'emp_passHide')"
                            style="display:none; cursor:pointer;"></i>
                        <i class="fa-solid fa-eye" id="emp_passShow"
                            onclick="togglePasswordVisibility('emp_password', 'emp_passShow', 'emp_passHide')"
                            style="cursor:pointer;"></i>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="joindate">Joining Date <span>*</span></label>
                    <input type="date" class="form-control" name="joindate" id="joindate" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="pfimg">Profile Image</label>
                    <input type="file" class="form-control" name="pfimg" id="pfimg" accept="image/*">
                </div>
                <!-- <div class="col-sm-12 col-md-4 col-xl-3 mb-3" id="preview-container">
                    <label>Preview</label>
                    <img id="preview-img" src="" alt="No image selected"
                        style="width: 150px; height: 150px; display: none; object-fit: cover; object-position: center;">
                </div> -->
            </div>

            <div class="body-head my-3">
                <h5 class="m-0">Address Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="perm_address1">Address Line 1 <span>*</span></label>
                    <input type="text" class="form-control" name="ad_1" id="perm_address1" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="perm_address2">Address Line 2</label>
                    <input type="text" class="form-control" name="ad_2" id="perm_address2">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="perm_city">District <span>*</span></label>
                    <input type="text" class="form-control" name="dis" id="perm_city" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="perm_state">State <span>*</span></label>
                    <input type="text" class="form-control" name="state" id="perm_state" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="perm_pincode">Pincode <span>*</span></label>
                    <input type="number" class="form-control" name="pin" id="perm_pincode" required>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Add Employee</button>
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

    function togglePasswordVisibility(inputId, showIconId, hideIconId) {
        const input = document.getElementById(inputId);
        const showIcon = document.getElementById(showIconId);
        const hideIcon = document.getElementById(hideIconId);

        if (input.type === 'password') {
            input.type = 'text';
            showIcon.style.display = 'none';
            hideIcon.style.display = 'inline';
        } else {
            input.type = 'password';
            showIcon.style.display = 'inline';
            hideIcon.style.display = 'none';
        }
    }
</script>

@endsection
