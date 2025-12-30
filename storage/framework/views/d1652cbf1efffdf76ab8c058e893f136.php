<?php $__env->startSection('content'); ?>
    <div class="body-div p-3">
        <div class="body-head">
            <h4>Enquiry List</h4>
            <a data-bs-toggle="modal" data-bs-target="#addEnquiry">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Enquiry</button>
            </a>
        </div>

        <div class="container-fluid listtable mt-3">
            <div class="filter-container">
                <div class="filter-container-start">
                    <select class="headerDropdown form-select filter-option">
                        <option value="All" selected>All</option>
                    </select>
                    <input type="text" class="form-control filterInput" placeholder=" Search">
                </div>
                <div class="filter-container-end">
                    <button id="exportCsv" class="btn btn-success btn-sm me-2">Export CSV</button>
                    <button id="exportPdf" class="btn btn-danger btn-sm">Export PDF</button>
                </div>
            </div>

            <div class="table-wrapper mt-3">
                <table class="example table-bordered table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Enquiry No</th>
                            <th>Name</th>
                            <th>Contact Number</th>
                            <th>Item Name</th>
                            <th>Store</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $enquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $enquiry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-id="<?php echo e($enquiry->id); ?>" data-enquiry_no="<?php echo e($enquiry->enquiry_no); ?>" data-customer_name="<?php echo e($enquiry->customer_name); ?>"
                                data-contact_number="<?php echo e($enquiry->contact_number); ?>" data-item_name="<?php echo e($enquiry->item_name); ?>" data-store_id="<?php echo e($enquiry->store_id); ?>"
                                data-status="<?php echo e($enquiry->status); ?>">
                                <td><?php echo e($key + 1); ?></td>
                                <td><?php echo e($enquiry->enquiry_no); ?></td>
                                <td><?php echo e($enquiry->customer_name); ?></td>
                                <td><?php echo e($enquiry->contact_number); ?></td>
                                <td><?php echo e($enquiry->item_name); ?></td>
                                <td><?php echo e($enquiry->store->store_name ?? '-'); ?></td>
                                <td><?php echo e($enquiry->status ?? '-'); ?></td>
                                <td>
                                    <a class="edit-btn btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editEnquiry">
                                        Edit
                                    </a>
                                    <button class="status-btn btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusEnquiry" data-id="<?php echo e($enquiry->id); ?>"
                                        data-status="<?php echo e($enquiry->status); ?>">
                                        Update Status
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Add Enquiry Modal -->
    <div class="modal fade" id="addEnquiry" tabindex="-1" aria-labelledby="addEnquiryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Add Enquiry</h4>
                </div>
                <div class="modal-body">
                    <form action="<?php echo e(route('enquiry.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="store_id">Store</label>
                                <select class="form-select" name="store_id" id="store_id" required>
                                    <option value="" selected disabled>Select Option</option>
                                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($store->id); ?>"><?php echo e($store->store_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="enquiry_no">Enquiry No</label>
                                <input type="text" class="form-control" name="enquiry_no" id="enquiry_no" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="customer_name">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" id="customer_name" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="contact_number">Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" id="contact_number" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="item_name">Item Name</label>
                                <input type="text" class="form-control" name="item_name" id="item_name" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mx-auto mt-3 gap-2">
                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                <button type="submit" class="modalbtn w-50">Add Enquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Enquiry Modal -->
    <div class="modal fade" id="editEnquiry" tabindex="-1" aria-labelledby="editEnquiryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Update Enquiry</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editEnquiryForm">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editstore">Store</label>
                                <select class="form-select" name="store_id" id="editstore" required>
                                    <option value="" selected disabled>Select Option</option>
                                    <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($store->id); ?>"><?php echo e($store->store_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editenqno">Enquiry No</label>
                                <input type="text" class="form-control" name="enquiry_no" id="editenqno" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editname">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" id="editname" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editcontact">Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" id="editcontact" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="edititem">Item Name</label>
                                <input type="text" class="form-control" name="item_name" id="edititem" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mx-auto mt-3 gap-2">
                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                <button type="submit" class="modalbtn w-50">Update Enquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="statusEnquiry" tabindex="-1" aria-labelledby="statusEnquiryLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Update Enquiry Status</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" id="statusEnquiryForm">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="status">Status</label>
                            <input type="text" class="form-control" name="status" id="status" required>
                        </div>
                        <div class="d-flex justify-content-between mt-3 gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        var table;

        $(document).ready(function() {
            table = $('.example').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "bDestroy": true,
                "info": false,
                "responsive": true,
                "pageLength": 10,
                "dom": '<"top"f>rt<"bottom"lp><"clear">'
            });

            $('.example thead th').each(function(index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action") {
                    $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
                }
            });

            $('.filterInput').on('keyup', function() {
                var selectedColumn = $('.headerDropdown').val();
                if (selectedColumn !== 'All') {
                    table.column(selectedColumn).search($(this).val()).draw();
                } else {
                    table.search($(this).val()).draw();
                }
            });

            $('.headerDropdown').on('change', function() {
                $('.filterInput').val('');
                table.search('').columns().search('').draw();
            });

            // Handle edit button click
            $(document).on('click', '.edit-btn', function() {
                let row = $(this).closest('tr');
                let id = row.data('id');

                $('#editEnquiryForm').attr('action', `/enquiry-update/${id}`);
                $('#editstore').val(row.data('store_id'));
                $('#editenqno').val(row.data('enquiry_no'));
                $('#editname').val(row.data('customer_name'));
                $('#editcontact').val(row.data('contact_number'));
                $('#edititem').val(row.data('item_name'));
            });
        });

        // Update Status
        $(document).on('click', '.status-btn', function() {
            let id = $(this).data('id');
            let status = $(this).data('status');

            $('#statusEnquiryForm').attr('action', `/enquiry-status/${id}`);
            $('#status').val(status);
        });

        // Export to CSV
        // ----------------- EXPORT CSV -----------------
        // ----------------- EXPORT CSV -----------------
        $("#exportCsv").click(function() {
            var csv = [];
            var headers = [];

            $('.example thead th').each(function(index) {
                if (index !== $('.example thead th').length - 1) {
                    headers.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
                }
            });
            csv.push(headers.join(","));

            table.rows({
                search: 'applied'
            }).every(function() {
                var row = [...this.data()];
                row.pop(); // remove action column

                row = row.map(function(col) {
                    col = col.toString().replace(/"/g, '""'); // escape quotes
                    return `"${col}"`; // wrap in quotes
                });

                csv.push(row.join(","));
            });

            var csvFile = new Blob([csv.join("\n")], {
                type: "text/csv"
            });
            var link = document.createElement("a");
            link.download = "enquiry_list.csv";
            link.href = URL.createObjectURL(csvFile);
            link.click();
        });


        // ----------------- EXPORT PDF -----------------
        $("#exportPdf").click(function() {
            var headers = [];
            var rowsData = [];

            $('.example thead th').each(function(index) {
                if (index !== $('.example thead th').length - 1) {
                    headers.push("<th>" + $(this).text().trim() + "</th>");
                }
            });

            table.rows({
                search: 'applied'
            }).every(function() {
                var row = [...this.data()];
                row.pop();

                rowsData.push("<tr>" + row.map(col => `<td>${col}</td>`).join("") + "</tr>");
            });

            var htmlContent = `
        <html>
        <head>
            <title>Enquiry List</title>
            <style>
                table { width: 100%; border-collapse: collapse; font-size: 13px; }
                th, td { border: 1px solid #000; padding: 6px; }
                h3 { text-align: center; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <h3>Enquiry List</h3>
            <table>
                <thead><tr>${headers.join("")}</tr></thead>
                <tbody>${rowsData.join("")}</tbody>
            </table>
        </body>
        </html>
    `;

            var win = window.open("", "", "height=700,width=900");
            win.document.write(htmlContent);
            win.document.close();
            win.print();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Sukoyo_Live\resources\views/enquiry/list.blade.php ENDPATH**/ ?>