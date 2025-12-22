<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('assets/css/dashboard_main.css')); ?>">

<div class="body-div p-3">

    <?php echo $__env->make('dashboard.dashboard_tabs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="body-head mb-3">
        <h4 class="m-0">POS Dashboard</h4>
    </div>

    <div class="container-fluid px-0">
        <div class="row d-flex flex-wrap" id="main_card">
            <?php $__empty_1 = true; $__currentLoopData = $storeData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-sm-6 col-md-4 mb-3 cards">
                <div class="cardsdiv">
                    <div class="cardshead">
                        <div>
                            <h6><?php echo e($data['store']->store_name); ?></h6>
                            <h5>No Of POS - <?php echo e(str_pad($data['pos_count'], 2, '0', STR_PAD_LEFT)); ?></h5>
                        </div>
                        <div>
                            <h6 class="text-end">Total</h6>
                            <h5 class="text-end">₹ <?php echo e(number_format($data['grand_total'], 0)); ?></h5>
                        </div>
                    </div>
                    <div class="cardssub">
                        <div>
                            <h6 class="text-start">Cash</h6>
                            <h5 class="text-start">₹ <?php echo e(number_format($data['cash_total'], 0)); ?></h5>
                        </div>
                        <div class="brdr"></div>
                        <div>
                            <h6 class="text-center">Online</h6>
                            <h5 class="text-center">₹ <?php echo e(number_format($data['online_total'], 0)); ?></h5>
                        </div>
                        <div class="brdr"></div>
                        <div>
                            <h6 class="text-end">Redeemed</h6>
                            <h5 class="text-end">₹ <?php echo e(number_format($data['redeemed_total'], 0)); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h5>No Store Data Available</h5>
                    <p>Please add stores and POS systems to view dashboard data.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="body-head my-2">
        <h4 class="m-0">Top Performance List</h4>
    </div>

    <div class="container-fluid listtable">
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
                        <th>Name</th>
                        <th>Store</th>
                        <th>Total Bills</th>
                        <th>Sales Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $topPerformers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $performer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($performer['employee']->empname); ?></td>
                        <td><?php echo e($performer['store_name']); ?></td>
                        <td><?php echo e($performer['total_bills']); ?></td>
                        <td>₹ <?php echo e(number_format($performer['total_sales'], 0)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center">No performance data available for today</td>
                    </tr>
                    <?php endif; ?>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/dashboard/pos.blade.php ENDPATH**/ ?>