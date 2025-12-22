@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-4">
        <h4>Purchase Invoice List</h4>
        <a href="{{ route('purchase.inv_add') }}">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Purchase Invoice</button>
        </a>
    </div>

    <div class="row" id="summary-cards">
    <div class="col-sm-12 col-md-4 col-xl-4 mb-3">
        <div class="listcard">
            <div class="d-flex align-items-center justify-content-start gap-2 mb-2">
                <img src="{{ asset('assets/images/icon_4.png') }}" height="25px" alt="">
                <h6 class="text-blue">Total Purchases</h6>
            </div>
            <h5>₹ {{ number_format($totalPurchase, 0) }}</h5>
        </div>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-4 mb-3">
        <div class="listcard">
            <div class="d-flex align-items-center justify-content-start gap-2 mb-2">
                <img src="{{ asset('assets/images/icon_5.png') }}" height="25px" alt="">
                <h6 class="text-success">Paid</h6>
            </div>
            <h5>₹ {{ number_format($paidAmount, 0) }}</h5>
        </div>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-4 mb-3">
        <div class="listcard">
            <div class="d-flex align-items-center justify-content-start gap-2 mb-2">
                <img src="{{ asset('assets/images/icon_6.png') }}" height="25px" alt="">
                <h6 class="text-brown">Unpaid</h6>
            </div>
            <h5>₹ {{ number_format($unpaidAmount, 0) }}</h5>
        </div>
    </div>
</div>

    <div class="container-fluid listtable">
        <div class="filter-container">
            <div class="filter-container-start">
                <select class="headerDropdown form-select filter-option">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control filterInput" placeholder=" Search">
            </div>
            <form method="GET" id="filterForm">
    <div class="filter-container-end d-flex align-items-center flex-wrap gap-2">
        <select class="form-select" name="days" id="days" onchange="document.getElementById('filterForm').submit();">
            <option value="">Select Days</option>
            <option value="10" {{ $days == 10 ? 'selected' : '' }}>Last 10 Days</option>
            <option value="20" {{ $days == 20 ? 'selected' : '' }}>Last 20 Days</option>
            <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
            <option value="60" {{ $days == 60 ? 'selected' : '' }}>Last 60 Days</option>
        </select>
    </div>
</form>

        </div>

        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Invoice Number</th>
                        <th>Vendor Name</th>
                        <th>Due In</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
@foreach($invoices as $key => $invoice)
@php
    $billDate = \Carbon\Carbon::parse($invoice->bill_date)->startOfDay();
    $dueDate = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->startOfDay() : null;
    $today = \Carbon\Carbon::now()->startOfDay();

    $dueIn = '-';
    if ($dueDate) {
        if ($today->gt($dueDate)) {
            $daysOverdue = $today->diffInDays($dueDate);
            $dueIn = "Overdue by {$daysOverdue} day(s)";
        } else {
            $daysLeft = $today->diffInDays($dueDate);
            $dueIn = "{$daysLeft} day(s) left";
        }
    }

    $status = $invoice->balance_amount == 0 ? 'Paid' : 'Unpaid';
    $statusClass = $status === 'Paid' ? 'text-success' : 'text-danger';

    $vendorName = $invoice->purchaseOrder->vendor->vendorname ?? 'N/A';
@endphp


    <tr>
        <td>{{ $key + 1 }}</td>
<td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') }}</td>
        <td>{{ $invoice->id }}</td>
<td>{{ $invoice->purchaseOrder->vendor->vendorname ?? 'N/A' }}</td>
        <td>{{ $dueIn }}</td>
        <td>₹ {{ number_format($invoice->total, 2) }}</td>
        <td><span class="{{ $statusClass }}">{{ $status }}</span></td>
        <td>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('purchase.inv_profile', $invoice->id) }}" data-bs-toggle="tooltip" title="Profile">
            <i class="fas fa-arrow-up-right-from-square"></i>
        </a>

        <!-- Delete Form -->
        <form action="{{ route('purchase.inv_delete', $invoice->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
    </tr>
@endforeach
</tbody>

            </table>
        </div>
    </div>
</div>

<script>
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
</script>

@endsection