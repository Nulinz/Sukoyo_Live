@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

<style>
    @media screen and (min-width: 990px) {
        .col-xl-3 {
            width: 25%;
        }
    }
</style>

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Purchase Order Profile</h4>
       
    </div>

    <div class="mainbdy d-block">

        <!-- Right Content -->
        <div class="contentright">
            <div class="tab-content">
                @include('purchase.order_prof_details')
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
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
            $(tableId + ' thead th').each(function(index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action") {
                    $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                }
            });
            $(filterInputId).on('keyup', function() {
                var selectedColumn = $(dropdownId).val();
                if (selectedColumn !== 'All') {
                    table.column(selectedColumn).search($(this).val()).draw();
                } else {
                    table.search($(this).val()).draw();
                }
            });
            $(dropdownId).on('change', function() {
                $(filterInputId).val('');
                table.search('').columns().search('').draw();
            });
            $(filterInputId).on('keyup', function() {
                table.search($(this).val()).draw();
            });
        }
        // Initialize each table
        initTable('#table1', '#headerDropdown1', '#filterInput1');
    });
</script>

@endsection