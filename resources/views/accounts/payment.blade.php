@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Payment List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addPayment">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Payment</button>
        </a>
    </div>

    <div class="container-fluid mt-3 listtable">
        <div class="filter-container">
            <div class="filter-container-start">
                <select class="headerDropdown form-select filter-option">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder=" Search">
            </div>
        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vendor Name</th>
                        <th>PO ID</th>
                        <th>Total Amount</th>
                        <th>Paid Amount</th>
                        <th>Balance</th>
                        <th>Payment Date</th>
                        <th>Payment Mode</th>
                        <th>Remarks</th>
                         <th>Status</th>
                    </tr>
                </thead>
               <tbody>
    @foreach ($payments as $index => $payment)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $payment->vendor->vendorname ?? '-' }}</td>
            <td>{{ $payment->purchaseOrder->id ?? '-' }}</td>
            <td>₹ {{ number_format($payment->pending_amount, 2) }}</td>
            <td>₹ {{ number_format($payment->payment_amount, 2) }}</td>
            <td>₹ {{ number_format($payment->now_balance, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
            <td>{{ $payment->payment_type }}</td>
            <td>{{ $payment->remarks ?? 'Nil' }}</td>
                        <td>
        @if(optional($payment->purchaseOrder)->balance_amount == 0)
            <span class="badge bg-success">Paid</span>
        @else
            <span class="badge bg-danger">Not Paid</span>
        @endif
    </td>
        </tr>
    @endforeach
</tbody>

            </table>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<!-- Add Payment Modal -->
<div class="modal fade" id="addPayment" tabindex="-1" aria-labelledby="addPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Payment Out</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('store.payment') }}">
                    @csrf
                    <div class="row">
                        <!-- Vendor Dropdown -->
                      <div class="col-sm-12 col-md-6 mb-2">
    <label for="addvendor">Vendor</label>
    <select class="form-select" name="vendor_id" id="addvendor" required>
        <option value="" selected disabled>Select Option</option>
        @foreach($vendors as $vendor)
            <option value="{{ $vendor->id }}">{{ $vendor->vendorname }}</option>
        @endforeach
    </select>
</div>


                        <!-- Invoice Dropdown -->
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addinv">PO No</label>
                            <select class="form-select" name="invoice_id" id="addinv" required>
                                <option value="" selected disabled>Select Option</option>
                            </select>
                        </div>

                        <!-- Pending Amount (readonly) -->
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addpending">Payment Pending</label>
                            <input type="text" class="form-control" name="pending_amount" id="addpending" readonly required>
                        </div>

                        <!-- Payment Amount -->
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addpayamt">Payment Amount</label>
                            <input type="text" class="form-control" name="payment_amount" id="addpayamt" required>
                        </div>

                        <!-- Payment Type -->
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addpaytype">Payment Type</label>
                            <select class="form-select" name="payment_type" id="addpaytype" required>
                                <option value="" selected disabled>Select Option</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>

                        <!-- Payment Date -->
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addpaydate">Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" id="addpaydate" required>
                        </div>

                        <!-- Remarks -->
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addremarks">Remarks</label>
                            <textarea rows="1" class="form-control" name="remarks" id="addremarks"></textarea>
                        </div>

                        <!-- Submit / Cancel Buttons -->
                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Scripts -->
<script>
    $(document).ready(function () {
        var table = $('.example').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            bDestroy: true,
            info: false,
            responsive: true,
            pageLength: 10,
            dom: '<"top"f>rt<"bottom"lp><"clear">'
        });

        // Filter columns
        $('.example thead th').each(function (index) {
            var headerText = $(this).text();
            if (headerText !== "" && headerText.toLowerCase() !== "action") {
                $('.headerDropdown').append(`<option value="${index}">${headerText}</option>`);
            }
        });

        $('.filterInput').on('keyup', function () {
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

        // Load invoices when vendor changes
        $('#addvendor').on('change', function () {
            var vendorId = $(this).val();
            $('#addinv').html('<option value="">Loading...</option>');
            $('#addpending').val('');

            $.ajax({
                url: '{{ route("get.invoices.by.vendor") }}',
                type: 'POST',
                data: {
                    vendor_id: vendorId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (data) {
                    let options = '<option value="" disabled selected>Select Invoice</option>';
                    $.each(data, function (i, invoice) {
                        options += `<option value="${invoice.id}">${invoice.id}</option>`;
                    });
                    $('#addinv').html(options);
                }
            });
        });

        // Load pending amount when invoice changes
        $('#addinv').on('change', function () {
            var invoiceId = $(this).val();

            $.ajax({
                url: '{{ route("get.pending.amount") }}',
                type: 'POST',
                data: {
                    invoice_id: invoiceId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (data) {
                    $('#addpending').val(data.pending);
                }
            });
        });
    });
</script>

@endsection
