<div class="profbtns d-flex align-items-center justify-content-start flex-wrap gap-2 form-div pt-0">
    <form method="GET" action="{{ route('party.vendorprofile', $vendor->id) }}" class="d-flex align-items-center gap-2 mb-3">
    <select name="range" class="form-select w-auto" onchange="this.form.submit()">
        <option value="7days" {{ $range == '7days' ? 'selected' : '' }}>Last 7 Days</option>
        <option value="30days" {{ $range == '30days' ? 'selected' : '' }}>Last 30 Days</option>
        <option value="month" {{ $range == 'month' ? 'selected' : '' }}>This Month</option>
        <option value="3months" {{ $range == '3months' ? 'selected' : '' }}>Last 3 Months</option>
        <option value="6months" {{ $range == '6months' ? 'selected' : '' }}>Last 6 Months</option>
        <option value="9months" {{ $range == '9months' ? 'selected' : '' }}>Last 9 Months</option>
        <option value="12months" {{ $range == '12months' ? 'selected' : '' }}>Last 12 Months</option> <!-- ✅ -->
        <option value="18months" {{ $range == '18months' ? 'selected' : '' }}>Last 18 Months</option> <!-- ✅ -->
        <option value="1year" {{ $range == '1year' ? 'selected' : '' }}>Last 1 Year</option>
        <option value="2years" {{ $range == '2years' ? 'selected' : '' }}>Last 2 Years</option> <!-- ✅ -->
    </select>

    <button type="button" class="btn btn-secondary" id="downloadBtn">
        <i class="fas fa-download pe-2"></i>Download
    </button>
    <button type="button" class="btn btn-secondary" onclick="printDiv('printArea')">
        <i class="fas fa-print pe-2"></i>Print
    </button>

</form>

</div>

<div class="cards" id="printArea">
    <div class="maincard row">
        <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
            <h5 class="mb-1">SUKOYO</h5>
            <!-- <h6 class="mb-0">Contact No: {{ $vendor->contact }}</h6> -->
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                <h5 class="mb-1">To</h5>
                <h6 class="mb-1">{{ $vendor->vendorname }}</h6>
                <h6 class="mb-0">{{ $vendor->billaddress }}</h6>
            </div>
           <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
              <h5 class="mb-1 text-md-end">
    {{ \Carbon\Carbon::today()->format('d-m-Y') }}
</h5>

               <h6 class="mb-1 text-md-end">Current Balance Amount</h6>
<h6 class="mb-0 text-md-end">₹{{ number_format($lastBalance, 2) }}</h6>

            </div>
        </div>

        <div class="container-fluid listtable">
            <div class="table-wrapper">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Voucher</th>
                            <th>Pending Amount</th>
                            <th>Paid Amount</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($transactions as $index => $trans)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($trans['date'])->format('d-m-Y') }}</td>
            <td>{{ $trans['voucher'] }}</td>
            <td>{{ $trans['credit'] ? '₹ ' . number_format($trans['credit'], 2) : '-' }}</td>
            <td>{{ $trans['debit'] ? '₹ ' . number_format($trans['debit'], 2) : '-' }}</td>
            <td>{{ $trans['balance'] ? '₹ ' . number_format($trans['balance'], 2) : '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">No transactions available</td>
        </tr>
    @endforelse
</tbody>

                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    document.getElementById('downloadBtn').addEventListener('click', function () {
        const element = document.getElementById('printArea');

        const opt = {
            margin:       0.2,
            filename:     'vendor_profile.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save();
    });
</script>
<script>
    function printDiv(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;

        // Open a new window and print just that section
        var printWindow = window.open('', '', 'height=800,width=1000');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write(`
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                h5, h6 { margin: 0 0 5px; }
            </style>
        `);
        printWindow.document.write('</head><body >');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();

        printWindow.print();
        printWindow.close();
    }
</script>
