@include('layouts.header')

<link rel="stylesheet" href="{{ asset('assets/css/bill.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

<style>
    #addedTable {
        display: none;
    }

    .modal-dialog {
        max-width: 500px;
    }
    
    .barcode-input {
        position: relative;
    }
    
    .barcode-input input {
        padding-right: 40px;
    }
    
    .barcode-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    
    .scanner-feedback {
        font-size: 0.8em;
        margin-top: 5px;
    }
    
    .scanner-feedback.success {
        color: #28a745;
    }
    
    .scanner-feedback.error {
        color: #dc3545;
    }
    
</style>

<div class="container-fluid">
    <div class="bill-div">
        <div class="bill-header">
            <div class="bill-head-left">
                <div class="header-ct">
                    <h6>Billing Screen 1</h6>
                    <a><i class="fas fa-xmark"></i></a>
                </div>
                <div class="header-ct">
                    <h6>Hold Bill & Add Another</h6>
                    <a><i class="fas fa-plus"></i></a>
                </div>
            </div>
            <div class="bill-head-right">
                <button type="button" class="cancelbtn">Retail POS</button>

                @php
                    $role = Session::get('role'); // or use Auth::user()->role if using auth system
                    $redirectRoute = $role === 'admin' ? route('dashboard.admin') : route('dashboard.bill');
                @endphp

                <a href="{{ $redirectRoute }}">
                    <button type="button" class="modalbtn">
                        <i class="fas fa-angle-left pe-2"></i>Exit POS
                    </button>
                </a>
            </div>
        </div>

        <div class="bill-body">
            <div class="bill-body-left pt-3">
            <div class="table-wrapper">
                                <!-- Barcode Scanner Input -->
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="barcode-input">
                                            <input type="text" 
                                                id="barcodeInput" 
                                                class="form-control" 
                                                placeholder="Scan or enter barcode and press Enter..." 
                                                autocomplete="off"
                                                style="height:40px; font-size: 16px; font-weight: bold;">
                                            <i class="fas fa-barcode barcode-icon"></i>
                                        </div>
                                        <div id="scannerFeedback" class="scanner-feedback"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" id="manualSelect" class="btn btn-outline-secondary w-100" style="height:40px;" data-bs-toggle="modal" data-bs-target="#itemSelectModal">
                                            <i class="fas fa-list"></i> Manual Select
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-info w-100" style="height:40px;" onclick="document.getElementById('barcodeInput').focus();">
                                            <i class="fas fa-keyboard"></i> Focus Scanner
                                        </button>
                                    </div>
                                </div>

                                <!-- Instructions -->
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Scan barcode or type item code and press Enter. Items will be automatically added with quantity 1.
                                    </small>
                                </div>

                <!-- Added Items Table -->
                        <table class="table table-bordered mt-4" id="addedTable" style="display:none">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 25%;">Item</th>
                            <th style="width: 10%;">Unit</th>
                            <th style="width: 10%;">Qty</th>
                            <th style="width: 12%;">Price</th>
                            <th style="width: 10%;">Discount (%)</th>
                            <th style="width: 10%;">Tax (%)</th>
                            <th style="width: 13%;">Amount (₹)</th>
                            <th style="width: 5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot style="display: none;" id="tableFooter">
                        <tr class="table-info">
                            <td colspan="7" class="text-end fw-bold">Total Items:</td>
                            <td class="fw-bold" id="totalItems">0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            </div>

            <div class="brdr"></div>

            <div class="bill-body-right pt-3">
                <div class="offerbtns">
                    <button type="button" class="listbtn px-1 mb-2" data-bs-toggle="modal" data-bs-target="#discount">
                        Add Discount [F5]
                    </button>
                    <button type="button" class="listbtn px-1 mb-2" data-bs-toggle="modal" data-bs-target="#additional">
                        Additional Charges [F6]
                    </button>
                    <button type="button" class="listbtn px-1 mb-2" data-bs-toggle="modal" data-bs-target="#lp">
                        Loyalty Points [F7]
                    </button>
                    <button type="button" class="listbtn px-1 mb-2" data-bs-toggle="modal" data-bs-target="#giftcards">
                        Gift Cards & Vouchers
                    </button>
                </div>

                <div class="details mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Customer Details</h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customer">
                            + Add Customer
                        </button>
                    </div>
                    <hr class="m-0">
                    <div class="mt-2">
                        <p class="mb-1 fw-semibold text-dark">Sukoyo Store</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0 text-muted" id="selectedCustomer">
                                @if(!empty($customer_name) && !empty($customer_contact))
                                    {{ $customer_name }} - {{ $customer_contact }}
                                @else
                                    No customer selected
                                @endif
                            </p>
                            <div class="d-flex flex-column align-items-end gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('assets/images/icon_7.png') }}" alt="Points" height="18">
                                    <span class="text-muted" id="loyaltyPointsDisplay">
                                        @if(!empty($customer_name) && !empty($customer_contact))
                                            {{ \App\Models\Customer::where('contact', $customer_contact)->value('loyalty_points') ?? 0 }} Points
                                        @else
                                            0 Points
                                        @endif
                                    </span>
                                </div>
                                <div style="display: none;" id="usedPointsContainer">
                                    <small class="text-warning">Used Points: <span id="usedPointsDisplay">0</span></small>
                                </div>
                                <div style="display: none;" id="remainingPointsContainer">
                                    <small class="text-success">Remaining Points: <span id="remainingPoints">0</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="details mt-3">
                    <div class="body-head mb-2">
                        <h4>Bill Details</h4>
                    </div>
                    <hr class="m-0">
                    <table class="table table-borderless mt-2 mb-0">
                        <tbody>
                            <tr>
                                <th class="text-start">Sub Total</th>
                                <td class="text-end" id="subTotal">₹ 0.00</td>
                            </tr>
                            <tr>
                                <th class="text-start">Item Discounts</th>
                                <td class="text-end" id="itemDiscounts">₹ 0.00</td>
                            </tr>
                            <tr id="billDiscountRow" style="color: #dc3545;">
                                <th class="text-start">
                                    Bill Discount 
                                    <span id="billDiscountInfo" style="font-size: 0.8em; color: #6c757d;"></span>
                                </th>
                                <td class="text-end" id="billDiscount">₹ 0.00</td>
                            </tr>
                            <tr>
                                <th class="text-start">Tax</th>
                                <td class="text-end" id="totalTax">₹ 0.00</td>
                            </tr>
                            <tr>
                                <th class="text-start">Additional Charges</th>
                                <td class="text-end" id="additionalCharges">₹ 0.00</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-start">Total</td>
                                <td class="text-end" id="grandTotal">₹ 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="details mt-3">
                    <div class="body-head mb-2">
                        <h4>Received Amount</h4>
                    </div>
                    <hr class="m-0">
                    <table class="table table-borderless mt-2 mb-0">
                        <tbody>
                            <tr>
                                <th class="text-start">
                                    <input type="number" class="form-control" id="receivedAmount" placeholder="Enter amount" value="0">
                                </th>
                                <td class="d-flex align-items-center justify-content-end">
                                    <select class="form-select" name="" id="mop">
                                        <option value="Cash">Cash</option>
                                        <option value="Online">Online</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-start">Change To Return</td>
                                <td class="text-end" id="changeToReturn">₹ 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="offerbtns mt-3">
                   <form id="posForm">
                       <input type="hidden" id="customer_name" name="customer_name">
                       <input type="hidden" id="customer_contact" name="customer_contact">
                       <input type="hidden" id="additionalChargesInput" value="0">
                       <button type="button" class="listbtn px-1 mb-2" id="savePrintBtn">Save & Print </button>                   
                   </form>
                    <button type="button" class="listbtn px-1 mb-2">Save & Print [F12]</button>
                    <button type="button" class="listbtn px-1 mb-2" data-bs-toggle="modal" data-bs-target="#corporatebill">Corporate Bill</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Item Selection Modal -->
