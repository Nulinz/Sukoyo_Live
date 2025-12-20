@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Low Stock List</h4>
    </div>

    <div class="container-fluid mt-3 listtable">
        <div class="filter-container">
            <div class="filter-container-start d-flex gap-2">
                <select class="headerDropdown form-select w-auto">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder="Search" />
            </div>

            <div class="filter-container-end d-flex gap-2">
                <select class="form-select" id="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select class="form-select" id="subcategory">
                    <option value="">All Subcategories</option>
                </select>

                <!-- Export Button -->
                <button type="button" class="btn btn-success" id="exportExcel">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
            </div>
        </div>

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
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let allCategories = @json($categories);

    // Initialize DataTable with AJAX and search
    let table = $('.example').DataTable({
        processing: true,
        serverSide: false,
        paging: true,
        searching: true,
        ordering: true,
        bDestroy: true,
        info: false,
        responsive: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('stock.getLowStockItems') }}",
            data: function (d) {
                d.category_id = $('#category').val();
                d.subcategory_id = $('#subcategory').val();
            },
            dataSrc: ""
        },
        columns: [
            { data: 'index' },
            { data: 'brand' },
            { data: 'item_code' },
            { data: 'item_name' },
            { data: 'category' },
            { data: 'subcategory' },
            { data: 'current_stock' },
            { data: 'status' }
        ],
        initComplete: function () {
            // Populate column dropdown
            this.api().columns().every(function (index) {
                let title = this.header().innerText;
                if (title && title.toLowerCase() !== "action" && title.toLowerCase() !== "progress") {
                    $('.headerDropdown').append(`<option value="${index}">${title}</option>`);
                }
            });
        }
    });

    // Filter input on keyup
    $('.filterInput').on('keyup', function () {
        var selectedColumn = $('.headerDropdown').val();
        if (selectedColumn !== 'All') {
            table.column(selectedColumn).search(this.value).draw();
        } else {
            table.search(this.value).draw();
        }
    });

    $('.headerDropdown').on('change', function () {
        $('.filterInput').val('');
        table.search('').columns().search('').draw();
    });

    // Filter Category/Subcategory
    $('#category, #subcategory').on('change', function () {
        table.ajax.reload();
    });

    // Populate subcategories based on category
    $('#category').on('change', function () {
        let selected = $(this).val();
        let subOptions = '<option value="">All Subcategories</option>';
        if (selected) {
            let subcats = allCategories.find(c => c.id == selected)?.subcategories || [];
            subcats.forEach(sc => {
                subOptions += `<option value="${sc.id}">${sc.name}</option>`;
            });
        }
        $('#subcategory').html(subOptions);
    });

    // Excel Export Functionality
    $('#exportExcel').on('click', function () {
        exportTableToExcel();
    });

    function exportTableToExcel() {
        // Get all visible/filtered data from DataTable
        let data = table.rows({ search: 'applied' }).data().toArray();
        
        if (data.length === 0) {
            alert('No data to export!');
            return;
        }

        // Create workbook and worksheet
        let wb = XLSX.utils.book_new();
        
        // Prepare data for Excel
        let excelData = [];
        
        // Add main heading
        excelData.push(['Low Stock Details']);
        excelData.push([]); // Empty row for spacing
        
        // Add headers
        let headers = ['#', 'Brand', 'Item Code', 'Item Name', 'Category', 'Sub Category', 'Current Stock', 'Status'];
        excelData.push(headers);
        
        // Add data rows
        data.forEach((row, index) => {
           excelData.push([
    index + 1,
    row.brand ?? '',
    row.item_code ?? '',
    row.item_name ?? '',
    row.category ?? '',
    row.subcategory ?? '',
    row.current_stock ?? '',   // FIXED
    row.status ?? ''
]);

        });

        // Create worksheet
        let ws = XLSX.utils.aoa_to_sheet(excelData);
        
        // Set column widths
        ws['!cols'] = [
            { width: 5 },   // #
            { width: 15 },  // Brand
            { width: 15 },  // Item Code
            { width: 25 },  // Item Name
            { width: 15 },  // Category
            { width: 15 },  // Sub Category
            { width: 12 },  // Current Stock
            { width: 10 }   // Status
        ];

        // Style the main heading
        if (ws['A1']) {
            ws['A1'].s = {
                font: { bold: true, sz: 16 },
                alignment: { horizontal: 'center' }
            };
        }

        // Merge cells for the heading
        ws['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 7 } } // Merge A1:H1 for heading
        ];

        // Style the headers (row 3, since heading is row 1 and empty row is 2)
        for (let col = 0; col < 8; col++) {
            let cellAddress = XLSX.utils.encode_cell({ r: 2, c: col });
            if (ws[cellAddress]) {
                ws[cellAddress].s = {
                    font: { bold: true },
                    fill: { fgColor: { rgb: "E6E6E6" } }
                };
            }
        }

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Low Stock Items");

        // Generate filename with current date
        let date = new Date();
        let filename = `Low_Stock_Items_${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}.xlsx`;

        // Save file
        XLSX.writeFile(wb, filename);
    }
});
</script>

<!-- Add SheetJS library for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@endsection