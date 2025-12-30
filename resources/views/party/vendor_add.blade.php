@extends('layouts.app')

@section('content')
<style>
    /* Custom styles for payment checkboxes */
.payment-option {
    padding: 8px 12px;
    border-radius: 5px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-option.topay-selected {
    background-color: #ffebee;
    border-color: #f44336;
    color: #d32f2f;
}

.payment-option.tocollect-selected {
    background-color: #e8f5e8;
    border-color: #4caf50;
    color: #2e7d32;
}

.payment-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #f5f5f5;
    color: #9e9e9e;
}

.payment-option input[type="checkbox"]:disabled {
    cursor: not-allowed;
}

.payment-option label {
    cursor: pointer;
    font-weight: 500;
}

.payment-option.disabled label {
    cursor: not-allowed;
}
</style>

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Vendor</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('party.vendorstore') }}" method="POST">
        @csrf
        <div class="container-fluid form-div">

            <div class="body-head mb-3">
                <h5>General Details</h5>
            </div>
<div class="row">
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="vendorname">Vendor Name <span>*</span></label>
        <input type="text" class="form-control" name="vendorname" id="vendorname" autofocus required>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="contact">Contact Number <span>*</span></label>
        <input type="text" class="form-control" name="contact" id="contact" required>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="email">Email ID <span>*</span></label>
        <input type="email" class="form-control" name="email" id="email" required>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="openbalance">Opening Balance</label>
        <div class="inpselectflex">
            <input type="number" class="form-control border-0" name="openbalance" id="openbalance" min="0" value="0">
            <select class="form-select border-0 text-uppercase" name="tax" id="tax">
                <option value="With Tax">With Tax</option>
                <option value="Without Tax" selected>Without Tax</option>
            </select>
        </div>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="payment">Payment</label>
        <div class="d-flex align-items-center gap-3 mt-1">
            <div class="form-check d-flex align-items-center gap-2 payment-option" id="topayOption">
                <input class="form-check-input mb-auto" type="checkbox" name="topay" id="topay">
                <label class="form-check-label mb-0" for="topay">To Pay</label>
            </div>
            <div class="form-check d-flex align-items-center gap-2 payment-option" id="tocollectOption">
                <input class="form-check-input mb-auto" type="checkbox" name="tocollect" id="tocollect">
                <label class="form-check-label mb-0" for="tocollect">To Collect</label>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="gst">GSTIN</label>
        <input type="text" class="form-control" name="gst" id="gst">
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="panno">Pan Card Number</label>
        <input type="text" class="form-control" name="panno" id="panno">
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="creditperiod">Credit Period <span>*</span></label>
        <div class="inpselectflex">
            <input type="number" class="form-control border-0" name="creditperiod" id="creditperiod" min="0" value="0" required>
            <h6 class="mb-0 text-center px-0">Days</h6>
        </div>
    </div>
    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
        <label for="creditlimit">Credit Limit</label>
        <input type="number" class="form-control" name="creditlimit" id="creditlimit" min="0" value="0">
    </div>
</div>

<hr>

<div class="body-head mb-3">
    <h5>Pricing Details</h5>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
        <label for="billaddress">Billing Address</label>
        <textarea rows="2" class="form-control" name="billaddress" id="billaddress"></textarea>
    </div>
    <div class="col-sm-12 col-md-6 col-xl-6 mb-3">
        <label for="shipaddress">Shipping Address</label>
        <textarea rows="2" class="form-control" name="shipaddress" id="shipaddress"></textarea>
    </div>
</div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Add Vendor</button>
            </div>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const topayCheckbox = document.getElementById('topay');
    const tocollectCheckbox = document.getElementById('tocollect');
    const topayOption = document.getElementById('topayOption');
    const tocollectOption = document.getElementById('tocollectOption');

    function updatePaymentOptions() {
        // Reset all classes first
        topayOption.classList.remove('topay-selected', 'disabled');
        tocollectOption.classList.remove('tocollect-selected', 'disabled');
        
        // Enable both checkboxes
        topayCheckbox.disabled = false;
        tocollectCheckbox.disabled = false;

        if (topayCheckbox.checked) {
            // To Pay is selected
            topayOption.classList.add('topay-selected');
            tocollectOption.classList.add('disabled');
            tocollectCheckbox.disabled = true;
            tocollectCheckbox.checked = false;
        } else if (tocollectCheckbox.checked) {
            // To Collect is selected
            tocollectOption.classList.add('tocollect-selected');
            topayOption.classList.add('disabled');
            topayCheckbox.disabled = true;
            topayCheckbox.checked = false;
        }
    }

    // Add event listeners
    topayCheckbox.addEventListener('change', updatePaymentOptions);
    tocollectCheckbox.addEventListener('change', updatePaymentOptions);

    // Also handle clicks on the entire option div
    topayOption.addEventListener('click', function(e) {
        if (!topayCheckbox.disabled && e.target !== topayCheckbox) {
            topayCheckbox.checked = !topayCheckbox.checked;
            updatePaymentOptions();
        }
    });

    tocollectOption.addEventListener('click', function(e) {
        if (!tocollectCheckbox.disabled && e.target !== tocollectCheckbox) {
            tocollectCheckbox.checked = !tocollectCheckbox.checked;
            updatePaymentOptions();
        }
    });
});
</script>
@endsection