<div class="modal fade" id="itemSelectModal" tabindex="-1" aria-labelledby="itemSelectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemSelectModalLabel">Select Item Manually</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="itemSearchInput" placeholder="Search items...">
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            @forelse($items as $item)
                                <tr data-item-id="{{ $item->id }}"
                                    data-item-code="{{ $item->item_code }}"
                                    data-item-name="{{ $item->item_name }}"
                                    data-unit="{{ $item->measure_unit }}"
                                    data-price="{{ $item->sales_price }}"
                                    data-tax="{{ $item->gst_rate }}"
                                    data-discount="{{ $item->department }}"
                                    data-stock="{{ $item->current_stock ?? 0 }}">
                                    <td>{{ $item->item_code }}</td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->measure_unit }}</td>
                                    <td>₹{{ number_format($item->sales_price, 2) }}</td>
                                    <td>{{ $item->current_stock ?? 0 }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary select-item-btn">Select</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No items available for this store</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Discount Modal -->
<div class="modal fade" id="discount" tabindex="-1" aria-labelledby="discountLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Bill Discounts</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="discountForm">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-3">
                            <label class="form-label fw-semibold">Apply Discount:</label>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="discountTiming" value="before" id="before" checked>
                                    <label class="form-check-label" for="before">Before Tax</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="discountTiming" value="after" id="after">
                                    <label class="form-check-label" for="after">After Tax</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-12 mb-3">
                            <label class="form-label fw-semibold">Discount Type:</label>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="discountType" value="percentage" id="discountPercentage" checked>
                                    <label class="form-check-label" for="discountPercentage">Percentage (%)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="discountType" value="amount" id="discountAmount">
                                    <label class="form-check-label" for="discountAmount">Fixed Amount (₹)</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-12 mb-3">
                            <label for="percentage" class="form-label">Percentage (%)</label>
                            <input type="number" class="form-control" id="percentage" min="0" max="100" step="0.01" placeholder="Enter percentage">
                        </div>
                        
                        <div class="col-sm-12 col-md-12 mb-3">
                            <label for="discamount" class="form-label">Amount (₹)</label>
                            <input type="number" class="form-control" id="discamount" min="0" step="0.01" placeholder="Enter amount">
                        </div>
                        
                        <!-- Current Discount Display -->
                        <div class="col-sm-12 mb-3" id="currentDiscountDisplay" style="display: none;">
                            <div class="alert alert-info">
                                <strong>Current Discount:</strong> <span id="currentDiscountText"></span>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center gap-2 mx-auto mt-3">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-25">Cancel</button>
                            <button type="button" id="clearDiscount" class="btn btn-warning w-25">Clear</button>
                            <button type="submit" class="modalbtn w-50">Apply Discount</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('pos.pos_popups')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Enhanced Automatic Barcode Scanner JavaScript Code
// Replace the existing barcode scanner section in your script

let itemCount = 0;
let billDiscountData = {
    type: null, // 'percentage' or 'amount'
    value: 0,
    timing: 'before', // 'before' or 'after'
    amount: 0
};

// Store items data for barcode lookup
let itemsData = @json($items); // This should be populated from your Laravel backend

// Enhanced Barcode Scanner functionality
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcodeInput');
    
    // Focus on barcode input when page loads
    barcodeInput.focus();
    
    // Handle Enter key press
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            processBarcode();
        }
    });
    
    // Handle barcode scanner input (scanners typically send data quickly and then blur)
    let scannerTimeout;
    barcodeInput.addEventListener('input', function(e) {
        clearTimeout(scannerTimeout);
        // Wait for scanner to finish input (usually very fast)
        scannerTimeout = setTimeout(() => {
            if (this.value.length >= 3) { // Minimum barcode length
                processBarcode();
            }
        }, 100);
    });
    
    // Also handle paste events (some scanners emulate paste)
    barcodeInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            processBarcode();
        }, 10);
    });
});

function processBarcode() {
    const barcodeInput = document.getElementById('barcodeInput');
    const barcode = barcodeInput.value.trim();
    const feedback = document.getElementById('scannerFeedback');
    
    if (!barcode) {
        showFeedback('Please scan or enter a barcode', 'error');
        return;
    }

    console.log('Processing barcode:', barcode); // Debug log
    console.log('Available items:', itemsData); // Debug log

    // Find item by barcode/item_code (case-insensitive search)
    const item = itemsData.find(item => 
        (item.item_code && item.item_code.toString().toLowerCase() === barcode.toLowerCase()) || 
        (item.barcode && item.barcode.toString().toLowerCase() === barcode.toLowerCase()) ||
        (item.id && item.id.toString() === barcode) ||
        (item.item_name && item.item_name.toLowerCase().includes(barcode.toLowerCase()))
    );

    console.log('Found item:', item); // Debug log

    if (item) {
        // Item found, add to bill with quantity 1
        addItemToBill(item);
        barcodeInput.value = ''; // Clear input
        showFeedback(`✓ ${item.item_name} added successfully`, 'success');
        
        // Focus back to barcode input for next scan
        setTimeout(() => {
            barcodeInput.focus();
            clearFeedback();
        }, 1500);
    } else {
        showFeedback(`✗ Item not found: ${barcode}`, 'error');
        // Keep the input for user to see what was scanned
        setTimeout(() => {
            barcodeInput.select(); // Select text for easy replacement
            clearFeedback();
        }, 3000);
    }
}

function showFeedback(message, type) {
    const feedback = document.getElementById('scannerFeedback');
    feedback.innerHTML = message;
    feedback.className = `scanner-feedback ${type}`;
}

function clearFeedback() {
    const feedback = document.getElementById('scannerFeedback');
    feedback.innerHTML = '';
    feedback.className = 'scanner-feedback';
}

function addItemToBill(item) {
    const qty = 1; // Always add with quantity 1
    
    console.log('Adding item to bill:', item); // Debug log
    
    // Check if item already exists in the table
    const existingRow = document.querySelector(`#addedTable tbody tr[data-item-id="${item.id}"]`);
    if (existingRow) {
        // Item already exists, update quantity
        const existingQtyCell = existingRow.cells[3];
        const existingQty = parseFloat(existingQtyCell.textContent) || 0;
        const newQty = existingQty + qty;
        
        // Check stock for new quantity
        const currentStock = parseFloat(item.current_stock) || 0;
        if (currentStock < newQty) {
            showFeedback(`✗ Insufficient stock. Available: ${currentStock}, Requested: ${newQty}`, 'error');
            return;
        }
        
        // Update the existing row
        updateExistingItemRow(existingRow, item, newQty);
        showFeedback(`✓ ${item.item_name} quantity updated to ${newQty}`, 'success');
    } else {
        // New item, check stock
        const currentStock = parseFloat(item.current_stock) || 0;
        if (currentStock < qty) {
            showFeedback(`✗ Insufficient stock. Available: ${currentStock}`, 'error');
            return;
        }
        
        // Add new row
        addNewItemRow(item, qty);
        showFeedback(`✓ ${item.item_name} added to bill`, 'success');
    }

    updateTotals();
    
    // Ensure table is visible
    const table = document.getElementById('addedTable');
    table.style.display = 'table';
}

