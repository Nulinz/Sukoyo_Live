<?php $__env->startSection('content'); ?>

<div class="body-div p-3">
    <div class="body-head">
        <h4>Sub Category List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addSubCategory">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Sub Category</button>
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
               
                <a data-bs-toggle="modal" data-bs-target="#bulkUpload">
                    <button class="exportbtn"><i class="fas fa-cloud-arrow-up pe-2"></i> Bulk Upload</button>
                </a>
            </div>
        </div>
<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUpload" tabindex="-1" aria-labelledby="bulkUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Bulk Upload Sub Categories</h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('inventory.subcategorybulkupload')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label>Upload File (.csv or .xlsx)</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                    </div>

                    <div class="mb-3">
                        <a href="<?php echo e(asset('public/sample/subcategory.csv')); ?>" download class="text-primary">
                            <i class="fas fa-download pe-2"></i>Download Sample CSV
                        </a>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="cancelbtn w-50" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="modalbtn w-50">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key + 1); ?></td>
                        <td><?php echo e($subcategory->category->name ?? '-'); ?></td>
                        <td><?php echo e($subcategory->name); ?></td>
                        <td><?php echo e($subcategory->remarks); ?></td>
                        <td>
                            <?php if($subcategory->status == 'Active'): ?>
                                <span class="text-success">Active</span>
                            <?php else: ?>
                                <span class="text-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?php echo e(route('inventory.subcategorytoggle', $subcategory->id)); ?>" data-bs-toggle="tooltip" data-bs-title="<?php echo e($subcategory->status == 'Active' ? 'Inactive' : 'Active'); ?>">
                                    <?php if($subcategory->status == 'Active'): ?>
                                        <i class="fas fa-circle-check text-success"></i>
                                    <?php else: ?>
                                        <i class="fas fa-circle-xmark text-danger"></i>
                                    <?php endif; ?>
                                </a>
                                <a data-bs-toggle="modal" data-bs-target="#editSubCategory<?php echo e($subcategory->id); ?>">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Sub Category Modal -->
                    <div class="modal fade" id="editSubCategory<?php echo e($subcategory->id); ?>" tabindex="-1" aria-labelledby="editSubCategoryLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="m-0">Update Sub Category</h4>
                                </div>
                                <div class="modal-body">
                                    <form action="<?php echo e(route('inventory.subcategoryupdate', $subcategory->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>Category</label>
                                                <select class="form-select" name="category_id" required>
                                                    <option value="" disabled>Select Category</option>
                                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($category->id); ?>" <?php echo e($subcategory->category_id == $category->id ? 'selected' : ''); ?>>
                                                            <?php echo e($category->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>SubCategory</label>
                                                <input type="text" class="form-control" name="name" value="<?php echo e($subcategory->name); ?>" required>
                                            </div>
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>Remarks</label>
                                                <textarea rows="2" class="form-control" name="remarks"><?php echo e($subcategory->remarks); ?></textarea>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                                <button type="submit" class="modalbtn w-50">Update Sub Category</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Sub Category Modal -->
<div class="modal fade" id="addSubCategory" tabindex="-1" aria-labelledby="addSubCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Sub Category</h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('inventory.subcategorystore')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Category</label>
                            <select class="form-select" name="category_id" required>
                                <option value="" selected disabled>Select Category</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>SubCategory</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Remarks</label>
                            <textarea rows="2" class="form-control" name="remarks"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Sub Category</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DataTables & Filter Script -->
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
            "dom": '<"top"f>rt<"bottom"lp><"clear">',
        });

        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action") {
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/inventory/subcategory_list.blade.php ENDPATH**/ ?>