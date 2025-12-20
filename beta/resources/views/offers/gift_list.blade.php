@extends('layouts.app')

@section('content')

    <div class="body-div p-3">
        <div class="body-head">
            <h4>Gift Card List</h4>
            <a href="{{ route('offers.giftadd') }}">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Gift Card</button>
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
                            <th>Card Code</th>
                            <th>No Of Cards</th>
                            <th>Card Type</th>
                            <th>Value</th>
                            <th>Reloadable</th>
                            <th>Issued Date</th>
                            <th>Expiry Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                   <tbody>
                        @foreach ($giftcards as $index => $gift)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $gift->card_code }}</td>
                                <td>{{ $gift->no_of_cards }}</td>
                                <td>{{ $gift->card_type }}</td>
                                <td>₹ {{ number_format($gift->card_value, 2) }}</td>
                                <td>{{ $gift->reloadable ? 'Yes' : 'No' }}</td>
                                <td>{{ \Carbon\Carbon::parse($gift->issue_date)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($gift->expiry_date)->format('d-m-Y') }}</td>
<td>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('offers.giftprofile', ['id' => $gift->id]) }}" 
           data-bs-toggle="tooltip" 
           data-bs-title="Profile">
            <i class="fas fa-arrow-up-right-from-square"></i>
        </a>
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


