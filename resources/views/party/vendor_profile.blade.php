@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Vendor Profile</h4>
    </div>

    <div class="body-div p-3">
        <div class="contentright">
            <div class="body-head my-2">
                <h4>{{ $vendor->vendorname }}</h4>
            </div>

            <!-- Tabs -->
            <div class="proftabs">
                <ul class="nav nav-tabs d-flex justify-content-start align-items-center gap-2 gap-lg-3" id="myTab" role="tablist">
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs active" data-bs-toggle="tab" type="button" data-bs-target="#details">
                            <img src="{{ asset('assets/images/profile_user.png') }}" class="pe-1" height="13px" alt=""> Profile
                        </button>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs" data-bs-toggle="tab" type="button" data-bs-target="#purchase">
                            <img src="{{ asset('assets/images/profile_bag.png') }}" class="pe-1" height="13px" alt=""> Purchase List
                        </button>
                    </li>
                    <li class="nav-item mb-2" role="presentation">
                        <button class="profiletabs" data-bs-toggle="tab" type="button" data-bs-target="#transaction">
                            <img src="{{ asset('assets/images/profile_card.png') }}" class="pe-1" height="10px" alt=""> Transaction
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="details" role="tabpanel">
                    <div class="cards mb-2">
                        <div class="maincard row py-0 mb-3">
                            <div class="cardhead my-3">
                                <h5>General Details</h5>
                            </div>
                          <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Vendor Name</h6>
    <h5 class="mb-0">{{ $vendor->vendorname }}</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Contact Number</h6>
    <h5 class="mb-0">{{ $vendor->contact }}</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Email ID</h6>
    <h5 class="mb-0">{{ $vendor->email ?? '-' }}</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Due Amount</h6>
    <h5 class="mb-0">₹ {{ number_format($vendor->topay, 2) }}</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Credit Period</h6>
    <h5 class="mb-0">{{ $vendor->creditperiod }} Days</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Credit Limit</h6>
    <h5 class="mb-0">₹ {{ number_format($vendor->creditlimit, 2) }}</h5>
</div>

                        </div>

                        <div class="maincard row py-0 mb-3">
                            <div class="cardhead my-3">
                                <h5>Business Details</h5>
                            </div>
                          <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">GSTIN</h6>
    <h5 class="mb-0">{{ $vendor->gst ?? '-' }}</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Pan Card Number</h6>
    <h5 class="mb-0">{{ $vendor->panno ?? '-' }}</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Billing Address</h6>
    <h5 class="mb-0">{{ $vendor->billaddress ?? '-' }}</h5>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <h6 class="mb-1">Shipping Address</h6>
    <h5 class="mb-0">{{ $vendor->shipaddress ?? '-' }}</h5>
</div>

                        </div>
                    </div>
                </div>

                <!-- Purchase List Tab -->
                <div class="tab-pane fade" id="purchase" role="tabpanel">
                    <div class="container-fluid listtable pt-0">
                        <div class="table-wrapper">
                            <table class="table table-bordered" id="table1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>PO No</th>
                                        <th>Items</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Discount</th>
                                        <th>Tax</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseInvoices as $index => $invoice)
                                        @foreach($invoice->purchaseInvoiceItems as $item)
                                        <tr>
                                            <td>{{ $loop->parent->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($invoice->bill_date)->format('d-m-Y') }}</td>
                                            <td>{{ $invoice->bill_no }}</td>
                                            <td>{{ $item->itemDetails->item_name ?? $item->item }}</td>
                                            <td>{{ $item->unit }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ $item->discount }}%</td>
                                            <td>{{ $item->tax }}%</td>
                                            <td>₹ {{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Transaction Tab -->
                <div class="tab-pane fade" id="transaction" role="tabpanel">
                    @include('party.vendor_prof_transaction')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#table1').DataTable();
    });
</script>

@endsection
