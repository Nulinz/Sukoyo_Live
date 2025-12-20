@extends('layouts.app')

@section('content')
<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Update Item</h4>
    </div>

    <form action="{{ route('inventory.itemupdate', $item->id) }}" method="POST">
        @csrf
        @method('POST')
        <div class="container-fluid form-div">
            <div class="row">
                <div class="body-head mb-3">
                    <h5>Item Details</h5>
                </div>

                {{-- Item Type --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Type <span>*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="item_type" value="Product" {{ $item->item_type == 'Product' ? 'checked' : '' }}>
                            <label class="form-check-label">Product</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="item_type" value="Service" {{ $item->item_type == 'Service' ? 'checked' : '' }}>
                            <label class="form-check-label">Service</label>
                        </div>
                    </div>
                </div>

                {{-- Item Code --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Code <span>*</span></label>
                    <input type="text" class="form-control" name="item_code" value="{{ $item->item_code }}" required>
                </div>

                {{-- Item Name --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Name <span>*</span></label>
                    <input type="text" class="form-control" name="item_name" value="{{ $item->item_name }}" required>
                </div>

                {{-- Brand --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Brand <span>*</span></label>
                    <select class="form-select" name="brand_id" required>
                        <option value="" disabled>Select Option</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Category <span>*</span></label>
                    <select class="form-select" name="category_id" id="category" required>
                        <option value="" disabled>Select Option</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Subcategory --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Sub Category <span>*</span></label>
                    <select class="form-select" name="subcategory_id" id="subcategory" required>
                        <option value="" disabled>Select Option</option>
                        @foreach($subcategories as $subcategory)
                            @if($subcategory->category_id == $item->category_id)
                                <option value="{{ $subcategory->id }}" {{ $item->subcategory_id == $subcategory->id ? 'selected' : '' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Discount (mapped to department column) --}}
                               <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Discount (%) <span>*</span></label>
                    <input type="number" class="form-control" name="discount" value="{{ $item->discount }}" min="0" step="0.01" required>
                </div>

                {{-- Sales Price --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Sales Price <span>*</span></label>
                    <input type="number" class="form-control" name="sales_price" value="{{ $item->sales_price }}" step="0.01" required>
                </div>

                {{-- Wholesale Price --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Wholesale Price <span>*</span></label>
                    <input type="number" class="form-control" name="wholesale_price" value="{{ $item->wholesale_price }}" step="0.01" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                 <label>MRP <span>*</span></label>
                <input type="number" step="0.01" class="form-control" value="{{ $item->mrp }}" name="mrp" min="0" required>
                </div>                

                {{-- Measuring Unit --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Measuring Unit <span>*</span></label>
                    <select class="form-select" name="measuring_unit" required>
                        <option value="pcs" {{ $item->measure_unit == 'pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="box" {{ $item->measure_unit == 'box' ? 'selected' : '' }}>Box</option>
                        <option value="kg" {{ $item->measure_unit == 'kg' ? 'selected' : '' }}>Kg</option>
                    </select>
                </div>

                {{-- Opening Stock --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Opening Stock <span>*</span></label>
                    <input type="number" class="form-control" name="opening_stock" value="{{ $item->opening_stock }}" min="0" required>
                </div>

                {{-- GST Rate --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>GST Tax Rate (%) <span>*</span></label>
                    <select class="form-select" name="gst_rate" required>
                        <option value="0" {{ $item->gst_rate == 0 ? 'selected' : '' }}>0%</option>
                        <option value="5" {{ $item->gst_rate == 5 ? 'selected' : '' }}>5%</option>
                        <option value="12" {{ $item->gst_rate == 12 ? 'selected' : '' }}>12%</option>
                        <option value="18" {{ $item->gst_rate == 18 ? 'selected' : '' }}>18%</option>
                        <option value="28" {{ $item->gst_rate == 28 ? 'selected' : '' }}>28%</option>
                    </select>
                </div>

                {{-- Item Description --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Item Description</label>
                    <textarea class="form-control" name="description">{{ $item->item_description }}</textarea>
                </div>

                {{-- Stock Status --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Stock Status <span>*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="status" value="Active" {{ $item->stock_status == 'Active' ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="status" value="Inactive" {{ $item->stock_status == 'Inactive' ? 'checked' : '' }}>
                            <label class="form-check-label">Inactive</label>
                        </div>
                    </div>
                </div>

                {{-- Min Stock --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Minimum Stock <span>*</span></label>
                    <input type="number" class="form-control" name="min_stock" value="{{ $item->min_stock }}" min="0" required>
                </div>

                {{-- Max Stock --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Maximum Stock <span>*</span></label>
                    <input type="number" class="form-control" name="max_stock" value="{{ $item->max_stock }}" min="0" required>
                </div>

                {{-- ABC Category --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>ABC Category <span>*</span></label>
                    <select class="form-select" name="abc_category" required>
                        <option value="A" {{ $item->abc_category == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ $item->abc_category == 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ $item->abc_category == 'C' ? 'selected' : '' }}>C</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="row">
                {{-- Purchase Price --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Purchase Price <span>*</span></label>
                    <input type="number" class="form-control" name="purchase_price" value="{{ $item->purchase_price }}" step="0.01" min="0" required>
                </div>

                {{-- Purchase GST Rate --}}
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Purchase GST (%) <span>*</span></label>
                    <select class="form-select" name="purchase_gst" required>
                        <option value="0" {{ $item->purchase_gst == 0 ? 'selected' : '' }}>0%</option>
                        <option value="5" {{ $item->purchase_gst == 5 ? 'selected' : '' }}>5%</option>
                        <option value="12" {{ $item->purchase_gst == 12 ? 'selected' : '' }}>12%</option>
                        <option value="18" {{ $item->purchase_gst == 18 ? 'selected' : '' }}>18%</option>
                        <option value="28" {{ $item->purchase_gst == 28 ? 'selected' : '' }}>28%</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">Update Item</button>
            </div>
        </div>
    </form>
</div>
@endsection
