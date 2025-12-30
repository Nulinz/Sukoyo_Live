<?php $__env->startSection('content'); ?>
<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Update Item</h4>
    </div>

    <form action="<?php echo e(route('inventory.itemupdate', $item->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('POST'); ?>
        <div class="container-fluid form-div">
            <div class="row">
                <div class="body-head mb-3">
                    <h5>Item Details</h5>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Type <span>*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="item_type" value="Product" <?php echo e($item->item_type == 'Product' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Product</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="item_type" value="Service" <?php echo e($item->item_type == 'Service' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Service</label>
                        </div>
                    </div>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Code <span>*</span></label>
                    <input type="text" class="form-control" name="item_code" value="<?php echo e($item->item_code); ?>" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Name <span>*</span></label>
                    <input type="text" class="form-control" name="item_name" value="<?php echo e($item->item_name); ?>" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Brand <span>*</span></label>
                    <select class="form-select" name="brand_id" required>
                        <option value="" disabled>Select Option</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand->id); ?>" <?php echo e($item->brand_id == $brand->id ? 'selected' : ''); ?>>
                                <?php echo e($brand->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Category <span>*</span></label>
                    <select class="form-select" name="category_id" id="category" required>
                        <option value="" disabled>Select Option</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e($item->category_id == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Sub Category <span>*</span></label>
                    <select class="form-select" name="subcategory_id" id="subcategory" required>
                        <option value="" disabled>Select Option</option>
                        <?php $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($subcategory->category_id == $item->category_id): ?>
                                <option value="<?php echo e($subcategory->id); ?>" <?php echo e($item->subcategory_id == $subcategory->id ? 'selected' : ''); ?>>
                                    <?php echo e($subcategory->name); ?>

                                </option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                
                               <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Discount (%) <span>*</span></label>
                    <input type="number" class="form-control" name="discount" value="<?php echo e($item->discount); ?>" min="0" step="0.01" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Sales Price <span>*</span></label>
                    <input type="number" class="form-control" name="sales_price" value="<?php echo e($item->sales_price); ?>" step="0.01" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Wholesale Price <span>*</span></label>
                    <input type="number" class="form-control" name="wholesale_price" value="<?php echo e($item->wholesale_price); ?>" step="0.01" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                 <label>MRP <span>*</span></label>
                <input type="number" step="0.01" class="form-control" value="<?php echo e($item->mrp); ?>" name="mrp" min="0" required>
                </div>                

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Measuring Unit <span>*</span></label>
                    <select class="form-select" name="measuring_unit" required>
                        <option value="pcs" <?php echo e($item->measure_unit == 'pcs' ? 'selected' : ''); ?>>Pcs</option>
                        <option value="box" <?php echo e($item->measure_unit == 'box' ? 'selected' : ''); ?>>Box</option>
                        <option value="kg" <?php echo e($item->measure_unit == 'kg' ? 'selected' : ''); ?>>Kg</option>
                    </select>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Opening Stock <span>*</span></label>
                    <input type="number" class="form-control" name="opening_stock" value="<?php echo e($item->opening_stock); ?>" min="0" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>GST Tax Rate (%) <span>*</span></label>
                    <select class="form-select" name="gst_rate" required>
                        <option value="0" <?php echo e($item->gst_rate == 0 ? 'selected' : ''); ?>>0%</option>
                        <option value="5" <?php echo e($item->gst_rate == 5 ? 'selected' : ''); ?>>5%</option>
                        <option value="12" <?php echo e($item->gst_rate == 12 ? 'selected' : ''); ?>>12%</option>
                        <option value="18" <?php echo e($item->gst_rate == 18 ? 'selected' : ''); ?>>18%</option>
                        <option value="28" <?php echo e($item->gst_rate == 28 ? 'selected' : ''); ?>>28%</option>
                    </select>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Description</label>
                    <textarea class="form-control" name="description"><?php echo e($item->item_description); ?></textarea>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Stock Status <span>*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="status" value="Active" <?php echo e($item->stock_status == 'Active' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="status" value="Inactive" <?php echo e($item->stock_status == 'Inactive' ? 'checked' : ''); ?>>
                            <label class="form-check-label">Inactive</label>
                        </div>
                    </div>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Minimum Stock <span>*</span></label>
                    <input type="number" class="form-control" name="min_stock" value="<?php echo e($item->min_stock); ?>" min="0" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Maximum Stock <span>*</span></label>
                    <input type="number" class="form-control" name="max_stock" value="<?php echo e($item->max_stock); ?>" min="0" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>ABC Category <span>*</span></label>
                    <select class="form-select" name="abc_category" required>
                        <option value="A" <?php echo e($item->abc_category == 'A' ? 'selected' : ''); ?>>A</option>
                        <option value="B" <?php echo e($item->abc_category == 'B' ? 'selected' : ''); ?>>B</option>
                        <option value="C" <?php echo e($item->abc_category == 'C' ? 'selected' : ''); ?>>C</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="row">
                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Purchase Price <span>*</span></label>
                    <input type="number" class="form-control" name="purchase_price" value="<?php echo e($item->purchase_price); ?>" step="0.01" min="0" required>
                </div>

                
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Purchase GST (%) <span>*</span></label>
                    <select class="form-select" name="purchase_gst" required>
                        <option value="0" <?php echo e($item->purchase_gst == 0 ? 'selected' : ''); ?>>0%</option>
                        <option value="5" <?php echo e($item->purchase_gst == 5 ? 'selected' : ''); ?>>5%</option>
                        <option value="12" <?php echo e($item->purchase_gst == 12 ? 'selected' : ''); ?>>12%</option>
                        <option value="18" <?php echo e($item->purchase_gst == 18 ? 'selected' : ''); ?>>18%</option>
                        <option value="28" <?php echo e($item->purchase_gst == 28 ? 'selected' : ''); ?>>28%</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Update Item</button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/inventory/item_edit.blade.php ENDPATH**/ ?>