function addNewItemRow(item, qty) {
    const price = parseFloat(item.sales_price) || 0;
    const discount = parseFloat(item.department) || 0; // Assuming department holds discount percentage
    const tax = parseFloat(item.gst_rate) || 0;

    // Calculate amounts - Corrected formula: Price + Tax - Discount
    const lineSubtotal = price * qty;
    const discountAmount = lineSubtotal * (discount / 100);
    const taxAmount = lineSubtotal * (tax / 100);
    const totalAmount = lineSubtotal + taxAmount - discountAmount; // Price + Tax - Discount

    itemCount++;

    const row = `
        <tr data-item-id="${item.id}" data-item-code="${item.item_code || ''}" data-stock="${item.current_stock || 0}">
            <td class="item-sno">${itemCount}</td>
            <td class="item-name" title="${item.item_name}">${item.item_name}</td>
            <td class="item-unit">${item.measure_unit || 'PCS'}</td>
            <td class="item-qty" contenteditable="true" data-original-qty="${qty}">${qty}</td>
            <td class="item-price">₹ ${price.toFixed(2)}</td>
            <td class="item-discount">${discount.toFixed(2)}</td>
            <td class="item-tax">${tax.toFixed(2)}</td>
            <td class="item-amount">₹ ${totalAmount.toFixed(2)}</td>
            <td class="item-actions">
                <button type="button" class="btn btn-danger btn-sm remove-item" title="Remove Item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    const table = document.getElementById('addedTable');
    const tbody = table.querySelector('tbody');
    tbody.insertAdjacentHTML('beforeend', row);
    
    // Make the new row's quantity editable
    makeQuantityEditable(tbody.lastElementChild);
    
    // Add highlight animation to newly added row
    const newRow = tbody.lastElementChild;
    newRow.classList.add('newly-added');
    setTimeout(() => {
        newRow.classList.remove('newly-added');
    }, 2000);
}

function updateExistingItemRow(row, item, newQty) {
    const price = parseFloat(item.sales_price) || 0;
    const discount = parseFloat(item.department) || 0;
    const tax = parseFloat(item.gst_rate) || 0;

    // Calculate new amounts - Corrected formula: Price + Tax - Discount
    const lineSubtotal = price * newQty;
    const discountAmount = lineSubtotal * (discount / 100);
    const taxAmount = lineSubtotal * (tax / 100);
    const totalAmount = lineSubtotal + taxAmount - discountAmount; // Price + Tax - Discount

    // Update the row
    row.cells[3].textContent = newQty; // Quantity
    row.cells[7].textContent = `₹ ${totalAmount.toFixed(2)}`; // Amount
    
    // Update data attributes
    row.querySelector('.item-qty').setAttribute('data-original-qty', newQty);
    
    // Add highlight animation to updated row
    row.classList.add('newly-added');
    setTimeout(() => {
        row.classList.remove('newly-added');
    }, 2000);
}

function makeQuantityEditable(row) {
    const qtyCell = row.querySelector('.item-qty');
    
    qtyCell.addEventListener('blur', function() {
        const newQty = parseFloat(this.textContent) || 1;
        const itemId = row.getAttribute('data-item-id');
        const maxStock = parseFloat(row.getAttribute('data-stock')) || 0;
        
        if (newQty <= 0) {
            alert('Quantity must be greater than 0');
            this.textContent = this.getAttribute('data-original-qty');
            return;
        }
        
        if (newQty > maxStock) {
            alert(`Insufficient stock. Maximum available: ${maxStock}`);
            this.textContent = this.getAttribute('data-original-qty');
            return;
        }
        
        // Find the item in itemsData
        const item = itemsData.find(item => item.id.toString() === itemId);
        if (item) {
            updateExistingItemRow(row, item, newQty);
            updateTotals();
        }
    });
    
    qtyCell.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.blur();
        }
        // Only allow numbers and decimal point
        if (!/[\d\.]/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
        }
    });
}

// Enhanced Remove item functionality
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
        const row = e.target.closest('tr');
        const itemName = row.querySelector('.item-name').textContent;
        
        if (confirm(`Remove ${item.item_name} from bill?`)) {
            row.remove();
            updateTotals();
            
            // Hide table if no items left
            const remainingRows = document.querySelectorAll('#addedTable tbody tr').length;
            if (remainingRows === 0) {
                document.getElementById('addedTable').style.display = 'none';
                itemCount = 0; // Reset counter
                showFeedback('All items removed from bill', 'success');
                setTimeout(clearFeedback, 2000);
            }
        }
    }
});

// Update totals function with corrected calculation
function updateTotals() {
    let subtotal = 0;
    let totalItemDiscountAmount = 0;
    let totalTaxAmount = 0;
    
    // Get all table rows
    const rows = document.querySelectorAll('#addedTable tbody tr');
    
    rows.forEach(row => {
        const cells = row.cells;
        const qty = parseFloat(cells[3].textContent) || 0;
        const price = parseFloat(cells[4].textContent.replace('₹', '').trim()) || 0;
        const discountPercent = parseFloat(cells[5].textContent) || 0;
        const taxPercent = parseFloat(cells[6].textContent) || 0;
        
        // Calculate individual amounts using corrected formula
        const itemSubtotal = qty * price;
        const itemDiscountAmount = itemSubtotal * (discountPercent / 100);
        const itemTaxAmount = itemSubtotal * (taxPercent / 100);
        
        // Add to totals
        subtotal += itemSubtotal;
        totalItemDiscountAmount += itemDiscountAmount;
        totalTaxAmount += itemTaxAmount;
    });
    
    // Get additional charges
    const additional = parseFloat(document.getElementById('additionalChargesInput')?.value || 0);
    
    // Calculate bill discount
    let billDiscountAmount = 0;
    if (billDiscountData.type && billDiscountData.value > 0) {
        if (billDiscountData.timing === 'before') {
            // Apply discount before tax on (subtotal - item discounts)
            const discountBase = subtotal - totalItemDiscountAmount;
            if (billDiscountData.type === 'percentage') {
                billDiscountAmount = discountBase * (billDiscountData.value / 100);
            } else {
                billDiscountAmount = Math.min(billDiscountData.value, discountBase);
            }
        } else {
            // Apply discount after tax on full amount including tax
            const discountBase = subtotal - totalItemDiscountAmount + totalTaxAmount;
            if (billDiscountData.type === 'percentage') {
                billDiscountAmount = discountBase * (billDiscountData.value / 100);
            } else {
                billDiscountAmount = Math.min(billDiscountData.value, discountBase);
            }
        }
    }
    
    // Update display
    document.getElementById('subTotal').textContent = `₹ ${subtotal.toFixed(2)}`;
    document.getElementById('itemDiscounts').textContent = `₹ ${totalItemDiscountAmount.toFixed(2)}`;
    document.getElementById('billDiscount').textContent = `₹ ${billDiscountAmount.toFixed(2)}`;
    document.getElementById('totalTax').textContent = `₹ ${totalTaxAmount.toFixed(2)}`;
    document.getElementById('additionalCharges').textContent = `₹ ${additional.toFixed(2)}`;
    
    // Calculate grand total using corrected formula: Subtotal + Tax - Item Discounts - Bill Discount + Additional
    const grandTotal = subtotal + totalTaxAmount - totalItemDiscountAmount - billDiscountAmount + additional;
    
    document.getElementById('grandTotal').textContent = `₹ ${grandTotal.toFixed(2)}`;
    
    // Update bill discount info
    updateBillDiscountDisplay();
    
    // Update change calculation
    calculateChange();
}

function updateBillDiscountDisplay() {
    const billDiscountRow = document.getElementById('billDiscountRow');
    const billDiscountInfo = document.getElementById('billDiscountInfo');
    
    if (billDiscountData.type && billDiscountData.value > 0) {
        billDiscountRow.style.display = 'table-row';
        let infoText = '';
        if (billDiscountData.type === 'percentage') {
            infoText = `(${billDiscountData.value}% ${billDiscountData.timing} tax)`;
        } else {
            infoText = `(₹${billDiscountData.value} ${billDiscountData.timing} tax)`;
        }
        billDiscountInfo.textContent = infoText;
    } else {
        billDiscountRow.style.display = 'none';
    }
}

function calculateChange() {
    const receivedAmount = parseFloat(document.getElementById('receivedAmount').value) || 0;
    const grandTotalText = document.getElementById('grandTotal').textContent;
    const grandTotal = parseFloat(grandTotalText.replace('₹', '').trim()) || 0;
    const change = receivedAmount - grandTotal;
    document.getElementById('changeToReturn').textContent = `₹ ${change.toFixed(2)}`;
}

// Auto-focus on barcode input when modal closes or page interaction occurs
document.addEventListener('click', function(e) {
    // Don't auto-focus if user is editing quantity or in a modal
    if (!e.target.closest('.modal') && !e.target.closest('[contenteditable="true"]')) {
        setTimeout(() => {
            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput && !document.querySelector('.modal.show')) {
                barcodeInput.focus();
            }
        }, 100);
    }
});

// Handle modal events to refocus on barcode input
document.addEventListener('hidden.bs.modal', function () {
    setTimeout(() => {
        document.getElementById('barcodeInput').focus();
    }, 300);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // F4 - Focus on barcode input
    if (e.key === 'F4') {
        e.preventDefault();
        document.getElementById('barcodeInput').focus();
    }
    
    // Escape - Clear barcode input
    if (e.key === 'Escape' && document.activeElement === document.getElementById('barcodeInput')) {
        document.getElementById('barcodeInput').value = '';
        clearFeedback();
    }
});

// Event listeners
document.getElementById('receivedAmount').addEventListener('input', calculateChange);
document.getElementById('additionalChargesInput').addEventListener('input', updateTotals);

console.log('Enhanced automatic barcode scanner initialized'); // Debug log
</script>

<script>
    const clearBtn = document.getElementById('clearbtn');
    const addBtn = document.getElementById('addbtn');
    const addedTable = document.getElementById('addedTable');
    const addedTableBody = addedTable.getElementsByTagName('tbody')[0];

    // Additional charges from modals (you can modify these based on your modal interactions)
    let additionalChargesAmount = 0;

    function updateTotalItems() {
        const totalCount = document.querySelectorAll('#addedTable tbody tr').length;
        const itemCountsElement = document.getElementById('itemCounts');
        if (itemCountsElement) {
            itemCountsElement.textContent = totalCount;
        }
    }

    function calculateBillTotals() {
        const rows = addedTableBody.getElementsByTagName('tr');
        let subTotal = 0;
        let totalDiscountAmount = 0;
        let totalTaxAmount = 0;

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].cells;
            const qty = parseFloat(cells[3].innerText) || 0;
            const price = parseFloat(cells[4].innerText.replace('₹', '').trim()) || 0;
            const discountPercent = parseFloat(cells[5].innerText) || 0;
            const taxPercent = parseFloat(cells[6].innerText) || 0;

            // Calculate item subtotal (before discount and tax)
            const itemSubTotal = qty * price;
            subTotal += itemSubTotal;

            // Calculate discount amount
            const discountAmount = itemSubTotal * (discountPercent / 100);
            totalDiscountAmount += discountAmount;

            // Calculate tax amount (on original price, not discounted)
            const taxAmount = itemSubTotal * (taxPercent / 100);
            totalTaxAmount += taxAmount;
        }

        // Update display
        document.getElementById('subTotal').textContent = `₹ ${subTotal.toFixed(2)}`;
        document.getElementById('itemDiscounts').textContent = `₹ ${totalDiscountAmount.toFixed(2)}`;
        document.getElementById('totalTax').textContent = `₹ ${totalTaxAmount.toFixed(2)}`;
        document.getElementById('additionalCharges').textContent = `₹ ${additionalChargesAmount.toFixed(2)}`;

        // Calculate grand total with bill discount
        let billDiscountAmount = 0;
        if (billDiscountData.type && billDiscountData.value > 0) {
            if (billDiscountData.timing === 'before') {
                // Apply discount before tax on (subtotal - item discounts)
                const discountBase = subTotal - totalDiscountAmount;
                if (billDiscountData.type === 'percentage') {
                    billDiscountAmount = discountBase * (billDiscountData.value / 100);
                } else {
                    billDiscountAmount = Math.min(billDiscountData.value, discountBase);
                }
            } else {
                // Apply discount after tax on full amount including tax
                const discountBase = subTotal - totalDiscountAmount + totalTaxAmount;
                if (billDiscountData.type === 'percentage') {
                    billDiscountAmount = discountBase * (billDiscountData.value / 100);
                } else {
                    billDiscountAmount = Math.min(billDiscountData.value, discountBase);
                }
            }
        }

        document.getElementById('billDiscount').textContent = `₹ ${billDiscountAmount.toFixed(2)}`;

        let grandTotal;
        if (billDiscountData.timing === 'before') {
            grandTotal = subTotal - totalDiscountAmount - billDiscountAmount + totalTaxAmount + additionalChargesAmount;
        } else {
            grandTotal = subTotal - totalDiscountAmount + totalTaxAmount - billDiscountAmount + additionalChargesAmount;
        }
        
        document.getElementById('grandTotal').textContent = `₹ ${grandTotal.toFixed(2)}`;

        // Update change calculation
        calculateChange();
    }

    // Clear Button function (if exists)
    if (clearBtn) {
        clearBtn.disabled = true;
        clearBtn.addEventListener('click', function() {
            addedTableBody.innerHTML = '';
            addedTable.style.display = 'none';
            clearBtn.disabled = true;
            // Reset bill discount when clearing
            billDiscountData = {
                type: null,
                value: 0,
                timing: 'before',
                amount: 0
            };
            updateTotalItems();
            calculateBillTotals();
        });
    }

    // Real-time calculation for input fields (if they exist)
    ['qtyInput', 'priceInput', 'discountInput', 'taxInput'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', () => {
                const qty = parseFloat(document.getElementById('qtyInput').value) || 0;
                const price = parseFloat(document.getElementById('priceInput').value) || 0;
                const discount = parseFloat(document.getElementById('discountInput').value) || 0;
                const tax = parseFloat(document.getElementById('taxInput').value) || 0;

                // Calculation
                const discountedPrice = price - (price * discount / 100);
                const taxedPrice = discountedPrice + (discountedPrice * tax / 100);
                const amount = qty * taxedPrice;
                const amtElement = document.getElementById('amtInput');
                if (amtElement) {
                    amtElement.value = amount.toFixed(2);
                }
            });
        }
    });

    // Handle inline editing of table cells
    document.getElementById('addedTable').addEventListener('blur', function(e) {
        const cell = e.target;
        const row = cell.closest('tr');
        if (!row) return;
        
        const cells = row.children;
        const qty = parseFloat(cells[3].innerText) || 0;
        const price = parseFloat(cells[4].innerText.replace('₹', '').trim()) || 0;
        const discount = parseFloat(cells[5].innerText) || 0;
        const tax = parseFloat(cells[6].innerText) || 0;

        // Recalculate amount for this row
        const itemSubTotal = qty * price;
        const discountAmount = itemSubTotal * (discount / 100);
        const taxAmount = itemSubTotal * (tax / 100);
        const amt = itemSubTotal - discountAmount + taxAmount;
        
        cells[7].innerText = `₹ ${amt.toFixed(2)}`;

        // Recalculate all totals
        updateTotals();
    }, true);

    // Customer form handling (if form exists)
    const customerForm = document.getElementById('customerForm');
    if (customerForm) {
        customerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            let name = document.getElementById('name').value;
            let contact = document.getElementById('contact').value;

            if (!name || !contact) {
                alert("Please enter both name and contact.");
                return;
            }

            document.getElementById('selectedCustomer').innerText = `${name} - ${contact}`;
            let modal = bootstrap.Modal.getInstance(document.getElementById('customer'));
            if (modal) {
                modal.hide();
            }
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // F5 for discount modal
        if (e.key === 'F5') {
            e.preventDefault();
            $('#discount').modal('show');
        }
        
        // F12 for save & print
        if (e.key === 'F12') {
            e.preventDefault();
            $('#saveBillBtn').click();
        }
        
        // Enter key on item select to add item
        if (e.key === 'Enter' && e.target.id === 'itemSelect') {
            e.preventDefault();
            document.getElementById('addItem').click();
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Initialize Select2 if itemInput exists
        const itemInput = $('#itemInput');
        if (itemInput.length) {
            itemInput.select2({
                width: "100%",
                placeholder: "Select Item",
                allowClear: true
            });

            itemInput.on('select2:open', function() {
                $('.select2-search__field').focus();
            });
        }

        // Initialize Select2 for itemSelect as well
        const itemSelect = $('#itemSelect');
        if (itemSelect.length) {
            itemSelect.select2({
                width: "100%",
                placeholder: "Select Item",
                allowClear: true
            });

            itemSelect.on('select2:open', function() {
                $('.select2-search__field').focus();
            });
        }
    });

    // Barcode scanning support
    let barcode = '';
    $(document).on('keypress', function(e) {
        const activeElement = $(document.activeElement);
        if (activeElement.is('#itemInput, #itemSelect') || activeElement.hasClass('select2-search__field')) {
            if (e.key === 'Enter') {
                // Try itemInput first
                let match = $(`#itemInput option[value="${barcode}"]`);
                if (match.length > 0) {
                    $('#itemInput').val(barcode).trigger('change');
                } else {
                    // Try itemSelect
                    match = $(`#itemSelect option[value="${barcode}"]`);
                    if (match.length > 0) {
                        $('#itemSelect').val(barcode).trigger('change');
                    } else {
                        alert("Item not found: " + barcode);
                    }
                }
                barcode = '';
            } else {
                barcode += e.key;
            }
        }
    });
