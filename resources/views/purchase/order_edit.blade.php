@extends('layouts.app')

@section('content')

<style>
    @media screen and (max-width: 767px) {
        .table-wrapper .example {
            width: 200% !important;
        }
    }
    
    /* Custom styles for select2 */
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }
    
    /* Custom styles for round off checkbox */
    .round-off-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }
    
    .round-off-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .round-off-wrapper label {
        margin: 0;
        cursor: pointer;
        font-weight: normal;
    }
    
    /* Display mode styles */
    .item-display {
        padding: 8px 12px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        min-height: 38px;
        display: flex;
        align-items: center;
    }
    
    .item-edit-mode {
        display: none;
    }
    
    .row-edit-mode .item-display {
        display: none;
    }
    
    .row-edit-mode .item-edit-mode {
        display: block;
    }
    
   .action-buttons {
    display: flex;
    gap: -8px;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: flex-start;
    }

    .btn-icon {
        padding: 0;
        font-size: 16px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        min-height: 18px;
        transition: all 0.2s;
        border-radius: 4px;
    }

    .btn-icon:hover {
        background: rgba(0,0,0,0.05);
    }

    .btn-edit {
        color: #0d6efd;
    }

    .btn-save {
        color: #198754;
    }

    .btn-cancel {
        color: #dc3545;
    }

    .btn-delete {
        color: #dc3545;
    }
    .btn-view {
        color: #198754;
    }
    .unit-select:disabled {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    pointer-events: none;
    background-image: none !important;
}

