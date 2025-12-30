@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head">
            <h4>Cash In Hand List</h4>
            <a data-bs-toggle="modal" data-bs-target="#collectCash">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i>Collect Cash</button>
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
                            <th>Cashier</th>
                            <th>Counter No</th>
                            <th>Cash Sales</th>

                            <th>Online</th>
                            <th>Collected Cash</th>
                            <th>Status</th>
                        </tr>
                    </thead>
<tbody>
    @foreach($salesData as $index => $data)
        @php
            $employee = $data->employee;
            $employeeId = $employee->id ?? null;
            $storeName = $employee->store->store_name ?? 'N/A';
            $collectedAmount = $employeeId ? ($collectedData[$employeeId] ?? 0) : 0;
            $status = ($collectedAmount >= $data->cash_sales) ? 'Collected' : 'Shortage';
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $employee->empname ?? 'Unknown' }}</td>
            <td>{{ $storeName }}</td>
            <td>₹ {{ number_format($data->cash_sales, 2) }}</td>
            <td>₹ {{ number_format($data->online_sales, 2) }}</td>
            <td>₹ {{ number_format($collectedAmount, 2) }}</td>
            <td>
                <span class="text-{{ $status == 'Collected' ? 'success' : 'danger' }}">
                    {{ $status }}
                </span>
            </td>
        </tr>
    @endforeach
</tbody>


                </table>
            </div>
        </div>
    </div>

    <!-- Collect Cash Modal -->
    <div class="modal fade" id="collectCash" tabindex="-1" aria-labelledby="collectCashLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Collect Cash</h4>
                </div>
                <div class="modal-body">
<form action="{{ route('cash.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-sm-12 col-md-6 mb-2">
            <label for="employee_id">Employee</label>
            <select class="form-select" name="employee_id" required>
                <option value="" selected disabled>Select Employee</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->empname }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-12 col-md-6 mb-2">
            <label for="amt">Amount</label>
            <input type="number" class="form-control" name="amount" required>
        </div>
        <div class="d-flex justify-content-between gap-2 mx-auto mt-3">
            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
            <button type="submit" class="modalbtn w-50">Collect</button>
        </div>
    </div>
</form>

                </div>
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