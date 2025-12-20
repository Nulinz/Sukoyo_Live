<?php $__env->startSection('content'); ?>
<div class="body-div p-3">
  <div class="body-head d-flex justify-content-between">
    <h4>Tutor List</h4>
    <a href="<?php echo e(route('class.tutoradd')); ?>">
      <button class="listbtn"><i class="fas fa-plus"></i> Add Tutor</button>
    </a>
  </div>
  <?php if(session('status')): ?>
      <div class="alert alert-info"><?php echo e(session('status')); ?></div>
  <?php endif; ?>

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
            <th>#</th><th>Name</th><th>Expertise</th>
            <th>Internal/External</th><th>Contact</th>
            <th>Status</th><th>Action</th>
          </tr>
        </thead>
<tbody>
  <?php $__empty_1 = true; $__currentLoopData = $tutors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
      <td><?php echo e($i+1); ?></td>
      <td><?php echo e($t->name); ?></td>
      <td><?php echo e($t->expertise); ?></td>
      <td><?php echo e($t->internal_external); ?></td>
      <td><?php echo e($t->contact); ?></td>
      <td>
        <?php if($t->status === 'Active'): ?>
          <span class="text-success">Active</span>
        <?php else: ?>
          <span class="text-danger">Inactive</span>
        <?php endif; ?>
      </td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <form action="<?php echo e(route('class.tutortoggle', $t->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="border-0 bg-transparent" data-bs-toggle="tooltip"
                    title="<?php echo e($t->status === 'Active' ? 'Set Inactive' : 'Set Active'); ?>">
              <?php if($t->status === 'Active'): ?>
                <i class="fas fa-circle-check text-success"></i>
              <?php else: ?>
                <i class="fas fa-circle-xmark text-danger"></i>
              <?php endif; ?>
            </button>
          </form>
          <a href="<?php echo e(route('class.tutorprofile', $t->id)); ?>" data-bs-toggle="tooltip" title="Profile">
            <i class="fas fa-arrow-up-right-from-square"></i>
          </a>
          <a href="<?php echo e(route('class.tutoredit', $t->id)); ?>" data-bs-toggle="tooltip" title="Edit">
            <i class="fas fa-pen-to-square"></i>
          </a>
        </div>
      </td>
    </tr>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
      <td colspan="7" class="text-center">No data available in table</td>
    </tr>
  <?php endif; ?>
</tbody>

      </table>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function () {
  var table = $('.example').DataTable({
    paging: true, searching: true, ordering: true,
    bDestroy: true, info: false, responsive: true,
    pageLength: 10,
    dom: '<"top"f>rt<"bottom"lp><"clear">'
  });

  $('.example thead th').each(function (i) {
    if (!['Action',''].includes($(this).text().trim())) {
      $('.headerDropdown').append(`<option value="${i}">${$(this).text()}</option>`);
    }
  });

  $('.filterInput').on('keyup', function() {
    var col = $('.headerDropdown').val();
    col !== 'All' ? table.column(col).search(this.value).draw() : table.search(this.value).draw();
  });

  $('.headerDropdown').on('change', function(){
    table.search('').columns().search('').draw();
    $('.filterInput').val('');
  });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/classes/tutor/list.blade.php ENDPATH**/ ?>