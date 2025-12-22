@extends('layouts.app')

@section('content')
<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Loyalty Points</h4>
    </div>

    <form action="{{ route('offers.lpstore') }}" method="POST">
        @csrf
        <div class="container-fluid form-div">

            <!-- Reward Setup -->
            <div class="body-head mb-3">
                <h5>Reward Setup</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                    <label for="earn_amt">Loyalty Point Conversion <span>*</span></label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control" name="earn_amt" id="earn_amt" placeholder="₹ Amount" required>
                        <input type="text" class="form-control" name="earn_points" id="earn_points" placeholder="Points" required>
                    </div>
                    <label class="small text-muted">Award a loyalty point to your customers for every ₹ 100 spent on sale transactions.</label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                    <label for="min_invoice_for_earning">Minimum Invoice Value to Earn Points <span>*</span></label>
                    <input type="text" class="form-control" name="min_invoice_for_earning" id="min_invoice_for_earning" required>
                    <label class="small text-muted">Customers must spend above ₹ 500 to get Loyalty points in each transaction.</label>
                </div>
            </div>

            <hr>

            <!-- Redeem Setup -->
            <div class="body-head mb-3">
                <h5>Redeem Setup</h5>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                    <label for="redeem_amt">Redemption Value <span>*</span></label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control" name="redeem_amt" id="redeem_amt" placeholder="₹ Value" required>
                        <input type="text" class="form-control" name="redeem_points" id="redeem_points" placeholder="Points" required>
                    </div>
                    <label class="small text-muted">Each loyalty point will give the user a discount of ₹ 10.</label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                    <label for="max_percent_invoice">Maximum % of Invoice Value Redeemable <span>*</span></label>
                    <input type="text" class="form-control" name="max_percent_invoice" id="max_percent_invoice" required>
                    <label class="small text-muted">Maximum loyalty discount redeemed will be 50% of invoice value.</label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
                    <label for="min_invoice_for_redeem">Minimum Invoice Value to Redeem Points <span>*</span></label>
                    <input type="text" class="form-control" name="min_invoice_for_redeem" id="min_invoice_for_redeem" required>
                    <label class="small text-muted">Invoices below ₹ 20 will not be able to redeem any loyalty discount.</label>
                </div>
            </div>

            <div class="col-12 mt-3 d-flex justify-content-center">
                <button type="submit" class="formbtn">Update Loyalty Points</button>
            </div>
        </div>
    </form>
</div>
@endsection
