@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

    <div class="body-div p-3">
        <div class="body-head mb-3">
            <h4>Bank Profile</h4>
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
    @foreach($bankAccounts as $bank)
    <div class="col-sm-12 col-md-12 col-xl-12 mb-2">
        <h5 class="mb-2">{{ $bank->bank_name }}</h5>
        <h6 class="mb-0">₹ {{ number_format($bank->balance, 2) }}</h6>
    </div>
    @endforeach
</div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Content -->
            <div class="contentright">
                <div class="body-head my-2">
                    <h4>State Bank Of India</h4>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <a data-bs-toggle="modal" data-bs-target="#adjMoney">
                            <button class="exportbtn"><i class="fas fa-plus pe-2"></i>Add / Reduce Money</button>
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#addTransfer">
                            <button class="exportbtn"><i class="fas fa-right-left pe-2"></i>Transfer</button>
                        </a>
                        <a data-bs-toggle="modal" data-bs-target="#addBank">
                            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Bank Account</button>
                        </a>
                    </div>
                </div>

                <div class="tab-content">
                    <!--<div class="cards mb-2">-->
                    <!--    <div class="maincard row py-0 mb-3">-->
                    <!--        <div class="cardhead my-3">-->
                    <!--            <h5>Bank Details</h5>-->
                    <!--        </div>-->
                    <!--        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">-->
                    <!--            <h6 class="mb-1">Bank Name</h6>-->
                    <!--            <h5 class="mb-0">State Bank Of India</h5>-->
                    <!--        </div>-->
                    <!--        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">-->
                    <!--            <h6 class="mb-1">Acct Holder Name</h6>-->
                    <!--            <h5 class="mb-0">Sabari</h5>-->
                    <!--        </div>-->
                    <!--        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">-->
                    <!--            <h6 class="mb-1">Account Number</h6>-->
                    <!--            <h5 class="mb-0">SBI123456789</h5>-->
                    <!--        </div>-->
                    <!--        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">-->
                    <!--            <h6 class="mb-1">IFSC Code</h6>-->
                    <!--            <h5 class="mb-0">IFSC123456789</h5>-->
                    <!--        </div>-->
                    <!--        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">-->
                    <!--            <h6 class="mb-1">Branch Name</h6>-->
                    <!--            <h5 class="mb-0">Salem</h5>-->
                    <!--        </div>-->
                    <!--        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">-->
                    <!--            <h6 class="mb-1">UPI ID</h6>-->
                    <!--            <h5 class="mb-0">sabari@okaxis</h5>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <div class="body-head my-2">
                        <h4>Transactions</h4>
                    </div>
                    <div class="container-fluid mt-1 listtable">
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
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\BankTransfer::latest()->take(10)->get() as $transfer)
                    <tr>
                        <td>{{ $transfer->date }}</td>
                        <td>{{ $transfer->fromBank->bank_name }}</td>
                        <td>{{ $transfer->toBank->bank_name }}</td>
                        <td>₹{{ number_format($transfer->amount, 2) }}</td>
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

    <!-- Add Transfer Modal -->
    <div class="modal fade" id="addTransfer" tabindex="-1" aria-labelledby="addTransferLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Add Transfer</h4>
                </div>
                <div class="modal-body">
<form action="{{ route('bank.transfer.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="transferfrom">Transfer From</label>
            <select class="form-select" name="transfer_from" id="transferfrom" required>
                <option value="" disabled selected>Select Bank</option>
                @foreach($bankAccounts as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->bank_name }} (₹{{ number_format($bank->balance, 2) }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="transferto">Transfer To</label>
            <select class="form-select" name="transfer_to" id="transferto" required>
                <option value="" disabled selected>Select Bank</option>
                @foreach($bankAccounts as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->bank_name }} (₹{{ number_format($bank->balance, 2) }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date" required>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="amt">Amount</label>
            <input type="number" class="form-control" name="amount" id="amt" step="0.01" required>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
            <button type="submit" class="modalbtn w-50">Add Transfer</button>
        </div>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addBank" tabindex="-1" aria-labelledby="addBankLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Add Bank Account</h4>
                </div>
                <div class="modal-body">
<form action="{{ route('accounts.bank_account.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="bankname">Bank Name</label>
            <input type="text" class="form-control" name="bank_name" id="bankname" required>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="acctholder">Account Holder Name</label>
            <input type="text" class="form-control" name="account_holder" id="acctholder" required>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="acctno">Account Number</label>
            <input type="text" class="form-control" name="account_number" id="acctno" required>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="ifsc">IFSC Code</label>
            <input type="text" class="form-control" name="ifsc_code" id="ifsc" required>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="branch">Branch Name</label>
            <input type="text" class="form-control" name="branch_name" id="branch" required>
        </div>
        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
            <label for="upiid">UPI ID</label>
            <input type="text" class="form-control" name="upi_id" id="upiid">
        </div>

        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
            <button type="submit" class="modalbtn w-50">Add Bank Account</button>
        </div>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>

    <!-- Adjust Money Modal -->
    <div class="modal fade" id="adjMoney" tabindex="-1" aria-labelledby="adjMoneyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Adjust Balance</h4>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="adjstmoney">Adjust Money In</label>
                                <select class="form-select" name="" id="adjstmoney" required>
                                    <option value="" selected disabled>Select Option</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="addreduce">Add Or Reduce</label>
                                <div class="d-flex align-items-center gap-3 mt-1">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input mb-auto" type="checkbox" value="" id="add">
                                        <label class="form-check-label mb-0" for="add">Add Money</label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input mb-auto" type="checkbox" value="" id="reduce">
                                        <label class="form-check-label mb-0" for="reduce">Reduce Money</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="adjdate">Date</label>
                                <input type="date" class="form-control" name="" id="adjdate" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="crtbalance">Current Balance</label>
                                <input type="number" class="form-control" name="" id="crtbalance" min="0" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="adjamt">Enter Amount</label>
                                <input type="number" class="form-control" name="" id="adjamt" min="0" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="newbalance">New Balance</label>
                                <input type="number" class="form-control" name="" id="newbalance" min="0" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                <button type="submit" class="modalbtn w-50">Adjust Money</button>
                            </div>
                        </div>
                    </form>
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