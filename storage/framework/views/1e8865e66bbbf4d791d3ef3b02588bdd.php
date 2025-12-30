<?php $__env->startSection('content'); ?>

<div class="body-div p-3">
    <div class="body-head">
        <h4>Category List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addCategory">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Category</button>
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
<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUpload" tabindex="-1" aria-labelledby="bulkUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h4>Bulk Upload Categories</h4></div>
            <div class="modal-body">
                <form action="<?php echo e(route('inventory.categorybulkupload')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label>Upload File (.csv or .xlsx)</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                    </div>

                    <div class="mb-3">
                        <a href="<?php echo e(asset('public/sample/category.csv')); ?>" download class="text-primary">
                            <i class="fas fa-download pe-2"></i>Download Sample CSV
                        </a>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
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
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e($category->name); ?></td>
                        <td><?php echo e($category->remarks); ?></td>
                        <td>
                            <?php if($category->status == 'Active'): ?>
                                <span class="text-success">Active</span>
                            <?php else: ?>
                                <span class="text-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?php echo e(route('inventory.categorystatus', $category->id)); ?>" data-bs-toggle="tooltip" data-bs-title="<?php echo e($category->status == 'Active' ? 'Inactive' : 'Active'); ?>">
                                    <?php if($category->status == 'Active'): ?>
                                        <i class="fas fa-circle-check text-success"></i>
                                    <?php else: ?>
                                        <i class="fas fa-circle-xmark text-danger"></i>
                                    <?php endif; ?>
                                </a>
                                <a data-bs-toggle="modal" data-bs-target="#editCategory<?php echo e($category->id); ?>">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editCategory<?php echo e($category->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header"><h4>Update Category</h4></div>
                                <div class="modal-body">
                                    <form action="<?php echo e(route('inventory.categoryupdate', $category->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-2">
                                            <label>Category</label>
                                            <input type="text" name="name" class="form-control" value="<?php echo e($category->name); ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Remarks</label>
                                            <textarea name="remarks" class="form-control" rows="2"><?php echo e($category->remarks); ?></textarea>
                                        </div>
                                        <div class="d-flex gap-2 mt-3">
                                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                            <button type="submit" class="modalbtn w-50">Update Category</button>
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

<!-- Add Modal -->
<div class="modal fade" id="addCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h4>Add Category</h4></div>
            <div class="modal-body">
                <form action="<?php echo e(route('inventory.categorystore')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-2">
                        <label>Category</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                        <button type="submit" class="modalbtn w-50">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DataTables & Filter Script -->
<script>
$(document).ready(function(){
    var table = $('.example').DataTable({
        paging:true, searching:true, ordering:true, bDestroy:true, info:false, responsive:true, pageLength:10,
        dom: '<"top"f>rt<"bottom"lp><"clear">',
    });

    $('.example thead th').each(function(index){
        var headerText = $(this).text();
        if(headerText != "" && headerText.toLowerCase() != "action") {
            $('.headerDropdown').append('<option value="'+index+'">'+headerText+'</option>');
        }
    });
    $('.filterInput').on('keyup', function(){
        var col = $('.headerDropdown').val();
        if(col !== 'All') table.column(col).search($(this).val()).draw();
        else table.search($(this).val()).draw();
    });
    $('.headerDropdown').on('change', function(){
        $('.filterInput').val('');
        table.search('').columns().search('').draw();
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/inventory/category_list.blade.php ENDPATH**/ ?>