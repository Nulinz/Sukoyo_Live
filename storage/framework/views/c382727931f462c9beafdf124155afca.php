<?php $__env->startSection('content'); ?>

    <div class="body-div p-3">
        <div class="body-head d-flex justify-content-between align-items-center">
            <h4>Zero Movement List</h4>
            <!-- Export Button -->
            <button type="button" class="btn btn-success" id="exportExcel">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
        </div>

        <div class="container-fluid mt-3 listtable">
            <div class="filter-container">
                <div class="filter-container-start">
                    <select class="headerDropdown form-select filter-option">
                        <option value="All" selected>All</option>
                    </select>
                    <input type="text" class="form-control filterInput" placeholder=" Search">
                </div>
                <div class="filter-container-end d-flex align-items-center flex-wrap gap-2">
                    <form action="<?php echo e(route('stock.zeromovement.page')); ?>" method="GET" id="filterForm">
                        <div class="filter-container d-flex justify-content-start flex-wrap gap-2">
                            <select class="form-select" name="days" id="days" style="width:120px;">
                                <option value="10" <?php echo e($days == 10 ? 'selected' : ''); ?>>Last 10 Days</option>
                                <option value="20" <?php echo e($days == 20 ? 'selected' : ''); ?>>Last 20 Days</option>
                                <option value="30" <?php echo e($days == 30 ? 'selected' : ''); ?>>Last 30 Days</option>
                            </select>

                            <select class="form-select" name="category" id="category" style="width:120px;">
                                <option value="">All Categories</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>" <?php echo e($categoryId == $cat->id ? 'selected' : ''); ?>>
                                        <?php echo e($cat->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <select class="form-select" name="subcategory" id="subcategory" style="width:120px;">
                                <option value="">All Subcategories</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <br>

            <div class="table-wrapper">
                <table id="zeroMovementTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Brand</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- 1️⃣ jQuery (ONLY ONE!) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 2️⃣ DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- 3️⃣ DataTables Bootstrap (if using Bootstrap) -->
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>

        $('#category').change(function () {
            let categoryId = $(this).val();
            $('#subcategory').html('<option value="">All Subcategories</option>');

            if (!categoryId) return;

            $.get(`/get-subcategories/${categoryId}`, function (data) {
                data.forEach(sub => {
                    $('#subcategory').append(`<option value="${sub.id}">${sub.name}</option>`);
                });
            });
        });



        $('#days, #subcategory').change(function () {
            $('#filterForm').submit();
        });

        // Retain subcategory after reload
        <?php if($categoryId && $subcategoryId): ?>
            $(document).ready(function () {
                $.get(`/get-subcategories/<?php echo e($categoryId); ?>`, function (data) {
                    let options = '<option value="">All Subcategories</option>';
                    data.forEach(function (subcat) {
                        const selected = subcat.id == <?php echo e($subcategoryId); ?> ? 'selected' : '';
                        options += `<option value="${subcat.id}" ${selected}>${subcat.name}</option>`;
                    });
                    $('#subcategory').html(options);
                });
            });
        <?php endif; ?>
    </script>
    <script>
        $(document).ready(function () {

            $('#zeroMovementTable').DataTable({
                processing: true,
                paging: true,
                searching: true,
                ordering: true,
                info: false,
                responsive: true,

                ajax: {
                    url: "<?php echo e(route('stock.zeromovement.list')); ?>",
                    dataSrc: 'data'
                },

                columns: [
                    // 1️⃣ Row index
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },

                    // 2️⃣ Brand
                    { data: 'brand' },

                    // 3️⃣ Item Code
                    { data: 'item_code' },

                    // 4️⃣ Item Name
                    { data: 'item_name' },

                    // 5️⃣ Category
                    { data: 'category' },

                    // 6️⃣ Sub Category
                    { data: 'subcategory' },

                    // 7️⃣ Current Stock
                    { data: 'current_stock' },

                    // 8️⃣ Status (computed)
                    {
                        data: 'current_stock',
                        render: function (data) {
                            return Number(data) === 0
                                ? '<span class="badge bg-danger">No Movement</span>'
                                : '<span class="badge bg-warning text-dark">Slow Moving</span>';
                        }
                    }
                ]
            });

        });
    </script>

    <!-- Add SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/stock/zero_list.blade.php ENDPATH**/ ?>