@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head mb-3">
            <h4>Individual Attendance</h4>
        </div>

        <form id="individualAttendanceForm" method="POST" action="{{ route('attendance.individual') }}">
            @csrf
            <div class="container-fluid form-div">
                <div class="row">
                    @if($role !== 'manager')
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                        <label for="store">Store <span>*</span></label>
                        <select class="form-select" name="store_id" id="store" autofocus required>
                            <option value="" selected disabled>Select Store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->store_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                        <input type="hidden" name="store_id" value="{{ $stores->first()->id }}">
                    @endif

                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                        <label for="designation">Designation</label>
                        <select class="form-select" name="designation" id="designation">
                            <option value="">All Designations</option>
                            @foreach($designations as $designation)
                                <option value="{{ $designation }}" {{ request('designation') == $designation ? 'selected' : '' }}>
                                    {{ ucfirst($designation) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                        <label for="employee">Employee <span>*</span></label>
                        <select class="form-select" name="employee_id" id="employee" required>
                            <option value="" selected disabled>Select Employee</option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>
                    
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3 inputs">
                        <label for="month">Month <span>*</span></label>
                        <input type="month" class="form-control" name="month" id="month" 
                               value="{{ request('month', date('Y-m')) }}" required>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                    <button type="submit" class="formbtn">Generate Report</button>
                </div>
            </div>
        </form>

        @if(!empty($individualData) && !isset($individualData['error']))


        
        <!-- Summary Table -->
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
                            <th>Total Working Days</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Unmarked</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('assets/images/avatar.png') }}" height="30px" alt="">
                                    {{ $individualData['employee']->empname }}
                                </div>
                            </td>
                            <td>{{ $individualData['employee']->empcode }}</td>
                            <td>{{ ucfirst($individualData['employee']->designation) }}</td>
                            <td>{{ $individualData['summary']['working_days'] }}</td>
                            <td>
                                <span class="badge bg-success">{{ $individualData['summary']['present_days'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $individualData['summary']['absent_days'] }}</span>
                            </td>
                            <td>
                                @if($individualData['summary']['unmarked_days'] > 0)
                                    <span class="badge bg-warning">{{ $individualData['summary']['unmarked_days'] }}</span>
                                @else
                                    <span class="badge bg-secondary">0</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $workingDays = $individualData['summary']['working_days'];
                                    $presentDays = $individualData['summary']['present_days'];
                                    $percentage = $workingDays > 0 ? round(($presentDays / $workingDays) * 100, 2) : 0;
                                @endphp
                                @if($percentage >= 90)
                                    <span class="badge bg-success">{{ $percentage }}%</span>
                                @elseif($percentage >= 75)
                                    <span class="badge bg-warning">{{ $percentage }}%</span>
                                @else
                                    <span class="badge bg-danger">{{ $percentage }}%</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        
        @elseif(isset($individualData['error']))
        <div class="alert alert-danger mt-4">
            <strong>Error:</strong> {{ $individualData['error'] }}
        </div>
        
        @else
        <div class="body-head mt-4">
            <!-- <h4>Attendance List</h4> -->
            <p style="margin-left:150px;">Please select store, employee, and month above, then click "Generate Report" to view individual attendance details.</p>
        </div>
        @endif

    </div>

    <script>
        $(document).ready(function () {
            // Initialize DataTable if data exists
            @if(!empty($individualData) && !isset($individualData['error']))
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

            // Initialize daily attendance table
            var dailyTable = $('.daily-table').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "bDestroy": true,
                "info": false,
                "responsive": true,
                "pageLength": 15,
                "dom": '<"top"f>rt<"bottom"lp><"clear">',
                "order": [[ 0, "asc" ]]
            });

            // List Filter
            $('.example thead th').each(function (index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "progress") {
                    $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
                }
            });

            $('.filterInput').on('keyup', function () {
                var selectedColumn = $('.headerDropdown').val();
                if (selectedColumn !== 'All') {
                    table.column(selectedColumn).search($(this).val()).draw();
                } else {
                    table.search($(this).val()).draw();
                }
            });

            $('.headerDropdown').on('change', function () {
                $('.filterInput').val('');
                table.search('').columns().search('').draw();
            });
            @endif

            // Load employees when store or designation changes
            function loadEmployees() {
                var storeId = @if($role !== 'manager') $('#store').val() @else $('input[name="store_id"]').val() @endif;
                var designation = $('#designation').val();
                
                if (storeId) {
                    $.ajax({
                        url: "{{ route('attendance.getEmployeesByStore') }}",
                        type: 'POST',
                        data: {
                            store_id: storeId,
                            designation: designation,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            var employeeSelect = $('#employee');
                            employeeSelect.html('<option value="" selected disabled>Select Employee</option>');
                            
                            $.each(response, function(index, employee) {
                                var selected = '{{ request("employee_id") }}' == employee.id ? 'selected' : '';
                                employeeSelect.append('<option value="' + employee.id + '" ' + selected + '>' + 
                                                    employee.name + ' (' + employee.employee_code + ')</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading employees:', error);
                            $('#employee').html('<option value="" selected disabled>Error loading employees</option>');
                        }
                    });
                } else {
                    $('#employee').html('<option value="" selected disabled>Select Employee</option>');
                }
            }

            @if($role !== 'manager')
            // Load employees when store changes (for admin)
            $('#store').on('change', function() {
                loadEmployees();
            });

            // Load employees on page load if store is already selected
            var selectedStore = $('#store').val();
            if (selectedStore) {
                loadEmployees();
            }
            @else
            // For managers, load employees automatically
            loadEmployees();
            @endif

            // Load employees when designation changes
            $('#designation').on('change', function() {
                loadEmployees();
            });

            // AJAX form submission (optional)
            $('#individualAttendanceForm').on('submit', function(e) {
                // You can add loading spinner here
                $('button[type="submit"]').prop('disabled', true).text('Generating...');
            });
        });
    </script>

@endsection