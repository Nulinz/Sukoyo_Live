@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Purchase Summary</h4>
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
                <select class="form-select" name="store_id" id="store_id">
                    <option value="all" {{ $selectedStore == 'all' ? 'selected' : '' }}>All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                            {{ $store->store_name }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select" name="vendor_id" id="vendor_id">
                    <option value="all" {{ $selectedVendor == 'all' ? 'selected' : '' }}>All Vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ $selectedVendor == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->vendorname }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select" name="date_range" id="date_range">
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
                <div class="d-flex gap-2">
                    <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export to Excel
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <!-- @if($purchases->count() > 0)
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Purchases</h5>
                        <h3>{{ number_format($totalPurchases) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Amount</h5>
                        <h3>₹{{ number_format($totalAmount, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Quantity</h5>
                        <h3>{{ number_format($totalQuantity) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Paid</h5>
                        <h3>₹{{ number_format($totalPaidAmount, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        @endif -->

        <div class="table-wrapper">
            <table class="example table table-bordered" id="purchaseTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Purchase Date</th>
                        <th>Purchase No</th>
                        <th>Party Name</th>
                        <th>Store/Warehouse</th>
                        <th>Items</th>
                        <th>Quantity</th>
                        <th>Purchase Amount</th>
                        <th>Paid Amount</th>
                        <th>Balance Amount</th>
                    </tr>
                </thead><br>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ str_pad($purchase->index, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ date('d/m/Y', strtotime($purchase->purchase_date)) }}</td>
                            <td>{{ $purchase->purchase_no }}</td>
                            <td>{{ $purchase->party_name }}</td>
                            <td>{{ $purchase->store_name }}</td>
                            <td class="text-center">{{ $purchase->total_items }}</td>
                            <td class="text-center">
                                {{ number_format($purchase->total_quantity, 0) }} PCS
                            </td>
                            <td class="text-end">
                                Rs.{{ number_format($purchase->total, 2) }}
                            </td>
                            <td class="text-end">
                                Rs.{{ number_format($purchase->paid_amount, 2) }}
                            </td>
                            <td class="text-end">
                                Rs.{{ number_format($purchase->balance_amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-3 text-muted">
                                No purchase data found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($purchases->count() > 0)
                    <tfoot>
                        <tr class="table-secondary">
                            <th colspan="7" class="text-end">Total</th>
                            <th class="text-end">
                                Rs.{{ number_format($totalAmount, 2) }}
                            </th>
                            <th class="text-end">
                                Rs.{{ number_format($totalPaidAmount, 2) }}
                            </th>
                            <th class="text-end">
                                Rs.{{ number_format($totalBalance, 2) }}
                            </th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Include SheetJS for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    // Store purchase data for Excel export
    const purchaseData = @json($purchases);
    const summaryData = {
        totalPurchases: {{ $totalPurchases ?? 0 }},
        totalAmount: {{ $totalAmount ?? 0 }},
        totalQuantity: {{ $totalQuantity ?? 0 }},
        totalPaidAmount: {{ $totalPaidAmount ?? 0 }},
        totalBalance: {{ $totalBalance ?? 0 }}
    };

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
            "order": [[ 1, "desc" ]], // Sort by purchase date descending
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

    // Filter change handlers
    document.getElementById('store_id').addEventListener('change', function() {
        updateReport();
    });

    document.getElementById('vendor_id').addEventListener('change', function() {
        updateReport();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        updateReport();
    });

    function updateReport() {
        const storeId = document.getElementById('store_id').value;
        const vendorId = document.getElementById('vendor_id').value;
        const dateRange = document.getElementById('date_range').value;
        const url = new URL(window.location.href);
        url.searchParams.set('store_id', storeId);
        url.searchParams.set('vendor_id', vendorId);
        url.searchParams.set('date_range', dateRange);
        window.location.href = url.toString();
    }

    function printReport() {
        window.print();
    }

    // Excel Export Function
    function exportToExcel() {
        try {
            // Create a new workbook
            const wb = XLSX.utils.book_new();
            
            // Prepare data for export
            const exportData = [];
            
            // Add header row
            exportData.push([
                'S.No',
                'Purchase Date',
                'Purchase No',
                'Party Name',
                'Store/Warehouse',
                'Total Items',
                'Total Quantity',
                'Purchase Amount',
                'Paid Amount',
                'Balance Amount'
            ]);
            
            // Add data rows
            purchaseData.forEach((purchase, index) => {
                exportData.push([
                    index + 1,
                    new Date(purchase.purchase_date).toLocaleDateString('en-GB'),
                    purchase.purchase_no || purchase.id,
                    purchase.party_name,
                    purchase.store_name || 'N/A',
                    purchase.total_items,
                    purchase.total_quantity.toFixed(0) + ' PCS',
                    '₹' + purchase.total.toLocaleString('en-IN', { minimumFractionDigits: 2 }),
                    '₹' + purchase.paid_amount.toLocaleString('en-IN', { minimumFractionDigits: 2 }),
                    '₹' + purchase.balance_amount.toLocaleString('en-IN', { minimumFractionDigits: 2 })
                ]);
            });
            
            // Add empty row
            exportData.push([]);
            
            // Add summary section
            exportData.push(['Summary', '', '', '', '', '', '', '', '', '']);
            exportData.push(['Total Purchases', summaryData.totalPurchases, '', '', '', '', '', '', '', '']);
            exportData.push(['Total Quantity', summaryData.totalQuantity.toLocaleString('en-IN'), '', '', '', '', '', '', '', '']);
            exportData.push(['Total Amount', '₹' + summaryData.totalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 }), '', '', '', '', '', '', '', '']);
            exportData.push(['Total Paid Amount', '₹' + summaryData.totalPaidAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 }), '', '', '', '', '', '', '', '']);
            exportData.push(['Total Balance Amount', '₹' + summaryData.totalBalance.toLocaleString('en-IN', { minimumFractionDigits: 2 }), '', '', '', '', '', '', '', '']);
            
            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(exportData);
            
            // Set column widths
            const columnWidths = [
                { wch: 8 },  // S.No
                { wch: 15 }, // Purchase Date
                { wch: 15 }, // Purchase No
                { wch: 20 }, // Party Name
                { wch: 18 }, // Store/Warehouse
                { wch: 12 }, // Total Items
                { wch: 15 }, // Total Quantity
                { wch: 18 }, // Purchase Amount
                { wch: 15 }, // Paid Amount
                { wch: 18 }  // Balance Amount
            ];
            ws['!cols'] = columnWidths;
            
            // Style the header row
            const headerStyle = {
                font: { bold: true },
                fill: { fgColor: { rgb: "D9D9D9" } },
                alignment: { horizontal: "center", vertical: "center" }
            };
            
            // Apply header styling
            for (let col = 0; col < 10; col++) {
                const cellRef = XLSX.utils.encode_cell({ r: 0, c: col });
                if (!ws[cellRef]) ws[cellRef] = {};
                ws[cellRef].s = headerStyle;
            }
            
            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Purchase Summary");
            
            // Generate filename with current date and time
            const now = new Date();
            const dateStr = now.getFullYear() + 
                           String(now.getMonth() + 1).padStart(2, '0') + 
                           String(now.getDate()).padStart(2, '0') + '_' +
                           String(now.getHours()).padStart(2, '0') + 
                           String(now.getMinutes()).padStart(2, '0') + 
                           String(now.getSeconds()).padStart(2, '0');
            
            const filename = `Purchase_Summary_${dateStr}.xlsx`;
            
            // Save the file
            XLSX.writeFile(wb, filename);
            
            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Excel file has been downloaded successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Excel file has been downloaded successfully.');
            }
            
        } catch (error) {
            console.error('Excel export error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Export Failed',
                    text: 'There was an error exporting the data. Please try again.',
                });
            } else {
                alert('There was an error exporting the data. Please try again.');
            }
        }
    }
</script>

<style>
@media print {
    .btn, .filter-container { display: none !important; }
    .card { break-inside: avoid; }
}

.btn-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-success:hover {
    background-color: #157347;
    border-color: #146c43;
}

.fas.fa-file-excel {
    color: #ffffff;
}
</style>
<script>
function printReport() {
    // Get the table HTML
    var tableContent = document.getElementById("purchaseTable").outerHTML;

    // Open a new window
    var printWindow = window.open('', '', 'height=700,width=900');

    // Write the content into the new window
    printWindow.document.write('<html><head><title>Purchase Summary</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('table { width:100%; border-collapse: collapse; font-size: 14px; }');
    printWindow.document.write('table, th, td { border: 1px solid #000; padding: 6px; text-align: left; }');
    printWindow.document.write('th { background: #f2f2f2; }');
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(tableContent);
    printWindow.document.write('</body></html>');

    // Close and trigger print
    printWindow.document.close();
    printWindow.print();
}
</script>


@endsection