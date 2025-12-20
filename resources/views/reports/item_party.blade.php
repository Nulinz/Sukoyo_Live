@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Item Report By Party</h4>
    </div>

    <div class="container-fluid mt-3 listtable">
        <div class="filter-container">
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
                <select class="form-select" name="vendor_id" id="vendor_id">
                    <option value="all" {{ $selectedVendor == 'all' ? 'selected' : '' }}>All Vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ $selectedVendor == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->vendorname }}
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
                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-sm" onclick="downloadReport()">
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <!-- <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Items</h5>
                        <h3 id="totalItems">{{ $items->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h3 id="totalOrders">{{ number_format($totalOrders) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Quantity</h5>
                        <h3 id="totalQuantity">{{ number_format($totalQuantity) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Amount</h5>
                        <h3 id="totalAmount">₹{{ number_format($totalAmount) }}</h3>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered" id="itemPartyTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Vendor Name</th>
                        <th>Store/Warehouse</th>
                        <th>Total Orders</th>
                        <th>Total Quantity</th>
                        <th>Total Amount</th>
                        <th>Avg Price</th>
                        <th>Last Purchase</th>
                    </tr>
                </thead><br>
                <tbody id="tableBody">
                    <!-- Dynamic content will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="text-center py-3" style="display: none;">
            <!-- <p class="text-muted">No purchase items found for the selected criteria.</p> -->
        </div>
    </div>
</div>

<script>
    // Initialize on page load
    $(document).ready(function() {
        loadItemPartyData();
    });

    // DataTables initialization
    function initializeDataTable() {
        if ($.fn.DataTable.isDataTable('.example')) {
            $('.example').DataTable().destroy();
        }
        
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

        // Setup filter dropdown
        setupFilterDropdown(table);
    }

    // Setup filter functionality
    function setupFilterDropdown(table) {
        $('.headerDropdown').empty().append('<option value="All" selected>All</option>');
        
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "progress") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
            }
        });

        $('.filterInput').off('keyup').on('keyup', function() {
            var selectedColumn = $('.headerDropdown').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search($(this).val()).draw();
            } else {
                table.search($(this).val()).draw();
            }
        });

        $('.headerDropdown').off('change').on('change', function() {
            $('.filterInput').val('');
            table.search('').columns().search('').draw();
        });
    }

    // Load item party data
    function loadItemPartyData() {
        const storeId = document.getElementById('store_id').value;
        const vendorId = document.getElementById('vendor_id').value;
        const dateRange = document.getElementById('date_range').value;

        // Show loading
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('itemPartyTable').style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'none';

        // Build query
        const params = new URLSearchParams();
        if (storeId !== 'all') {
            params.append('store_id', storeId);
        }
        if (vendorId !== 'all') {
            params.append('vendor_id', vendorId);
        }
        params.append('date_range', dateRange);

        // Fetch from API
        fetch(`{{ route('api.item_party_items') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                populateTable(data);
                updateSummaryCards(data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('noDataMessage').style.display = 'block';
            });
    }

    // Populate table with data
    function populateTable(items) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('itemPartyTable').style.display = 'table';
        document.getElementById('noDataMessage').style.display = 'none';

        if (!items || items.length === 0) {
            // Show "no data" message inside the table
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="10" class="text-center py-4 text-muted">
                    No purchase items found for the selected criteria.
                </td>
            `;
            tbody.appendChild(row);
            
            // Initialize DataTable even with no data to maintain functionality
            initializeDataTable();
            return;
        }

        items.forEach((item, idx) => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${(idx + 1).toString().padStart(2, '0')}</td>
                <td>${item.item_code ?? '-'}</td>
                <td>${item.item_name}</td>
                <td>${item.vendor_name}</td>
                <td>${item.store_name ?? '-'}</td>
                <td class="text-center">${item.total_orders}</td>
                <td class="text-center">${formatNumber(item.total_quantity)}</td>
                <td class="text-center">Rs.${formatNumber(item.total_amount)}</td>
                <td class="text-center">Rs.${formatNumber(item.avg_price)}</td>
                <td class="text-center">${formatDate(item.last_purchase)}</td>
            `;

            tbody.appendChild(row);
        });

        // Initialize DataTable after populating data
        initializeDataTable();
    }
    // Update summary cards
    function updateSummaryCards(items) {
        const totalItems = items.length;
        const totalOrders = items.reduce((sum, item) => sum + item.total_orders, 0);
        const totalQuantity = items.reduce((sum, item) => sum + item.total_quantity, 0);
        const totalAmount = items.reduce((sum, item) => sum + item.total_amount, 0);

        document.getElementById('totalItems').textContent = formatNumber(totalItems);
        document.getElementById('totalOrders').textContent = formatNumber(totalOrders);
        document.getElementById('totalQuantity').textContent = formatNumber(totalQuantity);
        document.getElementById('totalAmount').textContent = '₹' + formatNumber(totalAmount);
    }

    // Filter change handlers
    document.getElementById('store_id').addEventListener('change', function() {
        loadItemPartyData();
    });

    document.getElementById('vendor_id').addEventListener('change', function() {
        loadItemPartyData();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        loadItemPartyData();
    });

    // Download report
    function downloadReport() {
        const storeId = document.getElementById('store_id').value;
        const vendorId = document.getElementById('vendor_id').value;
        const dateRange = document.getElementById('date_range').value;

        const params = new URLSearchParams();
        if (storeId !== 'all') {
            params.append('store_id', storeId);
        }
        if (vendorId !== 'all') {
            params.append('vendor_id', vendorId);
        }
        params.append('date_range', dateRange);
        params.append('download', '1');

        window.location.href = `{{ route('reports.item_party') }}?${params.toString()}`;
    }

    // Print report
    function printReport() {
        window.print();
    }

    // Format number with Indian locale
    function formatNumber(num) {
        return new Intl.NumberFormat('en-IN').format(num);
    }

    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN');
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
    .table-wrapper, 
    .table-wrapper * {
        visibility: visible;
    }
    .table-wrapper {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .table {
        font-size: 12px;
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