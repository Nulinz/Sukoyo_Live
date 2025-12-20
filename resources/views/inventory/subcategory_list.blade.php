@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Sub Category List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addSubCategory">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Sub Category</button>
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
<div class="modal fade" id="bulkUpload" tabindex="-1" aria-labelledby="bulkUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Bulk Upload Sub Categories</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('inventory.subcategorybulkupload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Upload File (.csv or .xlsx)</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                    </div>

                    <div class="mb-3">
                        <a href="{{ asset('public/sample/subcategory.csv') }}" download class="text-primary">
                            <i class="fas fa-download pe-2"></i>Download Sample CSV
                        </a>
                    </div>

                    <div class="d-flex gap-2 mt-3">
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
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subcategories as $key => $subcategory)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $subcategory->category->name ?? '-' }}</td>
                        <td>{{ $subcategory->name }}</td>
                        <td>{{ $subcategory->remarks }}</td>
                        <td>
                            @if($subcategory->status == 'Active')
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('inventory.subcategorytoggle', $subcategory->id) }}" data-bs-toggle="tooltip" data-bs-title="{{ $subcategory->status == 'Active' ? 'Inactive' : 'Active' }}">
                                    @if($subcategory->status == 'Active')
                                        <i class="fas fa-circle-check text-success"></i>
                                    @else
                                        <i class="fas fa-circle-xmark text-danger"></i>
                                    @endif
                                </a>
                                <a data-bs-toggle="modal" data-bs-target="#editSubCategory{{ $subcategory->id }}">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Sub Category Modal -->
                    <div class="modal fade" id="editSubCategory{{ $subcategory->id }}" tabindex="-1" aria-labelledby="editSubCategoryLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="m-0">Update Sub Category</h4>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('inventory.subcategoryupdate', $subcategory->id) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>Category</label>
                                                <select class="form-select" name="category_id" required>
                                                    <option value="" disabled>Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $subcategory->category_id == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>SubCategory</label>
                                                <input type="text" class="form-control" name="name" value="{{ $subcategory->name }}" required>
                                            </div>
                                            <div class="col-sm-12 col-md-12 mb-2">
                                                <label>Remarks</label>
                                                <textarea rows="2" class="form-control" name="remarks">{{ $subcategory->remarks }}</textarea>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                                <button type="submit" class="modalbtn w-50">Update Sub Category</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Sub Category Modal -->
<div class="modal fade" id="addSubCategory" tabindex="-1" aria-labelledby="addSubCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Sub Category</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('inventory.subcategorystore') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Category</label>
                            <select class="form-select" name="category_id" required>
                                <option value="" selected disabled>Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>SubCategory</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label>Remarks</label>
                            <textarea rows="2" class="form-control" name="remarks"></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Sub Category</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DataTables & Filter Script -->
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
            "dom": '<"top"f>rt<"bottom"lp><"clear">',
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
    });
</script>

@endsection
