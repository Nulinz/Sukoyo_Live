@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

    <div class="body-div p-3">
        <div class="body-head mb-3">
            <h4>Student Profile</h4>
        </div>

        <div class="mainbdy">

            <!-- Left Content -->
            <div class="contentleft mb-3">
                <div class="cards mt-2">

                    <div class="basicdetails mb-3">
                        <div class="maincard">
                            <div class="form-div p-0 mb-4">
                                <div class="inpflex">
                                    <input type="search" class="form-control border-0 py-1 px-2" name="search" id="">
                                    <i class="fas fa-search text-center"></i>
                                </div>
                            </div>
                            <div class="leftcard">
                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2">
                                    <h5 class="mb-2">Dhanush</h5>
                                    <h6 class="mb-0">Pottery for Beginners</h6>
                                </div>
                                <div class="col-sm-12 col-md-12 col-xl-12 mb-2">
                                    <h5 class="mb-2">Arun</h5>
                                    <h6 class="mb-0">Pottery for Beginners</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Content -->
            <div class="contentright">
                <div class="body-head my-2">
                    <h4>Dhanush</h4>
                </div>
                <div class="proftabs">
                    <ul class="nav nav-tabs d-flex justify-content-start align-items-center gap-2 gap-lg-3" id="myTab"
                        role="tablist">
                        <li class="nav-item mb-2" role="presentation">
                            <button class="profiletabs active" data-bs-toggle="tab" type="button" data-bs-target="#details">
                                <img src="{{ asset('assets/images/profile_info.png') }}" class="pe-1" height="13px" alt="">
                                Details
                            </button>
                        </li>
                        <li class="nav-item mb-2" role="presentation">
                            <button class="profiletabs" data-bs-toggle="tab" type="button" data-bs-target="#payment">
                                <img src="{{ asset('assets/images/profile_card.png') }}" class="pe-1" height="13px" alt="">
                                Payment
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        @include('class.std_prof_details')
                    </div>
                    <div class="tab-pane fade" id="payment" role="tabpanel">
                        @include('class.std_prof_pay')
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
                $(tableId + ' thead th').each(function (index) {
                    var headerText = $(this).text();
                    if (headerText != "" && headerText.toLowerCase() != "action") {
                        $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                    }
                });
                $(filterInputId).on('keyup', function () {
                    var selectedColumn = $(dropdownId).val();
                    if (selectedColumn !== 'All') {
                        table.column(selectedColumn).search($(this).val()).draw();
                    } else {
                        table.search($(this).val()).draw();
                    }
                });
                $(dropdownId).on('change', function () {
                    $(filterInputId).val('');
                    table.search('').columns().search('').draw();
                });
                $(filterInputId).on('keyup', function () {
                    table.search($(this).val()).draw();
                });
            }
            // Initialize each table
            initTable('#table1', '#headerDropdown1', '#filterInput1');
        });
    </script>

@endsection