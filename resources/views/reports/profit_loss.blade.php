@extends('layouts.app')

@section('content')

<div class="body-div p-3" id="print-area">
    <div class="body-head d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('reports.list') }}" class="btn btn-link text-decoration-none p-0">
                <i class="fas fa-arrow-left me-2"></i>
            </a>
            <h4 class="d-inline">Profit And Loss</h4>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="filter-container d-flex justify-content-between mb-3 no-print">
            <div class="filter-container-start">
                <!-- Empty space for alignment -->
            </div>
            <div class="filter-container-end d-flex gap-2">
                <select class="form-select" name="store_id" id="store_id" style="min-width: 150px;">
                    <option value="all" {{ $selectedStore == 'all' ? 'selected' : '' }}>All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $selectedStore == $store->id ? 'selected' : '' }}>
                            {{ $store->store_name }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select" name="date_range" id="date_range" style="min-width: 150px;">
                    <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="profitLossTable">
                                <tbody>
                                    <tr class="border-bottom">
                                        <td class="fw-semibold text-muted">Profit And Loss Report</td>
                                        <td class="fw-semibold text-end text-muted">Amount</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Sale(+)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['sales'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Credit Note(-)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['credit_note'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Purchase(-)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['purchases'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Debit Note(-)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['debit_note'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Tax Payable(-)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['tax_payable'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Tax Receivable(+)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['tax_receivable'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Opening Stock(-)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['opening_stock'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Closing Stock(+)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['closing_stock'], 2) }}</td>
                                    </tr>
                                    <tr class="border-top border-bottom bg-light">
                                        <td class="py-2 fw-bold">Gross Profit</td>
                                        <td class="py-2 text-end fw-bold">Rs.{{ number_format($profitLossData['gross_profit'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Other Income(+)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['other_income'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2">Indirect Expenses(-)</td>
                                        <td class="py-2 text-end">Rs.{{ number_format($profitLossData['indirect_expenses'], 2) }}</td>
                                    </tr>
                                    <tr class="border-top bg-primary text-white">
                                        <td class="py-3 fw-bold fs-5">Net Profit</td>
                                        <td class="py-3 text-end fw-bold fs-5">Rs.{{ number_format($profitLossData['net_profit'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SheetJS for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    // Filter change handlers
    document.getElementById('store_id').addEventListener('change', function() {
        updateReport();
    });

    document.getElementById('date_range').addEventListener('change', function() {
        updateReport();
    });

    function updateReport() {
        const storeId = document.getElementById('store_id').value;
        const dateRange = document.getElementById('date_range').value;
        const url = new URL(window.location.href);
        url.searchParams.set('store_id', storeId);
        url.searchParams.set('date_range', dateRange);
        window.location.href = url.toString();
    }

    // Simple Excel Export Function
    function exportToExcel() {
        // Get the table
        var table = document.getElementById('profitLossTable');
        
        // Create workbook and worksheet
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.table_to_sheet(table);
        
        // Add the worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Profit & Loss");
        
        // Generate filename with current date
        const today = new Date();
        const dateStr = today.getFullYear() + '-' + 
                       String(today.getMonth() + 1).padStart(2, '0') + '-' +
                       String(today.getDate()).padStart(2, '0');
        const filename = `Profit_Loss_Report_${dateStr}.xlsx`;
        
        // Save the file
        XLSX.writeFile(wb, filename);
    }

    function printReport() {
        window.print();
    }
</script>

<style>
    /* Hide unwanted UI while printing */
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print, 
    .dataTables_length, 
    .dataTables_filter, 
    .dataTables_info, 
    .dataTables_paginate { 
        display: none !important; 
    }
}
</style>

@endsection