@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Warehouse List</h4>
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
        <th>Product Name</th>
        <th>Brand</th>
        <th>Unit</th>
        <th>Purchased Qty</th>
        <th>Sold Qty</th>
        <th>Available Qty</th>
        <th>Price (Per Unit)</th>
        <th>Total Value</th>
    </tr>
</thead>

<tbody>
    @foreach($items as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->item_name ?? 'N/A' }}</td>
            <td>{{ $item->brand_name ?? 'N/A' }}</td>
            <td>{{ $item->unit ?? 'N/A' }}</td>
            <td>{{ $item->total_purchased_qty }}</td>
            <td>{{ $item->total_sold_qty }}</td>
            <td>{{ $item->available_qty }}</td>
            <td>₹ {{ number_format($item->unit_price, 2) }}</td>
            <td>₹ {{ number_format($item->total_value, 2) }}</td>
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