<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('assets/css/profile.css')); ?>">

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Customer Profile</h4>
    </div>

    <div class="body-div p-3">
        <div class="contentright">
            <div class="body-head my-2">
                <h4><?php echo e($customer->name); ?></h4>
            </div>

            <div class="proftabs">
                <ul class="nav nav-tabs d-flex justify-content-start align-items-center gap-2 gap-lg-3" id="myTab" role="tablist">
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs active" data-bs-toggle="tab" type="button" data-bs-target="#details">
                            <img src="<?php echo e(asset('assets/images/profile_user.png')); ?>" class="pe-1" height="13px" alt=""> Profile
                        </button>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs" data-bs-toggle="tab" type="button" data-bs-target="#sales">
                            <img src="<?php echo e(asset('assets/images/profile_card.png')); ?>" class="pe-1" height="10px" alt=""> Sales Details
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="myTabContent">
                <!-- Customer Details Tab -->
                <div class="tab-pane fade show active" id="details" role="tabpanel">
                    <div class="cards mb-2">
                        <div class="maincard row py-0 mb-3">
                            <div class="cardhead my-3">
                                <h5>General Details</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <h6 class="mb-1">Customer Name</h6>
                                <h5 class="mb-0"><?php echo e($customer->name); ?></h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h6 class="mb-1">Contact Number</h6>
                                <h5 class="mb-0"><?php echo e($customer->contact); ?></h5>
                            </div>
                            <!--<div class="col-md-4 mb-3">-->
                            <!--    <h6 class="mb-1">Email ID</h6>-->
                            <!--    <h5 class="mb-0"><?php echo e($customer->email ?? 'N/A'); ?></h5>-->
                            <!--</div>-->
                            <div class="col-md-4 mb-3">
                                <h6 class="mb-1">Loyalty Points</h6>
                                <h5 class="mb-0"><?php echo e($customer->loyalty_points); ?></h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h6 class="mb-1">Address</h6>
                                <h5 class="mb-0"><?php echo e($customer->address); ?>, <?php echo e($customer->city); ?>, <?php echo e($customer->state); ?> - <?php echo e($customer->pincode); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Details Tab -->
                <div class="tab-pane fade" id="sales" role="tabpanel">
                    <div class="container-fluid listtable pt-0">
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
                                        <th>Date</th>
                                        <th>Invoice No</th>
                                        <th>Payment Type</th>
                                        <th>Amount</th>
                                        <th>Points Added</th>
                                        <th>Points Used</th>
                                        <th>Discount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td><?php echo e($sale->invoice_date->format('d-m-Y')); ?></td>
                                            <td><?php echo e($sale->id); ?></td>
                                            <td><?php echo e($sale->mode_of_payment); ?></td>
                                            <td>₹ <?php echo e(number_format($sale->grand_total, 2)); ?></td>
                                            <td><?php echo e($sale->loyalty_points_earned); ?></td>
                                            <td><?php echo e($sale->loyalty_points_used); ?></td>
                                            <td>₹ <?php echo e(number_format($sale->total_discount, 2)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No sales found for this customer.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#table1').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "order": [0, "asc"],
            "bDestroy": true,
            "info": false,
            "responsive": true,
            "pageLength": 30,
            "dom": '<"top"f>rt<"bottom"ilp><"clear">'
        });

        $('#table1 thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText !== "" && headerText.toLowerCase() !== "action") {
                $('#headerDropdown1').append('<option value="' + index + '">' + headerText + '</option>');
            }
        });

        $('#filterInput1').on('keyup', function () {
            let selectedColumn = $('#headerDropdown1').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search($(this).val()).draw();
            } else {
                table.search($(this).val()).draw();
            }
        });

        $('#headerDropdown1').on('change', function () {
            $('#filterInput1').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/party/customer_profile.blade.php ENDPATH**/ ?>