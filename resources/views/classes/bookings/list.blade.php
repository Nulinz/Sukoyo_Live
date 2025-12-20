@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Bookings List</h4>
        <a href="{{ route('class.bookingsadd') }}">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Bookings</button>
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
                        <th>No Of Bookings</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $key => $booking)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $booking->class_name }}</td>
                            <td>{{ $booking->class_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}</td>
                            <td>1</td> {{-- Or count logic if grouping --}}
                            <td>
                                @php
                                    $status = 'Open';
                                    if (isset($booking->status) && $booking->status === 'full') {
                                        $status = 'Fully Booked';
                                    }
                                @endphp
                                {{ $status }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                  <a href="{{ route('class.bookingsprofile', ['id' => $booking->id]) }}" data-bs-toggle="tooltip" title="Profile">
    <i class="fas fa-arrow-up-right-from-square"></i>
</a>


                                    <a href="{{ route('class.bookingsedit', ['id' => $booking->id]) }}" data-bs-toggle="tooltip" title="Edit">
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
            "dom": '<"top"f>rt<"bottom"lp><"clear">'
        });

        // List Filter
        $('.example thead th').each(function (index) {
            var headerText = $(this).text();
            if (headerText !== "" && headerText.toLowerCase() !== "action" && headerText.toLowerCase() !== "progress") {
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
