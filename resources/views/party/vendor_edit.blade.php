@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Update Vendor</h4>
    </div>

    <form action="{{ route('party.vendorupdate', $vendor->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="container-fluid form-div">

            <div class="body-head mb-3">
                <h5>General Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="vendorname">Vendor Name <span>*</span></label>
                    <input type="text" class="form-control" name="vendorname" id="vendorname" value="{{ old('vendorname', $vendor->vendorname) }}" required autofocus>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="contact">Contact Number <span>*</span></label>
                    <input type="text" class="form-control" name="contact" id="contact" value="{{ old('contact', $vendor->contact) }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="email">Email ID <span>*</span></label>
                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $vendor->email) }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="openbalance">Opening Balance </label>
                    <div class="inpselectflex">
                        <input type="number" class="form-control border-0" name="openbalance" id="openbalance" value="{{ old('openbalance', $vendor->openbalance) }}" min="0" >
                        <select class="form-select border-0 text-uppercase" name="tax" id="tax">
                            <option value="With Tax" {{ old('tax', $vendor->tax) == 'With Tax' ? 'selected' : '' }}>With Tax</option>
                            <option value="Without Tax" {{ old('tax', $vendor->tax) == 'Without Tax' ? 'selected' : '' }}>Without Tax</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Payment </label>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <div class="form-check d-flex align-items-center gap-2">
                            <input class="form-check-input mb-auto" type="checkbox" name="topay" id="topay" value="1" {{ old('topay', $vendor->topay) ? 'checked' : '' }}>
                            <label class="form-check-label mb-0" for="topay">To Pay</label>
                        </div>
                        <div class="form-check d-flex align-items-center gap-2">
                            <input class="form-check-input mb-auto" type="checkbox" name="tocollect" id="tocollect" value="1" {{ old('tocollect', $vendor->tocollect) ? 'checked' : '' }}>
                            <label class="form-check-label mb-0" for="tocollect">To Collect</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="gst">GSTIN <span>*</span></label>
                    <input type="text" class="form-control" name="gst" id="gst" value="{{ old('gst', $vendor->gst) }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="panno">Pan Card Number </label>
                    <input type="text" class="form-control" name="panno" id="panno" value="{{ old('panno', $vendor->panno) }}" >
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="creditperiod">Credit Period <span>*</span></label>
                    <div class="inpselectflex">
                        <input type="number" class="form-control border-0" name="creditperiod" id="creditperiod" value="{{ old('creditperiod', $vendor->creditperiod) }}" min="0" required>
                        <h6 class="mb-0 text-center px-0">Days</h6>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="creditlimit">Credit Limit</label>
                    <input type="number" class="form-control" name="creditlimit" id="creditlimit" value="{{ old('creditlimit', $vendor->creditlimit) }}" >
                </div>
            </div>

            <hr>

            <div class="body-head mb-3">
                <h5>Pricing Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                    <label for="billaddress">Billing Address </label>
                    <textarea rows="2" class="form-control" name="billaddress" id="billaddress" >{{ old('billaddress', $vendor->billaddress) }}</textarea>
                </div>
                <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                    <label for="shipaddress">Shipping Address</label>
                    <textarea rows="2" class="form-control" name="shipaddress" id="shipaddress" >{{ old('shipaddress', $vendor->shipaddress) }}</textarea>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Update Vendor</button>
            </div>
        </div>
    </form>
</div>

@endsection
