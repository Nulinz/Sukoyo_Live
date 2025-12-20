@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">

    <style>
        .logodiv {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media screen and (min-width: 767px) {
            .tab-content .cards {
                display: grid;
                grid-template-columns: 39% 60%;
                align-items: center;
                justify-content: space-between;
            }
        }

        @media screen and (max-width: 767px) {
            .tab-content .cards {
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                align-items: center;
                justify-content: space-between;
            }
        }
    </style>

    <div class="body-div p-3">
        <div class="body-head mb-3 d-flex justify-content-between align-items-center">
            <h4>Company Profile</h4>
            <a href="{{ route('settings.companyedit') }}" data-bs-toggle="tooltip" data-bs-title="Edit">
                <i class="fas fa-pen-to-square"></i>
            </a>
        </div>

        <div class="mainbdy d-block">

            <div class="contentright">
                <div class="tab-content">
                    @if($company)
                        <div class="cards mb-2">
                            <div class="maincard mb-3">
                                <div class="logodiv">
                                    <img src="{{ asset('public/uploads/logos/' . $company->company_logo) }}"
                                         class="d-flex mx-auto" height="50px" alt="Company Logo">
                                </div>
                            </div>
                            <div class="maincard row mb-3">
                                <div class="body-head mb-3">
                                    <h4>Company Details</h4>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Business Type</h6>
                                    <h5>{{ $company->business_type }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Company Name</h6>
                                    <h5>{{ $company->company_name }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Owner Name</h6>
                                    <h5>{{ $company->owner_name }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Contact Number</h6>
                                    <h5>{{ $company->contact_number }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Alt Contact Number</h6>
                                    <h5>{{ $company->alternate_contact_number }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Email ID</h6>
                                    <h5>{{ $company->email }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Website URL</h6>
                                    <h5>{{ $company->website_url ?? '-' }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Address</h6>
                                    <h5>{{ $company->address }}, {{ $company->city }}, {{ $company->state }} - {{ $company->pincode }}</h5>
                                </div>
                            </div>
                        </div>

                        <div class="cards mb-2">
                            <div class="maincard row mb-3">
                                <div class="body-head mb-3">
                                    <h4>Business Details</h4>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>GST Number</h6>
                                    <h5>{{ $company->gst_number }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>Pan Card Number</h6>
                                    <h5>{{ $company->pan_card_number }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>CIN/LLP Number</h6>
                                    <h5>{{ $company->cin_llp_number }}</h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6>Trade License Number</h6>
                                    <h5>{{ $company->trade_license_number }}</h5>
                                </div>
                            </div>

                            <div class="maincard row mb-3">
                                <div class="body-head mb-3">
                                    <h4>Bank Details</h4>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Bank Name</h6>
                                    <h5>{{ $company->bank_name }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Acct Holder Name</h6>
                                    <h5>{{ $company->account_holder_name }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Account Number</h6>
                                    <h5>{{ $company->account_number }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>IFSC Code</h6>
                                    <h5>{{ $company->ifsc_code }}</h5>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <h6>Branch Name</h6>
                                    <h5>{{ $company->branch_name }}</h5>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No company profile found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
