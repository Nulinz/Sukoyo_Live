@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head">
            <h4>Bookings Profile</h4>
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
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Email ID</th>
                            <th>Contact Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                  <tbody>
    <tr id="row-{{ $booking->id }}">
        <td>1</td>
        <td>{{ $booking->student_id }}</td>
        <td>{{ $booking->student_name }}</td>
        <td>{{ $booking->email }}</td>
        <td>{{ $booking->contact_number }}</td>
        <td class="status-text">
            @if($booking->status == 'Active')
                <span class="text-success">Active</span>
            @else
                <span class="text-danger">Inactive</span>
            @endif
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <a href="javascript:void(0);" class="toggle-status" data-id="{{ $booking->id }}" data-status="{{ $booking->status }}" data-bs-toggle="tooltip" data-bs-title="Change Status">
                    @if($booking->status == 'Active')
                        <i class="fas fa-circle-check text-success"></i>
                    @else
                        <i class="fas fa-circle-xmark text-danger"></i>
                    @endif
                </a>
                <!-- <a href="#">
                    <i class="fas fa-pen-to-square"></i>
                </a> -->
            </div>
        </td>
    </tr>
</tbody>
<script>
$(document).ready(function () {
    $('.toggle-status').on('click', function () {
        let btn = $(this);
        let bookingId = btn.data('id');

        $.ajax({
            url: "{{ route('bookings.updateStatus') }}",
            method: 'POST',
            data: {
                id: bookingId,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                // Update the icon
                btn.html(res.icon);
                btn.data('status', res.status);

                // Update the status text
                let statusText = btn.closest('tr').find('.status-text');
                if (res.status === 'Active') {
                    statusText.html('<span class="text-success">Active</span>');
                } else {
                    statusText.html('<span class="text-danger">Inactive</span>');
                }
            }
        });
    });
});
</script>


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