</script>
<script>
    // Add this CSS for print styling (add to your existing <style> section)
const printStyles = `
<style media="print">
    @page {
        size: 80mm auto;
        margin: 5mm;
    }
    
    .print-bill {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        line-height: 1.2;
        width: 70mm;
        margin: 0 auto;
        background: white;
        color: black;
    }
    
    .bill-header {
        text-align: center;
        border-bottom: 1px dashed #000;
        padding-bottom: 5px;
        margin-bottom: 10px;
    }
    
    .store-name {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 2px;
    }
    
    .bill-info {
        font-size: 10px;
        margin-bottom: 10px;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    
    .items-table th,
    .items-table td {
        text-align: left;
        padding: 2px 0;
        font-size: 10px;
    }
    
    .item-row {
        border-bottom: 1px dotted #ccc;
    }
    
    .item-name {
        font-weight: bold;
    }
    
    .item-details {
        font-size: 9px;
        color: #666;
    }
    
    .totals-section {
        border-top: 1px dashed #000;
        padding-top: 5px;
        margin-top: 10px;
    }
    
    .total-row {
        display: flex;
        justify-content: between;
        margin-bottom: 2px;
    }
    
    .total-label {
        flex: 1;
    }
    
    .total-value {
        text-align: right;
        min-width: 60px;
    }
    
    .grand-total {
        font-weight: bold;
        font-size: 14px;
        border-top: 1px solid #000;
        padding-top: 3px;
        margin-top: 5px;
    }
    
    .payment-info {
        margin-top: 10px;
        border-top: 1px dashed #000;
        padding-top: 5px;
    }
    
    .footer-message {
        text-align: center;
        margin-top: 15px;
        font-size: 10px;
        border-top: 1px dashed #000;
        padding-top: 5px;
    }
    
    /* Hide everything else when printing */
    body * {
        visibility: hidden;
    }
    
    .print-bill, .print-bill * {
        visibility: visible;
    }
    
    .print-bill {
        position: absolute;
        left: 0;
        top: 0;
    }
</style>
`;

