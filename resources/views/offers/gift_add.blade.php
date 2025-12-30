@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Gift Card</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('offers.giftstore') }}" method="POST">
        @csrf

        <div class="container-fluid form-div">

            <div class="body-head mb-3">
                <h5>Gift Card Details</h5>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="cardcode">Card Code <span>*</span></label>
                    <input type="text" class="form-control" name="card_code" id="cardcode" required autofocus>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="cards">No Of Cards <span>*</span></label>
                    <input type="number" class="form-control" name="no_of_cards" id="cards" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="cardtype">Card Type <span>*</span></label>
                    <select class="form-select" name="card_type" id="cardtype" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="Gold">Gold</option>
                        <option value="Platinum">Platinum</option>
                        <option value="Premium">Premium</option>
                    </select>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="cardvalue">Card Value <span>*</span></label>
                    <input type="text" class="form-control" name="card_value" id="cardvalue" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="issuedate">Issue Date <span>*</span></label>
                    <input type="date" class="form-control" name="issue_date" id="issuedate" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="expdate">Expiry Date <span>*</span></label>
                    <input type="date" class="form-control" name="expiry_date" id="expdate" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Reloadable <span>*</span></label>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <div class="form-check d-flex align-items-center gap-2">
                            <input class="form-check-input" type="radio" name="reloadable" value="1" id="yes" required>
                            <label class="form-check-label mb-0" for="yes">Yes</label>
                        </div>
                        <div class="form-check d-flex align-items-center gap-2">
                            <input class="form-check-input" type="radio" name="reloadable" value="0" id="no" required>
                            <label class="form-check-label mb-0" for="no">No</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Add Gift Card</button>
            </div>
        </div>
    </form>
</div>

@endsection
