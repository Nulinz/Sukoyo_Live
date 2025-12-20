  <div class="cards mb-2">
        <div class="maincard row justify-content-between py-0 mb-3">
            <div class="cardhead my-3">
                <h5>Invoice Details</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Date</h6>
                <h5 class="mb-0">{{ $billDate }}</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Invoice No</h6>
                <h5 class="mb-0">{{ $invoice->id }}</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Vendor Name</h6>
                <h5 class="mb-0">{{ $invoice->purchaseOrder->vendor->vendorname ?? 'N/A' }}</h5>
            </div>
            <!-- <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Due In</h6>
                <h5 class="mb-0">{{ $dueIn }}</h5>
            </div> -->
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Status</h6>
                <h5 class="mb-0">{{ $status }}</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Total Amount</h6>
                <h5 class="mb-0">₹ {{ number_format($invoice->total, 2) }}</h5>
            </div>
        </div>
    </div>

    {{-- Item List --}}
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
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Discount (%)</th>
                        <th>Tax (%)</th>
                        <th>Amount</th>
                    </tr>
                </thead>
<tbody>
    @foreach($items as $key => $item)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $item->item_name ?? 'N/A' }}</td>
            <td>{{ $item->unit ?? 'N/A' }}</td>
            <td>{{ $item->qty }}</td>
            <td>₹ {{ number_format($item->price, 2) }}</td>
            <td>{{ $item->discount }}%</td>
            <td>{{ $item->tax }}%</td>
            <td>₹ {{ number_format($item->amount, 2) }}</td>
        </tr>
    @endforeach
</tbody>

            </table>
        </div>
    </div>
</div>
