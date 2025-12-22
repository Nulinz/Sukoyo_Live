@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head">
            <h4>Class List</h4>
            <a href="{{ route('class.classadd') }}">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Class</button>
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
                            <th>Class Name</th>
                            <th>Class Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Tutor Assigned</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $index => $class)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $class->class_name }}</td>
                            <td>{{ $class->class_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($class->date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($class->time)->format('h:i A') }}</td>
                            <td>
                                @if($class->tutor)
                                    {{ $class->tutor->name }} 
                                    @if($class->tutor->internal_external)
                                        ({{ ucfirst($class->tutor->internal_external) }})
                                    @endif
                                @else
                                    <span class="text-muted">No Tutor Assigned</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $bookedCount = $class->bookings->count();
                                    $max = $class->max_participants;
                                @endphp

                                @if($bookedCount >= $max)
                                    <span class="text-danger">Booked</span>
                                @else
                                    <span class="text-success">Available</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('class.classprofile', $class->id) }}" data-bs-toggle="tooltip" data-bs-title="Profile">
                                        <i class="fas fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <a href="{{ route('class.classedit', $class->id) }}">
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