@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/dashboard_main.css') }}">

<div class="body-div p-3">

    @include('dashboard.dashboard_tabs')

    <div class="body-head mb-3">
        <h4 class="m-0">POS Dashboard</h4>
    </div>

    <div class="container-fluid px-0">
        <div class="row d-flex flex-wrap" id="main_card">
            @forelse($storeData as $data)
            <div class="col-sm-6 col-md-4 mb-3 cards">
                <div class="cardsdiv">
                    <div class="cardshead">
                        <div>
                            <h6>{{ $data['store']->store_name }}</h6>
                            <h5>No Of POS - {{ str_pad($data['pos_count'], 2, '0', STR_PAD_LEFT) }}</h5>
                        </div>
                        <div>
                            <h6 class="text-end">Total</h6>
                            <h5 class="text-end">₹ {{ number_format($data['grand_total'], 0) }}</h5>
                        </div>
                    </div>
                    <div class="cardssub">
                        <div>
                            <h6 class="text-start">Cash</h6>
                            <h5 class="text-start">₹ {{ number_format($data['cash_total'], 0) }}</h5>
                        </div>
                        <div class="brdr"></div>
                        <div>
                            <h6 class="text-center">Online</h6>
                            <h5 class="text-center">₹ {{ number_format($data['online_total'], 0) }}</h5>
                        </div>
                        <div class="brdr"></div>
                        <div>
                            <h6 class="text-end">Redeemed</h6>
                            <h5 class="text-end">₹ {{ number_format($data['redeemed_total'], 0) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h5>No Store Data Available</h5>
                    <p>Please add stores and POS systems to view dashboard data.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <div class="body-head my-2">
        <h4 class="m-0">Top Performance List</h4>
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
                        <th>Store</th>
                        <th>Total Bills</th>
                        <th>Sales Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topPerformers as $index => $performer)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $performer['employee']->empname }}</td>
                        <td>{{ $performer['store_name'] }}</td>
                        <td>{{ $performer['total_bills'] }}</td>
                        <td>₹ {{ number_format($performer['total_sales'], 0) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No performance data available for today</td>
                    </tr>
                    @endforelse
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