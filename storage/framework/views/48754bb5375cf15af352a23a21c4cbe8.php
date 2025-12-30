<div class="user-div">
    <div class="user">
        <img src="<?php echo e(asset('assets/images/avatar_1.png')); ?>" height="30px" class="rounded-5" alt="">
        <h6 class="px-2 m-0 rounded-1 text-capitalize">
            <?php echo e(session('empname', 'Admin')); ?> <br>
            <!-- <span><?php echo e(ucfirst(session('role', 'User'))); ?></span> -->
        </h6>
    </div>
    <div class="maindropdown">
        <div class="dropdowndiv">
            <div class="dropdownimg">
                <img src="<?php echo e(asset('assets/images/avatar_1.png')); ?>" height="60px" alt="">
                <div>
                    <h5><?php echo e(session('empname', 'Guest')); ?></h5>
                    <h6><?php echo e(ucfirst(session('role', 'User'))); ?></h6>
                </div>
            </div>
            <ul class="p-0">
                <li class="mb-3">
                    <a class="d-flex align-items-center gap-3" data-bs-toggle="modal" data-bs-target="#password">
                        <img src="<?php echo e(asset('assets/images/icon_password.png')); ?>" alt="">
                        <span>Change Password</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('logout')); ?>" class="d-flex align-items-center gap-3">
                        <img src="<?php echo e(asset('assets/images/icon_logout.png')); ?>" alt="">
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        $(".user-div").click(function (event) {
            event.stopPropagation();
            $(".maindropdown").fadeIn();
        });
        $(document).click(function () {
            $(".maindropdown").fadeOut();
        });
        $(".maindropdown").click(function (event) {
            event.stopPropagation();
        });
    });
</script>
<?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/layouts/user.blade.php ENDPATH**/ ?>