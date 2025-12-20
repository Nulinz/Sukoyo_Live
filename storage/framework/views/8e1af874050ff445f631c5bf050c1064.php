<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('assets/css/profile.css')); ?>">

<style>
    @media screen and (min-width: 990px) {
        .col-xl-3 {
            width: 16%;
        }
    }
</style>

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Purchase Invoice Profile</h4>
        <div>
            <a href="<?php echo e(route('accounts.payment')); ?>">
                <button class="listbtn">Payment Out</button>
            </a>
        </div>
    </div>

    <div class="mainbdy d-block">

        <!-- Right Content -->
        <div class="contentright">
            <div class="tab-content">
                <?php echo $__env->make('purchase.inv_prof_details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>

    </div>
</div>

<!-- Pay Out Modal -->
<div class="modal fade" id="payout" tabindex="-1" aria-labelledby="payoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Payment Out</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="payamt">Payment Amount</label>
                            <input type="number" class="form-control" name="" id="payamt" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="paydate">Payment Date</label>
                            <input type="date" class="form-control" name="" id="paydate" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="paytype">Payment Type</label>
                            <select class="form-select" name="" id="paytype" required>
                                <option value="" selected disabled>Select Option</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="remark">Remarks</label>
                            <textarea rows="1" class="form-control" name="" id="remark" required></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Payment Out</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        function initTable(tableId, dropdownId, filterInputId) {
            var table = $(tableId).DataTable({
                "paging": false,
                "searching": true,
                "ordering": true,
                "order": [0, "asc"],
                "bDestroy": true,
                "info": false,
                "responsive": true,
                "pageLength": 30,
                "dom": '<"top"f>rt<"bottom"ilp><"clear">',
            });
            $(tableId + ' thead th').each(function(index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action") {
                    $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                }
            });
            $(filterInputId).on('keyup', function() {
                var selectedColumn = $(dropdownId).val();
                if (selectedColumn !== 'All') {
                    table.column(selectedColumn).search($(this).val()).draw();
                } else {
                    table.search($(this).val()).draw();
                }
            });
            $(dropdownId).on('change', function() {
                $(filterInputId).val('');
                table.search('').columns().search('').draw();
            });
            $(filterInputId).on('keyup', function() {
                table.search($(this).val()).draw();
            });
        }
        // Initialize each table
        initTable('#table1', '#headerDropdown1', '#filterInput1');
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/purchase/inv_profile.blade.php ENDPATH**/ ?>