@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Daily Attendance</h4>
    </div>

    <form id="attendanceFilter">
        <div class="container-fluid form-div">
            <div class="row">
                @if($role !== 'manager')
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                    <label for="store">Store <span>*</span></label>
                    <select class="form-select" name="store_id" id="store" autofocus required>
                        <option value="" selected disabled>Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="store_id" value="{{ $stores->first()->id }}">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                    <label for="store_display">Store</label>
                    <input type="text" class="form-control" value="{{ $stores->first()->store_name }}" readonly>
                </div>
                @endif
                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                    <label for="designation">Designation</label>
                    <select class="form-select" name="designation" id="designation">
                        <option value="">All Designations</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation }}">{{ ucfirst($designation) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                    <label for="employee">Employee</label>
                    <select class="form-select" name="employee_id" id="employee">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->empname }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                    <label for="date">Date <span>*</span></label>
                    <input type="date" class="form-control" name="date" id="date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Filter Attendance</button>
            </div>
        </div>
    </form>

    <div class="body-head mt-3">
        <h4>Attendance List</h4>
    </div>
    <div class="container-fluid mt-2 listtable">
        <div class="filter-container">
            <div class="filter-container-start">
                <select class="headerDropdown form-select filter-option">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder="Search">
            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Employee Code</th>
                        <th>Designation</th>
                        <th>In-Time</th>
                        <th>Out-Time</th>
                        <th>Break-Out</th>
                        <th>Break-In</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    @foreach($attendances as $index => $attendance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('assets/images/avatar.png') }}" height="30px" alt="">
                                {{ $attendance->employee->empname }}
                            </div>
                        </td>
                        <td>{{ $attendance->employee->empcode }}</td>
                        <td>{{ ucfirst($attendance->employee->designation) }}</td>
                        <td>{{ $attendance->in_time ? \Carbon\Carbon::parse($attendance->in_time)->format('h:i A') : '-' }}</td>
                        <td>{{ $attendance->out_time ? \Carbon\Carbon::parse($attendance->out_time)->format('h:i A') : '-' }}</td>
                        <td>{{ $attendance->break_out ? \Carbon\Carbon::parse($attendance->break_out)->format('h:i A') : '-' }}</td>
                        <td>{{ $attendance->break_in ? \Carbon\Carbon::parse($attendance->break_in)->format('h:i A') : '-' }}</td>
                        <td>
                            @if($attendance->status == 'present')
                                <span class="badge bg-success">Present</span>
                            @else
                                <span class="badge bg-danger">Absent</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('.example').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "bDestroy": true,
            "info": false,
            "responsive": true,
            "pageLength": 10,
            "dom": '<"top"f>rt<"bottom"lp><"clear">',
        });

        // Function to load employees based on store and designation
        function loadEmployees() {
            var storeId = @if($role !== 'manager') $('#store').val() @else $('input[name="store_id"]').val() @endif;
            var designation = $('#designation').val();
            
            // Clear employee dropdown
            $('#employee').html('<option value="">All Employees</option>');
            
            if (storeId) {
                // Fetch employees for selected store and designation
                $.ajax({
                    url: "{{ route('attendance.getEmployeesByStore') }}",
                    type: 'POST',
                    data: {
                        store_id: storeId,
                        designation: designation,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(employees) {
                        $.each(employees, function(index, employee) {
                            $('#employee').append('<option value="' + employee.id + '">' + employee.name + ' (' + employee.employee_code + ')</option>');
                        });
                    },
                    error: function() {
                        console.error('Error loading employees');
                    }
                });
            }
        }

        // Store dropdown change event (for admin only)
        @if($role !== 'manager')
        $('#store').on('change', function() {
            loadEmployees();
        });
        @else
        // For managers, load employees on page load
        loadEmployees();
        @endif

        // Designation dropdown change event
        $('#designation').on('change', function() {
            loadEmployees();
        });

        // Filter form submission
        $('#attendanceFilter').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                store_id: $('input[name="store_id"], select[name="store_id"]').val(),
                employee_id: $('#employee').val(),
                designation: $('#designation').val(),
                date: $('#date').val(),
                _token: '{{ csrf_token() }}'
            };
            
            $.ajax({
                url: "{{ route('attendance.getAttendanceByFilters') }}",
                type: 'POST',
                data: formData,
                success: function(attendances) {
                    // Clear existing table data
                    table.clear();
                    
                    // Add new data
                    $.each(attendances, function(index, attendance) {
                        var inTime = attendance.in_time ? new Date('1970-01-01T' + attendance.in_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-';
                        var outTime = attendance.out_time ? new Date('1970-01-01T' + attendance.out_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-';
                        var breakOut = attendance.break_out ? new Date('1970-01-01T' + attendance.break_out).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-';
                        var breakIn = attendance.break_in ? new Date('1970-01-01T' + attendance.break_in).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-';
                        var status = attendance.status == 'present' ? '<span class="badge bg-success">Present</span>' : '<span class="badge bg-danger">Absent</span>';
                        
                        table.row.add([
                            index + 1,
                            '<div class="d-flex align-items-center gap-2"><img src="{{ asset('assets/images/avatar.png') }}" height="30px" alt="">' + attendance.employee.empname + '</div>',
                            attendance.employee.empcode,
                            attendance.employee.designation ? attendance.employee.designation.charAt(0).toUpperCase() + attendance.employee.designation.slice(1) : '',
                            inTime,
                            outTime,
                            breakOut,
                            breakIn,
                            status
                        ]);
                    });
                    
                    table.draw();
                },
                error: function() {
                    alert('Error fetching attendance data');
                }
            });
        });

        // List Filter functionality
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "progress") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
            }
        });
        
        $('.filterInput').on('keyup', function() {
            var selectedColumn = $('.headerDropdown').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search($(this).val()).draw();
            } else {
                table.search($(this).val()).draw();
            }
        });
        
        $('.headerDropdown').on('change', function() {
            $('.filterInput').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>

@endsection