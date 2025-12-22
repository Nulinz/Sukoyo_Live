<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 4px solid #28a745 !important; border-radius: 8px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
        <div class="d-flex align-items-start">
            <div class="flex-shrink-0 me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1" style="color: #155724; font-weight: 600;">Success!</h6>
                <p class="mb-0" style="color: #155724; font-size: 14px;"><?php echo e(session('success')); ?></p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>


<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-left: 4px solid #dc3545 !important; border-radius: 8px; background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
        <div class="d-flex align-items-start">
            <div class="flex-shrink-0 me-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <div class="flex-grow-1">
                
                <ul class="mb-0" style="list-style: none; padding-left: 0;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="mb-1" style="color: #721c24; font-size: 14px; padding-left: 20px; position: relative;">
                            <span style="position: absolute; left: 0; top: 0;">•</span>
                            <?php echo e($error); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss success alerts after 5 seconds
        const successAlerts = document.querySelectorAll('.alert-success');
        successAlerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // Smooth scroll to alert if it exists
        const alerts = document.querySelectorAll('.alert');
        if (alerts.length > 0) {
            alerts[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>


<style>
    .alert {
        animation: slideInDown 0.4s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .alert-dismissible .btn-close {
        padding: 0.75rem 1rem;
        opacity: 0.6;
        transition: opacity 0.3s ease;
    }
    
    .alert-dismissible .btn-close:hover {
        opacity: 1;
    }
    
    .alert ul li::before {
        content: "→";
        position: absolute;
        left: 0;
        color: inherit;
        font-weight: bold;
    }
</style>
<div class="body-div p-3">
    <div class="body-head">
        <h4>Repacking List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addRepacking">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Repacking</button>
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
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Pack Qty</th>
                        <th>Cost Price</th>
                        <th>Repacking Charge</th>
                        <th>Selling Price</th>
                        <th>Store</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $repackings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $repack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($repack->item->item_name ?? $repack->item_name ?? '-'); ?></td>
                        <td><?php echo e($repack->total_bulk_qty); ?> <?php echo e($repack->bulk_unit); ?></td>
                        <td><?php echo e($repack->repack_qty); ?> <?php echo e($repack->repack_uom); ?></td>
                        <td>₹ <?php echo e($repack->cost_per_pack); ?></td>
                        <td>₹ <?php echo e($repack->repacking_charge ?? 0); ?></td>
                        <td>₹ <?php echo e($repack->selling_price); ?></td>
                        <td><?php echo e($repack->store->store_name ?? ''); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="#" class="pop-up" data-bs-toggle="modal" data-bs-target="#editRepacking" id="<?php echo e($repack->id); ?>">
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


<!-- Edit Repacking Modal - Replace the existing modal in your blade file -->
<div class="modal fade" id="editRepacking" tabindex="-1" aria-labelledby="editRepackingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Edit Repacking</h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('inventory.repackingupdate')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <input type="hidden" name="form_repack_id" id="form_repack_id">
                     <!--Item Details Section (Single Item for Edit) -->
                    <div class="mb-4 p-3" style="background-color: #f8f9fa; border-radius: 0.375rem; border: 2px dashed #6c757d;">
                        <h5 class="mb-3">Item to Repack</h5>
                        
                        <div class="p-3" style="background-color: white; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label>Item Name <span class="text-danger">*</span></label>
                                    <select class="form-select edit-item-select-<?php echo e($repack->id); ?>" name="item_id" required>
                                        <option value="<?php echo e($repack->item_id); ?>" selected><?php echo e($repack->item->item_name ?? $repack->item_name); ?></option>
                                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($item->id != $repack->item_id): ?>
                                            <option value="<?php echo e($item->id); ?>"><?php echo e($item->item_name); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Total Bulk Qty <span class="text-danger">*</span></label>
                                    <div class="inpselectflex">
                                        <input type="number" step="any" class="form-control border-0" name="total_bulk_qty" value="<?php echo e($repack->total_bulk_qty); ?>" required>
                                        <select class="form-select border-0" name="bulk_unit">
                                            <option value="pcs" <?php echo e($repack->bulk_unit == 'pcs' ? 'selected' : ''); ?>>pcs</option>
                                            <option value="box" <?php echo e($repack->bulk_unit == 'box' ? 'selected' : ''); ?>>box</option>
                                            <option value="nos" <?php echo e($repack->bulk_unit == 'nos' ? 'selected' : ''); ?>>nos</option>
                                            <option value="kg" <?php echo e($repack->bulk_unit == 'kg' ? 'selected' : ''); ?>>kg</option>
                                            <option value="ltr" <?php echo e($repack->bulk_unit == 'ltr' ? 'selected' : ''); ?>>ltr</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Repacking Charge <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="repacking_charge" value="<?php echo e($repack->repacking_charge ?? 0); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <!--Repacking Type Section -->
                     <div class="mb-4 p-3" style="background-color: #fff3cd; border-radius: 0.375rem; border: 2px solid #ffc107;">
                        <h5 class="mb-3">Repacking Type</h5>
                        <div class="alert alert-info mb-0" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Note:</strong> For editing, the item type is determined by the original repacking configuration and cannot be changed.
                        </div>
                    </div>
                    
                     <!--Common Fields Section -->
                    <div class="p-3" style="background-color: #e9ecef; border-radius: 0.375rem;">
                        <h5 class="mb-3">Repacked Item Details</h5>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Variant Name <span class="text-danger">*</span></label>
                                <select class="form-select edit-variant-select-<?php echo e($repack->id); ?>" name="variant_name" required>
                                    <option value="<?php echo e($repack->variant_name); ?>" selected><?php echo e($repack->variant_name); ?></option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Repack UOM <span class="text-danger">*</span></label>
                                <select class="form-select" name="repack_uom" required>
                                    <option value="kg" <?php echo e($repack->repack_uom == 'kg' ? 'selected' : ''); ?>>kg</option>
                                    <option value="g" <?php echo e($repack->repack_uom == 'g' ? 'selected' : ''); ?>>g</option>
                                    <option value="ltr" <?php echo e($repack->repack_uom == 'ltr' ? 'selected' : ''); ?>>ltr</option>
                                    <option value="litre" <?php echo e($repack->repack_uom == 'litre' ? 'selected' : ''); ?>>litre</option>
                                    <option value="ml" <?php echo e($repack->repack_uom == 'ml' ? 'selected' : ''); ?>>ml</option>
                                    <option value="pcs" <?php echo e($repack->repack_uom == 'pcs' ? 'selected' : ''); ?>>pcs</option>
                                    <option value="pack" <?php echo e($repack->repack_uom == 'pack' ? 'selected' : ''); ?>>pack</option>
                                    <option value="box" <?php echo e($repack->repack_uom == 'box' ? 'selected' : ''); ?>>box</option>
                                    <option value="dozen" <?php echo e($repack->repack_uom == 'dozen' ? 'selected' : ''); ?>>dozen</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Repack Qty <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control" name="repack_qty" value="<?php echo e($repack->repack_qty); ?>" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Cost Per Pack <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="cost_per_pack" value="<?php echo e($repack->cost_per_pack); ?>" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Selling Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="selling_price" value="<?php echo e($repack->selling_price); ?>" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Store <span class="text-danger">*</span></label>
                                <select class="form-select" name="store_id" required>
                                    <option value="Warehouse" <?php echo e($repack->store_id == 'Warehouse' ? 'selected' : ''); ?>>Warehouse</option>
                                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($store->id); ?>" <?php echo e($repack->store_id == $store->id ? 'selected' : ''); ?>>
                                            <?php echo e($store->store_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                        <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                        <button type="submit" class="modalbtn w-50">Update Repacking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Repacking Modal with Multiple Items -->
<div class="modal fade" id="addRepacking" tabindex="-1" aria-labelledby="addRepackingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Repacking</h4>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('inventory.repackingstore')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                     <!--Items Section -->
                    <div class="mb-4 p-3" style="background-color: #f8f9fa; border-radius: 0.375rem; border: 2px dashed #6c757d;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Items to Repack</h5>
                            <button type="button" class="btn btn-success btn-sm" onclick="addItemRow()">
                                <i class="fas fa-plus me-1"></i>Add Item
                            </button>
                        </div>
                        
                        <div id="itemsContainer">
                             <!--Initial item row -->
                            <div class="item-row mb-3 p-3" style="background-color: white; border: 1px solid #dee2e6; border-radius: 0.375rem;" data-item-index="0">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label>Item Name <span class="text-danger">*</span></label>
                                        <select class="form-select searchable-select" name="items[0][item_id]" required>
                                            <option value="" disabled selected>Select Item</option>
                                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->item_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label>Total Bulk Qty <span class="text-danger">*</span></label>
                                        <div class="inpselectflex">
                                            <input type="number" step="any" class="form-control border-0" name="items[0][total_bulk_qty]" required>
                                            <select class="form-select border-0" name="items[0][bulk_unit]">
                                                <option value="pcs">pcs</option>
                                                <option value="box">box</option>
                                                <option value="nos">nos</option>
                                                <option value="kg">kg</option>
                                                <option value="ltr">ltr</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label>Packing Charge <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="items[0][repacking_charge]" required>
                                    </div>
                                    <div class="col-md-1 mb-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-item-btn" onclick="removeItemRow(0)" style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
<!-- Item Type Selection Section -->
<div class="mb-4 p-3" style="background-color: #fff3cd; border-radius: 0.375rem; border: 2px solid #ffc107;">
    <h5 class="mb-3">Repacking Type</h5>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="item_type" id="newItemRadio" value="new" checked required>
                <label class="form-check-label" for="newItemRadio">
                    <strong>Create New Item</strong>
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="item_type" id="existingItemRadio" value="existing" required>
                <label class="form-check-label" for="existingItemRadio">
                    <strong>Add to Existing Item</strong>
                </label>
            </div>
        </div>
        
         <!--Info message for existing item mode -->
        <div class="col-12" id="existingItemInfo" style="display: none;">
            <div class="alert alert-info mb-0" role="alert">
                <i class="fas fa-info-circle"></i> 
                <strong>Note:</strong> Select the item from "Items to Repack" section above. The repacked quantity will be added to that item's stock.
            </div>
        </div>
    </div>
</div>
                    
<!-- Common Fields Section -->
<div class="p-3" style="background-color: #e9ecef; border-radius: 0.375rem;" id="commonFieldsSection">
    <h5 class="mb-3">Convert To (Combined Repacked Item)</h5>
    <div class="row">
         <!--Fields for New Item Only -->
        <div class="col-sm-12 col-md-6 mb-2 new-item-field" id="variantNameContainer">
            <label>Variant Name <span class="text-danger">*</span></label>
            <select class="form-select variant-select searchable-variant" name="variant_name">
                <option value="" selected disabled>Select Variant Name</option>
            </select>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 new-item-field">
            <label>Repack UOM <span class="text-danger">*</span></label>
            <select class="form-select" name="repack_uom">
                <option value="kg">kg</option>
                <option value="g">g</option>
                <option value="ltr">ltr</option>
                <option value="litre">litre</option>
                <option value="ml">ml</option>
                <option value="pcs">pcs</option>
                <option value="pack">pack</option>
                <option value="box">box</option>
                <option value="dozen">dozen</option>
            </select>
        </div>
        
         <!--Repack Qty - Always visible -->
        <div class="col-sm-12 col-md-6 mb-2">
            <label>Repack Qty <span class="text-danger">*</span></label>
            <input type="number" step="any" class="form-control" name="repack_qty" required>
        </div>
        
         <!--Fields for New Item Only -->
        <div class="col-sm-12 col-md-6 mb-2 new-item-field">
            <label>Cost Per Pack <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="cost_per_pack">
        </div>
        <div class="col-sm-12 col-md-6 mb-2 new-item-field">
            <label>Selling Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="selling_price">
        </div>
        <div class="col-sm-12 col-md-6 mb-2 new-item-field">
            <label>Store <span class="text-danger">*</span></label>
            <select class="form-select" name="store_id">
                <option value="" selected disabled>Select Store</option>
                <option value="Warehouse">Warehouse</option>
                <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($store->id); ?>"><?php echo e($store->store_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>
</div>
                    
                    <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                        <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                        <button type="submit" class="modalbtn w-50">Add Repacking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

<script>
     $('.pop-up').on('click', function() {
        var repack_id = $(this).attr('id');
        
        $('#form_repack_id').val(repack_id);
        // console.log(repack_id);
    });
</script>
<script>
    let itemRowIndex = 1;
    let variantNames = [];
    
    // Load variant names when page loads
    $(document).ready(function() {
        loadVariantNames();
        
        // Initialize searchable dropdowns
        initializeSearchableDropdowns();
        
        // Initialize first variant select
        setTimeout(() => {
            populateVariantSelect($('.variant-select'));
        }, 500);
        
        // Handle item type radio button changes
        $('input[name="item_type"]').on('change', function() {
            if ($(this).val() === 'existing') {
                // Show info message
                $('#existingItemInfo').slideDown();
                
                // Hide all new-item-only fields
                $('.new-item-field').slideUp();
                
                // Hide "Add Item" button - only one item allowed for existing
                $('#itemsContainer').closest('.mb-4').find('button[onclick="addItemRow()"]').hide();
                
                // Remove required attribute from hidden fields
                $('.new-item-field input, .new-item-field select').prop('required', false);
                
                // Add hidden input to pass the selected item as existing_item_id if not exists
                if (!$('input[name="existing_item_id"]').length) {
                    $('#addRepacking form').append('<input type="hidden" name="existing_item_id" id="hiddenExistingItemId">');
                }
                
                // Get the currently selected item and set it as existing_item_id
                const currentItemId = $('[name="items[0][item_id]"]').val();
                if (currentItemId) {
                    $('#hiddenExistingItemId').val(currentItemId);
                }
                
            } else {
                // Hide info message
                $('#existingItemInfo').slideUp();
                
                // Show all new-item-only fields
                $('.new-item-field').slideDown();
                
                // Show "Add Item" button for new items
                $('#itemsContainer').closest('.mb-4').find('button[onclick="addItemRow()"]').show();
                
                // Add required attribute back to visible fields
                $('.new-item-field input[name="cost_per_pack"]').prop('required', true);
                $('.new-item-field input[name="selling_price"]').prop('required', true);
                $('.new-item-field select[name="variant_name"]').prop('required', true);
                $('.new-item-field select[name="repack_uom"]').prop('required', true);
                $('.new-item-field select[name="store_id"]').prop('required', true);
                
                // Remove hidden existing_item_id input
                $('#hiddenExistingItemId').remove();
            }
        });
        
        // Update existing_item_id whenever the item selection changes
        $(document).on('change', '[name="items[0][item_id]"]', function() {
            if ($('#existingItemRadio').is(':checked')) {
                const selectedItemId = $(this).val();
                $('#hiddenExistingItemId').val(selectedItemId);
            }
        });
        
        // Before form submission, ensure existing_item_id is set
        $('#addRepacking form').on('submit', function(e) {
            if ($('#existingItemRadio').is(':checked')) {
                const itemId = $('[name="items[0][item_id]"]').val();
                if (itemId) {
                    $('#hiddenExistingItemId').val(itemId);
                } else {
                    e.preventDefault();
                    alert('Please select an item from "Items to Repack" section.');
                    return false;
                }
            }
        });
    });
    
    function initializeSearchableDropdowns() {
        // Initialize Select2 for all searchable selects
        $('.searchable-select').select2({
            placeholder: 'Search and select an item...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.modal')
        });
        
        // Initialize Select2 for variant dropdowns
        $('.searchable-variant').select2({
            placeholder: 'Search and select variant...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.modal')
        });
        
        // Initialize for edit modals
        $('.edit-item-select').select2({
            placeholder: 'Search and select an item...',
            allowClear: false,
            width: '100%'
        });
    }
    
    function loadVariantNames() {
        const items = <?php echo json_encode($items, 15, 512) ?>;
        variantNames = [...new Set(items.map(item => item.item_name))];
    }
    
    function populateVariantSelect(selectElement) {
        selectElement.empty();
        selectElement.append('<option value="" selected disabled>Select Variant Name</option>');
        
        variantNames.forEach(name => {
            selectElement.append(`<option value="${name}">${name}</option>`);
        });
    }
    
    function addItemRow() {
        const container = document.getElementById('itemsContainer');
        const itemsData = <?php echo json_encode($items, 15, 512) ?>;
        
        let optionsHtml = '<option value="" disabled selected>Select Item</option>';
        itemsData.forEach(item => {
            optionsHtml += `<option value="${item.id}">${item.item_name}</option>`;
        });
        
        const newRow = `
            <div class="item-row mb-3 p-3" style="background-color: white; border: 1px solid #dee2e6; border-radius: 0.375rem;" data-item-index="${itemRowIndex}">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label>Item Name <span class="text-danger">*</span></label>
                        <select class="form-select searchable-select" name="items[${itemRowIndex}][item_id]" required>
                            ${optionsHtml}
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label>Total Bulk Qty <span class="text-danger">*</span></label>
                        <div class="inpselectflex">
                            <input type="number" step="any" class="form-control border-0" name="items[${itemRowIndex}][total_bulk_qty]" required>
                            <select class="form-select border-0" name="items[${itemRowIndex}][bulk_unit]">
                                <option value="pcs">pcs</option>
                                <option value="box">box</option>
                                <option value="nos">nos</option>
                                <option value="kg">kg</option>
                                <option value="ltr">ltr</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Repacking Charge <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="items[${itemRowIndex}][repacking_charge]" required>
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-item-btn" onclick="removeItemRow(${itemRowIndex})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', newRow);
        
        // Initialize Select2 for the new dropdown
        $(`[name="items[${itemRowIndex}][item_id]"]`).select2({
            placeholder: 'Search and select an item...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.modal')
        });
        
        itemRowIndex++;
        updateRemoveButtons();
    }
    
    function removeItemRow(index) {
        const row = document.querySelector(`[data-item-index="${index}"]`);
        if (row) {
            // Destroy Select2 instance before removing
            const select = row.querySelector('.searchable-select');
            if (select && $(select).hasClass("select2-hidden-accessible")) {
                $(select).select2('destroy');
            }
            row.remove();
            updateRemoveButtons();
        }
    }
    
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-item-btn');
            if (removeBtn && rows.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else if (removeBtn) {
                removeBtn.style.display = 'none';
            }
        });
    }
    
    // Handle modal show events to reinitialize Select2
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.searchable-select, .edit-item-select, .searchable-variant').each(function() {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                let placeholder = 'Search and select...';
                if ($(this).hasClass('searchable-variant')) {
                    placeholder = 'Search and select variant...';
                } else {
                    placeholder = 'Search and select an item...';
                }
                
                $(this).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(this).closest('.modal')
                });
            }
        });
    });
    
    // Handle modal hide events to clean up Select2
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('.select2-container').remove();
        // Reset form
        $(this).find('form')[0].reset();
        $('#existingItemInfo').hide();
        $('.new-item-field').show();
        $('#newItemRadio').prop('checked', true);
        
        // Show "Add Item" button again
        $('#itemsContainer').closest('.mb-4').find('button[onclick="addItemRow()"]').show();
        
        // Remove hidden input
        $('#hiddenExistingItemId').remove();
        
        // Restore required attributes
        $('.new-item-field input, .new-item-field select').prop('required', true);
    });

    $(document).ready(function() {
        // Initialize Select2 for edit modals when they are shown
        $('[id^="editRepacking"]').on('shown.bs.modal', function () {
            const modalId = $(this).attr('id').replace('editRepacking', '');
            
            // Initialize item select
            $(`.edit-item-select-${modalId}`).select2({
                placeholder: 'Search and select an item...',
                allowClear: false,
                width: '100%',
                dropdownParent: $(this)
            });
            
            // Initialize and populate variant select
            const variantSelect = $(`.edit-variant-select-${modalId}`);
            if (!variantSelect.hasClass("select2-hidden-accessible")) {
                // Get all unique item names for variants
                const items = <?php echo json_encode($items, 15, 512) ?>;
                const variantNames = [...new Set(items.map(item => item.item_name))];
                
                // Get current value
                const currentValue = variantSelect.find('option:selected').val();
                
                // Clear and repopulate
                variantSelect.empty();
                variantSelect.append(`<option value="${currentValue}" selected>${currentValue}</option>`);
                
                variantNames.forEach(name => {
                    if (name !== currentValue) {
                        variantSelect.append(`<option value="${name}">${name}</option>`);
                    }
                });
                
                // Initialize Select2
                variantSelect.select2({
                    placeholder: 'Search and select variant...',
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $(this)
                });
            }
        });
        
        // Clean up Select2 on modal hide
        $('[id^="editRepacking"]').on('hidden.bs.modal', function () {
            $(this).find('.select2-container').remove();
        });
    });
</script>

<script>
$(document).ready(function () {
    // Initialize DataTable with pagination, search, and sorting
    $('.example').DataTable({
        pageLength: 10,               // number of rows per page
        lengthMenu: [5, 10, 25, 50], // dropdown for rows per page
        ordering: true,               // enable column sorting
        searching: true,              // enable search box
        responsive: true,             // make table responsive
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search Repacking...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ repackings",
            infoEmpty: "No records found",
            zeroRecords: "No matching records found",
        }
    });
});
</script>


<style>
    .inpselectflex {
        display: flex;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        overflow: hidden;
    }
    .inpselectflex input {
        flex: 1;
    }
    .inpselectflex select {
        width: auto;
        min-width: 100px;
        border-left: 1px solid #ced4da;
    }
    .item-row {
        transition: all 0.3s ease;
    }
    .item-row:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Select2 Custom Styles */
    .select2-container--default .select2-selection--single {
        height: 38px;
        line-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 12px;
        padding-right: 20px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }
    
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }
    
    /* Make Select2 work well in modals */
    .modal .select2-container {
        z-index: 1056;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/inventory/repacking_list.blade.php ENDPATH**/ ?>