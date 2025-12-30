@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Update Class</h4>
    </div>

    <form action="{{ route('class.update', $class->id) }}" method="POST">
        @csrf
        <div class="container-fluid form-div">

            <div class="body-head mb-3">
                <h5>Class Details</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="classname">Class Name <span>*</span></label>
                    <input type="text" class="form-control" name="class_name" id="classname"
                        value="{{ $class->class_name }}" required autofocus>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="classtype">Class Type <span>*</span></label>
                    <select class="form-select" name="class_type" id="classtype" required>
                        <option value="" disabled>Select Option</option>
                        <option value="Online" {{ $class->class_type == 'Online' ? 'selected' : '' }}>Online</option>
                        <option value="Offline" {{ $class->class_type == 'Offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="maxpart">Max Participants <span>*</span></label>
                    <input type="number" class="form-control" name="max_participants" id="maxpart"
                        value="{{ $class->max_participants }}" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="pricingtype">Pricing Type <span>*</span></label>
                    <select class="form-select" name="pricing_type" id="pricingtype" required>
                        <option value="" disabled>Select Option</option>
                        <option value="Free" {{ $class->pricing_type == 'Free' ? 'selected' : '' }}>Free</option>
                        <option value="Paid" {{ $class->pricing_type == 'Paid' ? 'selected' : '' }}>Paid</option>
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
                    <input type="date" class="form-control" name="date" id="date"
                        value="{{ $class->date }}" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="time">Time <span>*</span></label>
                    <input type="time" class="form-control" name="time" id="time"
                        value="{{ $class->time }}" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="duration">Duration <span>*</span></label>
                    <input type="text" class="form-control" name="duration" id="duration"
                        value="{{ $class->duration }}" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="reconetime">Recurring / One-Time <span>*</span></label>
                    <select class="form-select" name="recurring_one_time" id="reconetime" required>
                        <option value="" disabled>Select Option</option>
                        <option value="Recurring" {{ $class->recurring_one_time == 'Recurring' ? 'selected' : '' }}>Recurring</option>
                        <option value="One-Time" {{ $class->recurring_one_time == 'One-Time' ? 'selected' : '' }}>One-Time</option>
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
                        <option value="" disabled>Select Option</option>
                        <option value="1" {{ $class->tutor_id == 1 ? 'selected' : '' }}>Arun</option>
                        <option value="2" {{ $class->tutor_id == 2 ? 'selected' : '' }}>John</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Update Class</button>
            </div>

        </div>
    </form>
</div>

@endsection
