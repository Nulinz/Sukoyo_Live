@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head">
            <h4>Employee List</h4>
            <a href="{{ route('settings.emp_add') }}">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Employee</button>
            </a>
        </div>

        <div class="container-fluid mt-3 listtable">
            <div class="filter-container">
                <div class="filter-container-start">
                    <select class="headerDropdown form-select filter-option">
                        <option value="All" selected>All</option>
                    </select>
                    <input type="text" class="form-control filterInput" placeholder=" Search">
                </div>
            </div>

            <div class="table-wrapper">
                <table class="example table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Store</th>
                            <th>Role</th>
                            <th>Contact Number</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                   <tbody>
@foreach($employees as $index => $employee)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $employee->empname }}</td>
<td>{{ $employee->store->store_name ?? 'N/A' }}</td>
        <td>{{ $employee->designation ?? 'N/A' }}</td>
        <td>+91 {{ $employee->contact }}</td>
        <td>{{ $employee->dis }}</td>
        <td>
            @if($employee->status == 'Active')
                <span class="text-success">Active</span>
            @else
                <span class="text-danger">Inactive</span>
            @endif
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                @if($employee->status == 'Active')
                    <a href="{{ route('settings.employee_toggle_status', $employee->id) }}" data-bs-toggle="tooltip" data-bs-title="Active">
                        <i class="fas fa-circle-check text-success"></i>
                    </a>
                @else
                    <a href="{{ route('settings.employee_toggle_status', $employee->id) }}" data-bs-toggle="tooltip" data-bs-title="Inactive">
                        <i class="fas fa-circle-xmark text-danger"></i>
                    </a>
                @endif
                <a href="{{ route('settings.emp_edit', $employee->id) }}">
                    <i class="fas fa-pen-to-square"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach
</tbody>

                </table>
            </div>
        </div>
    </div>

    <script>
        // DataTables List
        $(document).ready(function () {
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

        });

        // List Filter
        $(document).ready(function () {
            var table = $('.example').DataTable();
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
        });
    </script>

@endsection