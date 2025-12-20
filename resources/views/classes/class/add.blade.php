@extends('layouts.app')

@section('content')
<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Class</h4>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('class.store') }}" method="POST">
        @csrf
        <div class="container-fluid form-div">

            <div class="body-head mb-3">
                <h5>Class Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="classname">Class Name <span>*</span></label>
                    <input type="text" class="form-control" name="class_name" id="classname" required autofocus>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="classtype">Class Type <span>*</span></label>
                    <select class="form-select" name="class_type" id="classtype" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="Online">Online</option>
                        <option value="Offline">Offline</option>
                    </select>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="maxpart">Max Participants <span>*</span></label>
                    <input type="number" class="form-control" name="max_participants" id="maxpart" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="pricingtype">Pricing Type <span>*</span></label>
                    <select class="form-select" name="pricing_type" id="pricingtype" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="Free">Free</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="body-head mb-3">
                <h5>Set Schedule</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="date">Date <span>*</span></label>
                    <input type="date" class="form-control" name="date" id="date" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="time">Time <span>*</span></label>
                    <input type="time" class="form-control" name="time" id="time" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="duration">Duration (e.g., 1 hour) <span>*</span></label>
                    <input type="text" class="form-control" name="duration" id="duration" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="reconetime">Recurring / One-Time <span>*</span></label>
                    <select class="form-select" name="recurring_one_time" id="reconetime" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="Recurring">Recurring</option>
                        <option value="One-Time">One-Time</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="body-head mb-3">
                <h5>Assign Tutors</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label for="tutor">Tutor <span>*</span></label>
    <select class="form-select" name="tutor_id" id="tutor" required>
        <option value="" selected disabled>Select Option</option>
        @foreach($tutors as $tutor)
            <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
        @endforeach
    </select>
</div>

            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Add Class</button>
            </div>

        </div>
    </form>
</div>
@endsection
