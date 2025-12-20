<div class="body-div p-3">
    <div class="cards mb-2">
        <div class="maincard row justify-content-between py-0 mb-3">
            <div class="cardhead my-3">
                <h5>Details</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Date</h6>
                <h5 class="mb-0">{{ \Carbon\Carbon::parse($order->bill_date)->format('d-m-Y') }}</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">PO No</h6>
                <h5 class="mb-0">{{ $order->bill_no }}</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Vendor Name</h6>
                <h5 class="mb-0">{{ $order->vendor->vendorname ?? '-' }}</h5>
            </div>
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Total Amount</h6>
                <h5 class="mb-0">₹ {{ number_format($order->total, 2) }}</h5>
            </div>
             <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <h6 class="mb-1">Paid Amount</h6>
                <h5 class="mb-0">₹ {{ number_format($order->paid_amount, 2) }}</h5>
            </div>
        </div>
    </div>

    <div class="body-head mt-3">
        <h4>Item List</h4>
    </div>

    <div class="container-fluid listtable">
        <div class="table-wrapper">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Items</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Discount</th>
                        <th>Tax</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->item->item_name ?? '-' }}</td>
                            <td>{{ $item->unit }}</td>
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