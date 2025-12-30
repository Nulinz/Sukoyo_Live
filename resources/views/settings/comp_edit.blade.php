@extends('layouts.app')

@section('content')
<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Edit Company Profile</h4>
    </div>

    <form action="{{ route('settings.companyupdate') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="row">
            <!-- Company Details -->
            <div class="col-md-4 mb-3">
                <label>Business Type</label>
                <input type="text" name="business_type" class="form-control" value="{{ $company->business_type }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Company Name</label>
                <input type="text" name="company_name" class="form-control" value="{{ $company->company_name }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Owner Name</label>
                <input type="text" name="owner_name" class="form-control" value="{{ $company->owner_name }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Company Logo</label>
                <input type="file" name="company_logo" class="form-control">
                @if($company->company_logo)
                    <img src="{{ asset('public/uploads/logos/' . $company->company_logo) }}" height="40" class="mt-2">
                @endif
            </div>

            <!-- Contact -->
            <div class="col-md-4 mb-3">
                <label>Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="{{ $company->contact_number }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Alternate Contact</label>
                <input type="text" name="alternate_contact_number" class="form-control" value="{{ $company->alternate_contact_number }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $company->email }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Website URL</label>
                <input type="text" name="website_url" class="form-control" value="{{ $company->website_url }}">
            </div>

            <!-- Address -->
            <div class="col-md-4 mb-3">
                <label>Address</label>
                <input type="text" name="address" class="form-control" value="{{ $company->address }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="{{ $company->city }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>State</label>
                <input type="text" name="state" class="form-control" value="{{ $company->state }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Pincode</label>
                <input type="text" name="pincode" class="form-control" value="{{ $company->pincode }}">
            </div>

            <!-- Tax -->
            <div class="col-md-4 mb-3">
                <label>GST Number</label>
                <input type="text" name="gst_number" class="form-control" value="{{ $company->gst_number }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>PAN Card</label>
                <input type="text" name="pan_card_number" class="form-control" value="{{ $company->pan_card_number }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>CIN/LLP Number</label>
                <input type="text" name="cin_llp_number" class="form-control" value="{{ $company->cin_llp_number }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Trade License</label>
                <input type="text" name="trade_license_number" class="form-control" value="{{ $company->trade_license_number }}">
            </div>

            <!-- Bank -->
            <div class="col-md-4 mb-3">
                <label>Bank Name</label>
                <input type="text" name="bank_name" class="form-control" value="{{ $company->bank_name }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Account Holder Name</label>
                <input type="text" name="account_holder_name" class="form-control" value="{{ $company->account_holder_name }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Account Number</label>
                <input type="text" name="account_number" class="form-control" value="{{ $company->account_number }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>IFSC Code</label>
                <input type="text" name="ifsc_code" class="form-control" value="{{ $company->ifsc_code }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Branch Name</label>
                <input type="text" name="branch_name" class="form-control" value="{{ $company->branch_name }}">
            </div>

            <div class="col-md-12">
                <button class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection
