@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Voucher</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('offers.voucherstore') }}" method="POST">
        @csrf

        <div class="container-fluid form-div">

            <div class="body-head mb-3">
                <h5>Voucher Details</h5>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="vouchercode">Voucher Code <span>*</span></label>
                    <input type="text" class="form-control" name="voucher_code" id="vouchercode" required autofocus>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="vouchername">Voucher Name <span>*</span></label>
                    <input type="text" class="form-control" name="voucher_name" id="vouchername" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="cards">No Of Cards <span>*</span></label>
                    <input type="number" class="form-control" name="no_of_cards" id="cards" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="discvalue">Discount Value <span>*</span></label>
                    <input type="text" class="form-control" name="discount_value" id="discvalue" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="redeembrand">Redeemable Brand <span>*</span></label>
                    <select class="form-select" name="redeemable_brand" id="redeembrand" required>
                        <option value="" selected disabled>Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="redeemcat">Redeemable Category <span>*</span></label>
                    <select class="form-select" name="redeemable_category" id="redeemcat" required>
                        <option value="" selected disabled>Select Category</option>
                    </select>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="redeemsubcat">Redeemable Sub Category <span>*</span></label>
                   <select class="form-select" name="redeemable_subcategory" id="redeemsubcat" required>
    <option value="" selected disabled>Select Subcategory</option>
</select>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="redeemitem">Redeemable Item <span>*</span></label>
                    <select class="form-select" name="redeemable_item" id="redeemitem" required>
    <option value="" selected disabled>Select Item</option>
</select>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="issuedate">Issue Date <span>*</span></label>
                    <input type="date" class="form-control" name="issue_date" id="issuedate" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="expdate">Expiry Date <span>*</span></label>
                    <input type="date" class="form-control" name="expiry_date" id="expdate" required>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Add Voucher</button>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Brand → Category
        $('#redeembrand').on('change', function() {
            let brandID = $(this).val();
            $('#redeemcat').html('<option selected disabled>Loading...</option>');
            $.get('/get-categories/' + brandID, function(data) {
                $('#redeemcat').empty().append('<option selected disabled>Select Category</option>');
                $.each(data, function(index, category) {
                    $('#redeemcat').append('<option value="' + category.id + '">' + category.name + '</option>');
                });
            });
        });

        // Category → Subcategory
        $('#redeemcat').on('change', function() {
            let categoryID = $(this).val();
            $('#redeemsubcat').html('<option selected disabled>Loading...</option>');
            $.get('/get-subcategories/' + categoryID, function(data) {
                $('#redeemsubcat').empty().append('<option selected disabled>Select Subcategory</option>');
                $.each(data, function(index, subcat) {
                    $('#redeemsubcat').append('<option value="' + subcat.id + '">' + subcat.name + '</option>');
                });
            });
        });

        // Subcategory → Item
        $('#redeemsubcat').on('change', function() {
            let brandID = $('#redeembrand').val();
            let categoryID = $('#redeemcat').val();
            let subcatID = $(this).val();
            $('#redeemitem').html('<option selected disabled>Loading...</option>');
            $.get(`/get-items/${brandID}/${categoryID}/${subcatID}`, function(data) {
                $('#redeemitem').empty().append('<option selected disabled>Select Item</option>');
                $.each(data, function(index, item) {
                    $('#redeemitem').append('<option value="' + item.id + '">' + item.item_name + '</option>');
                });
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


@endsection
