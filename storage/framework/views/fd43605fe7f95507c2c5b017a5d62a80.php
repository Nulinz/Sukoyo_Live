<?php $__env->startSection('content'); ?>

<div class="body-div p-3">
    <div class="body-head">
        <h4>Item List</h4>
        <a href="<?php echo e(route('inventory.itemadd')); ?>">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Item</button>
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
                <a data-bs-toggle="modal" data-bs-target="#barcode">
                    <button class="exportbtn"><i class="fas fa-barcode pe-2"></i> Barcode Print</button>
                </a>
              <a data-bs-toggle="modal" data-bs-target="#itemBulkUpload">
    <button class="exportbtn"><i class="fas fa-cloud-arrow-up pe-2"></i> Bulk Upload</button>
</a>

            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Stock Qty</th>
                        <th>Selling Price</th>
                        <th>Purchase Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
              <tbody>
<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td><?php echo e($key + 1); ?></td>
        <td><?php echo e($item->item_code); ?></td>
        <td><?php echo e($item->item_name); ?></td>
        <td><?php echo e($item->current_stock); ?> <?php echo e(strtoupper($item->opening_unit)); ?></td>
        <td>₹ <?php echo e(number_format($item->sales_price, 2)); ?></td>
        <td>₹ <?php echo e(number_format($item->purchase_price, 2)); ?></td>
        <td>
            <?php if($item->stock_status == 'Active'): ?>
                <span class="text-success">Active</span>
            <?php else: ?>
                <span class="text-danger">Inactive</span>
            <?php endif; ?>
        </td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <?php if($item->stock_status == 'Active'): ?>
                    <a href="<?php echo e(route('inventory.item_toggle_status', $item->id)); ?>" data-bs-toggle="tooltip" data-bs-title="Inactive">
                        <i class="fas fa-circle-check text-success"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('inventory.item_toggle_status', $item->id)); ?>" data-bs-toggle="tooltip" data-bs-title="Active">
                        <i class="fas fa-circle-xmark text-danger"></i>
                    </a>
                <?php endif; ?>
                 <a href="<?php echo e(route('inventory.itemprofile', ['id' => $item->id])); ?>" data-bs-toggle="tooltip" data-bs-title="Profile">
    <i class="fas fa-arrow-up-right-from-square"></i>
</a>

               <a href="<?php echo e(route('inventory.itemedit', $item->id)); ?>" data-bs-toggle="tooltip" title="Edit">
    <i class="fas fa-pen"></i>
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
<!-- Item Bulk Upload Modal -->
<div class="modal fade" id="itemBulkUpload" tabindex="-1" aria-labelledby="itemBulkUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?php echo e(route('inventory.item_bulk_upload')); ?>" method="POST" enctype="multipart/form-data" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="itemBulkUploadLabel">Bulk Upload Items</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="file" class="form-label">Upload Excel/CSV File</label>
                    <input type="file" class="form-control" name="file" accept=".csv,.xlsx,.xls" required>
                </div>

                <div class="mb-3">
                    <a href="<?php echo e(asset('sample/items.csv')); ?>" download class="text-primary">
                        <i class="fas fa-download pe-2"></i>Download Sample CSV
                    </a>
                </div>

                <div class="text-muted">
                    <small>Make sure the file includes: item_code, item_name, brand, category, subcategory, price, stock etc.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="modalbtn w-100">Upload</button>
            </div>
        </form>
    </div>
</div>


<!-- Barcode Modal -->
<!-- Barcode Modal -->
<div class="modal fade" id="barcode" tabindex="-1" aria-labelledby="barcodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Barcode Print</h4>
            </div>
            <div class="modal-body">
<form id="barcodeForm" action="<?php echo e(route('barcode.generate')); ?>" method="POST" target="_blank">
    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-sm-12 col-md-4 mb-2">
                            <label for="itemcode">Item Code</label>
                            <select class="form-select" name="item_id" id="item_id" required>
                                <option value="" selected disabled>Select Option</option>
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"><?php echo e($item->item_code); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2">
                            <label>Item Name</label>
                            <input type="text" class="form-control" name="item_name" id="item_name" readonly>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2">
                            <label>MRP</label>
                            <input type="number" class="form-control" name="mrp" id="mrp" min="0" step="0.01" required>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2">
                            <label>Net Price</label>
                            <input type="number" class="form-control" name="net_price" id="net_price" min="0" step="0.01" required>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2">
                            <label>No Of Barcode</label>
                            <input type="number" class="form-control" name="barcode_count" id="barcode_count" min="1" required>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="modalbtn w-50">Print</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#item_id').on('change', function () {
            let itemId = $(this).val();
            if (itemId) {
                $.ajax({
                    url: '/get-item-details1/' + itemId,
                    type: 'GET',
                    success: function (data) {
                        $('#item_name').val(data.item_name);
                        $('#mrp').val(data.sales_price);
                        $('#net_price').val(data.purchase_price);
                    }
                });
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>


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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/inventory/item_list.blade.php ENDPATH**/ ?>