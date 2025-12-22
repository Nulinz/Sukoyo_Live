@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Item</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('inventory.itemstore') }}" method="POST">
        @csrf
        <div class="container-fluid form-div">

            <div class="body-head mb-3">
                <h5>Item Details</h5>
            </div>
            <div class="row">

                {{-- Item Type --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Type <span>*</span></label>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="item_type" value="Product" required>
                            <label class="form-check-label">Product</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="item_type" value="Service">
                            <label class="form-check-label">Service</label>
                        </div>
                    </div>
                </div>

                {{-- Item Code --}}
               <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="item_code">Item Code <span>*</span></label>
                    <input type="text" class="form-control" name="item_code" id="item_code" required>
                    <span id="item-code-warning" class="text-danger mt-1" style="display:none;"></span>
                </div>
              <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="hsn_code">HSN Code <span>*</span></label>
                    <input type="text" class="form-control" name="hsn_code" id="hsn_code" required>
                </div>
                {{-- Item Name --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="item_name">Item Name <span>*</span></label>
                    <input type="text" class="form-control" name="item_name" id="item_name" required>
                </div>

                {{-- Brand --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Brand <span>*</span></label>
                    <select class="form-select" name="brand_id" required>
                        <option value="" selected disabled>Select Option</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Category --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Category <span>*</span></label>
                    <select class="form-select" name="category_id" id="category" required>
                        <option value="" selected disabled>Select Option</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sub Category --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Sub Category <span>*</span></label>
                    <select class="form-select" name="subcategory_id" id="subcategory" required>
                        <option value="" selected disabled>Select Category First</option>
                    </select>
                </div>
                    

                {{-- Department --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Discount(%) <span>*</span></label>
                    <input type="text" class="form-control" name="discount" required>
                </div>

                {{-- Sales Price --}}
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label>Sales Price <span>*</span></label>
    <input type="number" step="0.01" class="form-control" name="sales_price" min="0" required>
</div>

{{-- MRP --}}
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label>MRP <span>*</span></label>
    <input type="number" step="0.01" class="form-control" name="mrp" min="0" required>
</div>

{{-- Wholesale Price --}}
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label>Wholesale Price <span>*</span></label>
    <input type="number" step="0.01" class="form-control" name="wholesale_price" min="0" required>
</div>


                {{-- Measuring Unit --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Measuring Unit <span>*</span></label>
                    <select class="form-select" name="measure_unit" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="pcs">pcs</option>
                        <option value="box">box</option>
                        <option value="nos">nos</option>
                    </select>
                </div>

                {{-- Opening Stock & Unit --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Opening Stock <span>*</span></label>
                    <div class="d-flex">
                        <input type="number" class="form-control" name="opening_stock" min="0" required>
                        <select class="form-select" name="opening_unit" required>
                            <option value="pcs">pcs</option>
                            <option value="box">box</option>
                            <option value="nos">nos</option>
                        </select>
                    </div>
                </div>

                {{-- GST Tax Rate --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>GST Tax Rate (%) <span>*</span></label>
                    <select class="form-select" name="gst_rate" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="0">0%</option>
                        <option value="5">5%</option>
                        <option value="12">12%</option>
                        <option value="18">18%</option>
                        <option value="28">28%</option>
                    </select>
                </div>

                {{-- Item Description --}}
                    <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                        <label>Item Description</label>
                        <textarea class="form-control" name="item_description"></textarea>
                    </div>

                {{-- Stock Warning / Status --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Stock Status <span>*</span></label>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="stock_status" value="Active" required>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="stock_status" value="Inactive">
                            <label class="form-check-label">Inactive</label>
                        </div>
                    </div>
                </div>

                {{-- Minimum Stock --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Minimum Stock <span>*</span></label>
                    <input type="number" class="form-control" name="min_stock" min="0" required>
                </div>

                {{-- Maximum Stock --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Maximum Stock <span>*</span></label>
                    <input type="number" class="form-control" name="max_stock" min="0" required>
                </div>

                {{-- ABC Category --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>ABC Category <span>*</span></label>
                    <select class="form-select" name="abc_category" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="body-head mb-3">
                <h5>Pricing Details</h5>
            </div>
            <div class="row">

                {{-- Purchase Price & Tax Type --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label>Purchase Price <span>*</span></label>
    <div class="d-flex">
        <input type="number" class="form-control" name="purchase_price" min="0" step="0.01" required>
        <select class="form-select" name="purchase_tax" required>
            <option value="With Tax">With Tax</option>
            <option value="Without Tax">Without Tax</option>
        </select>
    </div>
</div>

                {{-- Purchase GST Tax Rate --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>GST Tax Rate (%) <span>*</span></label>
                    <select class="form-select" name="purchase_gst" required>
                        <option value="" selected disabled>Select Option</option>
                        <option value="0">0%</option>
                        <option value="5">5%</option>
                        <option value="12">12%</option>
                        <option value="18">18%</option>
                        <option value="28">28%</option>
                    </select>
                </div>

            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Add Item</button>
            </div>
        </div>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#category').on('change', function () {
        var categoryId = $(this).val();
        $('#subcategory').html('<option value="">Loading...</option>');

        if (categoryId) {
            $.ajax({
                url: "{{ url('/get-subcategories-by-category') }}",
                type: "GET",
                data: { category_id: categoryId },
                success: function (res) {
                    if (res.status === 'success') {
                        var options = '<option value="" disabled selected>Select Option</option>';
                        $.each(res.data, function (key, subcategory) {
                            options += `<option value="${subcategory.id}">${subcategory.name}</option>`;
                        });
                        $('#subcategory').html(options);
                    } else {
                        $('#subcategory').html('<option value="">No subcategories found</option>');
                    }
                },
                error: function () {
                    $('#subcategory').html('<option value="">Something went wrong</option>');
                }
            });
        }
    });
</script>
<script>
    $('#item_code').on('blur', function () {
    var itemCode = $(this).val().trim();
    if (itemCode) {
        $.ajax({
            url: "{{ url('/check-item-code') }}",
            type: "GET",
            data: { item_code: itemCode },
            success: function (res) {
                if (res.exists) {
                    $('#item-code-warning').text('This item code already exists!').show();
                } else {
                    $('#item-code-warning').hide();
                }
            },
            error: function () {
                $('#item-code-warning').text('Error checking item code').show();
            }
        });
    } else {
        $('#item-code-warning').hide();
    }
});

</script>
@endsection
