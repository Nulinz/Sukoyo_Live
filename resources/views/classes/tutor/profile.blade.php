@extends('layouts.app')

@section('content')

<style>
    .card {
        border-radius: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
    }

    .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #f0f0f0;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }

    .card-body h5 {
        margin-top: 15px;
        font-weight: 600;
        color: #333;
    }

    .card-body p.text-muted {
        margin-bottom: 0.5rem;
        font-size: 14px;
    }

    .profile-title {
        font-weight: 700;
        color: #2c3e50;
    }

    .section-divider {
        border-top: 1px dashed #dcdcdc;
        margin: 1.5rem 0;
    }

    @media (max-width: 768px) {
        .card-body .row > div {
            margin-bottom: 1rem;
        }
    }
</style>

<div class="container py-4">
    <div class="card">
        <div class="card-header px-4 py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 profile-title">Tutor Profile</h4>
            {{-- Optional: Add Edit Button --}}
        </div>
        <div class="card-body px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <h5>Name</h5>
                    <p class="text-muted">{{ $tutor->name }}</p>

                    <h5>Expertise</h5>
                    <p class="text-muted">{{ $tutor->expertise }}</p>

                    <h5>Email ID</h5>
                    <p class="text-muted">{{ $tutor->email }}</p>

                    <h5>Contact</h5>
                    <p class="text-muted">{{ $tutor->contact }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Address</h5>
                    <p class="text-muted">{{ $tutor->address }}</p>

                    <h5>City, State & Pincode</h5>
                    <p class="text-muted">{{ $tutor->city }}, {{ $tutor->state }} - {{ $tutor->pincode }}</p>

                    <h5>Type</h5>
                    <p class="text-muted">{{ ucfirst($tutor->internal_external) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
