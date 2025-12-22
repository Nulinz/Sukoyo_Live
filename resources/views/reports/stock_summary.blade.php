@extends('layouts.app')

@section('content')
<div class="body-div p-3">
    <div class="body-head d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-link p-0 me-3">
                <i class="fas fa-arrow-left fa-lg"></i>
            </a>
            <h4 class="mb-0">Stock Summary</h4>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.stock_summary') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="store_id" class="form-label">Store</label>
                        <select class="form-select" name="store_id" id="store_id">
                            <option value="all" {{ $selectedStore == 'all' ? 'selected' : '' }}>All Stores</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                                    {{ $store->store_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="date_range" class="form-label">Date Range</label>
                        <select class="form-select" name="date_range" id="date_range">
                            <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="subcategory_id" class="form-label">Sub Category</label>
                        <select class="form-select" name="subcategory_id" id="subcategory_id">
                            <option value="">All Sub Categories</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="stock_filter" class="form-label">Stock Status</label>
                        <select class="form-select" name="stock_filter" id="stock_filter">
                            <option value="all" {{ $stockFilter == 'all' ? 'selected' : '' }}>All Items</option>
                            <option value="in_stock" {{ $stockFilter == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low_stock" {{ $stockFilter == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ $stockFilter == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Apply</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <!-- <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Items</h6>
                            <h4>{{ number_format($totalItems) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Stock Value</h6>
                            <h4>₹{{ number_format($totalStockValue, 2) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-rupee-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Low Stock</h6>
                            <h4>{{ $lowStockItems }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Out of Stock</h6>
                            <h4>{{ $outOfStockItems }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success" onclick="downloadReport()">
                <i class="fas fa-download me-2"></i>Download
            </button>
            <button type="button" class="btn btn-outline-primary" onclick="printReport()">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>
        <div class="d-flex align-items-center">
            <label class="me-2">Show:</label>
            <select class="form-select form-select-sm" style="width: auto;" onchange="changePageLength(this)">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="-1">All</option>
            </select>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="container-fluid listtable">
        <div class="table-wrapper">
            <table class="example table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Item Code</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Stock Quantity</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $key => $item)
                    <tr class="{{ 
                        $item->status == 'Out of Stock' ? 'table-danger' : 
                        ($item->status == 'Low Stock' ? 'table-warning' : '') 
                    }}">
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <div>
                                <strong>{{ $item->item_name }}</strong>
                                @if($item->brand !== '-')
                                    <br><small class="text-muted">{{ $item->brand }}</small>
                                @endif
                            </div>
                        </td>
                        <td>{{ $item->item_code ?? '-' }}</td>
                        <td>Rs.{{ number_format($item->purchase_price, 2) }}</td>
                        <td>Rs.{{ number_format($item->selling_price, 2) }}</td>
                        <td>
                            <span class="fw-bold">{{ $item->current_stock }}</span> 
                            {{ strtoupper($item->opening_unit) }}
                            @if($item->min_stock > 0)
                                <br><small class="text-muted">Min: {{ $item->min_stock }} {{ strtoupper($item->opening_unit) }}</small>
                            @endif
                        </td>
                        <td class="fw-bold">Rs.{{ number_format($item->stock_value, 2) }}</td>
                        <td>
                            @if($item->status == 'Out of Stock')
                                <span class="badge bg-danger">{{ $item->status }}</span>
                            @elseif($item->status == 'Low Stock')
                                <span class="badge bg-warning text-dark">{{ $item->status }}</span>
                            @else
                                <span class="badge bg-success">{{ $item->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end"><strong>Total:</strong></th>
                        <th><strong>Rs.{{ number_format($totalStockValue, 2) }}</strong></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Initialize DataTable
    var table = $('.example').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,
        "pageLength": 10,
        "order": [[0, "asc"]],
        "dom": '<"top"f>rt<"bottom"lip><"clear">',
        "language": {
            "search": "Search items:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });

    // Category change handler - load subcategories
    $('#category_id').on('change', function() {
        var categoryId = $(this).val();
        var subcategorySelect = $('#subcategory_id');
        
        // Clear subcategory options
        subcategorySelect.html('<option value="">All Sub Categories</option>');
        
        if (categoryId) {
            $.ajax({
                url: '{{ route("reports.get_subcategories_by_category") }}',
                type: 'GET',
                data: { category_id: categoryId },
                success: function(data) {
                    $.each(data, function(index, subcategory) {
                        subcategorySelect.append('<option value="' + subcategory.id + '">' + subcategory.name + '</option>');
                    });
                    
                    // Set selected subcategory if exists
                    @if($selectedSubCategory)
                        subcategorySelect.val('{{ $selectedSubCategory }}');
                    @endif
                },
                error: function() {
                    console.log('Error loading subcategories');
                }
            });
        }
    });

    // Load subcategories on page load if category is selected
    @if($selectedCategory)
        $('#category_id').trigger('change');
    @endif

    // Auto-submit form on filter change
    $('#store_id, #date_range, #category_id, #subcategory_id, #stock_filter').on('change', function() {
        $('#filterForm').submit();
    });
});

function downloadReport() {
    // Add download parameter to current URL
    var form = $('#filterForm');
    var downloadInput = $('<input>').attr('type', 'hidden').attr('name', 'download').val('csv');
    form.append(downloadInput);
    form.submit();
    downloadInput.remove();
}

function printReport() {
    window.print();
}

function changePageLength(select) {
    var table = $('.example').DataTable();
    table.page.len($(select).val()).draw();
}

function printReport() {
    window.print();
}

</script>

<style>
@media print {
    @page {
        size: A3 landscape;   /* Force landscape page */
        margin: 10mm;         /* Adjust margins */
    }
    body * {
        visibility: hidden; /* Hide everything */
    }
    .table-wrapper, 
    .table-wrapper * {
        visibility: visible; /* Show only the table */
    }
    .table-wrapper {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* Hide pagination/info in print */
    .dataTables_paginate,
    .dataTables_filter,
    .dataTables_length,
    .dataTables_info {
        display: none !important;
    }
}

</style>
@endsection