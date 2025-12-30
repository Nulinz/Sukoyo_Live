@extends('layouts.app')

@section('content')

<div class="body-div p-3" id="print-area">
    <div class="body-head">
        <h4>Bill Wise Profit Report</h4>
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
                
                <select class="form-select" name="employee_id" id="employee_id">
                    <option value="all" {{ $selectedEmployee == 'all' ? 'selected' : '' }}>All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $selectedEmployee == $employee->id ? 'selected' : '' }}>
                            {{ $employee->empname }}
                        </option>
                    @endforeach
                </select>

                <select class="form-select" name="profit_filter" id="profit_filter">
                    <option value="all" {{ $profitFilter == 'all' ? 'selected' : '' }}>All Bills</option>
                    <option value="profit" {{ $profitFilter == 'profit' ? 'selected' : '' }}>Profitable</option>
                    <option value="loss" {{ $profitFilter == 'loss' ? 'selected' : '' }}>Loss Making</option>
                    <option value="break_even" {{ $profitFilter == 'break_even' ? 'selected' : '' }}>Break Even</option>
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
                    <a href="javascript:void(0)" class="btn btn-outline-success btn-sm no-print" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export to Excel
                    </a>
                    <button class="btn btn-outline-secondary btn-sm no-print" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <!-- <div class="row mb-3 no-print">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                <div class="info-box bg-info text-white">
                    <div class="info-box-content">
                        <span class="info-box-text">Total Bills</span>
                        <span class="info-box-number" id="totalBills">{{ number_format($totalBills) }}</span>
                    </div>
                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                <div class="info-box bg-success text-white">
                    <div class="info-box-content">
                        <span class="info-box-text">Total Revenue</span>
                        <span class="info-box-number" id="totalRevenue">₹{{ number_format($totalRevenue, 2) }}</span>
                    </div>
                    <span class="info-box-icon"><i class="fas fa-rupee-sign"></i></span>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                <div class="info-box bg-warning text-white">
                    <div class="info-box-content">
                        <span class="info-box-text">Total Cost</span>
                        <span class="info-box-number" id="totalCost">₹{{ number_format($totalCostPrice, 2) }}</span>
                    </div>
                    <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                <div class="info-box {{ $totalProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                    <div class="info-box-content">
                        <span class="info-box-text">Total Profit</span>
                        <span class="info-box-number" id="totalProfit">₹{{ number_format($totalProfit, 2) }}</span>
                    </div>
                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                <div class="info-box {{ $avgProfitMargin >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                    <div class="info-box-content">
                        <span class="info-box-text">Avg Profit %</span>
                        <span class="info-box-number" id="avgProfit">{{ number_format($avgProfitMargin, 2) }}%</span>
                    </div>
                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                <div class="info-box bg-light text-dark">
                    <div class="info-box-content">
                        <span class="info-box-text">P: {{ $profitableBills }} | L: {{ $lossBills }}</span>
                        <span class="info-box-number">Bills</span>
                    </div>
                    <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                </div>
            </div>
        </div> -->

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5 no-print" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered" id="billProfitTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Employee</th>
                        @if(session('role') !== 'manager')
                            <th>Store</th>
                        @endif
                        <th>Payment</th>
                        <th>Items</th>
                        <th>Qty</th>
                        <th>Grand Total</th>
                        <th>Cost Price</th>
                        <th>Profit</th>
                        <th>Profit %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Dynamic content will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="text-center py-3 no-print" style="display: none;">
            <p class="text-muted">No data found for the selected criteria.</p>
        </div>
    </div>
</div>

