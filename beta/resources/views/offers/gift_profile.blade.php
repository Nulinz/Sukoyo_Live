@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

    <style>
        @media screen and (min-width: 990px) {
            .col-xl-3 {
                width: 20%;
            }
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-expired {
            color: #dc3545;
            font-weight: bold;
        }
    </style>

    <div class="body-div p-3">
        <div class="body-head mb-3">
            <h4>Gift Card Profile - {{ $giftCard->card_code }}</h4>
        </div>

        <div class="mainbdy d-block">

            <!-- Right Content -->
            <div class="contentright">
                <div class="tab-content">
                    <div class="cards mb-2">
                        <div class="maincard row py-0 mb-3">
                            <div class="cardhead my-3">
                                <h5>Details</h5>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Card Code</h6>
                                <h5 class="mb-0">{{ $giftCard->card_code }}</h5>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Card Type</h6>
                                <h5 class="mb-0">{{ $giftCard->card_type ?? 'Classic' }}</h5>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Value</h6>
                                <h5 class="mb-0">₹ {{ number_format($giftCard->value, 2) }}</h5>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Issued Date</h6>
                                <h5 class="mb-0">{{ $giftCard->created_at->format('d-m-Y') }}</h5>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Used Amount</h6>
                                <h5 class="mb-0">₹ {{ number_format($totalUsed, 2) }}</h5>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Balance</h6>
                                <h5 class="mb-0">₹ {{ number_format($balance, 2) }}</h5>
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Status</h6>
                                <h5 class="mb-0 {{ $status == 'Active' ? 'status-active' : 'status-expired' }}">{{ $status }}</h5>
                            </div>
                            @if($giftCard->customer)
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Customer</h6>
                                <h5 class="mb-0">{{ $giftCard->customer->name ?? 'N/A' }}</h5>
                            </div>
                            @endif
                            @if($giftCard->employee)
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Issued By</h6>
                                <h5 class="mb-0">{{ $giftCard->employee->empname ?? 'N/A' }}</h5>
                            </div>
                            @endif
                            @if($giftCard->store)
                            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                                <h6 class="mb-1">Store</h6>
                                <h5 class="mb-0">{{ $giftCard->store->store_name ?? 'N/A' }}</h5>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="container-fluid mt-3 listtable">
                        <div class="filter-container">
                            <div class="filter-container-start">
                                <select class="form-select filter-option" id="headerDropdown1">
                                    <option value="All" selected>All</option>
                                </select>
                                <input type="text" class="form-control" id="filterInput1" placeholder=" Search">
                            </div>
                        </div>

                        <div class="table-wrapper">
                            <table class="table table-bordered" id="table1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Name</th>
                                        <th>Employee</th>
                                        <th>Store</th>
                                        <th>Transaction Date</th>
                                        <th>Amount Used</th>
                                        <th>Invoice Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $index => $transaction)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $transaction->customer->name ?? 'Walk-in Customer' }}</td>
                                        <td>{{ $transaction->employee->empname ?? 'N/A' }}</td>
                                        <td>{{ $transaction->store->store_name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->invoice_date->format('d-m-Y H:i') }}</td>
                                        <td>₹ {{ number_format($transaction->gift_card_amount, 2) }}</td>
                                        <td>₹ {{ number_format($transaction->grand_total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No transactions found for this gift card</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function () {
            function initTable(tableId, dropdownId, filterInputId) {
                var table = $(tableId).DataTable({
                    "paging": false,
                    "searching": true,
                    "ordering": true,
                    "order": [0, "asc"],
                    "bDestroy": true,
                    "info": false,
                    "responsive": true,
                    "pageLength": 30,
                    "dom": '<"top"f>rt<"bottom"ilp><"clear">',
                });
                
                // Populate dropdown with column headers
                $(tableId + ' thead th').each(function (index) {
                    var headerText = $(this).text();
                    if (headerText != "" && headerText.toLowerCase() != "action") {
                        $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                    }
                });
                
                // Column-specific search
                $(filterInputId).on('keyup', function () {
                    var selectedColumn = $(dropdownId).val();
                    if (selectedColumn !== 'All') {
                        table.column(selectedColumn).search($(this).val()).draw();
                    } else {
                        table.search($(this).val()).draw();
                    }
                });
                
                // Reset search when dropdown changes
                $(dropdownId).on('change', function () {
                    $(filterInputId).val('');
                    table.search('').columns().search('').draw();
                });
            }
            
            // Initialize the table
            initTable('#table1', '#headerDropdown1', '#filterInput1');
        });
    </script>

@endsection