// Function to generate bill HTML
function generateBillHTML(billData) {
    const currentDate = new Date();
    const formattedDate = currentDate.toLocaleDateString('en-IN');
    const formattedTime = currentDate.toLocaleTimeString('en-IN', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
    
    let itemsHTML = '';
    billData.items.forEach((item, index) => {
        const itemTotal = (item.price * item.qty) - (item.price * item.qty * item.discount / 100) + (item.price * item.qty * item.tax / 100);
        
        itemsHTML += `
            <tr class="item-row">
                <td colspan="3" class="item-name">${item.name}</td>
            </tr>
            <tr class="item-row">
                <td class="item-details">${item.qty} x ₹${item.price.toFixed(2)}</td>
                <td class="item-details">${item.discount > 0 ? `(-${item.discount}%)` : ''} ${item.tax > 0 ? `(+${item.tax}% tax)` : ''}</td>
                <td class="item-details" style="text-align: right;">₹${itemTotal.toFixed(2)}</td>
            </tr>
        `;
    });

    return `
        <div class="print-bill">
            <div class="bill-header">
                <div class="store-name">SUKOYO STORE</div>
                <div style="font-size: 10px; margin-bottom: 5px;">Retail POS System</div>
                <div style="font-size: 9px;">
                    Date: ${formattedDate} | Time: ${formattedTime}<br>
                    Bill No: ${billData.billNumber || 'POS' + Date.now()}
                </div>
            </div>
            
            <div class="bill-info">
                <strong>Customer:</strong> ${billData.customer_name}<br>
                <strong>Contact:</strong> ${billData.customer_contact}<br>
                ${billData.loyalty_points_used > 0 ? `<strong>Loyalty Points Used:</strong> ${billData.loyalty_points_used}<br>` : ''}
            </div>
            
            <table class="items-table">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th>Item Details</th>
                        <th>Rate/Disc/Tax</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${itemsHTML}
                </tbody>
            </table>
            
            <div class="totals-section">
                <div class="total-row">
                    <span class="total-label">Sub Total:</span>
                    <span class="total-value">₹${billData.sub_total.toFixed(2)}</span>
                </div>
                
                ${billData.total_discount > 0 ? `
                <div class="total-row">
                    <span class="total-label">Total Discount:</span>
                    <span class="total-value">- ₹${billData.total_discount.toFixed(2)}</span>
                </div>
                ` : ''}
                
                ${billData.total_tax > 0 ? `
                <div class="total-row">
                    <span class="total-label">Total Tax:</span>
                    <span class="total-value">₹${billData.total_tax.toFixed(2)}</span>
                </div>
                ` : ''}
                
                ${billData.additional_charges > 0 ? `
                <div class="total-row">
                    <span class="total-label">Additional Charges:</span>
                    <span class="total-value">₹${billData.additional_charges.toFixed(2)}</span>
                </div>
                ` : ''}
                
                <div class="total-row grand-total">
                    <span class="total-label">GRAND TOTAL:</span>
                    <span class="total-value">₹${billData.grand_total.toFixed(2)}</span>
                </div>
            </div>
            
            <div class="payment-info">
                <div class="total-row">
                    <span class="total-label">Payment Mode:</span>
                    <span class="total-value">${billData.mode_of_payment}</span>
                </div>
                
                <div class="total-row">
                    <span class="total-label">Amount Received:</span>
                    <span class="total-value">₹${billData.received_amount.toFixed(2)}</span>
                </div>
                
                ${billData.received_amount - billData.grand_total !== 0 ? `
                <div class="total-row">
                    <span class="total-label">Change:</span>
                    <span class="total-value">₹${(billData.received_amount - billData.grand_total).toFixed(2)}</span>
                </div>
                ` : ''}
            </div>
            
            <div class="footer-message">
                <div style="margin-bottom: 5px;">*** THANK YOU FOR SHOPPING ***</div>
                <div style="font-size: 8px;">
                    This is a computer generated bill<br>
                    Visit Again!
                </div>
            </div>
        </div>
    `;
}

// Function to print bill
function printBill(billData) {
    // Add print styles to head if not already added
    if (!document.getElementById('print-styles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'print-styles';
        styleSheet.innerHTML = printStyles;
        document.head.appendChild(styleSheet);
    }
    
    // Create a temporary div for the bill
    const printDiv = document.createElement('div');
    printDiv.innerHTML = generateBillHTML(billData);
    
    // Add to body temporarily
    document.body.appendChild(printDiv);
    
    // Trigger print
    setTimeout(() => {
        window.print();
        
        // Remove the temporary div after printing
        setTimeout(() => {
            document.body.removeChild(printDiv);
        }, 1000);
    }, 100);
}

// Modified Save & Print button functionality
$(document).ready(function () {
    // Update the existing Save & Print button to have an ID
    $('button:contains("Save & Print [F12]")').attr('id', 'savePrintBtn');
    
    $('#savePrintBtn').click(function () {
        // Collect all the same data as the save button
        const customerName = $('#selectedCustomer').text().trim();
        let customerContact = '';
        
        if (customerName.includes(' - ')) {
            const parts = customerName.split(' - ');
            customerContact = parts[1];
        }
        
        if (!customerContact) {
            customerContact = $('#customer_contact').val() || '';
        }
        
        let actualCustomerName = customerName;
        if (customerName.includes(' - ')) {
            actualCustomerName = customerName.split(' - ')[0];
        }
        
        // Collect items with names for printing
        let items = [];
        $('#addedTable tbody tr').each(function () {
            let row = $(this);
            let cells = row.find('td');
            
            if (cells.length < 8) return;
            
            let itemName = cells.eq(1).text().trim();
            let itemId = null;
            
            $('#itemSelect option').each(function() {
                if ($(this).text().trim() === itemName && $(this).val() !== '') {
                    itemId = $(this).val();
                    return false;
                }
            });
            
            items.push({
                item_id: itemId,
                name: itemName, // Add name for printing
                unit: cells.eq(2).text().trim(),
                qty: parseFloat(cells.eq(3).text().trim()) || 0,
                price: parseFloat(cells.eq(4).text().replace('₹', '').trim()) || 0,
                discount: parseFloat(cells.eq(5).text().trim()) || 0,
                tax: parseFloat(cells.eq(6).text().trim()) || 0,
                amount: parseFloat(cells.eq(7).text().replace('₹', '').trim()) || 0
            });
        });
        
        if (items.length === 0) {
            alert("Please add at least one item to the bill.");
            return;
        }
        
        if (!actualCustomerName || actualCustomerName === 'No customer selected' || !customerContact) {
            alert("Please select a customer before saving the bill.");
            return;
        }
        
        // Extract totals
        const subTotal = parseFloat($('#subTotal').text().replace('₹ ', '').replace(',', '')) || 0;
        const totalDiscount = parseFloat($('#totalDiscount').text().replace('₹ ', '').replace(',', '')) || 0;
        const totalTax = parseFloat($('#totalTax').text().replace('₹ ', '').replace(',', '')) || 0;
        const additionalCharges = parseFloat($('#additionalCharges').text().replace('₹ ', '').replace(',', '')) || 0;
        const grandTotal = parseFloat($('#grandTotal').text().replace('₹ ', '').replace(',', '')) || 0;
        const receivedAmount = parseFloat($('#receivedAmount').val()) || 0;
        const modeOfPayment = $('#mop').val() || 'Cash';
        
        // Get loyalty points used
        let loyaltyPointsUsed = 0;
        const usedPointsElement = $('#usedPointsDisplay');
        if (usedPointsElement.length && usedPointsElement.is(':visible')) {
            const usedPointsText = usedPointsElement.text().trim();
            loyaltyPointsUsed = parseInt(usedPointsText.replace(/[^\d]/g, '')) || 0;
        }
        
        const payload = {
            customer_name: actualCustomerName,
            customer_contact: customerContact,
            items: items,
            sub_total: subTotal,
            total_discount: totalDiscount,
            total_tax: totalTax,
            additional_charges: additionalCharges,
            grand_total: grandTotal,
            received_amount: receivedAmount,
            mode_of_payment: modeOfPayment,
            loyalty_points_used: loyaltyPointsUsed
        };

        if (grandTotal <= 0) {
            alert("Grand total must be greater than 0.");
            return;
        }

        // Disable button
        $('#savePrintBtn').prop('disabled', true).text('Saving & Printing...');

        // Save and then print
        fetch("/save-bill", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err });
            }
            return response.json();
        })
        .then(data => {
            if (data.status) {
                // Add bill number from response if available
                if (data.data && data.data.bill_id) {
                    payload.billNumber = 'BILL-' + data.data.bill_id;
                }
                
                // Print the bill
                printBill(payload);
                
                // Show success message
                alert("Bill saved and sent to printer!");
                
                // Refresh page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                
            } else {
                $('#savePrintBtn').prop('disabled', false).text('Save & Print [F12]');
                alert("Failed to save bill: " + (data.message || "Unknown error"));
                console.error(data);
            }
        })
        .catch(error => {
            console.error("Request failed:", error);
            $('#savePrintBtn').prop('disabled', false).text('Save & Print [F12]');
            
            if (error.errors) {
                let messages = Object.values(error.errors).flat().join("\n");
                alert("Validation failed:\n" + messages);
            } else if (error.message) {
                alert("Error: " + error.message);
            } else {
                alert("Something went wrong while saving the bill.");
            }
        });
    });
    
    // Add keyboard shortcut for F12
    $(document).keydown(function(e) {
        if (e.key === 'F12') {
            e.preventDefault();
            $('#savePrintBtn').click();
        }
    });
});

