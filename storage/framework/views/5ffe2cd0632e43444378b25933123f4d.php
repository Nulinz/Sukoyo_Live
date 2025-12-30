<?php $__env->startSection('content'); ?>

    <div class="body-div p-3">
        <div class="body-head">
            <h4>Class List</h4>
            <a href="<?php echo e(route('class.classadd')); ?>">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Class</button>
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
                            <th>Class Name</th>
                            <th>Class Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Tutor Assigned</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($class->class_name); ?></td>
                            <td><?php echo e($class->class_type); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($class->date)->format('d-m-Y')); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($class->time)->format('h:i A')); ?></td>
                            <td>
                                <?php if($class->tutor): ?>
                                    <?php echo e($class->tutor->name); ?> 
                                    <?php if($class->tutor->internal_external): ?>
                                        (<?php echo e(ucfirst($class->tutor->internal_external)); ?>)
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">No Tutor Assigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $bookedCount = $class->bookings->count();
                                    $max = $class->max_participants;
                                ?>

                                <?php if($bookedCount >= $max): ?>
                                    <span class="text-danger">Booked</span>
                                <?php else: ?>
                                    <span class="text-success">Available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="<?php echo e(route('class.classprofile', $class->id)); ?>" data-bs-toggle="tooltip" data-bs-title="Profile">
                                        <i class="fas fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <a href="<?php echo e(route('class.classedit', $class->id)); ?>">
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

    <script>
        // DataTables List
        $(document).ready(function () {
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
        $(document).ready(function () {
            var table = $('.example').DataTable();
            $('.example thead th').each(function (index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "progress") {
                    $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
                }
            });
            $('.filterInput').on('keyup', function () {
                var selectedColumn = $('.headerDropdown').val();
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
        });
    </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/classes/class/list.blade.php ENDPATH**/ ?>