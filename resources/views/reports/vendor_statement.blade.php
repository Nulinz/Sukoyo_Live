@extends('layouts.app')

@section('title', 'Vendor Statement (Ledger)')

@section('content')

<div class="body-div p-3" id="print-area">
    <div class="body-head">
        <h4>Vendor Statement (Ledger)</h4>
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
                @if(session('role') !== 'manager')
                <select class="form-select" name="store_id" id="store_id">
                    <option value="all" {{ $selectedStore == 'all' ? 'selected' : '' }}>All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                            {{ $store->store_name }}
                        </option>
                    @endforeach
                </select>
                @endif
                <select class="form-select" name="vendor_id" id="vendor_id">
                    <option value="all" {{ $selectedVendor == 'all' ? 'selected' : '' }}>All Vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ $selectedVendor == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->vendorname }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select" name="date_range" id="date_range">
                    <option value="all_time" {{ $dateRange == 'all_time' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
                <select class="form-select" name="transaction_type" id="transaction_type">
                    <option value="all" {{ $transactionType == 'all' ? 'selected' : '' }}>All Transactions</option>
                    <option value="purchase" {{ $transactionType == 'purchase' ? 'selected' : '' }}>Purchases Only</option>
                    <option value="payment" {{ $transactionType == 'payment' ? 'selected' : '' }}>Payments Only</option>
                </select>
                <div class="d-flex gap-2">
               
                    <button class="btn btn-outline-secondary btn-sm no-print" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button class="btn btn-outline-success btn-sm no-print" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Date Range (Hidden by default) -->
        <div class="row mt-3 no-print" id="customDateRange" style="display: none;">
            <div class="col-md-3">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-primary" onclick="applyCustomDateRange()">Apply</button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Vendor</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Store</th>
                        <th>Debit (Dr.)</th>
                        <th>Credit (Cr.)</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @forelse($statementData as $vendorId => $vendorData)
                        @foreach($vendorData['transactions'] as $transaction)
                        <tr>
                            <td>{{ str_pad($counter, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ date('d-m-Y', strtotime($transaction['date'])) }}</td>
                            <td>{{ $vendorData['vendor']->vendorname }}</td>
                            <td>
                                {{ $transaction['reference'] }}
                                @if($transaction['due_date'])
                                <br><small class="text-muted">Due: {{ date('d-m-Y', strtotime($transaction['due_date'])) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $transaction['description'] }}
                                @if($transaction['payment_type'])
                                <br><small class="text-muted">{{ ucfirst($transaction['payment_type']) }}</small>
                                @endif
                            </td>
                            <td>{{ $transaction['store'] ?? '-' }}</td>
                            <td class="text-end">
                                @if($transaction['debit'] > 0)
                                Rs.{{ number_format($transaction['debit'], 2) }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-end">
                                @if($transaction['credit'] > 0)
                                Rs.{{ number_format($transaction['credit'], 2) }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="text-end">
                                Rs.{{ number_format($transaction['running_balance'], 2) }}
                            </td>
                            <td class="text-center">
                                @if($transaction['status'] == 'Outstanding')
                                <span class="badge bg-warning">{{ $transaction['status'] }}</span>
                                @elseif($transaction['status'] == 'Paid')
                                <span class="badge bg-success">{{ $transaction['status'] }}</span>
                                @else
                                <span class="badge bg-info">{{ $transaction['status'] }}</span>
                                @endif
                            </td>
                        </tr>
                        @php $counter++; @endphp
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-3 text-muted">
                                No vendor statements found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($statementData) > 0)
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Total</th>
                        <th class="text-end">
                            Rs.{{ number_format(collect($statementData)->sum('summary.total_debit'), 2) }}
                        </th>
                        <th class="text-end">
                            Rs.{{ number_format(collect($statementData)->sum('summary.total_credit'), 2) }}
                        </th>
                        <th class="text-end">
                            Rs.{{ number_format(collect($statementData)->sum('summary.balance'), 2) }}
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
    @if(session('role') !== 'manager')
    document.getElementById('store_id').addEventListener('change', function() {
        updateReport();
    });
    @endif

    document.getElementById('vendor_id').addEventListener('change', function() {
        updateReport();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        toggleCustomDateRange();
        if (this.value !== 'custom') {
            updateReport();
        }
    });

    document.getElementById('transaction_type').addEventListener('change', function() {
        updateReport();
    });

    function toggleCustomDateRange() {
        const dateRange = document.getElementById('date_range').value;
        const customDateRange = document.getElementById('customDateRange');
        
        if (dateRange === 'custom') {
            customDateRange.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
        }
    }

    function applyCustomDateRange() {
        updateReport();
    }

    function updateReport() {
        @if(session('role') !== 'manager')
        const storeId = document.getElementById('store_id').value;
        @else
        const storeId = 'all';
        @endif
        const vendorId = document.getElementById('vendor_id').value;
        const dateRange = document.getElementById('date_range').value;
        const transactionType = document.getElementById('transaction_type').value;
        const startDate = document.getElementById('start_date')?.value || '';
        const endDate = document.getElementById('end_date')?.value || '';
        
        const url = new URL(window.location.href);
        url.searchParams.set('store_id', storeId);
        url.searchParams.set('vendor_id', vendorId);
        url.searchParams.set('date_range', dateRange);
        url.searchParams.set('transaction_type', transactionType);
        
        if (dateRange === 'custom') {
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);
        }
        
        window.location.href = url.toString();
    }

    function printReport() {
        window.print();
    }

    function exportToExcel() {
        // Find the container with report data
        var table = document.querySelector("#print-area");
        
        // Convert HTML to worksheet
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.table_to_sheet(table);

        // Append worksheet
        XLSX.utils.book_append_sheet(wb, ws, "Vendor Ledger");

        // Export the Excel file
        XLSX.writeFile(wb, "vendor_ledger.xlsx");
    }

    // Initialize custom date range visibility
    $(document).ready(function() {
        toggleCustomDateRange();
    });
</script>

<style>
    /* Hide unwanted UI while printing */
@media print {
    @page {
        size: A3 landscape;
        margin: 10mm;
    }

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