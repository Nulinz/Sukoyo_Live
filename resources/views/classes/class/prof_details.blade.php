<div class="cards mb-2">
    <div class="maincard row py-0 mb-3">
        <div class="cardhead my-3">
            <h5>Details</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Class Name</h6>
            <h5 class="mb-0">Pottery for Beginners</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Class Type</h6>
            <h5 class="mb-0">Pottery</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Tutor</h6>
            <h5 class="mb-0">Arun (Internal)</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Date</h6>
            <h5 class="mb-0">01-01-2001</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Status</h6>
            <h5 class="mb-0">Fully Booked</h5>
        </div>
    </div>
</div>

<div class="container-fluid mt-3 listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="headerDropdown1">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="filterInput1" placeholder=" Search">
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table table-bordered" id="table1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Email ID</th>
                    <th>Contact Number</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>STU001</td>
                    <td>Dhanush</td>
                    <td>dhanush@gmail.com</td>
                    <td>+91 9876543210</td>
                    <td><span class="text-success">Active</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="" data-bs-toggle="tooltip" data-bs-title="Inactive">
                                <i class="fas fa-circle-xmark text-danger"></i>
                            </a>
                            <a href="{{ route('class.stdprofile') }}" data-bs-toggle="tooltip" data-bs-title="Profile">
                                <i class="fas fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>STU002</td>
                    <td>Arun</td>
                    <td>arun@gmail.com</td>
                    <td>+91 7894561230</td>
                    <td><span class="text-danger">Inactive</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="" data-bs-toggle="tooltip" data-bs-title="Active">
                                <i class="fas fa-circle-check text-success"></i>
                            </a>
                            <a href="{{ route('class.stdprofile') }}" data-bs-toggle="tooltip" data-bs-title="Profile">
                                <i class="fas fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>