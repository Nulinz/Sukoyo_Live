@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Item Profile</h4>
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
        <h5 class="mb-2">{{ $item->item_name }}</h5>
<h6 class="mb-0">
    In Stock - {{ $currentStock }} {{ strtoupper($item->opening_unit) }}
</h6>
    </div>
</div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Right Content -->
        <div class="contentright">
           <div class="body-head my-2">
    <h4>{{ $item->brand->brand_name ?? '-' }} {{ $item->item_name }}</h4>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a data-bs-toggle="modal" data-bs-target="#barcode">
            <button class="exportbtn"><i class="fas fa-barcode pe-2"></i>Barcode Print</button>
        </a>
        <a data-bs-toggle="modal" data-bs-target="#adjuststock">
            <button class="exportbtn"><i class="fas fa-box pe-2"></i>Adjust Stock</button>
        </a>
    </div>
</div>

            <div class="proftabs d-flex align-items-center justify-content-between flex-wrap gap-2">
                <ul class="nav nav-tabs d-flex justify-content-start align-items-center gap-2 gap-lg-3" id="myTab"
                    role="tablist">
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs active" data-bs-toggle="tab" type="button" data-bs-target="#details">
                            <img src="{{ asset('assets/images/profile_info.png') }}" class="pe-1" height="13px" alt=""> Details
                        </button>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs" data-bs-toggle="tab" type="button" data-bs-target="#stock">
                            <img src="{{ asset('assets/images/profile_stock.png') }}" class="pe-1" height="13px" alt=""> Stock Details
                        </button>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs" data-bs-toggle="tab" type="button" data-bs-target="#uom">
                            <img src="{{ asset('assets/images/profile_uom.png') }}" class="pe-1" height="10px" alt=""> UOM
                        </button>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs" role="tab" data-bs-toggle="tab" type="button" data-bs-target="#batch">
                            <img src="{{ asset('assets/images/profile_batch.png') }}" class="pe-1" height="13px" alt="">Batch
                        </button>
                    </li>
                </ul>
                <div class="body-head d-flex align-items-center flex-wrap gap-3">
                    <h5 class="text-decoration-none">Current Stock: <span>{{ $currentStock }}</span></h5>
                    <h5 class="text-decoration-none">Stock Value: <span> ₹{{ number_format($stockValue, 2) }}</span></h5>
                </div>
            </div>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="details" role="tabpanel">
                    <div class="cards mb-2">
  <div class="cards mb-2">
    <!-- General Details -->
    <div class="maincard row py-0 mb-3">
        <div class="cardhead my-3">
            <h5>General Details</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Item Type</h6>
            <h5 class="mb-0">{{ $item->item_type }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Item Code</h6>
            <h5 class="mb-0">{{ $item->item_code }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Item Name</h6>
            <h5 class="mb-0">{{ $item->item_name }}</h5>
        </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">HSN Code</h6>
            <h5 class="mb-0">{{ $item->hsn_code }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Brand</h6>
            <h5 class="mb-0">{{ $item->brand->name ?? '-' }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Category</h6>
            <h5 class="mb-0">{{ $item->category->name ?? '-' }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Sub Category</h6>
            <h5 class="mb-0">{{ $item->subcategory->name ?? '-' }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Discount</h6>
            <h5 class="mb-0">{{ $item->discount }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Measuring Unit</h6>
            <h5 class="mb-0">{{ $item->measure_unit }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Opening Stock</h6>
            <h5 class="mb-0">{{ $item->opening_stock }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Low Stock Warning</h6>
            <h5 class="mb-0">{{ $item->stock_status }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Minimum Stock</h6>
            <h5 class="mb-0">{{ $item->min_stock }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Maximum Stock</h6>
            <h5 class="mb-0">{{ $item->max_stock }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">ABC Category</h6>
            <h5 class="mb-0">{{ $item->abc_category }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Item Description</h6>
            <h5 class="mb-0">{{ $item->item_description ?? 'Nil' }}</h5>
        </div>
    </div>

    <!-- Price Details -->
    <div class="maincard row py-0 mb-3">
        <div class="cardhead my-3">
            <h5>Price Details</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Sales Price</h6>
            <h5 class="mb-0">₹ {{ number_format($item->sales_price, 2) }} with GST</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Wholesale Price</h6>
            <h5 class="mb-0">₹ {{ number_format($item->wholesale_price, 2) }} with GST</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Purchase Price</h6>
<h5 class="mb-0">
    ₹ {{ number_format($item->purchase_price, 2) }} 
    {{ $item->purchase_tax ?? 'With Tax' }}
</h5>

        </div>
        <!--<div class="col-sm-12 col-md-4 col-xl-3 mb-3">-->
        <!--    <h6 class="mb-1">HSN Code</h6>-->
        <!--    <h5 class="mb-0">-</h5> {{-- You can update if HSN is available --}}-->
        <!--</div>-->
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">GST Tax Rate</h6>
            <h5 class="mb-0">{{ $item->gst_rate }}%</h5>
        </div>
    </div>
</div>


    

</div>
                </div>
              <div class="tab-pane fade" id="stock" role="tabpanel">
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
                <th>Transaction Type</th>
                <th>Vendor Name</th>
                <th>Bill No</th>
                <th>Bill Date</th>
                <th>Quantity</th>
                <th>Current Stock</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($transactions as $index => $txn)
            <tr>
                <td>{{ $index + 1 }}</td>
                
                <td>
                    {{ $txn->created_at ? \Carbon\Carbon::parse($txn->created_at)->format('d-m-Y') : \Carbon\Carbon::parse($txn->date)->format('d-m-Y') }}
                </td>
                
                <td>{{ $txn->transaction_type }}</td>
                
                <td>{{ $txn->vendor_name }}</td>
                
                <td>{{ $txn->invoice_no }}</td>
                
                <td>
                    @if($txn->bill_date && $txn->bill_date !== '-')
                        {{ \Carbon\Carbon::parse($txn->bill_date)->format('d-m-Y') }}
                    @else
                        {{ $txn->bill_date }}
                    @endif
                </td>
                
                <td>{{ $txn->qty }}</td>
                
                <td>
                    @if ($loop->last)
                        <strong>{{ $currentStock }}</strong>
                    @else
                        {{ $txn->closing_stock }}
                    @endif
                </td>
            </tr>
        @endforeach
        @if($transactions->isEmpty())
            <tr>
                <td colspan="8" class="text-center">No Stock Transactions Found</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
    </div>
</div>












<div class="tab-pane fade" id="uom" role="tabpanel">
    <div class="container-fluid mt-1 listtable">
        <div class="filter-container">
            <div class="filter-container-start">
                <select class="form-select filter-option" id="headerDropdown2">
                    <option value="All" selected>All</option>
                </select>
                <input type="text" class="form-control" id="filterInput2" placeholder=" Search">
            </div>
            <div class="filter-container-end">
                <a data-bs-toggle="modal" data-bs-target="#addUOM">
                    <button class="listbtn"><i class="fas fa-plus pe-2"></i> Add UOM</button>
                </a>
            </div>
        </div>
<br>
        <div class="table-wrapper">
            <table class="table table-bordered" id="table2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>UOM Type</th>
                        <th>Qty (Box)</th>
                        <th>Rate Per Box</th>
                        <th>Opening Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($uoms as $index => $uom)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $uom->uom_type }}</td>
                            <td>{{ $uom->qty }}</td>
                            <td>₹ {{ number_format($uom->rate_per_box, 2) }}</td>
                            <td>{{ $uom->closing_stock }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#editUOM"
                                       onclick="editUOM({{ $uom->id }})">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                     @if($uoms->isEmpty())
        <tr>
            <td colspan="6" class="text-center">No UOM Found</td>
        </tr>
    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add UOM Modal -->
    <div class="modal fade" id="addUOM" tabindex="-1" aria-labelledby="addUOMLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ url('/uom') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <div class="modal-header">
                        <h4 class="m-0">Add UOM</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="adduomtype">UOM Type</label>
                                <select class="form-select" name="uom_type" id="adduomtype" required>
                                    <option value="" selected disabled>Select Option</option>
                                    <option value="Box">Box</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Piece">Piece</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="addqty">Qty (Box)</label>
                                <input type="number" class="form-control" name="qty" id="addqty" min="0" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="addrate">Rate Per Box</label>
                                <input type="number" class="form-control" name="rate_per_box" id="addrate" min="0" required>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="addclosestock">Opening Stock</label>
                                <input type="number" class="form-control" name="closing_stock" id="addclosestock" min="0" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                <button type="submit" class="modalbtn w-50">Add UOM</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit UOM Modal -->
    <div class="modal fade" id="editUOM" tabindex="-1" aria-labelledby="editUOMLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="editUOMForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editUOMId" name="id">
                    <div class="modal-header">
                        <h4 class="m-0">Update UOM</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="edituomtype">UOM Type</label>
                                <select class="form-select" name="uom_type" id="edituomtype" required>
                                    <option value="Box">Box</option>
                                    <option value="Pack">Pack</option>
                                    <option value="Piece">Piece</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                                <label for="editqty">Qty (Box)</label>
                                <input type="number" class="form-control" name="qty" id="editqty" min="0" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label for="editrate">Rate Per Box</label>
                                <input type="number" class="form-control" name="rate_per_box" id="editrate" min="0" required>
                            </div>
                            <div class="col-sm-12 col-md-12 mb-2">
                                <label for="editclosestock">Opening Stock</label>
                                <input type="number" class="form-control" name="closing_stock" id="editclosestock" min="0" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                <button type="submit" class="modalbtn w-50">Update UOM</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editUOM(id) {
    fetch(`/uom/edit/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editUOMId').value = data.id;
            document.getElementById('edituomtype').value = data.uom_type;
            document.getElementById('editqty').value = data.qty;
            document.getElementById('editrate').value = data.rate_per_box;
            document.getElementById('editclosestock').value = data.closing_stock;
            document.getElementById('editUOMForm').action = `/uom/update/${data.id}`;
        });
}
</script>

                <div class="tab-pane fade" id="batch" role="tabpanel">
                    @include('inventory.prof_batch')
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Barcode Modal -->
<div class="modal fade" id="barcode" tabindex="-1" aria-labelledby="barcodeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Barcode Print</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="d-flex align-items-center justify-content-center flex-wrap gap-4 my-3">
                            <div class="barcodeimg">
                                <img src="{{ asset('assets/images/Barcode.png') }}" height="75px" alt="">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="printer">Printer</label>
                            <select class="form-select" name="" id="printer" required>
                                <option value="" selected disabled>Select Option</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="numbarcode">No Of Barcode</label>
                            <input type="number" class="form-control" name="" id="numbarcode" min="0" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <a href="" class="w-50"><button type="button" class="cancelbtn w-100">Download</button></a>
                            <button type="submit" class="modalbtn w-50">Print</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Ajsust Stock Modal -->
<div class="modal fade" id="adjuststock" tabindex="-1" aria-labelledby="adjuststockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Adjust Stock</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-4 mb-2 pe-2">
                            <label for="itemname">Item Name</label>
                            <input type="text" class="form-control" name="" id="itemname" required>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2 pe-2">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" name="" id="date" required>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2 pe-2">
                            <label for="addreduce">Add or Reduce Stock</label>
                            <select class="form-select" name="" id="addreduce" required>
                                <option value="" selected disabled>Select Option</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2 pe-2">
                            <label for="adjqty">Adjust Quantity</label>
                            <div class="inpselectflex">
                                <input type="number" class="form-control border-0" name="" id="adjqty" min="0" required>
                                <select class="form-select border-0" name="" id="units">
                                    <option value="PCS">PCS</option>
                                    <option value="BOX">BOX</option>
                                    <option value="NOS">NOS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-4 mb-2 pe-2">
                            <label for="crtstock">Current Stock</label>
                            <input type="text" class="form-control" name="" id="crtstock" required>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2 pe-2">
                            <label for="stockadd">Stock Added</label>
                            <input type="text" class="form-control" name="" id="stockadd" required>
                        </div>
                        <div class="col-sm-12 col-md-4 mb-2 pe-2">
                            <label for="updstock">Updated Stocks</label>
                            <input type="text" class="form-control" name="" id="updstock" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                        <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                        <button type="submit" class="modalbtn w-50">Adjust Stock</button>
                    </div>
                </form>
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
        initTable('#table2', '#headerDropdown2', '#filterInput2');
        initTable('#table3', '#headerDropdown3', '#filterInput3');
    });
</script>

@endsection