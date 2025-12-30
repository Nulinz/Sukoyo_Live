<?php $__env->startSection('content'); ?>

<div class="body-div p-3">
    <div class="body-head">
        <h4>Purchase Order List</h4>
        <a href="<?php echo e(route('purchase.order_add')); ?>">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Purchase Order</button>
        </a>
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
                        <th>Date</th>
                        <th>PO Number</th>
                        <th>Vendor Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($order->bill_date)->format('d-m-Y')); ?></td>
                            <td><?php echo e($order->bill_no); ?></td>
                            <td><?php echo e($order->vendor ? $order->vendor->vendorname : '-'); ?></td>
                            <td>₹ <?php echo e(number_format($order->total, 2)); ?></td>
                            <td>
                                <span class="status-text text-<?php echo e($order->status == 'Active' ? 'success' : 'danger'); ?>">
                                    <?php echo e($order->status); ?>

                                </span>
                            </td>
<td>
    <div class="d-flex align-items-center gap-2">
        <!-- Toggle Status -->
        <a href="javascript:void(0);"
           class="toggle-order-status"
           data-id="<?php echo e($order->id); ?>"
           data-status="<?php echo e($order->status); ?>"
           data-bs-toggle="tooltip"
           data-bs-title="<?php echo e($order->status == 'Active' ? 'Deactivate' : 'Activate'); ?>">
            <?php if($order->status == 'Active'): ?>
                <i class="fas fa-circle-check text-success"></i>
            <?php else: ?>
                <i class="fas fa-circle-xmark text-danger"></i>
            <?php endif; ?>
        </a>

        <!-- Profile -->
        <a href="<?php echo e(route('purchase.order_profile', $order->id)); ?>" data-bs-toggle="tooltip" data-bs-title="Profile">
            <i class="fas fa-arrow-up-right-from-square"></i>
        </a>

        <!-- Edit -->
        <a href="<?php echo e(route('purchase.order_edit', $order->id)); ?>">
            <i class="fas fa-pen-to-square"></i>
        </a>

        <!-- Delete -->
        <form action="<?php echo e(route('purchase.order_delete', $order->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
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

    // Populate filter dropdown with column names
    $('.example thead th').each(function(index) {
        var headerText = $(this).text();
        if (headerText != "" && headerText.toLowerCase() != "action") {
            $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
        }
    });

    // Filter by column
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


<script>
$(document).ready(function(){
    $('.toggle-order-status').click(function(){
        var btn = $(this);
        var orderId = btn.data('id');

        $.ajax({
            url: "<?php echo e(url('purchase-order-toggle-status')); ?>/" + orderId,
            type: "POST",
            data: {
                _token: "<?php echo e(csrf_token()); ?>"
            },
            success: function(response){
                // Update status text
                let statusText = btn.closest('tr').find('.status-text');
                statusText.text(response.status);
                statusText
                    .removeClass('text-success text-danger')
                    .addClass(response.status == 'Active' ? 'text-success' : 'danger');

                // Update icon
                if(response.status == 'Active') {
                    btn.find('i')
                       .removeClass('fa-circle-xmark text-danger')
                       .addClass('fa-circle-check text-success');
                    btn.attr('data-bs-title', 'Deactivate');
                } else {
                    btn.find('i')
                       .removeClass('fa-circle-check text-success')
                       .addClass('fa-circle-xmark text-danger');
                    btn.attr('data-bs-title', 'Activate');
                }
            },
            error: function(){
                alert('Error updating status!');
            }
        });
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/purchase/order_list.blade.php ENDPATH**/ ?>