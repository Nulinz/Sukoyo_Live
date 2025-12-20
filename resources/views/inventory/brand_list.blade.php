@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Brand List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addBrand">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Brand</button>
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
            <div class="filter-container-end">
               
                <a data-bs-toggle="modal" data-bs-target="#bulkUpload">
                    <button class="exportbtn"><i class="fas fa-cloud-arrow-up pe-2"></i> Bulk Upload</button>
                </a>
            </div>
        </div>
<!-- Bulk Upload Modal -->
<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUpload" tabindex="-1" aria-labelledby="bulkUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Bulk Upload Brands</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('inventory.brandbulkupload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload File (.csv or .xlsx)</label>
                        <input type="file" class="form-control" name="file" accept=".csv,.xlsx,.xls" required>
                    </div>

                    <div class="mb-3">
                        <a href="{{ asset('public/sample/brand.csv') }}" download class="text-primary">
                            <i class="fas fa-download pe-2"></i>Download Sample CSV
                        </a>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                        <button type="button" class="cancelbtn w-50" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="modalbtn w-50">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Brand Name</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $index => $brand)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->remarks }}</td>
                        <td>
                            @if($brand->status == 'Active')
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('inventory.brandtoggle', $brand->id) }}" data-bs-toggle="tooltip"
                                   data-bs-title="{{ $brand->status == 'Active' ? 'Inactive' : 'Active' }}">
                                    @if($brand->status == 'Active')
                                        <i class="fas fa-circle-check text-success"></i>
                                    @else
                                        <i class="fas fa-circle-xmark text-danger"></i>
                                    @endif
                                </a>
                                <a data-bs-toggle="modal" data-bs-target="#editBrand{{ $brand->id }}">
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

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrand" tabindex="-1" aria-labelledby="addBrandLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Brand</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('inventory.brandstore') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Brand Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Remarks</label>
                            <textarea rows="2" class="form-control" name="remarks" required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Brand</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Brand Modals -->
@foreach($brands as $brand)
<div class="modal fade" id="editBrand{{ $brand->id }}" tabindex="-1" aria-labelledby="editBrandLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Update Brand</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('inventory.brandupdate', $brand->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Brand Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $brand->name }}" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Remarks</label>
                            <textarea rows="2" class="form-control" name="remarks" required>{{ $brand->remarks }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update Brand</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    // DataTables List
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

        // List Filter
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText !== "" && headerText.toLowerCase() !== "action" && headerText.toLowerCase() !== "progress") {
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
    });
</script>

@endsection
