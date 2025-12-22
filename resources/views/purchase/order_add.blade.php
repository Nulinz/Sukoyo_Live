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
</style>

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Purchase Order</h4>
    </div>

    <form method="POST" action="{{ route('purchase.order_store') }}" id="purchaseOrderForm">
        @csrf

        <div class="container-fluid form-div">
            <div class="body-head mb-3">
                <h5>Item Details</h5>
            </div>
            <div class="row">
             <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label>Vendor <span>*</span></label>
    @if($vendors->isEmpty())
        <div class="alert alert-warning p-2">
            No vendors available for your store.
        </div>
    @else
        <select class="form-select" name="vendor_id" id="vendor_id" required>
            <option value="" selected disabled>Select Option</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}">{{ $vendor->vendorname }}</option>
            @endforeach
        </select>
    @endif
</div>

<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label>Contact Number <span>*</span></label>
    <input type="text" class="form-control" name="contact" id="contact" required readonly>
</div>
<div class="col-sm-12 col-md-4 col-xl-3 mb-3">
    <label>Billing Address</label>
    <textarea rows="1" class="form-control" name="billaddress" id="billaddress" readonly></textarea>
</div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Bill No <span>*</span></label>
                    <input type="text" class="form-control" name="bill_no" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Bill Date <span>*</span></label>
                    <input type="date" class="form-control" name="bill_date" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label>Due Date <span>*</span></label>
                <input type="date" class="form-control" name="due_date" required>
                </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Transport Charge</label>
                    <input type="number" step="0.01" class="form-control" name="transport" value="0">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Packaging Charge</label>
                    <input type="number" step="0.01" class="form-control" name="packaging" value="0">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Store / Warehouse <span>*</span></label>
                    @if(session('role') === 'admin')
                        <select class="form-select" name="warehouse" required>
                            <option value="" selected disabled>Select Option</option>
                            <option value="Warehouse">Warehouse</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->store_name }}">{{ $store->store_name }}</option>
                            @endforeach
                        </select>
                    @else
                        <!-- Manager: fixed store -->
                        <input type="text" class="form-control" name="warehouse" value="{{ $stores[0]->store_name }}" readonly>
                    @endif
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
                            <tr data-row="0">
                                <td class="serial-no">1</td>
                                <td>
                                    <select class="form-select item-select" style="width: 200px" name="items[0][item_id]" required>
                                        <option value="" selected disabled>Select Item</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
<td>
    <select class="form-select" style="width: 100px" name="items[0][unit]" required>
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
                                <td><input type="number" step="0.01" class="form-control qty-input" name="items[0][qty]" value="0" required></td>
                                <td><input type="number" step="0.01" class="form-control price-input" name="items[0][price]" value="0" required></td>
                                <td><input type="number" step="0.01" class="form-control discount-input" name="items[0][discount]" value="0"></td>
                                <td><input type="number" step="0.01" class="form-control tax-input" name="items[0][tax]" value="0"></td>
                                <td><input type="number" step="0.01" class="form-control amount-input" name="items[0][amount]" value="0" readonly></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="listtdbtn" onclick="addRow()">Add</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
            <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <label>Payment Type <span>*</span></label>
                <select class="form-select" name="payment_type" required>
                    <option value="" selected disabled>Select Payment Type</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="UPI">UPI</option>
                    <option value="Wallet">Wallet</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Credit">Credit</option>
                </select>
            </div>

                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Reference No</label>
                    <input type="text" class="form-control" name="reference_no">
                </div>
                <div class="col-sm-12 col-md-8 col-xl-6 mb-3">
                    <label>Description</label>
                    <textarea rows="1" class="form-control" name="description"></textarea>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Total <span>*</span></label>
                    <input type="number" step="0.01" class="form-control" name="total" id="totalField" required readonly>
                    <div class="round-off-wrapper">
                        <input type="checkbox" id="roundOffTotalCheckbox">
                        <label for="roundOffTotalCheckbox">Round Off</label>
                    </div>
                    <input type="hidden" name="round_off_total_amount" id="roundOffTotalAmount" value="0">
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Paid Amount <span>*</span></label>
                    <input type="number" step="0.01" class="form-control" name="paid_amount" id="paidField" value="0" required>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Balance Amount <span>*</span></label>
                    <input type="number" step="0.01" class="form-control" name="balance_amount" id="balanceField" required readonly>
                    <div class="round-off-wrapper">
                        <input type="checkbox" id="roundOffCheckbox">
                        <label for="roundOffCheckbox">Round Off</label>
                    </div>
                    <input type="hidden" name="round_off_amount" id="roundOffAmount" value="0">
                </div>
            </div>

            <div class="col-12 mt-3 d-flex justify-content-center">
                <button type="submit" class="formbtn">Add Purchase Order</button>
            </div>
        </div>
    </form>
