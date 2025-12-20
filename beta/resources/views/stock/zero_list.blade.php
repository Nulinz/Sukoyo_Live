@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head d-flex justify-content-between align-items-center">
        <h4>Zero Movement List</h4>
        <!-- Export Button -->
        <button type="button" class="btn btn-success" id="exportExcel">
            <i class="fas fa-file-excel"></i> Export to Excel
        </button>
    </div>

    <div class="container-fluid mt-3 listtable">
        <div class="filter-container">
            <div class="filter-container-start">
                <select class="headerDropdown form-select filter-option">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder=" Search">
            </div>
            <div class="filter-container-end d-flex align-items-center flex-wrap gap-2">
                <form method="GET" id="filterForm">
                    <div class="filter-container d-flex justify-content-start flex-wrap gap-2">
                        <select class="form-select" name="days" id="days" style="width:120px;">
                            <option value="10" {{ $days == 10 ? 'selected' : '' }}>Last 10 Days</option>
                            <option value="20" {{ $days == 20 ? 'selected' : '' }}>Last 20 Days</option>
                            <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
                        </select>

                        <select class="form-select" name="category" id="category" style="width:120px;">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select" name="subcategory" id="subcategory" style="width:120px;">
                            <option value="">All Subcategories</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <br>

        <div class="table-wrapper">
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
                        <th>Days Idle</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->brand->name ?? '-' }}</td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            <td>{{ $item->subcategory->name ?? '-' }}</td>
                            <td>{{ number_format($item->current_stock, 0) }}</td>
                            <td>{{ $days }} days</td>
                            <td>Zero Movement</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No items found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Subcategory AJAX + auto-submit
    $('#category').change(function() {
        const categoryId = $(this).val();
        $('#filterForm').submit();

        if (!categoryId) {
            $('#subcategory').html('<option value="">All Subcategories</option>');
            return;
        }

        $.get(`/get-subcategories/${categoryId}`, function(data) {
            let options = '<option value="">All Subcategories</option>';
            data.forEach(function(subcat) {
                options += `<option value="${subcat.id}">${subcat.name}</option>`;
            });
            $('#subcategory').html(options);
        });
    });

    $('#days, #subcategory').change(function() {
        $('#filterForm').submit();
    });

    // Retain subcategory after reload
    @if ($categoryId && $subcategoryId)
        $(document).ready(function() {
            $.get(`/get-subcategories/{{ $categoryId }}`, function(data) {
                let options = '<option value="">All Subcategories</option>';
                data.forEach(function(subcat) {
                    const selected = subcat.id == {{ $subcategoryId }} ? 'selected' : '';
                    options += `<option value="${subcat.id}" ${selected}>${subcat.name}</option>`;
                });
                $('#subcategory').html(options);
            });
        });
    @endif
</script>

<script>
    // DataTables Init
    $(document).ready(function() {
        var table = $('.example').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            bDestroy: true,
            info: false,
            responsive: true,
            pageLength: 10,
            dom: '<"top"f>rt<"bottom"lp><"clear">',
        });

        // Add column headers to dropdown
        $('.example thead th').each(function(index) {
            var headerText = $(this).text();
            if (headerText && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "progress") {
                $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
            }
        });

        // Search filter
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

        // ---------------- Export to Excel ----------------
        $('#exportExcel').on('click', function () {
            exportTableToExcel();
        });

        function exportTableToExcel() {
            let wb = XLSX.utils.book_new();
            let excelData = [];

            // Heading
            excelData.push(['Zero Movement Items']);
            excelData.push([]);

            // Headers
            let headers = ['#', 'Brand', 'Item Code', 'Item Name', 'Category', 'Sub Category', 'Current Stock', 'Days Idle', 'Status'];
            excelData.push(headers);

            // Data rows (only visible/filtered)
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
                { width: 12 }, { width: 12 }, { width: 15 }
            ];

            // Merge heading across columns
            ws['!merges'] = [{ s: { r:0, c:0 }, e: { r:0, c:8 } }];

            XLSX.utils.book_append_sheet(wb, ws, "Zero Movement Items");

            let date = new Date();
            let filename = `Zero_Movement_Items_${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}.xlsx`;

            XLSX.writeFile(wb, filename);
        }
    });
</script>

<!-- Add SheetJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@endsection