// Optional: Add a preview function to test the bill format
function previewBill() {
    // Get current bill data for preview
    const items = [];
    $('#addedTable tbody tr').each(function () {
        let row = $(this);
        let cells = row.find('td');
        
        if (cells.length < 8) return;
        
        items.push({
            name: cells.eq(1).text().trim(),
            qty: parseFloat(cells.eq(3).text().trim()) || 0,
            price: parseFloat(cells.eq(4).text().replace('₹', '').trim()) || 0,
            discount: parseFloat(cells.eq(5).text().trim()) || 0,
            tax: parseFloat(cells.eq(6).text().trim()) || 0,
            amount: parseFloat(cells.eq(7).text().replace('₹', '').trim()) || 0
        });
    });
    
    if (items.length === 0) {
        alert("Please add items to preview the bill.");
        return;
    }
    
    const previewData = {
        customer_name: $('#selectedCustomer').text().split(' - ')[0] || 'Walk-in Customer',
        customer_contact: $('#selectedCustomer').text().split(' - ')[1] || 'N/A',
        items: items,
        sub_total: parseFloat($('#subTotal').text().replace('₹ ', '').replace(',', '')) || 0,
        total_discount: parseFloat($('#totalDiscount').text().replace('₹ ', '').replace(',', '')) || 0,
        total_tax: parseFloat($('#totalTax').text().replace('₹ ', '').replace(',', '')) || 0,
        additional_charges: parseFloat($('#additionalCharges').text().replace('₹ ', '').replace(',', '')) || 0,
        grand_total: parseFloat($('#grandTotal').text().replace('₹ ', '').replace(',', '')) || 0,
        received_amount: parseFloat($('#receivedAmount').val()) || 0,
        mode_of_payment: $('#mop').val() || 'Cash',
        loyalty_points_used: 0,
        billNumber: 'PREVIEW-001'
    };
    
    // Create preview in a new window
    const previewWindow = window.open('', '_blank', 'width=400,height=600');
    previewWindow.document.write(`
        <html>
            <head>
                <title>Bill Preview</title>
                ${printStyles}
            </head>
            <body>
                ${generateBillHTML(previewData)}
            </body>
        </html>
    `);
    previewWindow.document.close();
}
</script>
<script>
    // Multiple Screen POS System - Individual Script
// Add this script to your existing POS page

