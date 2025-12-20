@php
    $layout = session('role') === 'employee' ? 'layouts.app_pos' : 'layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="body-div p-3">
    <div class="body-head">
        <h4>Transfer Details - #{{ $transfer->id }}</h4>
        <div class="d-flex gap-2">
            <!-- <a href="{{ route('inventory.addmoretransfer', $transfer->id) }}">
                <button class="modalbtn"><i class="fas fa-plus pe-2"></i>Add More Items</button>
            </a> -->
            <a href="{{ route('inventory.transferlist') }}">
                <button class="listbtn"><i class="fas fa-arrow-left pe-2"></i>Back to List</button>
            </a>
        </div>
    </div>

    <div class="container-fluid mt-3 listtable">
        <!-- Transfer Summary Info -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box">
                    <label>Transfer Date:</label>
                    <p>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('d-M-Y') }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <label>To Store:</label>
                    <p>{{ $transfer->store->store_name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <label>Total Items:</label>
                    <p><span class="badge bg-primary">{{ $transfer->transfer_item_count }} Items</span></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <label>Remarks:</label>
                    <p>{{ $transfer->remarks ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="filter-container">
            <div class="filter-container-start">
                <select class="headerDropdown form-select filter-option">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder=" Search">
            </div>
        </div>

        <!-- Transfer Items Table -->
        <div class="table-wrapper mt-3">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfer->transferDetails as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->item->item_code ?? 'N/A' }}</td>
                            <td>{{ $detail->item_name }}</td>
                            <td><span class="badge bg-success">{{ $detail->transfer_qty }}</span></td>
                            <td>{{ $detail->unit }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No items found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.info-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.info-box label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    font-size: 0.9em;
}

.info-box p {
    margin: 0;
    color: #212529;
    font-size: 1em;
}

.badge {
    padding: 0.5em 0.8em;
    font-size: 0.9em;
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
            if (headerText != "" && headerText != "#") {
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