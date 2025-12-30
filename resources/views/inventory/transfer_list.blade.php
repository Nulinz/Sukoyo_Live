@php
    $layout = session('role') === 'employee' ? 'layouts.app_pos' : 'layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="body-div p-3">
    <div class="body-head">
        <h4>Item Transfer History</h4>
        <a href="{{ route('inventory.transferitems') }}">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>New Transfer</button>
        </a>
    </div>

    <div class="container-fluid mt-3 listtable">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                        <th>Transfer Date</th>
                        <th>To Store</th>
                        <th>Total Items</th>
                        <th>Remarks</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $index => $transfer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d-M-Y') }}</td>
                            <td>{{ $transfer->store->store_name ?? 'N/A' }}</td>
                            <td><span class="badge bg-primary">{{ $transfer->transfer_item_count }} Items</span></td>
                            <td>{{ $transfer->remarks ?? '-' }}</td>
                            <td>{{ $transfer->created_at->format('d-M-Y H:i') }}</td>
                            <td>
                                <a href="{{ route('inventory.transferprofile', $transfer->id) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No transfer records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Custom Styles for Checkbox Positioning -->
<style>
    /* Adjust checkbox position in dropdown */
    .select2-results__option input[type="checkbox"],
    .dropdown-item input[type="checkbox"],
    .multiselect-option input[type="checkbox"] {
        margin-left: 8px;
        margin-right: 8px;
    }
    
    /* Alternative: If using a specific class structure */
    .select2-results__options .select2-results__option::before {
        margin-left: 8px;
    }
    
    /* For bootstrap-select or similar plugins */
    .dropdown-menu .form-check-input {
        margin-left: 8px;
    }
</style>

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
            "order": [[0, "asc"]],
            "dom": '<"top"f>rt<"bottom"lp><"clear">'
        });

        $('.example thead th').each(function (index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "date created") {
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
    });
</script>
@endsection