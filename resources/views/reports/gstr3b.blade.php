@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/reports.css') }}">
<style>
    .report-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .report-table {
        font-size: 12px;
    }
    .report-table th {
        background: #e9ecef;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        padding: 8px 4px;
    }
    .report-table td {
        border: 1px solid #dee2e6;
        padding: 6px 4px;
        text-align: right;
    }
    .report-table td:nth-child(2),
    .report-table td:nth-child(3),
    .report-table td:nth-child(4),
    .report-table td:nth-child(5),
    .report-table td:nth-child(6) {
        text-align: left;
    }
    .total-row {
        background: #f8f9fa;
        font-weight: 600;
    }
    .btn-group .btn {
        margin-right: 5px;
    }
    @media print {
        .no-print { display: none !important; }
        .report-table { font-size: 10px; }
        .report-table th, .report-table td { padding: 4px 2px; }
    }
</style>

<div class="body-div p-3">
    <div class="body-head mb-4">
        <h4>GSTR-3B - Purchase Report</h4>
    </div>

    <div class="container-fluid">
        <!-- Filters Section -->
        <div class="row mb-4 no-print">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" id="filterForm">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="start_date" class="form-control" 
                                           value="{{ $startDate }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="end_date" class="form-control" 
                                           value="{{ $endDate }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tax Rate</label>
                                    <select name="tax_rate" class="form-control" required>
                                        @foreach($availableTaxRates as $rate)
                                            <option value="{{ $rate }}" {{ $taxRate == $rate ? 'selected' : '' }}>
                                                {{ $rate }}% GST
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <button type="button" class="btn btn-success me-2" onclick="exportToExcel()">Export Excel</button>
                                    <button type="button" class="btn btn-info" onclick="printReport()">Print</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Header -->
        <!-- <div class="report-header text-center">
            <h3>{{ $companyGst->name ?? 'NEW FOREVER' }}</h3>
            <p class="mb-1">{{ $companyGst->gstaddress ?? 'Ground Floor, Kakkat Building, Broadway, Ernakulam' }}</p>
            <p class="mb-1">Kerala - 682036, India</p>
            <p class="mb-1">Contact: {{ $companyGst->contact_no ?? '9447757878' }}</p>
            <p class="mb-3">E-Mail: {{ $companyGst->email_id ?? 'sales@newforever.in' }}</p>
            
            <h4>GSTR-3B - Purchase Register</h4>
            <p>{{ \Carbon\Carbon::parse($startDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-Y') }}</p>
            <p>GST Registration: {{ $companyGst->gst_number ?? '32CVAP4297A1ZE' }}</p>
        </div> -->

        <!-- Purchase Table -->
        <div class="row">
            <div class="col-12">
                <h5>Purchases of</h5>
                <p><strong>Local Purchase - Taxable to Registered Dealer @ {{ $taxRate }}%</strong></p>
                
                <div class="table-responsive">
                    <table class="table table-bordered report-table" id="purchaseTable">
                        <thead>
                            <tr>
                                <th rowspan="2">Date</th>
                                <th rowspan="2">Particulars</th>
                                <th rowspan="2">Party GSTIN/UIN</th>
                                <th rowspan="2">Vch Type</th>
                                <th rowspan="2">Vch No.</th>
                                <th rowspan="2">Doc No.</th>
                                <th rowspan="2">Doc Date</th>
                                <th rowspan="2">Taxable Amount</th>
                                <th rowspan="2">IGST</th>
                                <th rowspan="2">CGST</th>
                                <th rowspan="2">SGST/ UTGST</th>
                                <th rowspan="2">Cess</th>
                                <th rowspan="2">Tax Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalTaxable = 0;
                                $totalIgst = 0;
                                $totalCgst = 0;
                                $totalSgst = 0;
                                $totalCess = 0;
                                $totalTax = 0;
                            @endphp
                            
                            @forelse($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase['date'] }}</td>
                                <td>{{ $purchase['particulars'] }}</td>
                                <td>{{ $purchase['party_gstin'] }}</td>
                                <td>{{ $purchase['vch_type'] }}</td>
                                <td>{{ $purchase['vch_no'] }}</td>
                                <td>{{ $purchase['doc_no'] }}</td>
                                <td>{{ $purchase['doc_date'] }}</td>
                                <td>{{ number_format($purchase['taxable_amount'], 2) }}</td>
                                <td>{{ number_format($purchase['igst'], 2) }}</td>
                                <td>{{ number_format($purchase['cgst'], 2) }}</td>
                                <td>{{ number_format($purchase['sgst'], 2) }}</td>
                                <td>{{ number_format($purchase['cess'], 2) }}</td>
                                <td>{{ number_format($purchase['tax_amount'], 2) }}</td>
                            </tr>
                            @php
                                $totalTaxable += $purchase['taxable_amount'];
                                $totalIgst += $purchase['igst'];
                                $totalCgst += $purchase['cgst'];
                                $totalSgst += $purchase['sgst'];
                                $totalCess += $purchase['cess'];
                                $totalTax += $purchase['tax_amount'];
                            @endphp
                            @empty
                            <tr>
                                <td colspan="13" class="text-center">No records found for {{ $taxRate }}% tax rate in the selected date range.</td>
                            </tr>
                            @endforelse
                            
                            @if(count($purchases) > 0)
                            <!-- Total Row -->
                            <tr class="total-row">
                                <td colspan="7"><strong>Total</strong></td>
                                <td><strong>{{ number_format($totalTaxable, 2) }}</strong></td>
                                <td><strong>{{ number_format($totalIgst, 2) }}</strong></td>
                                <td><strong>{{ number_format($totalCgst, 2) }}</strong></td>
                                <td><strong>{{ number_format($totalSgst, 2) }}</strong></td>
                                <td><strong>{{ number_format($totalCess, 2) }}</strong></td>
                                <td><strong>{{ number_format($totalTax, 2) }}</strong></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    // Get data from controller
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const taxRate = document.querySelector('select[name="tax_rate"]').value;
    
    fetch(`{{ route('reports.gstr3b-purchase.export') }}?start_date=${startDate}&end_date=${endDate}&tax_rate=${taxRate}`)
        .then(response => response.json())
        .then(data => {
            // Create workbook
            const wb = XLSX.utils.book_new();
            
            // Company header data
            const headerData = [
                ['NEW FOREVER'],
                ['Ground Floor, Kakkat Building'],
                ['Broadway, Ernakulam'],
                ['Kerala - 682036, India'],
                ['Contact: 9447757878'],
                ['E-Mail: sales@newforever.in'],
                [''],
                ['GSTR-3B - Purchase Register'],
                [`${data.period.start} to ${data.period.end}`],
                [`GST Registration: ${data.company ? data.company.gst_number : '32CVAP4297A1ZE'}`],
                ['Purchases of'],
                [`Local Purchase - Taxable to Registered Dealer @ ${data.tax_rate}%`],
                ['']
            ];
            
            // Table headers
            const tableHeaders = [
                ['Date', 'Particulars', 'Party GSTIN/UIN', 'Vch Type', 'Vch No.', 'Doc No.', 'Doc Date', 'Taxable Amount', 'IGST', 'CGST', 'SGST/UTGST', 'Cess', 'Tax Amount']
            ];
            
            // Table data
            const tableData = data.data.map(row => [
                row.date,
                row.particulars,
                row.party_gstin,
                row.vch_type,
                row.vch_no,
                row.doc_no,
                row.doc_date,
                parseFloat(row.taxable_amount).toFixed(2),
                parseFloat(row.igst).toFixed(2),
                parseFloat(row.cgst).toFixed(2),
                parseFloat(row.sgst).toFixed(2),
                parseFloat(row.cess).toFixed(2),
                parseFloat(row.tax_amount).toFixed(2)
            ]);
            
            // Calculate totals
            const totals = data.data.reduce((acc, row) => {
                acc.taxable_amount += parseFloat(row.taxable_amount);
                acc.igst += parseFloat(row.igst);
                acc.cgst += parseFloat(row.cgst);
                acc.sgst += parseFloat(row.sgst);
                acc.cess += parseFloat(row.cess);
                acc.tax_amount += parseFloat(row.tax_amount);
                return acc;
            }, {taxable_amount: 0, igst: 0, cgst: 0, sgst: 0, cess: 0, tax_amount: 0});
            
            // Total row
            const totalRow = [
                ['Total', '', '', '', '', '', '', 
                 totals.taxable_amount.toFixed(2), 
                 totals.igst.toFixed(2), 
                 totals.cgst.toFixed(2), 
                 totals.sgst.toFixed(2), 
                 totals.cess.toFixed(2), 
                 totals.tax_amount.toFixed(2)]
            ];
            
            // Combine all data
            const wsData = [...headerData, ...tableHeaders, ...tableData, ...totalRow];
            
            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            
            // Set column widths
            ws['!cols'] = [
                {wch: 12}, {wch: 25}, {wch: 15}, {wch: 12}, {wch: 10}, 
                {wch: 12}, {wch: 12}, {wch: 15}, {wch: 10}, {wch: 10}, 
                {wch: 12}, {wch: 8}, {wch: 12}
            ];
            
            // Merge header cells for company name
            ws['!merges'] = [
                {s: {r: 0, c: 0}, e: {r: 0, c: 12}}, // Company name
                {s: {r: 7, c: 0}, e: {r: 7, c: 12}}, // Report title
                {s: {r: 8, c: 0}, e: {r: 8, c: 12}}, // Date range
                {s: {r: 9, c: 0}, e: {r: 9, c: 12}}  // GST registration
            ];
            
            XLSX.utils.book_append_sheet(wb, ws, "GSTR-3B Purchase");
            
            // Save file
            const filename = `GSTR-3B-Purchase-${taxRate}%-${startDate}-to-${endDate}.xlsx`;
            XLSX.writeFile(wb, filename);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error exporting to Excel');
        });
}

function printReport() {
    window.print();
}
</script>
<style>
    @media print {
    body * {
        visibility: hidden; /* Hide everything by default */
    }
    .body-div, .body-div * {
        visibility: visible; /* Show only the report section */
    }
    .body-div {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* Hide buttons and filters */
    .no-print { 
        display: none !important; 
    }

    /* Make report cleaner */
    .report-header {
        background: none !important;
        padding: 0 !important;
        border: none !important;
    }

    .report-table {
        font-size: 10px;
        border-collapse: collapse;
        width: 100%;
    }

    .report-table th, 
    .report-table td {
        padding: 4px 6px;
        border: 1px solid #000;
    }

    /* Optional: Add page title */
    @page {
        margin: 15mm;
    }
}

</style>
@endsection