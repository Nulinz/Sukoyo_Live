@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/dashboard_main.css') }}">

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4 class="m-0">Store Profile</h4>
    </div>

    <!-- Store Information Cards -->
    <div class="container-fluid px-0">
        <div class="row g-3 mb-4" id="main_card">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="card-subtitle mb-2 text-muted small">Store Name</h6>
                        <h5 class="card-title mb-0 fw-semibold">{{ $store->store_name }}</h5>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="card-subtitle mb-2 text-muted small">Store ID</h6>
                        <h5 class="card-title mb-0 fw-semibold">{{ $store->store_id }}</h5>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="card-subtitle mb-2 text-muted small">Total Product</h6>
                        <h5 class="card-title mb-0 fw-semibold">{{ $totalProductCount }}</h5>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="card-subtitle mb-2 text-muted small">Total Value</h6>
                        <h5 class="card-title mb-0 fw-semibold">₹{{ number_format($totalValue, 2) }}</h5>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3">
                        <h6 class="card-subtitle mb-2 text-muted small">Location</h6>
                        <h5 class="card-title mb-0 fw-semibold">{{ $store->city }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                 <div class="filter-container">
            <div class="filter-container-start">
                <select class="headerDropdown form-select filter-option">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder=" Search">
            </div>
        </div><br>

                <div class="table-responsive">
                    <table class="example table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Unit</th>
                                <th>Price (per unit)</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                                    <td>{{ $item->item->brand->name ?? 'N/A' }}</td>
                                    <td>{{ $item->qty }} {{ $item->unit }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>₹{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            @if($items->isEmpty())
                                <tr><td colspan="6" class="text-center">No Products Found</td></tr>
                            @endif
                        </tbody>
                    </table>
                    <br><br>
                </div>
            </div>
        </div>
    </div>
    <br>
</div>
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
            "dom": '<"top"f>rt<"bottom"lp><"clear">',
        });

    });

    // List Filter
    $(document).ready(function() {
        var table = $('.example').DataTable();
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "progress") {
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
