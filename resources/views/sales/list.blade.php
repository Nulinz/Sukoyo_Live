@extends('layouts.app')

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
                        <th>Bill No</th>
                        <th>Store</th>
                        <th>POS System</th>
                        <th>Bill Type</th>
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
                    @foreach($sales as $index => $sale)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>BILL-{{ $sale->id }}</td>
                            <td>{{ $sale->store_name }}</td>
                            <td>{{ $sale->employee->empname ?? 'N/A' }}</td>
                            <td>{{ ucfirst($sale->status) }}</td>
                            <td>{{ 'INV' . str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                            <td>{{ $sale->items->count() }}</td>
                            <td>{{ $sale->invoice_date->format('d-m-Y') }}</td>
                            <td>{{ $sale->mode_of_payment }}</td>
                            <td>₹ {{ number_format($sale->grand_total, 2) }}</td>
                            <td>
                                <a href="{{ route('sales.profile', ['id' => $sale->id]) }}">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
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

        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText !== "" && headerText.toLowerCase() !== "action") {
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
