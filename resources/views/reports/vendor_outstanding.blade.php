@extends('layouts.app')

@section('title', 'Vendor Outstanding Report')

@section('content')

<div class="body-div p-3" id="print-area">
    <div class="body-head">
        <h4>Vendor Outstanding Report</h4>
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
                </select>
                <select class="form-select" name="outstanding_filter" id="outstanding_filter">
                    <option value="all" {{ $outstandingFilter == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="outstanding_only" {{ $outstandingFilter == 'outstanding_only' ? 'selected' : '' }}>Outstanding Only</option>
                    <option value="paid_only" {{ $outstandingFilter == 'paid_only' ? 'selected' : '' }}>Fully Paid Only</option>
                </select>
                <div class="d-flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['download' => '1']) }}" class="btn btn-outline-primary btn-sm no-print">
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
                        <th>Vendor Name</th>
                        <th>Contact</th>
                        <th>Store</th>
                        <th>Total Transactions</th>
                        <th>Outstanding Transactions</th>
                        <th>Total Purchase Amount</th>
                        <th>Total Paid Amount</th>
                        <th>Outstanding Amount</th>
                        <th>Payment %</th>
                        <th>Last Transaction</th>
                        <th>Status</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendorOutstanding as $index => $vendor)
                        <tr class="{{ $vendor->total_outstanding_amount > 0 ? 'table-warning' : '' }}">
                            <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <strong>{{ $vendor->vendor_name }}</strong>
                                @if($vendor->vendor_email !== 'N/A')
                                    <br><small class="text-muted">{{ $vendor->vendor_email }}</small>
                                @endif
                            </td>
                            <td>{{ $vendor->vendor_contact ?? '-' }}</td>
                            <td>{{ $vendor->store_name ?? '-' }}</td>
                            <td class="text-center">{{ $vendor->total_transactions }}</td>
                            <td class="text-center">
                                @if($vendor->outstanding_transactions > 0)
                                    <span class="text-danger">{{ $vendor->outstanding_transactions }}</span>
                                @else
                                    <span class="text-success">0</span>
                                @endif
                            </td>
                            <td class="text-end">Rs.{{ number_format($vendor->total_purchase_amount, 2) }}</td>
                            <td class="text-end">Rs.{{ number_format($vendor->total_paid_amount, 2) }}</td>
                            <td class="text-end">
                                @if($vendor->total_outstanding_amount > 0)
                                    <span class="text-danger fw-bold">Rs.{{ number_format($vendor->total_outstanding_amount, 2) }}</span>
                                @else
                                    <span class="text-success">Rs.0.00</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="progress" style="height: 18px; width: 80px; margin: 0 auto;">
                                    <div class="progress-bar 
                                        {{ $vendor->payment_percentage == 100 ? 'bg-success' : ($vendor->payment_percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                        role="progressbar" 
                                        style="width: {{ $vendor->payment_percentage }}%"
                                        aria-valuenow="{{ $vendor->payment_percentage }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <small>{{ number_format($vendor->payment_percentage, 1) }}%</small>
                            </td>
                            <td class="text-center">
                                {{ $vendor->last_transaction_date ? date('d-m-Y', strtotime($vendor->last_transaction_date)) : '-' }}
                            </td>
                            <td class="text-center">
                                @if($vendor->status == 'Fully Paid')
                                    <span class="badge bg-success">{{ $vendor->status }}</span>
                                @elseif($vendor->status == 'Partially Paid')
                                    <span class="badge bg-warning">{{ $vendor->status }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $vendor->status }}</span>
                                @endif
                            </td>
                            <td class="text-center no-print">
                                <button type="button" class="btn btn-sm btn-info" onclick="viewTransactionDetails({{ $vendor->vendor_id }}, '{{ $vendor->vendor_name }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-3 text-muted">
                                No vendor data found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($vendorOutstanding->count() > 0)
                    <tfoot>
                        <tr class="table-secondary">
                            <th colspan="6" class="text-end">Total</th>
                            <th class="text-end">
                                Rs.{{ number_format($vendorOutstanding->sum('total_purchase_amount'), 2) }}
                            </th>
                            <th class="text-end">
                                Rs.{{ number_format($vendorOutstanding->sum('total_paid_amount'), 2) }}
                            </th>
                            <th class="text-end">
                                Rs.{{ number_format($vendorOutstanding->sum('total_outstanding_amount'), 2) }}
                            </th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details - <span id="vendorNameTitle"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bill No</th>
                                <th>Bill Date</th>
                                <th>Due Date</th>
                                <th>Store</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                                <th>Payment Type</th>
                                <!-- <th>Days Past Due</th> -->
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="transactionDetailsBody">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
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
            "order": [[8, "desc"]], // Order by outstanding amount desc
            "columnDefs": [
                { "orderable": false, "targets": [12] } // Actions column not orderable
            ]
        });
    });

    // List Filter
    $(document).ready(function() {
        var table = $('.example').DataTable();
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "actions" && headerText.toLowerCase() != "progress") {
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
        updateReport();
    });

    document.getElementById('outstanding_filter').addEventListener('change', function() {
        updateReport();
    });

    function updateReport() {
        @if(session('role') !== 'manager')
        const storeId = document.getElementById('store_id').value;
        @else
        const storeId = 'all';
        @endif
        const vendorId = document.getElementById('vendor_id').value;
        const dateRange = document.getElementById('date_range').value;
        const outstandingFilter = document.getElementById('outstanding_filter').value;
        
        const url = new URL(window.location.href);
        url.searchParams.set('store_id', storeId);
        url.searchParams.set('vendor_id', vendorId);
        url.searchParams.set('date_range', dateRange);
        url.searchParams.set('outstanding_filter', outstandingFilter);
        window.location.href = url.toString();
    }

    function printReport() {
        window.print();
    }

    function viewTransactionDetails(vendorId, vendorName) {
        $('#vendorNameTitle').text(vendorName);
        $('#transactionDetailsBody').html('<tr><td colspan="11" class="text-center">Loading...</td></tr>');
        $('#transactionDetailsModal').modal('show');
        
        // Fetch transaction details
        $.ajax({
            url: `/reports/vendor-outstanding/vendor/${vendorId}/transactions`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                let html = '';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function(transaction, index) {
                        const statusClass = transaction.status.includes('Unpaid') ? 'text-danger' : 
                                          (transaction.status.includes('Partially') ? 'text-warning' : 'text-success');
                        
                        
                        html += `
                            <tr class="${transaction.balance_amount > 0 ? 'table-warning' : ''}">
                                <td>${transaction.index}</td>
                                <td>${transaction.bill_no}</td>
                                <td>${transaction.bill_date ? new Date(transaction.bill_date).toLocaleDateString('en-GB') : 'N/A'}</td>
                                <td>${transaction.due_date ? new Date(transaction.due_date).toLocaleDateString('en-GB') : 'N/A'}</td>
                                <td>${transaction.store_name}</td>
                                <td class="text-end">Rs.${parseFloat(transaction.total_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                                <td class="text-end">Rs.${parseFloat(transaction.paid_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                                <td class="text-end ${transaction.balance_amount > 0 ? 'text-danger' : 'text-success'}">
                                    <strong>Rs.${parseFloat(transaction.balance_amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong>
                                </td>
                                <td>${transaction.payment_type}</td>
                                <td class="text-center">
                                    <span class="badge badge-${transaction.status.includes('Unpaid') ? 'danger' : (transaction.status.includes('Partially') ? 'warning' : 'success')}">
                                        ${transaction.status}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="11" class="text-center">No transactions found</td></tr>';
                }
                $('#transactionDetailsBody').html(html);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                
                let errorMessage = 'Error loading transaction details';
                if (xhr.status === 404) {
                    errorMessage = 'Transaction details endpoint not found';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred';
                }
                
                $('#transactionDetailsBody').html(`<tr><td colspan="11" class="text-center text-danger">${errorMessage}</td></tr>`);
            }
        });
    }
</script>

<style>
@media print {
    @page {
        size: A3 landscape;   /* Force landscape page */
        margin: 10mm;         /* Adjust margins */
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

    /* Remove scroll & show full table */
    .table-wrapper {
        overflow: visible !important;
        height: auto !important;
    }

    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 11px;  /* Shrink font so all columns fit */
    }

    th, td {
        border: 1px solid #000 !important;
        padding: 4px !important;
        text-align: center;
        white-space: nowrap; /* Prevent line breaks */
    }

    /* Hide controls and DataTables UI */
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