<?php $__env->startSection('content'); ?>
<div class="body-div p-3">
    <div class="body-head">
        <h4>Customer List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addCustomer">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Customer</button>
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
                        <th>Customer Name</th>
                        <th>Contact Number</th>
                        <th>Loyalty Points</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($customer->name); ?></td>
                        <td><?php echo e($customer->contact); ?></td>
                        <td><?php echo e($customer->loyalty_points); ?></td>
                        <td><span class="<?php echo e($customer->status == 'Active' ? 'text-success' : 'text-danger'); ?>"><?php echo e($customer->status); ?></span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                               <a href="<?php echo e(route('party.customerstatus', $customer->id)); ?>" data-bs-toggle="tooltip" title="Toggle Status">
    <?php if($customer->status == 'Active'): ?>
        <i class="fas fa-toggle-on text-success"></i>
    <?php else: ?>
        <i class="fas fa-toggle-off text-secondary"></i>
    <?php endif; ?>
</a>

                              <a href="<?php echo e(route('party.customerprofile', $customer->id)); ?>" data-bs-toggle="tooltip" title="Profile">
    <i class="fas fa-arrow-up-right-from-square"></i>
</a>

                                <a href="#" class="editCustomerBtn" data-id="<?php echo e($customer->id); ?>"
                                   data-name="<?php echo e($customer->name); ?>"
                                   data-contact="<?php echo e($customer->contact); ?>"
                                   data-address="<?php echo e($customer->address); ?>"
                                   data-city="<?php echo e($customer->city); ?>"
                                   data-state="<?php echo e($customer->state); ?>"
                                   data-pincode="<?php echo e($customer->pincode); ?>"
                                   data-bs-toggle="modal" data-bs-target="#editCustomer">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomer" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Customer</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo e(route('party.customer.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>City</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>State</label>
                            <input type="text" class="form-control" name="state">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Pincode</label>
                            <input type="text" class="form-control" name="pincode">
                        </div>
                        <div class="d-flex justify-content-between gap-2 mx-auto mt-3">
                            <button type="button" class="cancelbtn w-50" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Customer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomer" tabindex="-1" aria-labelledby="editCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Update Customer</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="editCustomerForm">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="editname" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact" id="editcontact" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address" id="editaddress">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>City</label>
                            <input type="text" class="form-control" name="city" id="editcity">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>State</label>
                            <input type="text" class="form-control" name="state" id="editstate">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Pincode</label>
                            <input type="text" class="form-control" name="pincode" id="editpincode">
                        </div>
                        <div class="d-flex justify-content-between gap-2 mx-auto mt-3">
                            <button type="button" class="cancelbtn w-50" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update Customer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    $(document).ready(function () {
        const table = $('.example').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            bDestroy: true,
            info: false,
            responsive: true,
            pageLength: 10,
            dom: '<"top"f>rt<"bottom"lp><"clear">'
        });

        $('.example thead th').each(function (index) {
            let headerText = $(this).text();
            if (headerText !== "" && headerText.toLowerCase() !== "action") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
            }
        });

        $('.filterInput').on('keyup', function () {
            let selectedColumn = $('.headerDropdown').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search($(this).val()).draw();
            } else {
                table.search($(this).val()).draw();
            }
        });

        $('.headerDropdown').on('change', function () {
            $('.filterInput').val('');
            table.search('').columns().search('').draw();
        });

        // Handle edit modal data
        $('.editCustomerBtn').on('click', function () {
            const id = $(this).data('id');
            $('#editCustomerForm').attr('action', '/customer-update/' + id);
            $('#editname').val($(this).data('name'));
            $('#editcontact').val($(this).data('contact'));
            $('#editaddress').val($(this).data('address'));
            $('#editcity').val($(this).data('city'));
            $('#editstate').val($(this).data('state'));
            $('#editpincode').val($(this).data('pincode'));
        });
    });
</script>

<?php if(session('success')): ?>
    <script>alert('<?php echo e(session('success')); ?>');</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/party/customer_list.blade.php ENDPATH**/ ?>