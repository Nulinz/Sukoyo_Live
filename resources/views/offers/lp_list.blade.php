@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head">
            <h4>Loyalty Points</h4>
            <a href="{{ route('offers.lpedit') }}">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i>Set Loyalty Points</button>
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
                            <th>Points Earned</th>
                            <th>Points Claimed</th>
                            <th>min_invoice_for_earning</th>
                            <th>Available Points</th>
                        </tr>
                    </thead>
                        <tbody>
                            @foreach($loyaltyPoints as $index => $lp)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $lp->earn_points }} for ₹{{ $lp->earn_amt }}</td>
                                    <td>{{ $lp->redeem_points }} for ₹{{ $lp->redeem_amt }}</td>
                                    <td>₹{{ $lp->min_invoice_for_earning }}</td>
                                    <td>₹{{ $lp->redeem_amt }}</td>
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