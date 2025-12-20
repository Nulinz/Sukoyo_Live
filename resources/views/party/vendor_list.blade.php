@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Vendor List</h4>
        <a href="{{ route('party.vendoradd') }}">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Vendor</button>
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
              <!-- Bulk upload button to open modal -->
<button type="button" class="exportbtn" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
    <i class="fas fa-cloud-arrow-up pe-2"></i>Bulk Upload
</button>

<!-- Bulk upload modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('party.vendorbulkupload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Vendors (CSV)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="file" name="csv_file" class="form-control" required accept=".csv">

          <div class="my-2">
            <a href="{{ asset('public/sample/vendors.csv') }}" download class="text-primary">
              <i class="fas fa-download pe-2"></i>Download Sample CSV
            </a>
          </div>

          <small class="text-muted">CSV columns: vendorname, contact, email, openbalance, tax, topay, tocollect, gst, panno, creditperiod, creditlimit, billaddress, shipaddress</small>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vendor Name</th>
                        <th>Contact Number</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $index => $vendor)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $vendor->vendorname }}</td>
                        <td>{{ $vendor->contact }}</td>
                        <td>₹ {{ number_format($vendor->openbalance, 2) }}</td>
                      <td>
    @if($vendor->status == 'Active')
        <span class="text-success">Active</span>
    @else
        <span class="text-danger">Inactive</span>
    @endif
</td>
<td>
    <div class="d-flex align-items-center gap-2">
        @if($vendor->status == 'Active')
            <a href="{{ route('party.vendor_toggle_status', $vendor->id) }}" data-bs-toggle="tooltip" data-bs-title="Deactivate">
                <i class="fas fa-circle-check text-success"></i>
            </a>
        @else
            <a href="{{ route('party.vendor_toggle_status', $vendor->id) }}" data-bs-toggle="tooltip" data-bs-title="Activate">
                <i class="fas fa-circle-xmark text-danger"></i>
            </a>
        @endif
       <a href="{{ route('party.vendorprofile', ['id' => $vendor->id]) }}" data-bs-toggle="tooltip" data-bs-title="Profile">
    <i class="fas fa-arrow-up-right-from-square"></i>
</a>

       <a href="{{ route('party.vendoredit', $vendor->id) }}">
    <i class="fas fa-pen-to-square"></i>
</a>

    </div>
</td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No vendors found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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

        // Add header filter options
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
            }
        });

        // Filter logic
        $('.filterInput').on('keyup', function() {
            var selectedColumn = $('.headerDropdown').val();
            if (selectedColumn !== 'All') {
                table.column(selectedColumn).search(this.value).draw();
            } else {
                table.search(this.value).draw();
            }
        });

        $('.headerDropdown').on('change', function() {
            $('.filterInput').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>

@endsection
