@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

<style>
@media screen and (min-width: 990px) {
    .col-xl-3 {
        width: 20%;
    }
}
</style>

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Class Profile</h4>
    </div>

    <div class="mainbdy d-block">
        <div class="contentright">
            <div class="tab-content">
                <!-- Class Details -->
                <div class="cards mb-2">
                    <div class="maincard row py-0 mb-3">
                        <div class="cardhead my-3">
                            <h5>Details</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Class Name</h6>
                            <h5 class="mb-0">{{ $class->class_name }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Class Type</h6>
                            <h5 class="mb-0">{{ $class->class_type }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Tutor</h6>
                            <h5 class="mb-0">
                                @php
                                    echo $class->tutor_id == 1 ? 'Arun (Internal)' : 'External Tutor';
                                @endphp
                            </h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Date</h6>
                            <h5 class="mb-0">{{ \Carbon\Carbon::parse($class->date)->format('d-m-Y') }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Status</h6>
                            <h5 class="mb-0">
                                {{ $students->count() >= $class->max_participants ? 'Fully Booked' : 'Available' }}
                            </h5>
                        </div>
                    </div>
                </div>

                <!-- Students Table -->
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
                                    <th>Class Type</th>
                                    <th>Class Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $key => $student)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>STU{{ str_pad($student->id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $student->student_name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->contact_number }}</td>
                                    <td>{{ $student->class_type }}</td>
                                    <td>{{ $student->class_name }}</td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        function initTable(tableId, dropdownId, filterInputId) {
            var table = $(tableId).DataTable({
                "paging": false,
                "searching": true,
                "ordering": true,
                "order": [0, "asc"],
                "bDestroy": true,
                "info": false,
                "responsive": true,
                "pageLength": 30,
                "dom": '<"top"f>rt<"bottom"ilp><"clear">',
            });
            $(tableId + ' thead th').each(function (index) {
                var headerText = $(this).text();
                if (headerText !== "" && headerText.toLowerCase() !== "action") {
                    $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                }
            });
            $(filterInputId).on('keyup', function () {
                var selectedColumn = $(dropdownId).val();
                if (selectedColumn !== 'All') {
                    table.column(selectedColumn).search($(this).val()).draw();
                } else {
                    table.search($(this).val()).draw();
                }
            });
            $(dropdownId).on('change', function () {
                $(filterInputId).val('');
                table.search('').columns().search('').draw();
            });
        }
        initTable('#table1', '#headerDropdown1', '#filterInput1');
    });
</script>
@endsection
