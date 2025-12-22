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
                            <th>Min Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function () {
            let allCategories = @json($categories);

            // Initialize DataTable with AJAX
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
                    type: 'GET',
                    data: function (d) {
                        d.category_id = $('#category').val();
                        d.subcategory_id = $('#subcategory').val();
                    },
                    dataSrc: "data"
                },
                columns: [
                    { data: 'index' },
                    { data: 'brand' },
                    { data: 'item_code' },
                    { data: 'item_name' },
                    { data: 'category' },
                    { data: 'subcategory' },
                    { data: 'current_stock' },
                    { data: 'min_stock' },
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
                let selectedColumn = $('.headerDropdown').val();
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
                table.ajax.reload();
            });

            // Excel Export Functionality
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

                // Add heading
                excelData.push(['Low Stock Details']);
                excelData.push([]);
                excelData.push(['#', 'Brand', 'Item Code', 'Item Name', 'Category', 'Sub Category', 'Current Stock', 'Min Stock', 'Status']);

                data.forEach((row, index) => {
                    excelData.push([
                        index + 1,
                        row.brand ?? '',
                        row.item_code ?? '',
                        row.item_name ?? '',
                        row.category ?? '',
                        row.subcategory ?? '',
                        row.current_stock ?? '',
                        row.min_stock ?? '',
                        row.status ?? ''
                    ]);
                });

                let ws = XLSX.utils.aoa_to_sheet(excelData);

                // Set column widths
                ws['!cols'] = [
                    { width: 5 }, { width: 15 }, { width: 15 }, { width: 25 }, { width: 15 },
                    { width: 15 }, { width: 12 }, { width: 12 }, { width: 12 }
                ];

                // Merge heading
                ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 8 } }];

                XLSX.utils.book_append_sheet(wb, ws, "Low Stock Items");

                let date = new Date();
                let filename = `Low_Stock_Items_${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}.xlsx`;

                XLSX.writeFile(wb, filename);
            }

            // Initialize Bootstrap tooltips safely
            if (typeof tooltipTriggerList === 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    </script>


    <!-- Add SheetJS library for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@endsection