<div class="headtabs d-flex justify-content-start align-items-center flex-wrap gap-2 mb-4">
    <a href="<?php echo e(route('dashboard.admin')); ?>">
        <button class="headbtn <?php echo e(Request::routeIs('dashboard.admin') ? 'active' : ''); ?>">Overview</button>
    </a>
    <a href="<?php echo e(route('dashboard.pos')); ?>">
        <button class="headbtn <?php echo e(Request::routeIs('dashboard.pos') ? 'active' : ''); ?>">POS Dashboard</button>
    </a>
    <a href="<?php echo e(route('dashboard.abc')); ?>">
        <button class="headbtn <?php echo e(Request::routeIs('dashboard.abc') ? 'active' : ''); ?>">ABC Dashboard</button>
    </a>
</div><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/dashboard/dashboard_tabs.blade.php ENDPATH**/ ?>