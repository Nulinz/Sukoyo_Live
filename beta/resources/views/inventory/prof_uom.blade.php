<div class="container-fluid mt-1 listtable">
    <div class="filter-container">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="headerDropdown2">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="filterInput2" placeholder=" Search">
        </div>
        <div class="filter-container-end">
            <a data-bs-toggle="modal" data-bs-target="#addUOM">
                <button class="listbtn"><i class="fas fa-plus pe-2"></i> Add UOM</button>
            </a>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table table-bordered" id="table2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>UOM Type</th>
                    <th>Qty (Box)</th>
                    <th>Rate Per Box</th>
                    <th>Closing Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Box</td>
                    <td>10</td>
                    <td>₹ 500</td>
                    <td>300</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a data-bs-toggle="modal" data-bs-target="#editUOM">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add UOM Modal -->
<div class="modal fade" id="addUOM" tabindex="-1" aria-labelledby="addUOMLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add UOM</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="adduomtype">UOM Type</label>
                            <select class="form-select" name="" id="adduomtype" required>
                                <option value="" selected disabled>Select Option</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="addqty">Qty (Box)</label>
                            <input type="number" class="form-control" name="" id="addqty" min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="addrate">Rate Per Box</label>
                            <input type="number" class="form-control" name="" id="addrate" min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="addclosestock">Closing Stock</label>
                            <input type="number" class="form-control" name="" id="addclosestock" min="0" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Add UOM</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit UOM Modal -->
<div class="modal fade" id="editUOM" tabindex="-1" aria-labelledby="editUOMLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Update UOM</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="edituomtype">UOM Type</label>
                            <select class="form-select" name="" id="edituomtype" required>
                                <option value="" selected disabled>Select Option</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2 pe-md-2">
                            <label for="editqty">Qty (Box)</label>
                            <input type="number" class="form-control" name="" id="editqty" min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="editrate">Rate Per Box</label>
                            <input type="number" class="form-control" name="" id="editrate" min="0" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="editclosestock">Closing Stock</label>
                            <input type="number" class="form-control" name="" id="editclosestock" min="0" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Update UOM</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>