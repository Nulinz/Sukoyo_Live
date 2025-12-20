@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Expense Profile</h4>
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
    @foreach($expenseCategories as $cat)
    <div class="col-sm-12 col-md-12 col-xl-12 mb-2 expense-category-card" data-id="{{ $cat->id }}">
        <h5 class="mb-2">{{ $cat->name }}</h5>
        <h6 class="mb-0">₹ {{ number_format($categoryTotals[$cat->id] ?? 0, 2) }}</h6>
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
                <div>
                    <h4 class="mb-1">Expenses Details</h4>
                    <!-- <h6>Indirect Expense</h6> -->
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <a data-bs-toggle="modal" data-bs-target="#addExpcat">
                        <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Expense Category</button>
                    </a>
                    <a data-bs-toggle="modal" data-bs-target="#addExpense">
                        <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Expenses</button>
                    </a>
                </div>
            </div>

            <div class="tab-content">
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
            <th>#</th>
            <th>Date</th>
            <th>Expense No</th>
            <th>Name</th>
            <th>Payment Type</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody id="expense-table-body">
        @foreach($expenses as $index => $expense)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
            <td>{{ $expense->expense_no }}</td>
            <td>{{ $expense->vendor->vendorname ?? '-' }}</td>
            <td>{{ $expense->payment_type ?? '-' }}</td>
            <td>₹ {{ number_format($expense->amount, 2) }}</td>
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

<!-- Add Expense Category Modal -->
<div class="modal fade" id="addExpcat" tabindex="-1" aria-labelledby="addExpcatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Expense Category</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('expense.category.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="addexpcat">Expense Category</label>
                            <input type="text" class="form-control" name="name" id="addexpcat" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="addexptype">Expense Type</label>
                            <select class="form-select" name="type" id="addexptype">
                                <option value="" selected disabled>Select Option</option>
                                <option value="Direct">Direct</option>
                                <option value="Indirect">Indirect</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Expense Category</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpense" tabindex="-1" aria-labelledby="addExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Expenses</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('expense.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="expcat">Expense Category</label>
                            <select class="form-select" name="expense_category_id" id="expcat" required>
                                <option value="" selected disabled>Select Expense Category</option>
                                @foreach($expenseCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="expno">Expense No</label>
                            <input type="text" class="form-control" name="expense_no" id="expno" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="vendor">Vendor</label>
                            <select class="form-select" name="vendor_id" id="vendor" required>
                                <option value="" selected disabled>Select Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->vendorname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="paytype">Payment Type</label>
                            <select class="form-select" name="payment_type" id="paytype" required>
                                <option value="" selected disabled>Select Option</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="amt">Amount</label>
                            <input type="number" class="form-control" name="amount" id="amt" min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="balance">Balance</label>
                            <input type="number" class="form-control" name="balance" id="balance" min="0" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Expenses</button>
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
        }

        initTable('#table1', '#headerDropdown1', '#filterInput1');
    });
</script>
<script>
    $(document).ready(function () {
        $('.expense-category-card').on('click', function () {
            let categoryId = $(this).data('id');

            $.ajax({
                url: "{{ route('expenses.by.category') }}",
                type: "GET",
                data: { category_id: categoryId },
                success: function (response) {
                    $('#expense-table-body').html(response);
                },
                error: function () {
                    alert("Failed to load expenses. Please try again.");
                }
            });
        });
    });
</script>

@endsection
