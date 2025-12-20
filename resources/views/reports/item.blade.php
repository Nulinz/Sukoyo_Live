@extends('layouts.app')

@section('content')

<div class="body-div p-3" id="print-area">
    <div class="body-head">
        <h4>Item Sales and Purchase Summary</h4>
    </div>

    <div class="container-fluid mt-3 listtable">
        <div class="filter-container no-print">
            <div class="filter-container-start">
                <select class="headerDropdown form-select filter-option">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder=" Search">
            </div>
            <div class="filter-container-end">
                <select class="form-select" name="store_id" id="store_id">
                    <option value="all" {{ $selectedStore == 'all' ? 'selected' : '' }}>All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                            {{ $store->store_name }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select" name="date_range" id="date_range">
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
                <div class="d-flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['download' => 'csv']) }}" class="btn btn-outline-primary btn-sm no-print">
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                    <button class="btn btn-outline-secondary btn-sm no-print" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
                
            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Sales Quantity</th>
                        <th>Purchase Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $item->item_code ?? '-' }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td class="text-center">
                                @if($item->sales_quantity > 0)
                                    {{ number_format($item->sales_quantity, 0) }} PCS
                                @else
                                    0 PCS
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->purchase_quantity > 0)
                                    {{ number_format($item->purchase_quantity, 0) }} PCS
                                @else
                                    0 PCS
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">
                                No data found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($items->count() > 0)
                    <tfoot>
                        <tr class="table-secondary">
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-center">
                                {{ number_format($totalSalesQty) }} PCS
                            </th>
                            <th class="text-center">
                                {{ number_format($totalPurchaseQty) }} PCS
                            </th>
                        </tr>
                    </tfoot>
                @endif
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

    // Filter change handlers
    document.getElementById('store_id').addEventListener('change', function() {
        updateReport();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        updateReport();
    });

    function updateReport() {
        const storeId = document.getElementById('store_id').value;
        const dateRange = document.getElementById('date_range').value;
        const url = new URL(window.location.href);
        url.searchParams.set('store_id', storeId);
        url.searchParams.set('date_range', dateRange);
        window.location.href = url.toString();
    }

    function printReport() {
        window.print();
    }
</script>

<style>
    /* Hide unwanted UI while printing */
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print, 
    .dataTables_length, 
    .dataTables_filter, 
    .dataTables_info, 
    .dataTables_paginate { 
        display: none !important; 
    }
}

</style>

@endsection
