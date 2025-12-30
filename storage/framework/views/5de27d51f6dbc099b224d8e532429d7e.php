<div class="container-fluid mt-1 listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="headerDropdown3">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="filterInput3" placeholder="Search">
        </div>
        <div class="filter-container-end">
            <a data-bs-toggle="modal" data-bs-target="#addBatch">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i> Add Batch</button>
            </a>
        </div>
    </div>
    <br>
    
    <div class="table-wrapper">
        <table class="table table-bordered" id="table3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Batch No</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>MFG Date</th>
                    <th>EXP Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($batch->item_code); ?></td>
                        <td><?php echo e($batch->batch_no); ?></td>
                        <td>₹ <?php echo e(number_format($batch->price, 2)); ?></td>
                        <td><?php echo e($batch->qty); ?></td>
                        <td><?php echo e($batch->mfg_date->format('d-m-Y')); ?></td>
                        <td><?php echo e($batch->exp_date->format('d-m-Y')); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#editBatch"
                                   onclick="editBatch(<?php echo e($batch->id); ?>)">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                                <a href="#" onclick="deleteBatch(<?php echo e($batch->id); ?>)" class="text-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php if($batches->isEmpty()): ?>
                    <tr>
                        <td colspan="8" class="text-center">No Batch Found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Batch Modal -->
<div class="modal fade" id="addBatch" tabindex="-1" aria-labelledby="addBatchLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="<?php echo e(url('/batch')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="item_id" value="<?php echo e($item->id); ?>">
                <div class="modal-header">
                    <h4 class="m-0">Add Batch</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="additemcode">Item Code (Barcode)</label>
                            <input type="text" class="form-control" name="item_code" id="additemcode" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addbatchno">Batch No</label>
                            <input type="text" class="form-control" name="batch_no" id="addbatchno" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addprice">Price</label>
                            <input type="number" class="form-control" name="price" id="addprice" step="0.01" min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addqty">Quantity</label>
                            <input type="number" class="form-control" name="qty" id="addqty" min="1" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addmfgdate">MFG Date</label>
                            <input type="date" class="form-control" name="mfg_date" id="addmfgdate" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addexpdate">EXP Date</label>
                            <input type="date" class="form-control" name="exp_date" id="addexpdate" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Batch</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatch" tabindex="-1" aria-labelledby="editBatchLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editBatchForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" id="editBatchId" name="id">
                <div class="modal-header">
                    <h4 class="m-0">Update Batch</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="edititemcode">Item Code (Barcode)</label>
                            <input type="text" class="form-control" name="item_code" id="edititemcode" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editbatchno">Batch No</label>
                            <input type="text" class="form-control" name="batch_no" id="editbatchno" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editprice">Price</label>
                            <input type="number" class="form-control" name="price" id="editprice" step="0.01" min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editqty_batch">Quantity</label>
                            <input type="number" class="form-control" name="qty" id="editqty_batch" min="1" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editmfgdate">MFG Date</label>
                            <input type="date" class="form-control" name="mfg_date" id="editmfgdate" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editexpdate">EXP Date</label>
                            <input type="date" class="form-control" name="exp_date" id="editexpdate" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update Batch</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editBatch(id) {
    fetch(`/batch/edit/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editBatchId').value = data.id;
            document.getElementById('edititemcode').value = data.item_code;
            document.getElementById('editbatchno').value = data.batch_no;
            document.getElementById('editprice').value = data.price;
            document.getElementById('editqty_batch').value = data.qty;
            document.getElementById('editmfgdate').value = data.mfg_date;
            document.getElementById('editexpdate').value = data.exp_date;
            document.getElementById('editBatchForm').action = `/batch/update/${data.id}`;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading batch data');
        });
}

function deleteBatch(id) {
    if (confirm('Are you sure you want to delete this batch?')) {
        fetch(`/batch/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting batch');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting batch');
        });
    }
}

// Form validation for dates
document.addEventListener('DOMContentLoaded', function() {
    // Add date validation for add form
    const addMfgDate = document.getElementById('addmfgdate');
    const addExpDate = document.getElementById('addexpdate');
    
    addMfgDate.addEventListener('change', function() {
        addExpDate.min = this.value;
    });
    
    addExpDate.addEventListener('change', function() {
        if (this.value < addMfgDate.value) {
            alert('Expiry date cannot be before manufacturing date');
            this.value = '';
        }
    });
    
    // Add date validation for edit form
    const editMfgDate = document.getElementById('editmfgdate');
    const editExpDate = document.getElementById('editexpdate');
    
    editMfgDate.addEventListener('change', function() {
        editExpDate.min = this.value;
    });
    
    editExpDate.addEventListener('change', function() {
        if (this.value < editMfgDate.value) {
            alert('Expiry date cannot be before manufacturing date');
            this.value = '';
        }
    });
});
</script><?php /**PATH /var/www/sukoyo/resources/views/inventory/prof_batch.blade.php ENDPATH**/ ?>