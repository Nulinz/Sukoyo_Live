<?php $__env->startSection('content'); ?>

<div class="body-div p-3">
    <div class="body-head">
        <h4>Vendor List</h4>
        <a href="<?php echo e(route('party.vendoradd')); ?>">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Vendor</button>
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
            <div class="filter-container-end">
              <!-- Bulk upload button to open modal -->
<button type="button" class="exportbtn" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
    <i class="fas fa-cloud-arrow-up pe-2"></i>Bulk Upload
</button>

<!-- Bulk upload modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?php echo e(route('party.vendorbulkupload')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Vendors (CSV)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="file" name="csv_file" class="form-control" required accept=".csv">

          <div class="my-2">
            <a href="<?php echo e(asset('public/sample/vendors.csv')); ?>" download class="text-primary">
              <i class="fas fa-download pe-2"></i>Download Sample CSV
            </a>
          </div>

          <small class="text-muted">CSV columns: vendorname, contact, email, openbalance, tax, topay, tocollect, gst, panno, creditperiod, creditlimit, billaddress, shipaddress</small>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vendor Name</th>
                        <th>Contact Number</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($vendor->vendorname); ?></td>
                        <td><?php echo e($vendor->contact); ?></td>
                        <td>₹ <?php echo e(number_format($vendor->openbalance, 2)); ?></td>
                      <td>
    <?php if($vendor->status == 'Active'): ?>
        <span class="text-success">Active</span>
    <?php else: ?>
        <span class="text-danger">Inactive</span>
    <?php endif; ?>
</td>
<td>
    <div class="d-flex align-items-center gap-2">
        <?php if($vendor->status == 'Active'): ?>
            <a href="<?php echo e(route('party.vendor_toggle_status', $vendor->id)); ?>" data-bs-toggle="tooltip" data-bs-title="Deactivate">
                <i class="fas fa-circle-check text-success"></i>
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('party.vendor_toggle_status', $vendor->id)); ?>" data-bs-toggle="tooltip" data-bs-title="Activate">
                <i class="fas fa-circle-xmark text-danger"></i>
            </a>
        <?php endif; ?>
       <a href="<?php echo e(route('party.vendorprofile', ['id' => $vendor->id])); ?>" data-bs-toggle="tooltip" data-bs-title="Profile">
    <i class="fas fa-arrow-up-right-from-square"></i>
</a>

       <a href="<?php echo e(route('party.vendoredit', $vendor->id)); ?>">
    <i class="fas fa-pen-to-square"></i>
</a>

    </div>
</td>

                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center">No vendors found.</td>
                    </tr>
                    <?php endif; ?>
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

        // Add header filter options
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
            }
        });

        // Filter logic
        $('.filterInput').on('keyup', function() {
            var selectedColumn = $('.headerDropdown').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search(this.value).draw();
            } else {
                table.search(this.value).draw();
            }
        });

        $('.headerDropdown').on('change', function() {
            $('.filterInput').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/party/vendor_list.blade.php ENDPATH**/ ?>