.unit-select:not(:disabled) {
    cursor: pointer;
}
</style>

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Update Purchase Order</h4>
    </div>

    <form action="{{ route('purchase.order_update', $purchaseOrder->id) }}" method="POST" id="purchaseOrderForm">
        @csrf
        @method('PUT')

        <div class="container-fluid form-div">
            <div class="body-head mb-3">
                <h5>Item Details</h5>
            </div>
            <div class="row">
                <!-- Vendor, bill no etc. are readonly -->
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Vendor <span>*</span></label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder->vendor->vendorname }}" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Contact Number <span>*</span></label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder->contact }}" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Billing Address</label>
                    <textarea rows="1" class="form-control" readonly>{{ $purchaseOrder->billaddress }}</textarea>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Bill No <span>*</span></label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder->bill_no }}" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Bill Date <span>*</span></label>
                    <input type="date" class="form-control" value="{{ $purchaseOrder->bill_date }}" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Due Date <span>*</span></label>
                    <input type="date" class="form-control" value="{{ $purchaseOrder->due_date }}" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Transport Charge</label>
                    <input type="number" step="0.01" class="form-control" name="transport" value="{{ $purchaseOrder->transport }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Packaging Charge</label>
                    <input type="number" step="0.01" class="form-control" name="packaging" value="{{ $purchaseOrder->packaging }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Store / Warehouse <span>*</span></label>
                    <select class="form-select" disabled>
                        <option value="" disabled {{ $purchaseOrder->warehouse == null ? 'selected' : '' }}>Select Option</option>
                        <option value="Store" {{ $purchaseOrder->warehouse == 'Store' ? 'selected' : '' }}>Store</option>
                        <option value="Warehouse" {{ $purchaseOrder->warehouse == 'Warehouse' ? 'selected' : '' }}>Warehouse</option>
                    </select>
                    <input type="hidden" name="warehouse" value="{{ $purchaseOrder->warehouse }}">
                </div>
            </div>

            <div class="container-fluid listtable">
                <div class="table-wrapper">
                    <table class="example table table-bordered" id="itemTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Unit</th>
                                <th>Qty</th>
                                <th>Price/Unit</th>
                                <th>Discount %</th>
                                <th>Tax %</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($purchaseOrder->items as $index => $item)
                            <tr data-row="{{ $index }}" data-item-id="{{ $item->item_id }}" data-item-name="{{ $item->item->item_name ?? 'Unknown Item' }}">
                                <td class="serial-no">{{ $index+1 }}</td>
                                <td>
                                    <!-- Display Mode -->
                                    <div class="item-display">{{ $item->item->item_name ?? 'Unknown Item' }}</div>
                                    
                                    <!-- Edit Mode -->
                                    <div class="item-edit-mode">
                                        <select class="form-select item-select" style="width: 200px">
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Hidden input to store item_id (always submitted) -->
                                    <input type="hidden" class="item-id-input" name="items[{{ $index }}][item_id]" value="{{ $item->item_id }}">
                                </td>
                                <td>
                                    <select class="form-select unit-select" style="width: 100px" name="items[{{ $index }}][unit]" required disabled>
                                        <option value="" disabled>Select Unit</option>
                                        <option value="kg" {{ $item->unit == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="g" {{ $item->unit == 'g' ? 'selected' : '' }}>g</option>
                                        <option value="litre" {{ $item->unit == 'litre' ? 'selected' : '' }}>litre</option>
                                        <option value="ml" {{ $item->unit == 'ml' ? 'selected' : '' }}>ml</option>
                                        <option value="pcs" {{ $item->unit == 'pcs' ? 'selected' : '' }}>pcs</option>
                                        <option value="pack" {{ $item->unit == 'pack' ? 'selected' : '' }}>pack</option>
                                        <option value="box" {{ $item->unit == 'box' ? 'selected' : '' }}>box</option>
                                        <option value="dozen" {{ $item->unit == 'dozen' ? 'selected' : '' }}>dozen</option>
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" class="form-control qty-input" name="items[{{ $index }}][qty]" value="{{ $item->qty }}" required readonly></td>
                                <td><input type="number" step="0.01" class="form-control price-input" name="items[{{ $index }}][price]" value="{{ $item->price }}" required readonly></td>
                                <td><input type="number" step="0.01" class="form-control discount-input" name="items[{{ $index }}][discount]" value="{{ $item->discount }}" readonly></td>
                                <td><input type="number" step="0.01" class="form-control tax-input" name="items[{{ $index }}][tax]" value="{{ $item->tax }}" readonly></td>
                                <td><input type="number" step="0.01" class="form-control amount-input" name="items[{{ $index }}][amount]" value="{{ $item->amount }}" readonly></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-icon btn-edit" onclick="editRow(this)" title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn-icon btn-save" onclick="saveRow(this)" style="display:none;" title="Save">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn-icon btn-cancel" onclick="cancelRow(this)" style="display:none;" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" class="btn-icon btn-delete" onclick="removeRow(this)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <button type="button" class="btn btn-primary" onclick="addNewRow()">Add New Item</button>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Payment Type <span>*</span></label>
                    <input type="text" class="form-control" value="{{ $purchaseOrder->payment_type }}" disabled>
                    <input type="hidden" name="payment_type" value="{{ $purchaseOrder->payment_type }}">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Reference No</label>
                    <input type="text" class="form-control" name="reference_no" value="{{ $purchaseOrder->reference_no }}">
                </div>
                <div class="col-sm-12 col-md-8 col-xl-6 mb-3">
                    <label>Description</label>
                    <textarea rows="1" class="form-control" name="description">{{ $purchaseOrder->description }}</textarea>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Total <span>*</span></label>
                    <input type="number" step="0.01" class="form-control" name="total" id="totalField" value="{{ $purchaseOrder->total }}" required readonly>
                    <div class="round-off-wrapper">
                        <input type="checkbox" id="roundOffTotalCheckbox">
                        <label for="roundOffTotalCheckbox">Round Off</label>
                    </div>
                    <input type="hidden" name="round_off_total_amount" id="roundOffTotalAmount" value="0">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Paid Amount <span>*</span></label>
                    <input type="number" step="0.01" class="form-control" name="paid_amount" id="paidField" value="{{ $purchaseOrder->paid_amount }}" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Balance Amount <span>*</span></label>
                    <input type="number" step="0.01" class="form-control" name="balance_amount" id="balanceField" value="{{ $purchaseOrder->balance_amount }}" required readonly>
                    <div class="round-off-wrapper">
                        <input type="checkbox" id="roundOffCheckbox">
                        <label for="roundOffCheckbox">Round Off</label>
                    </div>
                    <input type="hidden" name="round_off_amount" id="roundOffAmount" value="0">
                </div>
            </div>

            <div class="col-12 mt-3 d-flex justify-content-center">
                <button type="submit" class="formbtn">Update Purchase Order</button>
            </div>
        </div>
    </form>
</div>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Global variables
let rowIndex = {{ count($purchaseOrder->items) }};
let itemsData = @json($items);
let calculationTimeout = null;

// Pre-build options HTML once for performance
let itemOptionsHTML = '<option value="">Select Item</option>';
itemsData.forEach(item => {
    const escapedName = $('<div>').text(item.item_name).html();
    itemOptionsHTML += `<option value="${item.id}">${escapedName}</option>`;
});

// Store original row data for cancel functionality
let originalRowData = {};

// Initialize
$(document).ready(function() {
    attachEventListeners();
    
    // Store original data for all existing rows
    $('#itemsBody tr').each(function() {
        const rowIndex = $(this).data('row');
        storeOriginalRowData(this, rowIndex);
    });
});

// Store original row data
function storeOriginalRowData(row, index) {
    const $row = $(row);
    originalRowData[index] = {
        itemId: $row.find('.item-id-input').val(),
        itemName: $row.data('item-name'),
        unit: $row.find('.unit-select').val(),
        qty: $row.find('.qty-input').val(),
        price: $row.find('.price-input').val(),
        discount: $row.find('.discount-input').val(),
        tax: $row.find('.tax-input').val(),
        amount: $row.find('.amount-input').val()
    };
}

// Attach event listeners
function attachEventListeners() {
    // Transport and packaging charges
    $('[name="transport"], [name="packaging"]').on('input', function() {
        debouncedCalculateTotal();
    });
    
    // Paid amount
    $('#paidField').on('input', calculateBalance);
    
    // Round off checkboxes
    $('#roundOffTotalCheckbox').on('change', calculateTotal);
    $('#roundOffCheckbox').on('change', calculateBalance);
}

// Edit row - enable editing mode
function editRow(btn) {
    const row = btn.closest('tr');
    const $row = $(row);
    const rowIdx = $row.data('row');
    
    // Store current data before editing
    storeOriginalRowData(row, rowIdx);
    
    // Add edit mode class
    $row.addClass('row-edit-mode');
    
    // Get current item ID from hidden input
    const itemId = $row.find('.item-id-input').val();
    
    // Initialize item dropdown with current value
    const $select = $row.find('.item-select');
    $select.html(itemOptionsHTML);
    $select.val(itemId);
    
    // Initialize Select2
    $select.select2({
        placeholder: "Search and select item",
        allowClear: true,
        width: '200px',
        dropdownParent: $row
    });
    
    // Enable all inputs and selects
    $row.find('.unit-select, .qty-input, .price-input, .discount-input, .tax-input').prop('readonly', false).prop('disabled', false);
    
    // Add calculation listeners
    $row.find('.qty-input, .price-input, .discount-input, .tax-input').off('input').on('input', function() {
        debouncedCalculateRow(this);
    });
    
    // Toggle buttons
    $(btn).hide();
    $row.find('.btn-save, .btn-cancel').show();
    $row.find('.btn-delete').hide();
}

// Save row - save changes and return to display mode
function saveRow(btn) {
    const row = btn.closest('tr');
    const $row = $(row);
    
    // Validate item selection
    const itemId = $row.find('.item-select').val();
    if (!itemId) {
        alert('Please select an item');
        return;
    }
    
    // Get item name
    const itemName = $row.find('.item-select option:selected').text();
    
    // Update hidden input with selected item ID
    $row.find('.item-id-input').val(itemId);
    
    // Update display
    $row.find('.item-display').text(itemName);
    $row.data('item-id', itemId);
    $row.data('item-name', itemName);
    
    // Destroy Select2
    if ($row.find('.item-select').hasClass('select2-hidden-accessible')) {
        $row.find('.item-select').select2('destroy');
    }
    
    // Remove edit mode
    $row.removeClass('row-edit-mode');
    
    // Disable inputs
    $row.find('.unit-select, .qty-input, .price-input, .discount-input, .tax-input').prop('readonly', true);
    $row.find('.unit-select').prop('disabled', true);
    
    // Remove calculation listeners
    $row.find('.qty-input, .price-input, .discount-input, .tax-input').off('input');
    
    // Toggle buttons
    $row.find('.btn-save, .btn-cancel').hide();
    $row.find('.btn-edit, .btn-delete').show();
    
    // Store new data
    const rowIdx = $row.data('row');
    storeOriginalRowData(row, rowIdx);
    
    // Recalculate
    calculateTotal();
}

// Cancel row - discard changes and return to display mode
function cancelRow(btn) {
    const row = btn.closest('tr');
    const $row = $(row);
    const rowIdx = $row.data('row');
    
    // Check if this is a new row (no original data)
    if (!originalRowData[rowIdx] || !originalRowData[rowIdx].itemId) {
        // This is a new unsaved row, just remove it
        removeRow(btn);
        return;
    }
    
    // Restore original data if exists
    if (originalRowData[rowIdx]) {
        const original = originalRowData[rowIdx];
        
        // Restore values
        $row.find('.item-id-input').val(original.itemId);
        $row.find('.item-display').text(original.itemName);
        $row.find('.unit-select').val(original.unit);
        $row.find('.qty-input').val(original.qty);
        $row.find('.price-input').val(original.price);
        $row.find('.discount-input').val(original.discount);
        $row.find('.tax-input').val(original.tax);
        $row.find('.amount-input').val(original.amount);
        
        $row.data('item-id', original.itemId);
        $row.data('item-name', original.itemName);
    }
    
    // Destroy Select2
    if ($row.find('.item-select').hasClass('select2-hidden-accessible')) {
        $row.find('.item-select').select2('destroy');
    }
    
    // Remove edit mode
    $row.removeClass('row-edit-mode');
    
    // Disable inputs
    $row.find('.unit-select, .qty-input, .price-input, .discount-input, .tax-input').prop('readonly', true);
    $row.find('.unit-select').prop('disabled', true);
    
    // Remove calculation listeners
    $row.find('.qty-input, .price-input, .discount-input, .tax-input').off('input');
    
    // Toggle buttons
    $row.find('.btn-save, .btn-cancel').hide();
    $row.find('.btn-edit, .btn-delete').show();
    
    // Recalculate totals
    calculateTotal();
}

// Add new row
function addNewRow() {
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-row', rowIndex);
    newRow.setAttribute('data-item-id', '');
    newRow.setAttribute('data-item-name', '');
    newRow.className = 'row-edit-mode';
    
    newRow.innerHTML = `
        <td class="serial-no">${rowIndex + 1}</td>
        <td>
            <div class="item-display"></div>
            <div class="item-edit-mode">
                <select class="form-select item-select" style="width: 200px">
                    ${itemOptionsHTML}
                </select>
            </div>
            <input type="hidden" class="item-id-input" name="items[${rowIndex}][item_id]" value="">
        </td>
        <td>
            <select class="form-select unit-select" style="width: 100px" name="items[${rowIndex}][unit]" required>
                <option value="" selected disabled>Select Unit</option>
                <option value="kg">kg</option>
                <option value="g">g</option>
                <option value="litre">litre</option>
                <option value="ml">ml</option>
                <option value="pcs">pcs</option>
                <option value="pack">pack</option>
                <option value="box">box</option>
                <option value="dozen">dozen</option>
            </select>
        </td>
        <td><input type="number" step="0.01" class="form-control qty-input" name="items[${rowIndex}][qty]" value="0" required></td>
        <td><input type="number" step="0.01" class="form-control price-input" name="items[${rowIndex}][price]" value="0" required></td>
        <td><input type="number" step="0.01" class="form-control discount-input" name="items[${rowIndex}][discount]" value="0"></td>
        <td><input type="number" step="0.01" class="form-control tax-input" name="items[${rowIndex}][tax]" value="0"></td>
        <td><input type="number" step="0.01" class="form-control amount-input" name="items[${rowIndex}][amount]" value="0" readonly></td>
        <td>
            <div class="action-buttons">
                <button type="button" class="btn-icon btn-edit" onclick="editRow(this)" style="display:none;" title="Edit">
                    <i class="fas fa-pen"></i>
                </button>
                <button type="button" class="btn-icon btn-save" onclick="saveRow(this)" title="Save">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" class="btn-icon btn-cancel" onclick="cancelRow(this)" title="Cancel">
                    <i class="fas fa-times"></i>
                </button>
                <button type="button" class="btn-icon btn-delete" onclick="removeRow(this)" style="display:none;" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    `;
    
    document.getElementById('itemsBody').appendChild(newRow);
    
    // Initialize Select2 for new row
    const $newSelect = $(newRow).find('.item-select');
    $newSelect.select2({
        placeholder: "Search and select item",
        allowClear: true,
        width: '200px',
        dropdownParent: $(newRow)
    });
    
    // Add calculation listeners
    $(newRow).find('.qty-input, .price-input, .discount-input, .tax-input').on('input', function() {
        debouncedCalculateRow(this);
    });
    
    rowIndex++;
    reorderSerialNumbers();
}

// Remove row
function removeRow(btn) {
    const rows = document.querySelectorAll('#itemsBody tr');
    if (rows.length <= 1) {
        alert('At least one item row is required!');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this item?')) {
        return;
    }
    
    const row = btn.closest('tr');
    const rowIdx = $(row).data('row');
    
    // Destroy Select2 if exists
    $(row).find('select.item-select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
    
    // Remove from original data storage
    delete originalRowData[rowIdx];
    
    row.remove();
    reorderSerialNumbers();
    calculateTotal();
}

// Reorder serial numbers
function reorderSerialNumbers() {
    const rows = document.querySelectorAll('#itemsBody tr');
    rows.forEach((row, index) => {
        row.querySelector('.serial-no').textContent = index + 1;
    });
}

// Debounced calculation
function debouncedCalculateRow(element) {
    clearTimeout(calculationTimeout);
    calculationTimeout = setTimeout(() => {
        calculateRowAmount(element);
    }, 150);
}

function debouncedCalculateTotal() {
    clearTimeout(calculationTimeout);
    calculationTimeout = setTimeout(() => {
        calculateTotal();
    }, 150);
}

// Calculate amount for a single row
function calculateRowAmount(element) {
    const row = element.closest('tr');
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
    const tax = parseFloat(row.querySelector('.tax-input').value) || 0;
    
    let amount = qty * price;
    amount = amount - (amount * discount / 100);
    amount = amount + (amount * tax / 100);
    
    row.querySelector('.amount-input').value = amount.toFixed(2);
    calculateTotal();
}

// Calculate total
function calculateTotal() {
    const amounts = document.querySelectorAll('.amount-input');
    const transport = parseFloat($('[name="transport"]').val()) || 0;
    const packaging = parseFloat($('[name="packaging"]').val()) || 0;
    
    let itemsTotal = 0;
    for (let i = 0; i < amounts.length; i++) {
        itemsTotal += parseFloat(amounts[i].value) || 0;
    }
    
    let grandTotal = itemsTotal + transport + packaging;
    
    if ($('#roundOffTotalCheckbox').is(':checked')) {
        const roundedTotal = Math.round(grandTotal);
        const roundOffDiff = grandTotal - roundedTotal;
        $('#totalField').val(roundedTotal.toFixed(2));
        $('#roundOffTotalAmount').val(roundOffDiff.toFixed(2));
    } else {
        $('#totalField').val(grandTotal.toFixed(2));
        $('#roundOffTotalAmount').val('0');
    }
    
    calculateBalance();
}

// Calculate balance
function calculateBalance() {
    const total = parseFloat($('#totalField').val()) || 0;
    const paid = parseFloat($('#paidField').val()) || 0;
    let balance = total - paid;
    
    if ($('#roundOffCheckbox').is(':checked')) {
        const roundedBalance = Math.round(balance);
        const roundOffDiff = balance - roundedBalance;
        $('#balanceField').val(roundedBalance.toFixed(2));
        $('#roundOffAmount').val(roundOffDiff.toFixed(2));
    } else {
        $('#balanceField').val(balance.toFixed(2));
        $('#roundOffAmount').val('0');
    }
}

// Form submission validation
// Replace the form submission validation section with this:

$('#purchaseOrderForm').on('submit', function(e) {
    e.preventDefault();
    
    // Check if any row is still in edit mode
    const editModeRows = $('.row-edit-mode').length;
    if (editModeRows > 0) {
        alert('Please save or cancel all items in edit mode before submitting.');
        return false;
    }
    
    // Validate all items
    const rows = $('#itemsBody tr');
    let hasError = false;
    
    rows.each(function() {
        const itemId = $(this).find('.item-id-input').val();
        if (!itemId) {
            hasError = true;
            return false;
        }
    });
    
    if (hasError) {
        alert('Please ensure all items are selected and saved.');
        return false;
    }
    
    // Re-enable all inputs
    $('#itemsBody select, #itemsBody input').prop('disabled', false);
    
    // Check item count
    const itemCount = rows.length;
    
    if (itemCount > 100) {
        // Use chunked submission for large datasets
        submitInChunks();
    } else {
        // Normal submission for smaller datasets
        this.submit();
    }
});

function submitInChunks() {
    const form = $('#purchaseOrderForm');
    const formData = new FormData(form[0]);
    const items = [];
    
    // Collect all items
    $('#itemsBody tr').each(function(index) {
        const $row = $(this);
        items.push({
            item_id: $row.find('.item-id-input').val(),
            unit: $row.find('.unit-select').val(),
            qty: $row.find('.qty-input').val(),
            price: $row.find('.price-input').val(),
            discount: $row.find('.discount-input').val() || 0,
            tax: $row.find('.tax-input').val() || 0,
            amount: $row.find('.amount-input').val()
        });
    });
    
    // Prepare base data
    const baseData = {
        _token: $('input[name="_token"]').val(),
        _method: 'PUT',
        total: $('#totalField').val(),
        paid_amount: $('#paidField').val(),
        balance_amount: $('#balanceField').val(),
        transport: $('input[name="transport"]').val() || 0,
        packaging: $('input[name="packaging"]').val() || 0,
        reference_no: $('input[name="reference_no"]').val(),
        description: $('textarea[name="description"]').val(),
        payment_type: $('input[name="payment_type"]').val(),
        warehouse: $('input[name="warehouse"]').val(),
        round_off_total_amount: $('#roundOffTotalAmount').val() || 0,
        round_off_amount: $('#roundOffAmount').val() || 0,
        items: items
    };
    
    // Show loading indicator
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    
    // Submit with all items at once (optimized payload)
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: JSON.stringify(baseData),
        contentType: 'application/json',
        processData: false,
        success: function(response) {
            window.location.href = response.redirect || '{{ route("purchase.order_list") }}';
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html(originalText);
            const errorMsg = xhr.responseJSON?.message || 'Failed to update purchase order. Please try again.';
            alert(errorMsg);
            console.error('Error:', xhr);
        }
    });
}

</script>

@endsection