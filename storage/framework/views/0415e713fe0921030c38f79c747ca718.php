  <div class="cards mb-2">
        <div class="maincard row justify-content-between py-0 mb-3">
            <div class="cardhead my-3">
                <h5>Invoice Details</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Date</h6>
                <h5 class="mb-0"><?php echo e($billDate); ?></h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Invoice No</h6>
                <h5 class="mb-0"><?php echo e($invoice->id); ?></h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Vendor Name</h6>
                <h5 class="mb-0"><?php echo e($invoice->purchaseOrder->vendor->vendorname ?? 'N/A'); ?></h5>
            </div>
            <!-- <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Due In</h6>
                <h5 class="mb-0"><?php echo e($dueIn); ?></h5>
            </div> -->
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Status</h6>
                <h5 class="mb-0"><?php echo e($status); ?></h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Total Amount</h6>
                <h5 class="mb-0">₹ <?php echo e(number_format($invoice->total, 2)); ?></h5>
            </div>
        </div>
    </div>

    
    <div class="body-head mt-3">
        <h4>Item List</h4>
    </div>

    <div class="container-fluid listtable">
        <div class="filter-container">
            <div class="filter-container-start">
                <select class="form-select filter-option" id="headerDropdown1">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control" id="filterInput1" placeholder=" Search">
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table table-bordered" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Discount (%)</th>
                        <th>Tax (%)</th>
                        <th>Amount</th>
                    </tr>
                </thead>
<tbody>
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($key + 1); ?></td>
            <td><?php echo e($item->item_name ?? 'N/A'); ?></td>
            <td><?php echo e($item->unit ?? 'N/A'); ?></td>
            <td><?php echo e($item->qty); ?></td>
            <td>₹ <?php echo e(number_format($item->price, 2)); ?></td>
            <td><?php echo e($item->discount); ?>%</td>
            <td><?php echo e($item->tax); ?>%</td>
            <td>₹ <?php echo e(number_format($item->amount, 2)); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>

            </table>
        </div>
    </div>
</div>
<?php /**PATH /var/www/sukoyo/resources/views/purchase/inv_prof_details.blade.php ENDPATH**/ ?>