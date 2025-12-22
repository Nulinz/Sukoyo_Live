@extends('layouts.app')

@section('content')

<div class="body-div p-3">
    <div class="body-head">
        <h4>Store List</h4>
        <a data-bs-toggle="modal" data-bs-target="#addStore">
            <button class="listbtn"><i class="fas fa-plus pe-2"></i>Add Store</button>
        </a>
    </div>

    <div class="container-fluid mt-3 listtable">
        <div class="filter-container">
    <div class="filter-container-start">
        <select class="headerDropdown form-select filter-option">
            <option value="All" selected>All</option>
            @foreach($columns as $field => $label)
                <option value="{{ $field }}">{{ $label }}</option>
            @endforeach
        </select>
        <input type="text" class="form-control filterInput" placeholder=" Search">
    </div>
</div>


        <div class="table-wrapper">
            <table class="example table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Store ID</th>
                        <th>Store Name</th>
                        <th>Email ID</th>
                        <th>Contact Number</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
               <tbody>
    @foreach($stores as $key => $store)
    <tr>
        <td>{{ $key+1 }}</td>
        <td>{{ $store->store_id }}</td>
        <td>{{ $store->store_name }}</td>
        <td>{{ $store->email }}</td>
        <td>{{ $store->contact_number }}</td>
        <td>{{ $store->city }}</td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('store.toggle_status', $store->id) }}" data-bs-toggle="tooltip"
       data-bs-title="{{ $store->status == 'Active' ? 'Deactivate' : 'Activate' }}">
        @if($store->status == 'Active')
            <i class="fas fa-circle-check text-success"></i>
        @else
            <i class="fas fa-circle-xmark text-danger"></i>
        @endif
    </a>
                <a data-bs-toggle="modal" data-bs-target="#editStore{{ $store->id }}">
                    <i class="fas fa-pen-to-square"></i>
                </a>
              <a href="{{ route('store.profile', $store->id) }}" data-bs-toggle="tooltip" title="View Store Profile">
    <i class="fas fa-arrow-up-right-from-square"></i>
</a>

            </div>
        </td>
    </tr>

    <!-- Edit Modal per store -->
    <div class="modal fade" id="editStore{{ $store->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="m-0">Update Store</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('store.update', $store->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Store ID</label>
                                <input type="text" class="form-control" name="store_id" value="{{ $store->store_id }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Store Name</label>
                                <input type="text" class="form-control" name="store_name" value="{{ $store->store_name }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Email ID</label>
                                <input type="email" class="form-control" name="email" value="{{ $store->email }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Contact Number</label>
                                <input type="text" class="form-control" name="contact_number" value="{{ $store->contact_number }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Address</label>
                                <input type="text" class="form-control" name="address" value="{{ $store->address }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>City</label>
                                <input type="text" class="form-control" name="city" value="{{ $store->city }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>State</label>
                                <input type="text" class="form-control" name="state" value="{{ $store->state }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Pincode</label>
                                <input type="text" class="form-control" name="pincode" value="{{ $store->pincode }}" required>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-2">
                                <label>Geo Location</label>
                                <input type="text" class="form-control" name="geo_location" value="{{ $store->geo_location }}" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                                <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                                <button type="submit" class="modalbtn w-50">Update Store</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</tbody>

            </table>
        </div>
    </div>
</div>

<!-- Add Store Modal -->
<div class="modal fade" id="addStore" tabindex="-1" aria-labelledby="addStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Store</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('store.add') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addstoreid">Store ID</label>
                            <input type="text" class="form-control" name="store_id" id="addstoreid" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addstorename">Store Name</label>
                            <input type="text" class="form-control" name="store_name" id="addstorename" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addemail">Email ID</label>
                            <input type="email" class="form-control" name="email" id="addemail" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addcontact">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" id="addcontact" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addaddress">Address</label>
                            <input type="text" class="form-control" name="address" id="addaddress" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addcity">City</label>
                            <input type="text" class="form-control" name="city" id="addcity" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addstate">State</label>
                            <input type="text" class="form-control" name="state" id="addstate" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addpincode">Pincode</label>
                            <input type="text" class="form-control" name="pincode" id="addpincode" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addgeoloc">Geo Location</label>
                            <input type="text" class="form-control" name="geo_location" id="addgeoloc">
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add Store</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Edit Store Modal -->
<!-- <div class="modal fade" id="editStore" tabindex="-1" aria-labelledby="editStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Update Store</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editstoreid">Store ID</label>
                            <input type="text" class="form-control" name="" id="editstoreid" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editstorename">Store Name</label>
                            <input type="text" class="form-control" name="" id="editstorename" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editemail">Email ID</label>
                            <input type="email" class="form-control" name="" id="editemail" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editcontact">Contact Number</label>
                            <input type="number" class="form-control" name="" id="editcontact"  required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editaddress">Address</label>
                            <input type="text" class="form-control" name="" id="editaddress" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editcity">City</label>
                            <input type="text" class="form-control" name="" id="editcity" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editstate">State</label>
                            <input type="text" class="form-control" name="" id="editstate" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editpincode">Pincode</label>
                            <input type="number" class="form-control" name="" id="editpincode"  required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editgeoloc">Geo Location</label>
                            <input type="text" class="form-control" name="" id="editgeoloc" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update Store</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->

<script>
$(document).ready(function() {
    var table = $('.example').DataTable();

    // Map field name to column index
    var fieldToIndex = {
        'store_id': 1,
        'store_name': 2,
        'email': 3,
        'contact_number': 4,
        'city': 5
        // update indexes if your table changes
    };

    $('.filterInput').on('keyup', function() {
        var selectedField = $('.headerDropdown').val();
        var value = $(this).val();

        if (selectedField !== 'All') {
            var colIndex = fieldToIndex[selectedField];
            table.columns().search(''); // clear global search
            table.column(colIndex).search(value).draw();
        } else {
            table.search(value).draw();
        }
    });

    $('.headerDropdown').on('change', function() {
        $('.filterInput').val('');
        table.search('').columns().search('').draw();
    });
});
</script>


@endsection