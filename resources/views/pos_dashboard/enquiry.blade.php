@php
    $layout = session('role') === 'employee' ? 'layouts.app_pos' : 'layouts.app';
@endphp

@extends($layout)


@section('content')
<div class="body-div p-3">
    <div class="body-head">
        <h4>Enquiry List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addEnquiry">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Enquiry</button>
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

        <div class="table-wrapper mt-3">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Enquiry No</th>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Item Name</th>
                        <th>Store</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enquiries as $key => $enquiry)
                        <tr data-id="{{ $enquiry->id }}"
                            data-enquiry_no="{{ $enquiry->enquiry_no }}"
                            data-customer_name="{{ $enquiry->customer_name }}"
                            data-contact_number="{{ $enquiry->contact_number }}"
                            data-item_name="{{ $enquiry->item_name }}"
                            data-store_id="{{ $enquiry->store_id }}">
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $enquiry->enquiry_no }}</td>
                            <td>{{ $enquiry->customer_name }}</td>
                            <td>{{ $enquiry->contact_number }}</td>
                            <td>{{ $enquiry->item_name }}</td>
                            <td>{{ $enquiry->store->store_name ?? '-' }}</td>
                            <td>
                                <a class="edit-btn" data-bs-toggle="modal" data-bs-target="#editEnquiry">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
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
                <form action="{{ route('enquiry.store.data') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
    <label for="store_id">Store</label>
    <select class="form-select" name="store_id" id="store_id" required>
        <option value="" selected disabled>Select Option</option>
        @foreach ($stores as $store)
            <option value="{{ $store->id }}">{{ $store->store_name }}</option>
        @endforeach
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
                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
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
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editstore">Store</label>
                            <select class="form-select" name="store_id" id="editstore" required>
                                <option value="" selected disabled>Select Option</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                @endforeach
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
                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update Enquiry</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    $(document).ready(function () {
        var table = $('.example').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "bDestroy": true,
            "info": false,
            "responsive": true,
            "pageLength": 10,
            "dom": '<"top"f>rt<"bottom"lp><"clear">'
        });

        $('.example thead th').each(function (index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action") {
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

        // Handle edit button click
        $(document).on('click', '.edit-btn', function () {
            let row = $(this).closest('tr');
            let id = row.data('id');

            $('#editEnquiryForm').attr('action', `/enquiry-update-data/${id}`);
            $('#editstore').val(row.data('store_id'));
            $('#editenqno').val(row.data('enquiry_no'));
            $('#editname').val(row.data('customer_name'));
            $('#editcontact').val(row.data('contact_number'));
            $('#edititem').val(row.data('item_name'));
        });
    });
</script>
@endsection
