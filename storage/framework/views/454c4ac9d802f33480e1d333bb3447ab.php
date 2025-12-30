<?php $__env->startSection('title', 'Vendor Statement (Ledger)'); ?>

<?php $__env->startSection('content'); ?>

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
                <?php if(session('role') !== 'manager'): ?>
                <select class="form-select" name="store_id" id="store_id">
                    <option value="all" <?php echo e($selectedStore == 'all' ? 'selected' : ''); ?>>All Stores</option>
                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($store->id); ?>" <?php echo e($selectedStore == $store->id ? 'selected' : ''); ?>>
                            <?php echo e($store->store_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php endif; ?>
                <select class="form-select" name="vendor_id" id="vendor_id">
                    <option value="all" <?php echo e($selectedVendor == 'all' ? 'selected' : ''); ?>>All Vendors</option>
                    <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($vendor->id); ?>" <?php echo e($selectedVendor == $vendor->id ? 'selected' : ''); ?>>
                            <?php echo e($vendor->vendorname); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select class="form-select" name="date_range" id="date_range">
                    <option value="all_time" <?php echo e($dateRange == 'all_time' ? 'selected' : ''); ?>>All Time</option>
                    <option value="today" <?php echo e($dateRange == 'today' ? 'selected' : ''); ?>>Today</option>
                    <option value="yesterday" <?php echo e($dateRange == 'yesterday' ? 'selected' : ''); ?>>Yesterday</option>
                    <option value="last_7_days" <?php echo e($dateRange == 'last_7_days' ? 'selected' : ''); ?>>Last 7 Days</option>
                    <option value="last_30_days" <?php echo e($dateRange == 'last_30_days' ? 'selected' : ''); ?>>Last 30 Days</option>
                    <option value="this_month" <?php echo e($dateRange == 'this_month' ? 'selected' : ''); ?>>This Month</option>
                    <option value="last_month" <?php echo e($dateRange == 'last_month' ? 'selected' : ''); ?>>Last Month</option>
                    <option value="this_year" <?php echo e($dateRange == 'this_year' ? 'selected' : ''); ?>>This Year</option>
                    <option value="custom" <?php echo e($dateRange == 'custom' ? 'selected' : ''); ?>>Custom Range</option>
                </select>
                <select class="form-select" name="transaction_type" id="transaction_type">
                    <option value="all" <?php echo e($transactionType == 'all' ? 'selected' : ''); ?>>All Transactions</option>
                    <option value="purchase" <?php echo e($transactionType == 'purchase' ? 'selected' : ''); ?>>Purchases Only</option>
                    <option value="payment" <?php echo e($transactionType == 'payment' ? 'selected' : ''); ?>>Payments Only</option>
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
                <input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo e($startDate); ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo e($endDate); ?>">
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
                    <?php $counter = 1; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $statementData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendorId => $vendorData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $__currentLoopData = $vendorData['transactions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(str_pad($counter, 2, '0', STR_PAD_LEFT)); ?></td>
                            <td><?php echo e(date('d-m-Y', strtotime($transaction['date']))); ?></td>
                            <td><?php echo e($vendorData['vendor']->vendorname); ?></td>
                            <td>
                                <?php echo e($transaction['reference']); ?>

                                <?php if($transaction['due_date']): ?>
                                <br><small class="text-muted">Due: <?php echo e(date('d-m-Y', strtotime($transaction['due_date']))); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e($transaction['description']); ?>

                                <?php if($transaction['payment_type']): ?>
                                <br><small class="text-muted"><?php echo e(ucfirst($transaction['payment_type'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($transaction['store'] ?? '-'); ?></td>
                            <td class="text-end">
                                <?php if($transaction['debit'] > 0): ?>
                                Rs.<?php echo e(number_format($transaction['debit'], 2)); ?>

                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if($transaction['credit'] > 0): ?>
                                Rs.<?php echo e(number_format($transaction['credit'], 2)); ?>

                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                Rs.<?php echo e(number_format($transaction['running_balance'], 2)); ?>

                            </td>
                            <td class="text-center">
                                <?php if($transaction['status'] == 'Outstanding'): ?>
                                <span class="badge bg-warning"><?php echo e($transaction['status']); ?></span>
                                <?php elseif($transaction['status'] == 'Paid'): ?>
                                <span class="badge bg-success"><?php echo e($transaction['status']); ?></span>
                                <?php else: ?>
                                <span class="badge bg-info"><?php echo e($transaction['status']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php $counter++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center py-3 text-muted">
                                No vendor statements found for the selected criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if(count($statementData) > 0): ?>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="6" class="text-end">Total</th>
                        <th class="text-end">
                            Rs.<?php echo e(number_format(collect($statementData)->sum('summary.total_debit'), 2)); ?>

                        </th>
                        <th class="text-end">
                            Rs.<?php echo e(number_format(collect($statementData)->sum('summary.total_credit'), 2)); ?>

                        </th>
                        <th class="text-end">
                            Rs.<?php echo e(number_format(collect($statementData)->sum('summary.balance'), 2)); ?>

                        </th>
                        <th></th>
                    </tr>
                </tfoot>
                <?php endif; ?>
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
    <?php if(session('role') !== 'manager'): ?>
    document.getElementById('store_id').addEventListener('change', function() {
        updateReport();
    });
    <?php endif; ?>

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
        <?php if(session('role') !== 'manager'): ?>
        const storeId = document.getElementById('store_id').value;
        <?php else: ?>
        const storeId = 'all';
        <?php endif; ?>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/reports/vendor_statement.blade.php ENDPATH**/ ?>