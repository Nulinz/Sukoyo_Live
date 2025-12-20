@extends('layouts.app_pos')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Sales List</h4>
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
                        <th>Invoice No</th>
                        <th>Customer Name</th>
                        <th>No Of Items</th>
                        <th>Date</th>
                        <th>Payment Type</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
<tbody>
@foreach($salesInvoices as $index => $invoice)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $invoice->id }}</td>
        <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
        <td>{{ $invoice->items->count() }}</td>
        <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
        <td>{{ $invoice->mode_of_payment }}</td>
        <td>₹ {{ number_format($invoice->grand_total, 2) }}</td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('sales.profile.details', ['id' => $invoice->id]) }}" data-bs-toggle="tooltip" data-bs-title="Profile">
                    <i class="fas fa-arrow-up-right-from-square"></i>
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
    $(document).ready(function() {
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
    $(document).ready(function() {
        var table = $('.example').DataTable();
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