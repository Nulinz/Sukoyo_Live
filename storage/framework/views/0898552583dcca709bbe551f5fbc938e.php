<?php $__env->startSection('content'); ?>

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/reports.css')); ?>">

    <div class="body-div p-3">
        <div class="body-head mb-4">
            <h4>Reports</h4>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="body-head py-3">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <img src="<?php echo e(asset('assets/images/icon_box.png')); ?>" height="20px" alt="">
                            <h4>Items</h4>
                        </div>
                    </div>
                    <ul class="list-unstyled">
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.item-sales-purchase-summary')); ?>" class="d-inline-flex">
                                Item Sales and Purchase Summary
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.low_stock')); ?>" class="d-inline-flex">
                                Low Stock Summary
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.stock_summary')); ?>" class="d-inline-flex">
                                Stock Summary
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.item_party')); ?>" class="d-inline-flex">
                                Item Report By Party
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="body-head py-3">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <img src="<?php echo e(asset('assets/images/icon_user.png')); ?>" height="20px" alt="">
                            <h4>Party</h4>
                        </div>
                    </div>
                    <ul class="list-unstyled">
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.sales_summary')); ?>" class="d-inline-flex">
                                Sales Summary
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.vendor_report')); ?>" class="d-inline-flex">
                                Vendor Report
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.vendor_statement')); ?>" class="d-inline-flex">
                                Vendor Statement (Ledger)
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.vendor_outstanding')); ?>" class="d-inline-flex">
                                Vendor Wise Outstanding
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="body-head py-3">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <img src="<?php echo e(asset('assets/images/icon_gst.png')); ?>" height="20px" alt="">
                            <h4>GST</h4>
                        </div>
                    </div>
                    <ul class="list-unstyled">
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.gstr3b-purchase')); ?>" class="d-inline-flex">
                                GSTR-3b
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="" class="d-inline-flex">
                                GSTR-2 (Purchase)
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="body-head py-3">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <img src="<?php echo e(asset('assets/images/icon_card.png')); ?>" height="20px" alt="">
                            <h4>Transaction</h4>
                        </div>
                    </div>
                    <ul class="list-unstyled">
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.profit_loss')); ?>" class="d-inline-flex">
                                Profit And Loss Report
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.purchase_summary')); ?>" class="d-inline-flex">
                                Purchase Summary
                            </a>
                        </li>
                        <li class="p-2">
                            <a href="<?php echo e(route('reports.bill_wise_profit')); ?>" class="d-inline-flex">
                                Bill Wise Profit
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/reports/list.blade.php ENDPATH**/ ?>