(function() {
    'use strict';
    
    // Global variables for multiple screens
    let screens = {};
    let currentScreenId = 'screen-1';
    let screenCounter = 1;
    
    // Initialize multiple screens functionality
    function initMultipleScreens() {
        // Create the screen tabs container
        createScreenTabsContainer();
        
        // Initialize first screen with current data
        initializeFirstScreen();
        
        // Add event listeners
        setupEventListeners();
        
        console.log('Multiple Screens POS initialized');
    }
    
    // Create the tabs container at the top
    function createScreenTabsContainer() {
        const billHeader = document.querySelector('.bill-header');
        if (!billHeader) return;
        
        const tabsContainer = document.createElement('div');
        tabsContainer.id = 'screen-tabs-container';
        tabsContainer.style.cssText = `
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            overflow-x: auto;
            white-space: nowrap;
        `;
        
        tabsContainer.innerHTML = `
            <div class="screen-tabs" id="screenTabs" style="display: flex; gap: 5px; flex: 1;">
                <div class="screen-tab active" data-screen="screen-1" style="
                    padding: 8px 16px;
                    background: #007bff;
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 14px;
                    min-width: 120px;
                    position: relative;
                ">
                    <span>Bill #1</span>
                    <button class="close-tab" data-screen="screen-1" style="
                        background: rgba(255,255,255,0.3);
                        border: none;
                        color: white;
                        border-radius: 3px;
                        width: 20px;
                        height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        font-size: 12px;
                    ">×</button>
                </div>
            </div>
            <button id="addNewScreen" style="
                padding: 8px 16px;
                background: #28a745;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 5px;
                font-size: 14px;
            ">
                <i class="fas fa-plus"></i> New Bill
            </button>
        `;
        
        // Insert before bill-header
        billHeader.parentNode.insertBefore(tabsContainer, billHeader);
    }
    
    // Initialize first screen with current data
    function initializeFirstScreen() {
        screens['screen-1'] = captureCurrentScreenData();
        screens['screen-1'].id = 'screen-1';
        screens['screen-1'].name = 'Bill #1';
        screens['screen-1'].isActive = true;
    }
    
    // Capture current screen data
    function captureCurrentScreenData() {
        const items = [];
        
        // Capture items from table
        document.querySelectorAll('#addedTable tbody tr').forEach(row => {
            const cells = row.cells;
            if (cells.length >= 8) {
                items.push({
                    name: cells[1].textContent.trim(),
                    unit: cells[2].textContent.trim(),
                    qty: cells[3].textContent.trim(),
                    price: cells[4].textContent.trim(),
                    discount: cells[5].textContent.trim(),
                    tax: cells[6].textContent.trim(),
                    amount: cells[7].textContent.trim()
                });
            }
        });
        
        return {
            items: items,
            customer: {
                name: document.getElementById('selectedCustomer')?.textContent || 'No customer selected',
                contact: document.getElementById('customer_contact')?.value || '',
                loyaltyPoints: document.getElementById('loyaltyPointsDisplay')?.textContent || '0 Points'
            },
            totals: {
                subTotal: document.getElementById('subTotal')?.textContent || '₹ 0.00',
                totalDiscount: document.getElementById('totalDiscount')?.textContent || '₹ 0.00',
                totalTax: document.getElementById('totalTax')?.textContent || '₹ 0.00',
                additionalCharges: document.getElementById('additionalCharges')?.textContent || '₹ 0.00',
                grandTotal: document.getElementById('grandTotal')?.textContent || '₹ 0.00'
            },
            payment: {
                receivedAmount: document.getElementById('receivedAmount')?.value || '0',
                modeOfPayment: document.getElementById('mop')?.value || 'Cash',
                changeToReturn: document.getElementById('changeToReturn')?.textContent || '₹ 0.00'
            },
            additionalChargesInput: document.getElementById('additionalChargesInput')?.value || '0'
        };
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Add new screen button
        document.getElementById('addNewScreen').addEventListener('click', addNewScreen);
        
        // Tab click events
        document.getElementById('screenTabs').addEventListener('click', handleTabClick);
        
        // Auto-save current screen data when switching
        setupAutoSave();
    }
    
    // Add new screen
    function addNewScreen() {
        // Save current screen data
        saveCurrentScreenData();
        
        screenCounter++;
        const newScreenId = `screen-${screenCounter}`;
        
        // Create new screen data
        screens[newScreenId] = createEmptyScreenData();
        screens[newScreenId].id = newScreenId;
        screens[newScreenId].name = `Bill #${screenCounter}`;
        screens[newScreenId].isActive = true;
        
        // Add new tab
        addScreenTab(newScreenId, `Bill #${screenCounter}`);
        
        // Switch to new screen
        switchToScreen(newScreenId);
    }
    
    // Create empty screen data
    function createEmptyScreenData() {
        return {
            items: [],
            customer: {
                name: 'No customer selected',
                contact: '',
                loyaltyPoints: '0 Points'
            },
            totals: {
                subTotal: '₹ 0.00',
                totalDiscount: '₹ 0.00',
                totalTax: '₹ 0.00',
                additionalCharges: '₹ 0.00',
                grandTotal: '₹ 0.00'
            },
            payment: {
                receivedAmount: '0',
                modeOfPayment: 'Cash',
                changeToReturn: '₹ 0.00'
            },
            additionalChargesInput: '0'
        };
    }
    
    // Add screen tab
    function addScreenTab(screenId, name) {
        const tabsContainer = document.getElementById('screenTabs');
        
        const tab = document.createElement('div');
        tab.className = 'screen-tab';
        tab.setAttribute('data-screen', screenId);
        tab.style.cssText = `
            padding: 8px 16px;
            background: #6c757d;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            min-width: 120px;
            position: relative;
        `;
        
        tab.innerHTML = `
            <span>${name}</span>
            <button class="close-tab" data-screen="${screenId}" style="
                background: rgba(255,255,255,0.3);
                border: none;
                color: white;
                border-radius: 3px;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 12px;
            ">×</button>
        `;
        
        tabsContainer.appendChild(tab);
    }
    
    // Handle tab clicks
    function handleTabClick(e) {
        if (e.target.classList.contains('close-tab')) {
            e.stopPropagation();
            const screenId = e.target.getAttribute('data-screen');
            closeScreen(screenId);
        } else if (e.target.closest('.screen-tab')) {
            const screenId = e.target.closest('.screen-tab').getAttribute('data-screen');
            switchToScreen(screenId);
        }
    }
    
    // Switch to screen
    function switchToScreen(screenId) {
        if (screenId === currentScreenId) return;
        
        // Save current screen data
        saveCurrentScreenData();
        
        // Update active states
        document.querySelectorAll('.screen-tab').forEach(tab => {
            tab.classList.remove('active');
            tab.style.background = '#6c757d';
        });
        
        const targetTab = document.querySelector(`[data-screen="${screenId}"]`);
        if (targetTab) {
            targetTab.classList.add('active');
            targetTab.style.background = '#007bff';
        }
        
        // Load screen data
        currentScreenId = screenId;
        loadScreenData(screenId);
        
        console.log(`Switched to ${screenId}`);
    }
    
    // Save current screen data
    function saveCurrentScreenData() {
        if (screens[currentScreenId]) {
            screens[currentScreenId] = {
                ...screens[currentScreenId],
                ...captureCurrentScreenData()
            };
        }
    }
    
    // Load screen data
    function loadScreenData(screenId) {
        const screenData = screens[screenId];
        if (!screenData) return;
        
        // Clear current items
        const tableBody = document.querySelector('#addedTable tbody');
        if (tableBody) {
            tableBody.innerHTML = '';
        }
        
        // Hide table if no items
        const table = document.getElementById('addedTable');
        if (screenData.items.length === 0) {
            if (table) table.style.display = 'none';
        } else {
            if (table) table.style.display = 'table';
            
            // Load items
            screenData.items.forEach((item, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name}</td>
                        <td>${item.unit}</td>
                        <td>${item.qty}</td>
                        <td>${item.price}</td>
                        <td>${item.discount}</td>
                        <td>${item.tax}</td>
                        <td class="item-amount">${item.amount}</td>
                        <td><button type="button" class="btn btn-danger btn-sm remove">X</button></td>
                    </tr>
                `;
                if (tableBody) {
                    tableBody.insertAdjacentHTML('beforeend', row);
                }
            });
        }
        
        // Load customer data
        const selectedCustomer = document.getElementById('selectedCustomer');
        if (selectedCustomer) {
            selectedCustomer.textContent = screenData.customer.name;
        }
        
        const customerContact = document.getElementById('customer_contact');
        if (customerContact) {
            customerContact.value = screenData.customer.contact;
        }
        
        const loyaltyPoints = document.getElementById('loyaltyPointsDisplay');
        if (loyaltyPoints) {
            loyaltyPoints.textContent = screenData.customer.loyaltyPoints;
        }
        
        // Load totals
        const subTotal = document.getElementById('subTotal');
        if (subTotal) subTotal.textContent = screenData.totals.subTotal;
        
        const totalDiscount = document.getElementById('totalDiscount');
        if (totalDiscount) totalDiscount.textContent = screenData.totals.totalDiscount;
        
        const totalTax = document.getElementById('totalTax');
        if (totalTax) totalTax.textContent = screenData.totals.totalTax;
        
        const additionalCharges = document.getElementById('additionalCharges');
        if (additionalCharges) additionalCharges.textContent = screenData.totals.additionalCharges;
        
        const grandTotal = document.getElementById('grandTotal');
        if (grandTotal) grandTotal.textContent = screenData.totals.grandTotal;
        
        // Load payment data
        const receivedAmount = document.getElementById('receivedAmount');
        if (receivedAmount) receivedAmount.value = screenData.payment.receivedAmount;
        
        const mop = document.getElementById('mop');
        if (mop) mop.value = screenData.payment.modeOfPayment;
        
        const changeToReturn = document.getElementById('changeToReturn');
        if (changeToReturn) changeToReturn.textContent = screenData.payment.changeToReturn;
        
        const additionalChargesInput = document.getElementById('additionalChargesInput');
        if (additionalChargesInput) additionalChargesInput.value = screenData.additionalChargesInput;
        
        // Reset form selections
        const itemSelect = document.getElementById('itemSelect');
        if (itemSelect) itemSelect.value = '';
        
        const qty = document.getElementById('qty');
        if (qty) qty.value = '1';
    }
    
    // Close screen
    function closeScreen(screenId) {
        // Prevent closing if it's the only screen
        if (Object.keys(screens).length <= 1) {
            alert('Cannot close the last remaining bill screen.');
            return;
        }
        
        // Confirm if screen has items
        if (screens[screenId] && screens[screenId].items.length > 0) {
            if (!confirm('This bill contains items. Are you sure you want to close it?')) {
                return;
            }
        }
        
        // Remove from screens
        delete screens[screenId];
        
        // Remove tab
        const tab = document.querySelector(`[data-screen="${screenId}"]`);
        if (tab) {
            tab.remove();
        }
        
        // If current screen was closed, switch to first available
        if (screenId === currentScreenId) {
            const firstScreenId = Object.keys(screens)[0];
            switchToScreen(firstScreenId);
        }
    }
    
    // Setup auto-save functionality
    function setupAutoSave() {
        // Save data when items are added/removed
        const originalAddItem = document.getElementById('addItem').onclick;
        document.getElementById('addItem').addEventListener('click', function() {
            setTimeout(() => {
                saveCurrentScreenData();
            }, 100);
        });
        
        // Save when customer is changed
        const customerButton = document.querySelector('[data-bs-target="#customer"]');
        if (customerButton) {
            customerButton.addEventListener('click', function() {
                setTimeout(() => {
                    saveCurrentScreenData();
                }, 500);
            });
        }
        
        // Save when payment details change
        const receivedAmount = document.getElementById('receivedAmount');
        if (receivedAmount) {
            receivedAmount.addEventListener('input', function() {
                setTimeout(() => {
                    saveCurrentScreenData();
                }, 100);
            });
        }
        
        const mopSelect = document.getElementById('mop');
        if (mopSelect) {
            mopSelect.addEventListener('change', function() {
                setTimeout(() => {
                    saveCurrentScreenData();
                }, 100);
            });
        }
    }
    
    // Add keyboard shortcuts
    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Ctrl + T for new screen
            if (e.ctrlKey && e.key === 't') {
                e.preventDefault();
                addNewScreen();
            }
            
            // Ctrl + W to close current screen
            if (e.ctrlKey && e.key === 'w') {
                e.preventDefault();
                closeScreen(currentScreenId);
            }
            
            // Ctrl + Tab to switch screens
            if (e.ctrlKey && e.key === 'Tab') {
                e.preventDefault();
                const screenIds = Object.keys(screens);
                const currentIndex = screenIds.indexOf(currentScreenId);
                const nextIndex = (currentIndex + 1) % screenIds.length;
                switchToScreen(screenIds[nextIndex]);
            }
        });
    }
    
    // Public API for external access
    window.POSMultiScreen = {
        addNewScreen: addNewScreen,
        switchToScreen: switchToScreen,
        closeScreen: closeScreen,
        getCurrentScreen: () => currentScreenId,
        getAllScreens: () => screens,
        saveCurrentScreen: saveCurrentScreenData
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMultipleScreens);
    } else {
        initMultipleScreens();
    }
    
    // Setup keyboard shortcuts
    setupKeyboardShortcuts();
    
    // Add some helpful styles
    const style = document.createElement('style');
    style.textContent = `
        .screen-tab:hover {
            background: #5a6268 !important;
        }
        
        .screen-tab.active:hover {
            background: #0056b3 !important;
        }
        
        .close-tab:hover {
            background: rgba(255,255,255,0.5) !important;
        }
        
        #screen-tabs-container {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .screen-tabs::-webkit-scrollbar {
            height: 6px;
        }
        
        .screen-tabs::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .screen-tabs::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .screen-tabs::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    `;
    document.head.appendChild(style);
    
})();

// Additional helper functions for integration with existing save functions
(function() {
    let isInitialized = false;
    
    // Initialize save bill override only once
    function initializeSaveBillOverride() {
        if (isInitialized) return;
        isInitialized = true;
        
        // Override the jQuery save bill handler
        $(document).off('click', '#saveBillBtn, #savePrintBtn');
        
        // New save bill handler that doesn't refresh page
        $(document).on('click', '#saveBillBtn, #savePrintBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isPrintAndSave = $(this).attr('id') === 'savePrintBtn';
            const buttonText = isPrintAndSave ? 'Save & Print [F12]' : 'Save Bill [F11]';
            const processingText = isPrintAndSave ? 'Saving & Printing...' : 'Saving...';
            
            // Collect customer data
            const customerName = $('#selectedCustomer').text().trim();
            let customerContact = '';
            
            // Extract contact from the displayed text (format: "Name - Contact")
            if (customerName.includes(' - ')) {
                const parts = customerName.split(' - ');
                customerContact = parts[1];
            }
            
            // If no customer selected, get from hidden fields if they exist
            if (!customerContact) {
                customerContact = $('#customer_contact').val() || '';
            }
            
            // Extract customer name without contact
            let actualCustomerName = customerName;
            if (customerName.includes(' - ')) {
                actualCustomerName = customerName.split(' - ')[0];
            }
            
            // Collect item data from the correct table (addedTable)
            let items = [];
            $('#addedTable tbody tr').each(function () {
                let row = $(this);
                let cells = row.find('td');
                
                // Skip if no cells found
                if (cells.length < 8) return;
                
                // Get item name and find corresponding item ID
                let itemName = cells.eq(1).text().trim();
                let itemId = null;
                
                // Find item ID from the select options
                $('#itemSelect option').each(function() {
                    if ($(this).text().trim() === itemName && $(this).val() !== '') {
                        itemId = $(this).val();
                        return false; // break the loop
                    }
                });
                
                items.push({
                    item_id: itemId,
                    name: itemName, // Add name for printing
                    unit: cells.eq(2).text().trim(),
                    qty: parseFloat(cells.eq(3).text().trim()) || 0,
                    price: parseFloat(cells.eq(4).text().replace('₹', '').trim()) || 0,
                    discount: parseFloat(cells.eq(5).text().trim()) || 0,
                    tax: parseFloat(cells.eq(6).text().trim()) || 0,
                    amount: parseFloat(cells.eq(7).text().replace('₹', '').trim()) || 0
                });
            });
            
            // Check if we have items
            if (items.length === 0) {
                showNotification("Please add at least one item to the bill.", 'error');
                return;
            }
            
            // Check if customer is selected
            if (!actualCustomerName || actualCustomerName === 'No customer selected' || !customerContact) {
                showNotification("Please select a customer before saving the bill.", 'error');
                return;
            }
            
            // Extract numeric values from the bill summary
            const subTotal = parseFloat($('#subTotal').text().replace('₹ ', '').replace(',', '')) || 0;
            const totalDiscount = parseFloat($('#totalDiscount').text().replace('₹ ', '').replace(',', '')) || 0;
            const totalTax = parseFloat($('#totalTax').text().replace('₹ ', '').replace(',', '')) || 0;
            const additionalCharges = parseFloat($('#additionalCharges').text().replace('₹ ', '').replace(',', '')) || 0;
            const grandTotal = parseFloat($('#grandTotal').text().replace('₹ ', '').replace(',', '')) || 0;
            const receivedAmount = parseFloat($('#receivedAmount').val()) || 0;
            const modeOfPayment = $('#mop').val() || 'Cash';
            
            // Collect loyalty points data from the used points display
            let loyaltyPointsUsed = 0;
            
            // Check if used points are displayed (from your loyalty system)
            const usedPointsElement = $('#usedPointsDisplay');
            if (usedPointsElement.length && usedPointsElement.is(':visible')) {
                const usedPointsText = usedPointsElement.text().trim();
                loyaltyPointsUsed = parseInt(usedPointsText.replace(/[^\d]/g, '')) || 0;
            }
            
            // Alternative: Get from global variable if you're using it
            if (typeof usedPointsInSession !== 'undefined' && usedPointsInSession > 0) {
                loyaltyPointsUsed = usedPointsInSession;
            }
            
            // Alternative: Get from loyalty modal if it has the value
            const redeemPointsInput = $('#redeemPoints');
            if (redeemPointsInput.length && redeemPointsInput.val()) {
                loyaltyPointsUsed = parseInt(redeemPointsInput.val()) || 0;
            }
            
            // Prepare payload
            const payload = {
                customer_name: actualCustomerName,
                customer_contact: customerContact,
                items: items,
                sub_total: subTotal,
                total_discount: totalDiscount,
                total_tax: totalTax,
                additional_charges: additionalCharges,
                grand_total: grandTotal,
                received_amount: receivedAmount,
                mode_of_payment: modeOfPayment,
                loyalty_points_used: loyaltyPointsUsed
            };

            console.log("Sending payload:", payload);
            console.log("Loyalty points used:", loyaltyPointsUsed);

            // Validate before sending
            if (grandTotal <= 0) {
                showNotification("Grand total must be greater than 0.", 'error');
                return;
            }

            // Disable the save button to prevent multiple clicks
            $(this).prop('disabled', true).text(processingText);
            const currentButton = $(this);

            // Send AJAX request
            fetch("/save-bill", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err });
                }
                return response.json();
            })
            .then(data => {
                if (data.status) {
                    // Show success message
                    showNotification("Bill saved successfully!", 'success');
                    
                    // Show additional success info if loyalty points were used
                    if (loyaltyPointsUsed > 0 && data.data) {
                        setTimeout(() => {
                            showNotification(`Loyalty Points Used: ${loyaltyPointsUsed} | Points Earned: ${data.data.loyalty_points_earned || 0} | New Balance: ${data.data.new_loyalty_balance || 0}`, 'info');
                        }, 1000);
                    }
                    
                    // Handle printing if it's save & print
                    if (isPrintAndSave && typeof printBill === 'function') {
                        // Add bill number from response if available
                        if (data.data && data.data.bill_id) {
                            payload.billNumber = 'BILL-' + data.data.bill_id;
                        }
                        
                        // Print the bill
                        printBill(payload);
                        showNotification("Bill sent to printer!", 'info');
                    }
                    
                    // ✅ CLEAR ONLY CURRENT SCREEN DATA - NO PAGE REFRESH
                    clearCurrentScreenOnly();
                    
                } else {
                    // Re-enable button on failure
                    currentButton.prop('disabled', false).text(buttonText);
                    showNotification("Failed to save bill: " + (data.message || "Unknown error"), 'error');
                    console.error(data);
                }
            })
            .catch(error => {
                console.error("Request failed:", error);
                
                // Re-enable button on error
                currentButton.prop('disabled', false).text(buttonText);
                
                if (error.errors) {
                    // Laravel validation errors
                    let messages = Object.values(error.errors).flat().join(" | ");
                    showNotification("Validation failed: " + messages, 'error');
                } else if (error.message) {
                    showNotification("Error: " + error.message, 'error');
                } else {
                    showNotification("Something went wrong while saving the bill.", 'error');
                }
            });
        });
    }
    
    // Function to clear only current screen data
    function clearCurrentScreenOnly() {
        // Clear items table
        const tableBody = document.querySelector('#addedTable tbody');
        if (tableBody) {
            tableBody.innerHTML = '';
        }
        
        // Hide table
        const table = document.getElementById('addedTable');
        if (table) {
            table.style.display = 'none';
        }
        
        // Reset customer selection
        const selectedCustomer = document.getElementById('selectedCustomer');
        if (selectedCustomer) {
            selectedCustomer.textContent = 'No customer selected';
        }
        
        const customerContact = document.getElementById('customer_contact');
        if (customerContact) {
            customerContact.value = '';
        }
        
        const loyaltyPoints = document.getElementById('loyaltyPointsDisplay');
        if (loyaltyPoints) {
            loyaltyPoints.textContent = '0 Points';
        }
        
        // Reset totals
        const subTotal = document.getElementById('subTotal');
        if (subTotal) subTotal.textContent = '₹ 0.00';
        
        const totalDiscount = document.getElementById('totalDiscount');
        if (totalDiscount) totalDiscount.textContent = '₹ 0.00';
        
        const totalTax = document.getElementById('totalTax');
        if (totalTax) totalTax.textContent = '₹ 0.00';
        
        const additionalCharges = document.getElementById('additionalCharges');
        if (additionalCharges) additionalCharges.textContent = '₹ 0.00';
        
        const grandTotal = document.getElementById('grandTotal');
        if (grandTotal) grandTotal.textContent = '₹ 0.00';
        
        // Reset payment fields
        const receivedAmount = document.getElementById('receivedAmount');
        if (receivedAmount) receivedAmount.value = '0';
        
        const mop = document.getElementById('mop');
        if (mop) mop.value = 'Cash';
        
        const changeToReturn = document.getElementById('changeToReturn');
        if (changeToReturn) changeToReturn.textContent = '₹ 0.00';
        
        const additionalChargesInput = document.getElementById('additionalChargesInput');
        if (additionalChargesInput) additionalChargesInput.value = '0';
        
        // Reset form selections
        const itemSelect = document.getElementById('itemSelect');
        if (itemSelect) itemSelect.value = '';
        
        const qty = document.getElementById('qty');
        if (qty) qty.value = '1';
        
        // Reset item counter
        if (typeof itemCount !== 'undefined') {
            itemCount = 0;
        }
        
        // Re-enable buttons
        $('#saveBillBtn, #savePrintBtn').prop('disabled', false);
        $('#saveBillBtn').text('Save Bill [F11]');
        $('#savePrintBtn').text('Save & Print [F12]');
        
        // Update current screen data in multiple screens system
        if (window.POSMultiScreen) {
            // Update current screen with empty data
            const currentScreenId = window.POSMultiScreen.getCurrentScreen();
            const screens = window.POSMultiScreen.getAllScreens();
            if (screens[currentScreenId]) {
                screens[currentScreenId] = {
                    ...screens[currentScreenId],
                    items: [],
                    customer: {
                        name: 'No customer selected',
                        contact: '',
                        loyaltyPoints: '0 Points'
                    },
                    totals: {
                        subTotal: '₹ 0.00',
                        totalDiscount: '₹ 0.00',
                        totalTax: '₹ 0.00',
                        additionalCharges: '₹ 0.00',
                        grandTotal: '₹ 0.00'
                    },
                    payment: {
                        receivedAmount: '0',
                        modeOfPayment: 'Cash',
                        changeToReturn: '₹ 0.00'
                    },
                    additionalChargesInput: '0'
                };
            }
        }
        
        console.log('Current screen cleared after successful bill save');
    }
    
    // Add notification system for screen operations
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 12px 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            border-radius: 5px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            max-width: 300px;
            word-wrap: break-word;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Remove after duration based on message length
        const duration = type === 'error' ? 5000 : 3000;
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
    }
    
    // Initialize when DOM is ready
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSaveBillOverride);
        } else {
            initializeSaveBillOverride();
        }
    }
    
    // Initialize
    init();
    
    // Extend the public API
    if (window.POSMultiScreen) {
        window.POSMultiScreen.showNotification = showNotification;
        window.POSMultiScreen.clearCurrentScreen = clearCurrentScreenOnly;
    }
    
    // Expose functions globally for access
    window.POSScreenUtils = {
        clearCurrentScreen: clearCurrentScreenOnly,
        showNotification: showNotification
    };
})();

console.log('Multiple Screen POS System Loaded Successfully');
console.log('Keyboard Shortcuts:');
console.log('- Ctrl + T: New Bill Screen');
console.log('- Ctrl + W: Close Current Screen');
console.log('- Ctrl + Tab: Switch Between Screens');
</script>
@include('layouts.footer')