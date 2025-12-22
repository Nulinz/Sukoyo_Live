<div class="cards mb-2">
    <div class="maincard row py-0 mb-3">
        <div class="cardhead my-3">
            <h5>Details</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Store</h6>
            <h5 class="mb-0">{{ $sale->store_name }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Bill Type</h6>
            <h5 class="mb-0">{{ ucfirst($sale->status) }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">POS System</h6>
            <h5 class="mb-0">{{ $sale->employee_name }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Invoice No</h6>
            <h5 class="mb-0">{{ 'INV' . str_pad($sale->id, 4, '0', STR_PAD_LEFT) }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Customer Name</h6>
            <h5 class="mb-0">{{ $sale->customer->name ?? 'N/A' }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Date</h6>
            <h5 class="mb-0">{{ $sale->invoice_date->format('d-m-Y H:i') }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Payment Type</h6>
            <h5 class="mb-0">{{ $sale->mode_of_payment }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Discount</h6>
            <h5 class="mb-0">₹ {{ number_format($sale->total_discount, 2) }}</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Total Amount</h6>
            <h5 class="mb-0">₹ {{ number_format($sale->grand_total, 2) }}</h5>
        </div>
    </div>
</div>

<div class="body-head mt-3">
    <h4>Item List</h4>
</div>

<div class="container-fluid listtable">
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
                    <th>Items</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Amount</th>
                </tr>
            </thead>
<tbody>
                    @foreach ($sale->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->item->item_name ?? 'N/A' }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ $item->discount }}%</td>
                            <td>{{ $item->tax }}%</td>
                            <td>₹ {{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
</div>