</div>

<!-- Include Select2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Global variables
let rowIndex = 1;
let itemsData = @json($items);

// Initialize
$(document).ready(function() {
    initializeSelect2();
    attachEventListeners();
});

// Initialize Select2 for all item selects
function initializeSelect2() {
    $('.item-select').select2({
        placeholder: "Search and select item",
        allowClear: true,
        width: '200px'
    });
}

// Attach event listeners using event delegation for better performance
function attachEventListeners() {
    // Use event delegation for dynamically added rows
    $('#itemsBody').on('input', '.qty-input, .price-input, .discount-input, .tax-input', function() {
        calculateRowAmount(this);
    });
    
    // Transport and packaging charges
    $('[name="transport"], [name="packaging"]').on('input', calculateTotal);
    
    // Paid amount
    $('#paidField').on('input', calculateBalance);
    
    // Round off checkboxes
    $('#roundOffTotalCheckbox').on('change', calculateTotal);
    $('#roundOffCheckbox').on('change', calculateBalance);
}

// Add new row
function addRow() {
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-row', rowIndex);
    
    newRow.innerHTML = `
        <td class="serial-no">${rowIndex + 1}</td>
        <td>
            <select class="form-select item-select-${rowIndex}" style="width: 200px" name="items[${rowIndex}][item_id]" required>
                <option value="" selected disabled>Select Item</option>
                ${itemsData.map(item => `<option value="${item.id}">${item.item_name}</option>`).join('')}
            </select>
        </td>
        <td>
            <select class="form-select" style="width: 100px" name="items[${rowIndex}][unit]" required>
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
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="listtdbtn" onclick="removeRow(this)">Delete</button>
            </div>
        </td>
    `;
    
    document.getElementById('itemsBody').appendChild(newRow);
    
    // Initialize Select2 for the new row
    $(`.item-select-${rowIndex}`).select2({
        placeholder: "Search and select item",
        allowClear: true,
        width: '200px'
    });
    
    rowIndex++;
}

// Remove row
function removeRow(btn) {
    // Check if there's only one row left
    const rows = document.querySelectorAll('#itemsBody tr');
    if (rows.length <= 1) {
        alert('At least one item row is required!');
        return;
    }
    
    const row = btn.closest('tr');
    
    // Destroy Select2 before removing
    $(row).find('select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
    
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

// Calculate amount for a single row
function calculateRowAmount(element) {
    const row = element.closest('tr');
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
    const tax = parseFloat(row.querySelector('.tax-input').value) || 0;
    
    // Calculate amount
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
    amounts.forEach(input => {
        itemsTotal += parseFloat(input.value) || 0;
    });
    
    let grandTotal = itemsTotal + transport + packaging;
    
    // Check if round off is enabled for total
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
    
    // Check if round off is enabled for balance
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
</script>

<script>
    document.getElementById('vendor_id').addEventListener('change', function () {
        const vendorId = this.value;
        if (vendorId) {
            fetch(`/vendor-details/${vendorId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('contact').value = data.contact || '';
                    document.getElementById('billaddress').value = data.billaddress || '';
                })
                .catch(error => {
                    console.error('Error fetching vendor details:', error);
                });
        }
    });
</script>

@endsection