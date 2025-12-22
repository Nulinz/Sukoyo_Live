@extends('layouts.app_pos')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

<style>
    @media screen and (min-width: 990px) {
        .col-xl-3 {
            width: 20%;
        }

        .exportbtn {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 6px 12px;
            margin-left: 5px;
            border-radius: 4px;
            cursor: pointer;
        }

        .exportbtn i {
            margin-right: 4px;
        }
    }
</style>

<!-- 📦 Printable Content Starts -->
<div id="printableArea">

<div class="body-div p-3">
    <div class="body-head mb-3 d-flex justify-content-between align-items-center">
        <h4>Sales Profile</h4>
        <div>
            <button class="exportbtn" id="downloadPdf"><i class="fas fa-download"></i>Download Bill</button>
            <button class="exportbtn" id="printPage"><i class="fas fa-print"></i>Print</button>
        </div>
    </div>

    <div class="mainbdy d-block">
        <div class="contentright">
            <div class="tab-content">
                <div class="cards mb-2">
                    <div class="maincard row py-0 mb-3">
                        <div class="cardhead my-3">
                            <h5>Details</h5>
                        </div>

                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Invoice No</h6>
                            <h5 class="mb-0">{{ $invoice->id }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Customer Name</h6>
                            <h5 class="mb-0">{{ $invoice->customer->name ?? 'N/A' }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Date</h6>
                            <h5 class="mb-0">{{ $invoice->invoice_date->format('d-m-Y H:i') }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Payment Type</h6>
                            <h5 class="mb-0">{{ $invoice->mode_of_payment }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Discount</h6>
                            <h5 class="mb-0">₹ {{ number_format($invoice->total_discount, 2) }}</h5>
                        </div>
                        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                            <h6 class="mb-1">Total Amount</h6>
                            <h5 class="mb-0">₹ {{ number_format($invoice->grand_total, 2) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="body-head mt-3">
                    <h4>Item List</h4>
                </div>

                <div class="container-fluid listtable">
                    <div class="table-wrapper">
                        <table class="table table-bordered" id="table1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Items</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Discount</th>
                                    <th>Tax</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>{{ (int) $item->qty }}</td>
                                        <td>{{ $item->discount }}%</td>
                                        <td>{{ $item->tax }}%</td>
                                        <td>₹ {{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

</div>
<!-- 📦 Printable Content Ends -->

<!-- 🔌 Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- 📜 Script -->
<script>
    $(document).ready(function () {
        $('#table1').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
            responsive: true,
            bDestroy: true
        });
    });

    document.getElementById('printPage').addEventListener('click', function () {
        var printContents = document.getElementById('printableArea').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    });

    document.getElementById('downloadPdf').addEventListener('click', function () {
        const element = document.getElementById('printableArea');
        const opt = {
            margin: 0.4,
            filename: 'SalesInvoice_{{ $invoice->id }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save();
    });
</script>

@endsection
