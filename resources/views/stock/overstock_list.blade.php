@extends('layouts.app')

@section('content')
<div class="body-div p-3">
    <div class="body-head d-flex justify-content-between align-items-center">
        <h4>Overstocked List</h4>
        <!-- Export Button -->
        <button type="button" class="btn btn-success" id="exportExcel">
            <i class="fas fa-file-excel"></i> Export to Excel
        </button>
    </div>

    <div class="container-fluid mt-3 listtable">
        <form method="GET" id="filterForm">
            <div class="filter-container">
                <div class="filter-container-start d-flex align-items-center gap-2">
                    <select class="headerDropdown form-select filter-option">
                        <option value="All" selected>All</option>
                    </select>
                    <input type="text" class="form-control filterInput" placeholder="Search">
                </div>
                <div class="filter-container-end d-flex align-items-center flex-wrap gap-2">
                    <select class="form-select" name="category" id="category">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <select class="form-select" name="subcategory" id="subcategory">
                        <option value="">Select Subcategory</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="table-wrapper mt-3">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Brand</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Current Stock</th>
                        <th>Max Stock Limit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->brand->name ?? '-' }}</td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            <td>{{ $item->subcategory->name ?? '-' }}</td>
                            <td>{{ $item->current_stock }}</td>
                            <td>{{ $item->max_stock }}</td>
                            <td><span class="badge bg-danger">Overstocked</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No overstocked items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let form = $('#filterForm');

    // Initialize DataTable
    var table = $('.example').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: false,
        responsive: true,
        pageLength: 10,
        dom: '<"top"f>rt<"bottom"lp><"clear">',
    });

    // Add column headers to dropdown dynamically
    $('.example thead th').each(function(index) {
        var headerText = $(this).text().trim();
        if (headerText !== "" && headerText.toLowerCase() !== "action" && headerText.toLowerCase() !== "progress") {
            $('.headerDropdown').append(`<option value="${index}">${headerText}</option>`);
        }
    });

    // Column or global search
    $('.filterInput').on('keyup', function() {
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

    // Load subcategories and submit form when category is selected
    $('#category').on('change', function () {
        let categoryId = $(this).val();
        $('#subcategory').html('<option value="">Select Subcategory</option>');

        if (categoryId) {
            $.get('/get-subcategories/' + categoryId, function (data) {
                $.each(data, function (key, subcat) {
                    $('#subcategory').append(`<option value="${subcat.id}">${subcat.name}</option>`);
                });

                $('#subcategory').val('');
                form.submit();
            });
        } else {
            form.submit();
        }
    });

    // Submit form on subcategory change
    $('#subcategory').on('change', function () {
        form.submit();
    });

    // Pre-fill subcategory if category is selected on reload
    let selectedCategory = '{{ request('category') }}';
    let selectedSubcategory = '{{ request('subcategory') }}';
    if (selectedCategory) {
        $.get('/get-subcategories/' + selectedCategory, function (data) {
            $.each(data, function (key, subcat) {
                $('#subcategory').append(`<option value="${subcat.id}">${subcat.name}</option>`);
            });
            $('#subcategory').val(selectedSubcategory);
        });
    }

    // ---------------- Export to Excel ----------------
    $('#exportExcel').on('click', function () {
        exportTableToExcel();
    });

    function exportTableToExcel() {
        let data = table.rows({ search: 'applied' }).data().toArray();

        if (data.length === 0) {
            alert('No data to export!');
            return;
        }

        let wb = XLSX.utils.book_new();
        let excelData = [];

        // Heading
        excelData.push(['Overstocked Items']);
        excelData.push([]);

        // Headers
        let headers = ['#', 'Brand', 'Item Code', 'Item Name', 'Category', 'Sub Category', 'Current Stock', 'Max Stock Limit', 'Status'];
        excelData.push(headers);

        // Data rows
        $('.example tbody tr:visible').each(function() {
            let rowData = [];
            $(this).find('td').each(function() {
                rowData.push($(this).text().trim());
            });
            excelData.push(rowData);
        });

        let ws = XLSX.utils.aoa_to_sheet(excelData);

        // Column widths
        ws['!cols'] = [
            { width: 5 }, { width: 15 }, { width: 15 },
            { width: 25 }, { width: 15 }, { width: 20 },
            { width: 12 }, { width: 15 }, { width: 12 }
        ];

        // Merge cells for heading
        ws['!merges'] = [{ s: { r:0, c:0 }, e: { r:0, c:8 } }];

        XLSX.utils.book_append_sheet(wb, ws, "Overstocked Items");

        let date = new Date();
        let filename = `Overstocked_Items_${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}.xlsx`;

        XLSX.writeFile(wb, filename);
    }
});
</script>

<!-- Add SheetJS library for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@endsection
