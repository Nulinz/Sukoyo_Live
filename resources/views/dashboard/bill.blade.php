@extends('layouts.app_pos')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/dashboard_main.css') }}">

    <div class="body-div p-3">


        <!-- Cards -->
        <div class="container-fluid px-0">
            <div class="row d-flex flex-wrap" id="main_card">

                <div class="body-head mb-3">
                    <h4 class="m-0">POS - 1</h4>
                </div>
            <div class="col-sm-6 col-md-4 col-xl-4 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h6>Today Bill</h6>
                <h5>{{ $todayBillCount }}</h5>
            </div>
            <img src="{{ asset('assets/images/icon_10.png') }}" height="50px" alt="">
        </div>
    </div>
</div>
<div class="col-sm-6 col-md-4 col-xl-4 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h6>Today Sales</h6>
                <h5>₹ {{ number_format($todaySalesTotal, 2) }}</h5>
            </div>
            <img src="{{ asset('assets/images/icon_11.png') }}" height="50px" alt="">
        </div>
    </div>
</div>
<div class="col-sm-6 col-md-4 col-xl-4 mb-3 cards">
    <div class="cardsdiv">
        <div class="cardsmain">
            <div>
                <h6>Loyalty Redeemed</h6>
                <h5>₹ {{ number_format($todayLoyaltyRedeemed, 2) }}</h5>
            </div>
            <img src="{{ asset('assets/images/icon_12.png') }}" height="50px" alt="">
        </div>
    </div>
</div>


            </div>
        </div>

        <div class="body-head my-2">
            <h4 class="m-0">Recent Bill</h4>
        </div>

        <div class="container-fluid listtable">
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
                            <th>Name</th>
                            <th>Invoice No</th>
                            <th>Items</th>
                            <th>Total Bill</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                   <tbody>
   @foreach($recentBills as $index => $bill)
    <tr onclick="window.location='{{ route('invoice.details', $bill->id) }}'" style="cursor:pointer;">
        <td>{{ $index + 1 }}</td>
        <td>{{ $bill->customer->name ?? 'Walk-in' }}</td>
        <td>{{ 'INV' . str_pad($bill->id, 4, '0', STR_PAD_LEFT) }}</td>
        <td>{{ $bill->items->count() }}</td>
        <td>{{ number_format($bill->grand_total, 2) }}</td>
        <td>₹ {{ number_format($bill->received_amount, 2) }}</td>
    </tr>
@endforeach

</tbody>

                </table>
            </div>
        </div>

    </div>

    <script>
        // DataTables List
        $(document).ready(function () {
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
        $(document).ready(function () {
            var table = $('.example').DataTable();
            $('.example thead th').each(function (index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action" && headerText.toLowerCase() != "progress") {
                    $('.headerDropdown').append('<option value="' + index + '">' + headerText + '</option>');
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
        });
    </script>

@endsection