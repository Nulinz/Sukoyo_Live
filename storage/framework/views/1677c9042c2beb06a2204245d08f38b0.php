<?php $__env->startSection('content'); ?>

<div class="body-div p-3">
    <div class="body-head d-flex justify-content-between align-items-center">
        <h4>Sales Summary</h4>
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
                    <?php if(session('role') === 'manager'): ?>
                        <option value="<?php echo e(session('store_id')); ?>" selected><?php echo e(session('store_name') ?? 'My Store'); ?></option>
                    <?php else: ?>
                        <option value="all" <?php echo e($selectedStore == 'all' ? 'selected' : ''); ?>>All Stores</option>
                        <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($store->id); ?>" <?php echo e($selectedStore == $store->id ? 'selected' : ''); ?>>
                                <?php echo e($store->store_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
                <select class="form-select" name="employee_id" id="employee_id">
                    <option value="all" <?php echo e($selectedEmployee == 'all' ? 'selected' : ''); ?>>All Employees</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($employee->id); ?>" <?php echo e($selectedEmployee == $employee->id ? 'selected' : ''); ?>>
                            <?php echo e($employee->empname); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-select" name="payment_mode" id="payment_mode">
                    <option value="all" <?php echo e($paymentMode == 'all' ? 'selected' : ''); ?>>All Payment Modes</option>
                    <option value="cash" <?php echo e($paymentMode == 'cash' ? 'selected' : ''); ?>>Cash</option>
                    <option value="card" <?php echo e($paymentMode == 'card' ? 'selected' : ''); ?>>Card</option>
                    <option value="upi" <?php echo e($paymentMode == 'upi' ? 'selected' : ''); ?>>UPI</option>
                    <option value="online" <?php echo e($paymentMode == 'online' ? 'selected' : ''); ?>>Online</option>
                </select>
                <select class="form-select" name="date_range" id="date_range">
                    <option value="today" <?php echo e($dateRange == 'today' ? 'selected' : ''); ?>>Today</option>
                    <option value="yesterday" <?php echo e($dateRange == 'yesterday' ? 'selected' : ''); ?>>Yesterday</option>
                    <option value="last_7_days" <?php echo e($dateRange == 'last_7_days' ? 'selected' : ''); ?>>Last 7 Days</option>
                    <option value="last_30_days" <?php echo e($dateRange == 'last_30_days' ? 'selected' : ''); ?>>Last 30 Days</option>
                    <option value="this_month" <?php echo e($dateRange == 'this_month' ? 'selected' : ''); ?>>This Month</option>
                    <option value="last_month" <?php echo e($dateRange == 'last_month' ? 'selected' : ''); ?>>Last Month</option>
                    <option value="this_year" <?php echo e($dateRange == 'this_year' ? 'selected' : ''); ?>>This Year</option>
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
                        <h6 class="card-title">Total Sales</h6>
                        <h4 id="totalSales"><?php echo e($totalSales); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Amount</h6>
                        <h4 id="totalAmount">₹<?php echo e(number_format($totalAmount)); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Cash Sales</h6>
                        <h4 id="cashSales">₹<?php echo e(number_format($cashSales)); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Online Sales</h6>
                        <h4 id="onlineSales">₹<?php echo e(number_format($onlineSales)); ?></h4>
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
            <table class="example table table-bordered" id="salesSummaryTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Name</th>
                        <th>Employee</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Dynamic content will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="text-center py-3" style="display: none;">
            <!-- <p class="text-muted">No sales found for the selected criteria.</p> -->
        </div>
    </div>
</div>

<script>
    // Initialize on page load
    $(document).ready(function() {
        loadSalesSummaryData();
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

    // Load sales summary data
    function loadSalesSummaryData() {
        const storeId = document.getElementById('store_id').value;
        const employeeId = document.getElementById('employee_id').value;
        const paymentMode = document.getElementById('payment_mode').value;
        const dateRange = document.getElementById('date_range').value;

        // Show loading, hide table and no data message
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('salesSummaryTable').style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'none';

        // Build query
        const params = new URLSearchParams();
        if (storeId !== 'all') {
            params.append('store_id', storeId);
        }
        if (employeeId !== 'all') {
            params.append('employee_id', employeeId);
        }
        if (paymentMode !== 'all') {
            params.append('payment_mode', paymentMode);
        }
        params.append('date_range', dateRange);

        // Fetch from API
        fetch(`<?php echo e(route('api.sales_summary_items')); ?>?${params.toString()}`)
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
function populateTable(sales) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    // Hide loading spinner first
    document.getElementById('loadingSpinner').style.display = 'none';
    
    // Always show the table
    document.getElementById('salesSummaryTable').style.display = 'table';
    document.getElementById('noDataMessage').style.display = 'none';

    if (!sales || sales.length === 0) {
        // Show "No data" message within the table
        const row = document.createElement('tr');
        row.innerHTML = `
            <td colspan="8" class="text-center py-4">
                <p class="text-muted mb-0">No sales found for the selected criteria.</p>
            </td>
        `;
        tbody.appendChild(row);
        
        // Don't initialize DataTable when there's no data
        return;
    }

    sales.forEach((sale, idx) => {
        const row = document.createElement('tr');
        
        // Payment mode badge
        const paymentBadge = getPaymentModeBadge(sale.mode_of_payment);

        row.innerHTML = `
            <td>${(idx + 1).toString().padStart(2, '0')}</td>
            <td>${formatDate(sale.invoice_date)}</td>
            <td>${sale.id}</td>
            <td>${sale.customer_name}</td>
            <td>${sale.employee_name}</td>
            <td class="text-center">${paymentBadge}</td>
            <td class="text-end">Rs.${formatNumber(sale.grand_total)}</td>
            <td class="text-center">
                <span class="badge bg-success text-white">${sale.status}</span>
            </td>
        `;

        tbody.appendChild(row);
    });

    // Initialize DataTable after populating data (only when there's data)
    initializeDataTable();
}

    // Get payment mode badge
    function getPaymentModeBadge(mode) {
        const badges = {
            'Cash': '<span class="badge bg-success">Cash</span>',
            'Card': '<span class="badge bg-primary">Card</span>',
            'Upi': '<span class="badge bg-info">UPI</span>',
            'Online': '<span class="badge bg-warning">Online</span>'
        };
        return badges[mode] || `<span class="badge bg-secondary">${mode}</span>`;
    }

    // Update summary cards
    function updateSummaryCards(sales) {
        const totalSales = sales.length;
        const totalAmount = sales.reduce((sum, sale) => sum + sale.grand_total, 0);
        const cashSales = sales.filter(sale => sale.mode_of_payment === 'Cash')
                               .reduce((sum, sale) => sum + sale.grand_total, 0);
        const onlineSales = sales.filter(sale => ['Card', 'Upi', 'Online'].includes(sale.mode_of_payment))
                                 .reduce((sum, sale) => sum + sale.grand_total, 0);

        document.getElementById('totalSales').textContent = formatNumber(totalSales);
        document.getElementById('totalAmount').textContent = '₹' + formatNumber(totalAmount);
        document.getElementById('cashSales').textContent = '₹' + formatNumber(cashSales);
        document.getElementById('onlineSales').textContent = '₹' + formatNumber(onlineSales);
    }

    // Filter change handlers
    document.getElementById('store_id').addEventListener('change', function() {
        loadSalesSummaryData();
    });

    document.getElementById('employee_id').addEventListener('change', function() {
        loadSalesSummaryData();
    });

    document.getElementById('payment_mode').addEventListener('change', function() {
        loadSalesSummaryData();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        loadSalesSummaryData();
    });

    // Download report
    function downloadReport() {
        const storeId = document.getElementById('store_id').value;
        const employeeId = document.getElementById('employee_id').value;
        const paymentMode = document.getElementById('payment_mode').value;
        const dateRange = document.getElementById('date_range').value;

        const params = new URLSearchParams();
        if (storeId !== 'all') {
            params.append('store_id', storeId);
        }
        if (employeeId !== 'all') {
            params.append('employee_id', employeeId);
        }
        if (paymentMode !== 'all') {
            params.append('payment_mode', paymentMode);
        }
        params.append('date_range', dateRange);
        params.append('download', '1');

        window.location.href = `<?php echo e(route('reports.sales_summary')); ?>?${params.toString()}`;
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

</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/reports/sales_summary.blade.php ENDPATH**/ ?>