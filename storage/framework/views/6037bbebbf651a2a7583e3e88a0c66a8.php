<?php $__env->startSection('content'); ?>

<div class="body-div p-3" id="print-area">
    <div class="body-head">
        <h4>Low Stock Summary</h4>
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
                    <option value="all" <?php echo e($selectedStore == 'all' ? 'selected' : ''); ?>>All Stores</option>
                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($store->id); ?>" <?php echo e($selectedStore == $store->id ? 'selected' : ''); ?>>
                            <?php echo e($store->store_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <a href="javascript:void(0)" class="btn btn-outline-success btn-sm no-print" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export to Excel
                    </a>
                    <button class="btn btn-outline-secondary btn-sm no-print" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5 no-print" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered" id="lowStockTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Stock Quantity</th>
                        <th>Low Stock Level</th>
                        <th>Stock Value</th>
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
            <p class="text-muted">No low stock items found for the selected criteria.</p>
        </div>
    </div>
</div>

<script>
    // Initialize on page load
    $(document).ready(function() {
        loadLowStockData();
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

    function loadLowStockData() {
        const storeId = document.getElementById('store_id').value;
        const dateRange = document.getElementById('date_range').value;

        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('lowStockTable').style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'none';

        const params = new URLSearchParams();
        if (storeId !== 'all') {
            params.append('store_id', storeId);
        }
        params.append('date_range', dateRange);

        fetch(`<?php echo e(route('api.low_stock_items')); ?>?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                populateTable(data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('noDataMessage').style.display = 'block';
            });
    }

    function populateTable(items) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        document.getElementById('loadingSpinner').style.display = 'none';

        if (!items || items.length === 0) {
            document.getElementById('noDataMessage').style.display = 'block';
            document.getElementById('lowStockTable').style.display = 'none';
            return;
        }

        document.getElementById('lowStockTable').style.display = 'table';
        document.getElementById('noDataMessage').style.display = 'none';

        items.forEach((item, idx) => {
            const row = document.createElement('tr');
            const statusClass = item.status === 'Out of Stock' ? 'bg-danger' : 'bg-warning';

            row.innerHTML = `
                <td>${(idx + 1).toString().padStart(2, '0')}</td>
                <td>${item.item_code ?? '-'}</td>
                <td>${item.item_name}</td>
                <td class="text-center">${item.current_stock} PCS</td>
                <td class="text-center">${item.min_stock} PCS</td>
                <td class="text-center">Rs.${formatNumber(item.stock_value)}</td>
                <td class="text-center">
                    <span class="badge ${statusClass} text-white">
                        ${item.status}
                    </span>
                </td>
            `;
            tbody.appendChild(row);
        });

        initializeDataTable();
    }

    document.getElementById('store_id').addEventListener('change', function() {
        loadLowStockData();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        loadLowStockData();
    });

// Export table to Excel (CSV format - safe for Excel)
function exportToExcel() {
    var table = document.getElementById("lowStockTable");
    var rows = table.querySelectorAll("tr");
    let csv = [];

    for (var i = 0; i < rows.length; i++) {
        let cols = rows[i].querySelectorAll("td, th");
        let row = [];
        for (var j = 0; j < cols.length; j++) {
            // Escape quotes
            let text = cols[j].innerText.replace(/"/g, '""');
            row.push('"' + text + '"');
        }
        csv.push(row.join(","));
    }

    // Download CSV
    var csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = "low_stock_report.csv";
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
    .table {
        font-size: 12px;
    }
}
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/reports/low_stock.blade.php ENDPATH**/ ?>