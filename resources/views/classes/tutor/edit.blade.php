@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Update Tutor</h4>
    </div>

    <form action="{{ route('class.tutorupdate', $tutor->id) }}" method="POST">
        @csrf

        <div class="container-fluid form-div">
            <div class="body-head mb-3">
                <h5>Tutor Details</h5>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="name">Tutor Name <span>*</span></label>
                    <input type="text" class="form-control" name="name" id="name"
                        value="{{ $tutor->name }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="expertise">Expertise <span>*</span></label>
                    <select class="form-select" name="expertise" id="expertise" required>
                        <option value="" disabled>Select Option</option>
                        @foreach($expertiseList as $item)
                            <option value="{{ $item }}" {{ $tutor->expertise == $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="email">Email ID <span>*</span></label>
                    <input type="email" class="form-control" name="email" id="email"
                        value="{{ $tutor->email }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="contact">Contact Number <span>*</span></label>
                    <input type="text" class="form-control" name="contact" id="contact"
                        value="{{ $tutor->contact }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="internal_external">Internal / External <span>*</span></label>
                    <select class="form-select" name="internal_external" id="internal_external" required>
                        <option value="" disabled>Select Option</option>
                        <option value="Internal" {{ $tutor->internal_external == 'Internal' ? 'selected' : '' }}>Internal</option>
                        <option value="External" {{ $tutor->internal_external == 'External' ? 'selected' : '' }}>External</option>
                    </select>
                </div>
              
            </div>

            <hr>
            <div class="body-head mb-3">
                <h5>Address Details</h5>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="address">Address <span>*</span></label>
                    <input type="text" class="form-control" name="address" id="address"
                        value="{{ $tutor->address }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="city">City <span>*</span></label>
                    <input type="text" class="form-control" name="city" id="city"
                        value="{{ $tutor->city }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="state">State <span>*</span></label>
                    <input type="text" class="form-control" name="state" id="state"
                        value="{{ $tutor->state }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="pincode">Pincode <span>*</span></label>
                    <input type="text" class="form-control" name="pincode" id="pincode"
                        value="{{ $tutor->pincode }}" required>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="formbtn">Update Tutor</button>
            </div>
        </div>
    </form>
</div>

@endsection