<!-- Bill Details Modal -->
<div class="modal fade" id="billDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bill Item Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Item Code</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Total Cost</th>
                                <th>Total Selling</th>
                                <th>Item Profit</th>
                                <th>Profit %</th>
                            </tr>
                        </thead>
                        <tbody id="billDetailsBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize on page load
    $(document).ready(function() {
        loadBillProfitData();
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

        setupFilterDropdown(table);
    }

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

    function loadBillProfitData() {
        const storeId = document.getElementById('store_id') ? document.getElementById('store_id').value : 'all';
        const employeeId = document.getElementById('employee_id').value;
        const profitFilter = document.getElementById('profit_filter').value;
        const dateRange = document.getElementById('date_range').value;

        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('billProfitTable').style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'none';

        const params = new URLSearchParams();
        if (storeId && storeId !== 'all') {
            params.append('store_id', storeId);
        }
        if (employeeId !== 'all') {
            params.append('employee_id', employeeId);
        }
        params.append('profit_filter', profitFilter);
        params.append('date_range', dateRange);

        fetch(`{{ route('reports.bill_wise_profit_items') }}?${params.toString()}`)
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

    function populateTable(bills) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        document.getElementById('loadingSpinner').style.display = 'none';

        if (!bills || bills.length === 0) {
            document.getElementById('noDataMessage').style.display = 'block';
            document.getElementById('billProfitTable').style.display = 'none';
            return;
        }

        document.getElementById('billProfitTable').style.display = 'table';
        document.getElementById('noDataMessage').style.display = 'none';

        bills.forEach((bill, idx) => {
            const row = document.createElement('tr');
            const statusClass = bill.profit_amount < 0 ? 'table-danger' : (bill.profit_amount > 0 ? 'table-success' : 'table-warning');
            const profitClass = bill.profit_amount < 0 ? 'text-danger' : (bill.profit_amount > 0 ? 'text-success' : 'text-warning');
            const badgeClass = bill.status === 'Profit' ? 'bg-success' : (bill.status === 'Loss' ? 'bg-danger' : 'bg-warning');
            
            row.className = statusClass;

            const storeColumn = {{ session('role') !== 'manager' ? 'true' : 'false' }} ? 
                `<td>${bill.store_name}</td>` : '';

            row.innerHTML = `
                <td>${(idx + 1).toString().padStart(2, '0')}</td>
                <td>${new Date(bill.invoice_date).toLocaleDateString('en-GB')}</td>
                <td>${bill.id}</td>
                <td>${bill.customer_name}</td>
                <td>${bill.employee_name}</td>
                ${storeColumn}
                <td>${bill.mode_of_payment}</td>
                <td class="text-center">${bill.total_items}</td>
                <td class="text-center">${formatNumber(bill.total_quantity)}</td>
                <td class="text-end"><strong>Rs.${formatNumber(bill.grand_total)}</strong></td>
                <td class="text-end">Rs.${formatNumber(bill.total_cost_price)}</td>
                <td class="text-end ${profitClass}"><strong>Rs.${formatNumber(bill.profit_amount)}</strong></td>
                <td class="text-center ${profitClass}"><strong>${bill.profit_percentage.toFixed(2)}%</strong></td>
                <td class="text-center">
                    <span class="badge ${badgeClass} text-white">
                        ${bill.status}
                    </span>
                </td>

            `;
            tbody.appendChild(row);
        });

        initializeDataTable();
    }

    function updateSummaryCards(bills) {
        if (!bills || bills.length === 0) return;

        const totalBills = bills.length;
        const totalRevenue = bills.reduce((sum, bill) => sum + bill.grand_total, 0);
        const totalCost = bills.reduce((sum, bill) => sum + bill.total_cost_price, 0);
        const totalProfit = bills.reduce((sum, bill) => sum + bill.profit_amount, 0);
        const avgProfit = totalRevenue > 0 ? (totalProfit / totalRevenue) * 100 : 0;

        document.getElementById('totalBills').textContent = formatNumber(totalBills);
        document.getElementById('totalRevenue').textContent = '₹' + formatNumber(totalRevenue);
        document.getElementById('totalCost').textContent = '₹' + formatNumber(totalCost);
        document.getElementById('totalProfit').textContent = '₹' + formatNumber(totalProfit);
        document.getElementById('avgProfit').textContent = avgProfit.toFixed(2) + '%';
    }

    // Event listeners for filters
    if (document.getElementById('store_id')) {
        document.getElementById('store_id').addEventListener('change', loadBillProfitData);
    }
    document.getElementById('employee_id').addEventListener('change', loadBillProfitData);
    document.getElementById('profit_filter').addEventListener('change', loadBillProfitData);
    document.getElementById('date_range').addEventListener('change', loadBillProfitData);

    // Function to show bill details
    function showBillDetails(billId) {
        $.ajax({
            url: `/reports/bill-item-details/${billId}`,
            method: 'GET',
            success: function(data) {
                var tbody = $('#billDetailsBody');
                tbody.empty();
                
                if (data.length > 0) {
                    $.each(data, function(index, item) {
                        var profitClass = item.item_profit < 0 ? 'text-danger' : (item.item_profit > 0 ? 'text-success' : 'text-warning');
                        
                        var row = '<tr>' +
                            '<td>' + item.item_name + '</td>' +
                            '<td>' + (item.item_code || '-') + '</td>' +
                            '<td>' + item.qty + '</td>' +
                            '<td>' + (item.unit || 'PCS') + '</td>' +
                            '<td>₹' + parseFloat(item.unit_cost_price).toFixed(2) + '</td>' +
                            '<td>₹' + parseFloat(item.unit_selling_price).toFixed(2) + '</td>' +
                            '<td>₹' + parseFloat(item.discount).toFixed(2) + '</td>' +
                            '<td>₹' + parseFloat(item.tax).toFixed(2) + '</td>' +
                            '<td>₹' + parseFloat(item.total_cost_price).toFixed(2) + '</td>' +
                            '<td>₹' + parseFloat(item.total_selling_price).toFixed(2) + '</td>' +
                            '<td class="' + profitClass + '"><strong>₹' + parseFloat(item.item_profit).toFixed(2) + '</strong></td>' +
                            '<td class="' + profitClass + '"><strong>' + parseFloat(item.item_profit_percentage).toFixed(2) + '%</strong></td>' +
                            '</tr>';
                        
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="12" class="text-center">No items found</td></tr>');
                }
                
                $('#billDetailsModal').modal('show');
            },
            error: function() {
                alert('Error loading bill details');
            }
        });
    }

    // Export table to Excel (CSV format - safe for Excel)
    function exportToExcel() {
        var table = document.getElementById("billProfitTable");
        var rows = table.querySelectorAll("tr");
        let csv = [];

        for (var i = 0; i < rows.length; i++) {
            let cols = rows[i].querySelectorAll("td, th");
            let row = [];
            for (var j = 0; j < cols.length; j++) {
                // Skip action column
                if (j === cols.length - 1 && cols[j].querySelector('button')) {
                    continue;
                }
                // Escape quotes
                let text = cols[j].innerText.replace(/"/g, '""');
                row.push('"' + text + '"');
            }
            csv.push(row.join(","));
        }

        // Download CSV
        var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        var downloadLink = document.createElement("a");
        downloadLink.download = "bill_wise_profit_report.csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Print Report (disable pagination)
    function printReport() {
        var table = $('.example').DataTable();
        table.page.len(-1).draw(); // Show all rows before printing
        setTimeout(() => {
            window.print();
            table.page.len(10).draw(); // Reset pagination after printing
        }, 500);
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('en-IN').format(num);
    }
</script>

<style>
.info-box {
    display: block;
    min-height: 70px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 4px;
    position: relative;
    margin-bottom: 15px;
}

.info-box-content {
    padding: 8px 10px;
    margin-left: 60px;
}

.info-box-icon {
    border-top-left-radius: 4px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 4px;
    display: block;
    float: left;
    height: 70px;
    width: 60px;
    text-align: center;
    font-size: 24px;
    line-height: 70px;
    background: rgba(0,0,0,0.2);
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 11px;
    display: block;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 14px;
}

.table-responsive {
    font-size: 12px;
}

.btn-sm {
    padding: 2px 6px;
    font-size: 11px;
}

.badge {
    font-size: 10px;
}

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
        .no-print,
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        display: none !important;
    }
    .table,
     {
        font-size: 10px;
    }
}
</style>

@endsection