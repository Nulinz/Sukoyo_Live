@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>POS System List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addPOS">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add POS System</button>
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
                        <th>System No</th>
                        <th>Store</th>
                        <th>System Type</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($posSystems as $index => $pos)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $pos->system_no }}</td>
                        <td>{{ $pos->store->store_name ?? 'N/A' }}</td>
                        <td>{{ $pos->system_type }}</td>
                        <td>{{ $pos->remarks }}</td>
                        <td>
                            @if($pos->status)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('settings.pos.toggle', $pos->id) }}" data-bs-toggle="tooltip" data-bs-title="{{ $pos->status ? 'Inactive' : 'Active' }}">
                                    @if($pos->status)
                                        <i class="fas fa-circle-check text-success"></i>
                                    @else
                                        <i class="fas fa-circle-xmark text-danger"></i>
                                    @endif
                                </a>
                                <a data-bs-toggle="modal" data-bs-target="#editPOS"
                                   data-id="{{ $pos->id }}"
                                   data-system_no="{{ $pos->system_no }}"
                                   data-system_type="{{ $pos->system_type }}"
                                   data-remarks="{{ $pos->remarks }}"
                                   data-store_id="{{ $pos->store_id }}">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add POS Modal -->
<div class="modal fade" id="addPOS" tabindex="-1" aria-labelledby="addPOSLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add POS System</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('settings.pos.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addposno">POS System No</label>
                            <input type="text" class="form-control" name="system_no" id="addposno" required>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addstore">Store</label>
                            <select class="form-select" name="store_id" id="addstore" required>
                                <option value="">Select Store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-2">
                            <label>System Type</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="checkbox" name="system_type[]" value="Retail" id="add-retail">
                                    <label class="form-check-label mb-0" for="add-retail">Retail</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="checkbox" name="system_type[]" value="Wholesale" id="add-wholesale">
                                    <label class="form-check-label mb-0" for="add-wholesale">Wholesale</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addremarks">Remarks</label>
                            <textarea rows="2" class="form-control" name="remarks" id="addremarks"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add POS</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit POS Modal -->
<div class="modal fade" id="editPOS" tabindex="-1" aria-labelledby="editPOSLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Update POS System</h4>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="editPosForm">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editposno">POS System No</label>
                            <input type="text" class="form-control" name="system_no" id="editposno" required>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editstore">Store</label>
                            <select class="form-select" name="store_id" id="editstore" required>
                                <option value="">Select Store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-6 mb-2">
                            <label>System Type</label>
                            <div class="d-flex align-items-center gap-3 mt-1">
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="checkbox" name="system_type[]" value="Retail" id="edit-retail">
                                    <label class="form-check-label mb-0" for="edit-retail">Retail</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="checkbox" name="system_type[]" value="Wholesale" id="edit-wholesale">
                                    <label class="form-check-label mb-0" for="edit-wholesale">Wholesale</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="editremarks">Remarks</label>
                            <textarea rows="2" class="form-control" name="remarks" id="editremarks"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update POS</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DataTables & Modal JS -->
<script>
$(document).ready(function() {
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

    // Fill Edit Modal data
    $('#editPOS').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var system_no = button.data('system_no');
        var system_type = button.data('system_type').split(',');
        var remarks = button.data('remarks');
        var store_id = button.data('store_id');

        var modal = $(this);
        modal.find('#editposno').val(system_no);
        modal.find('#editremarks').val(remarks);
        modal.find('#editstore').val(store_id);

        modal.find('input[type=checkbox]').prop('checked', false);
        system_type.forEach(function(type){
            modal.find('input[value="' + type.trim() + '"]').prop('checked', true);
        });

        modal.find('form#editPosForm').attr('action', '/pos-system/update/' + id);
    });
});
</script>

@endsection
