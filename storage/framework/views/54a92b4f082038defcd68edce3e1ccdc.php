<?php $__env->startSection('content'); ?>
<div class="body-div p-3">
    <div class="body-head">
        <h4>Sales List</h4>
    </div>

    <div class="container-fluid mt-3 listtable">
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
                        <th>Bill No</th>
                        <th>Store</th>
                        <th>POS System</th>
                        <th>Bill Type</th>
                        <th>Invoice No</th>
                        <th>Customer Name</th>
                        <th>No Of Items</th>
                        <th>Date</th>
                        <th>Payment Type</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td>BILL-<?php echo e($sale->id); ?></td>
                            <td><?php echo e($sale->store_name); ?></td>
                            <td><?php echo e($sale->employee->empname ?? 'N/A'); ?></td>
                            <td><?php echo e(ucfirst($sale->status)); ?></td>
                            <td><?php echo e('INV' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)); ?></td>
                            <td><?php echo e($sale->customer->name ?? 'N/A'); ?></td>
                            <td><?php echo e($sale->items->count()); ?></td>
                            <td><?php echo e($sale->invoice_date->format('d-m-Y')); ?></td>
                            <td><?php echo e($sale->mode_of_payment); ?></td>
                            <td>₹ <?php echo e(number_format($sale->grand_total, 2)); ?></td>
                            <td>
                                <a href="<?php echo e(route('sales.profile', ['id' => $sale->id])); ?>">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('.example').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "bDestroy": true,
            "info": false,
            "responsive": true,
            "pageLength": 10,
            "dom": '<"top"f>rt<"bottom"lp><"clear">'
        });

        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText !== "" && headerText.toLowerCase() !== "action") {
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/sales/list.blade.php ENDPATH**/ ?>