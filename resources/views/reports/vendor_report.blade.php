@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head d-flex justify-content-between align-items-center">
        <h4>Vendor Report</h4>
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
                    @if(session('role') === 'manager')
                        <option value="{{ session('store_id') }}" selected>{{ session('store_name') ?? 'My Store' }}</option>
                    @else
                        <option value="all" {{ $selectedStore == 'all' ? 'selected' : '' }}>All Stores</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                                {{ $store->store_name }}
                            </option>
                        @endforeach
                    @endif
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
                        <h6 class="card-title">Total Vendors</h6>
                        <h4 id="totalVendors">{{ $totalVendors }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Orders</h6>
                        <h4 id="totalOrders">{{ number_format($totalOrders) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Quantity</h6>
                        <h4 id="totalQuantity">{{ number_format($totalQuantity) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Amount</h6>
                        <h4 id="totalAmount">₹{{ number_format($totalAmount) }}</h4>
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
            <table class="example table table-bordered" id="vendorReportTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vendor Name</th>
                        <th>Contact</th>
                        <th>Total Orders</th>
                        <th>Total Items</th>
                        <th>Purchase Quantity</th>
                        <th>Purchase Amount</th>
                        <th>Last Purchase</th>
                    </tr>
                </thead><br><br>
                <tbody id="tableBody">
                    <!-- Dynamic content will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="text-center py-3" style="display: none;">
            <!-- <p class="text-muted">No vendor data found for the selected criteria.</p> -->
        </div>
    </div>
</div>

<script>
    // Initialize on page load
    $(document).ready(function() {
        loadVendorReportData();
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

    // Load vendor report data
    function loadVendorReportData() {
        const storeId = document.getElementById('store_id').value;
        const vendorId = document.getElementById('vendor_id').value;
        const dateRange = document.getElementById('date_range').value;

        // Show loading, hide table and no data message
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('vendorReportTable').style.display = 'none';
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
        fetch(`{{ route('api.vendor_report_items') }}?${params.toString()}`)
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
function populateTable(vendors) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    // Hide loading spinner first
    document.getElementById('loadingSpinner').style.display = 'none';
    
    // Always show the table
    document.getElementById('vendorReportTable').style.display = 'table';
    document.getElementById('noDataMessage').style.display = 'none';

    if (!vendors || vendors.length === 0) {
        // Show "No data" message within the table
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="8" class="text-center py-4">
                <p class="text-muted mb-0">No vendor data found for the selected criteria.</p>
            </td>
        `;
        tbody.appendChild(row);
        
        // Don't initialize DataTable when there's no data
        return;
    }

    vendors.forEach((vendor, idx) => {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>${(idx + 1).toString().padStart(2, '0')}</td>
            <td>${vendor.vendor_name}</td>
            <td>${vendor.vendor_contact}</td>
            <td class="text-center">${vendor.total_orders}</td>
            <td class="text-center">${vendor.total_items}</td>
            <td class="text-center">${formatNumber(vendor.total_quantity)} Pcs</td>
            <td class="text-end">Rs.${formatNumber(vendor.total_amount)}</td>
            <td class="text-center">${formatDate(vendor.last_purchase)}</td>
        `;

        tbody.appendChild(row);
    });

    // Initialize DataTable after populating data (only when there's data)
    initializeDataTable();
}

    // Update summary cards
    function updateSummaryCards(vendors) {
        const totalVendors = vendors.length;
        const totalOrders = vendors.reduce((sum, vendor) => sum + vendor.total_orders, 0);
        const totalQuantity = vendors.reduce((sum, vendor) => sum + vendor.total_quantity, 0);
        const totalAmount = vendors.reduce((sum, vendor) => sum + vendor.total_amount, 0);

        document.getElementById('totalVendors').textContent = formatNumber(totalVendors);
        document.getElementById('totalOrders').textContent = formatNumber(totalOrders);
        document.getElementById('totalQuantity').textContent = formatNumber(totalQuantity);
        document.getElementById('totalAmount').textContent = '₹' + formatNumber(totalAmount);
    }

    // Filter change handlers
    document.getElementById('store_id').addEventListener('change', function() {
        loadVendorReportData();
    });

    document.getElementById('vendor_id').addEventListener('change', function() {
        loadVendorReportData();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        loadVendorReportData();
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

        window.location.href = `{{ route('reports.vendor_report') }}?${params.toString()}`;
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
@endsection