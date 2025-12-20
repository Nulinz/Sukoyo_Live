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
                <!-- <div class="header-ct">
                    <h6>Billing Screen 1</h6>
                    <a><i class="fas fa-xmark"></i></a>
                </div>
                <div class="header-ct">
                    <h6>Hold Bill & Add Another</h6>
                    <a><i class="fas fa-plus"></i></a>
                </div> -->
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
                    <div id="singlePaymentInput">
                        <input type="number" class="form-control" id="receivedAmount" placeholder="Enter amount" value="0" min="0" step="0.01">
                    </div>
                    <div id="splitPaymentInputs" style="display: none;">
                        <div class="mb-2">
                            <label class="form-label small">Cash Amount</label>
                            <input type="number" class="form-control" id="cashAmount" placeholder="Enter cash amount" value="0" min="0" step="0.01">
                        </div>
                        <div>
                            <label class="form-label small">Online Amount</label>
                            <input type="number" class="form-control" id="onlineAmount" placeholder="Enter online amount" value="0" min="0" step="0.01">
                        </div>
                    </div>
                </th>
                <td class="d-flex align-items-center justify-content-end">
                    <select class="form-select" name="" id="mop">
                        <option value="Cash">Cash</option>
                        <option value="Online">Online</option>
                        <option value="Both">Both</option>
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
                     <button type="button" class="listbtn px-1 mb-2" onclick="validateAndOpenCorporateBill()" data-bs-toggle="modal" data-bs-target="#corporateBillOptions">Corporate Bill</button>                  

                   </form>
                    <!--<button type="button" class="listbtn px-1 mb-2">Save & Print [F12]</button>-->
                </div>
<script>
    function validateAndOpenCorporateBill() {
    // Check if items exist in the bill
    if ($('#addedTable tbody tr').length === 0) {
        alert('Please add items to the bill before creating a corporate bill.');
        return;
    }

    // Get mode of payment
    const mopValue = $('#mop').val();
    
    // Get received amount based on payment mode
    let receivedAmount = 0;
    
    if (mopValue === 'Both') {
        const cashAmount = parseFloat($('#cashAmount').val()) || 0;
        const onlineAmount = parseFloat($('#onlineAmount').val()) || 0;
        receivedAmount = cashAmount + onlineAmount;
        
        // Validate split payments
        if (cashAmount <= 0 && onlineAmount <= 0) {
            alert('Please enter cash amount and/or online amount for split payment.');
            $('#cashAmount').focus();
            return;
        }
    } else {
        receivedAmount = parseFloat($('#receivedAmount').val()) || 0;
    }
    
    const grandTotalText = $('#grandTotal').text().replace(/[₹,\s]/g, '') || '0';
    const grandTotal = parseFloat(grandTotalText) || 0;

    // Check if received amount is 0 or empty
    if (receivedAmount === 0) {
        if (mopValue === 'Both') {
            alert('Please enter the cash and/or online amounts before creating a corporate bill.');
            $('#cashAmount').focus();
        } else {
            alert('Please enter the received amount before creating a corporate bill.');
            $('#receivedAmount').focus();
        }
        return;
    }

    // Check if received amount is less than grand total
    if (receivedAmount < grandTotal) {
        const proceed = confirm(`Received amount (₹${receivedAmount.toFixed(2)}) is less than bill total (₹${grandTotal.toFixed(2)}). Do you want to proceed?`);
        if (!proceed) {
            if (mopValue === 'Both') {
                $('#cashAmount').focus();
            } else {
                $('#receivedAmount').focus();
            }
            return;
        }
    }

    // If validation passes, open the options modal
    $('#corporateBillOptions').modal('show');
}
</script>
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
                                <th>Type</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Batch ID</th>
                                <th>Batch Info</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- Items will be loaded dynamically via JavaScript -->
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    Loading items...
                                </td>
                            </tr>
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
@include('corporate_bill')
@include('pos.pos_popups')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Enhanced POS System with Fixed Issues
// This script fixes the cursor focus and additional charges issues

let itemCount = 0;
let billDiscountData = {
    type: null, // 'percentage' or 'amount'
    value: 0,
    timing: 'before', // 'before' or 'after'
    amount: 0
};

// Additional charges data - Fixed implementation
let additionalChargesAmount = 0;

// Store items data for barcode lookup
let itemsData = [];
let itemsLoaded = false;
let itemsLoading = false;
let totalItemsCount = 0;
let loadedItemsCount = 0;
// Track which input is currently focused to prevent auto-focus conflicts
let currentFocusedInput = null;

// Enhanced Barcode Scanner functionality
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcodeInput');
    
    loadItemsDataChunked();
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

    // Initialize discount modal functionality
    initializeDiscountModal();
    
    // Initialize additional charges functionality - Fixed
    initializeAdditionalCharges();
    
    // Initialize event listeners - Fixed
    initializeEventListeners();
    
    // Track input focus to prevent conflicts
    initializeFocusTracking();
});
function loadItemsDataChunked() {
    if (itemsLoading) return;
    
    itemsLoading = true;
    let currentChunk = 0;
    
    // Show loading indicator
    showLoadingIndicatorWithProgress();
    
    // Get store_id and role from page
    const storeId = '{{ $store_id ?? "" }}';
    const role = '{{ Session::get("role") }}';
    
    function loadChunk(chunk) {
        // Build URL
        let url = `/pos/get-items?chunk=${chunk}`;
        if (role === 'admin' && storeId) {
            url += `&store_id=${storeId}`;
        } else if (storeId) {
            url += `&store_id=${storeId}`;
        }
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                // Append items to existing array
                itemsData = itemsData.concat(data.data);
                loadedItemsCount = itemsData.length;
                totalItemsCount = data.total_count;
                
                // Update progress
                updateLoadingProgress(loadedItemsCount, totalItemsCount);
                
                // Update table incrementally
                appendItemsToTable(data.data);
                
                console.log(`Loaded chunk ${chunk}: ${data.data.length} items (Total: ${loadedItemsCount}/${totalItemsCount})`);
                
                // Load next chunk if available
                if (data.has_more) {
                    currentChunk++;
                    setTimeout(() => loadChunk(currentChunk), 100); // Small delay between chunks
                } else {
                    // All chunks loaded
                    itemsLoaded = true;
                    itemsLoading = false;
                    hideLoadingIndicator();
                    showNotification(`All ${totalItemsCount} items loaded successfully!`, 'success');
                    console.log(`Total items loaded: ${itemsData.length}`);
                }
            } else {
                console.error('Failed to load items:', data.message);
                itemsLoading = false;
                hideLoadingIndicator();
                showNotification('Failed to load items. Please refresh the page.', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading items:', error);
            itemsLoading = false;
            hideLoadingIndicator();
            showNotification('Error loading items. Please refresh the page.', 'error');
        });
    }
    
    // Start loading first chunk
    loadChunk(0);
}

function showLoadingIndicatorWithProgress() {
    const indicator = document.createElement('div');
    indicator.id = 'itemsLoadingIndicator';
    indicator.className = 'alert alert-info position-fixed';
    // indicator.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 350px;';
    // indicator.innerHTML = `
    //     <div class="d-flex align-items-center justify-content-between">
    //         <div class="d-flex align-items-center">
    //             <div class="spinner-border spinner-border-sm me-2" role="status">
    //                 <span class="visually-hidden">Loading...</span>
    //             </div>
    //             <span id="loadingText">Loading items...</span>
    //         </div>
    //         <span id="loadingProgress" class="badge bg-primary">0/0</span>
    //     </div>
    //     <div class="progress mt-2" style="height: 5px;">
    //         <div id="loadingProgressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
    //     </div>
    // `;
    document.body.appendChild(indicator);
}

// NEW FUNCTION: Update loading progress
function updateLoadingProgress(loaded, total) {
    const progressText = document.getElementById('loadingProgress');
    const progressBar = document.getElementById('loadingProgressBar');
    const loadingText = document.getElementById('loadingText');
    
    if (progressText && progressBar && loadingText) {
        const percentage = total > 0 ? Math.round((loaded / total) * 100) : 0;
        progressText.textContent = `${loaded}/${total}`;
        progressBar.style.width = `${percentage}%`;
        loadingText.textContent = `Loading items... ${percentage}%`;
    }
}

// NEW FUNCTION: Append items to table incrementally
function appendItemsToTable(items) {
    const tbody = document.getElementById('itemsTableBody');
    if (!tbody) return;
    
    // Clear "Loading..." message on first load
    if (tbody.querySelector('td[colspan="9"]')) {
        tbody.innerHTML = '';
    }
    
    items.forEach(item => {
        const isBatch = item.item_type === 'batch';
        let isExpired = false;
        let isExpiringSoon = false;
        
        if (isBatch && item.exp_date) {
            const expDate = new Date(item.exp_date);
            const now = new Date();
            isExpired = expDate < now;
            isExpiringSoon = !isExpired && ((expDate - now) / (1000 * 60 * 60 * 24)) <= 30;
        }
        
        let rowClass = '';
        if (isExpired) rowClass = 'expired-batch';
        else if (isExpiringSoon) rowClass = 'expiring-soon';
        
        const row = document.createElement('tr');
        row.className = rowClass;
        row.setAttribute('data-item-id', item.id);
        row.setAttribute('data-batch-id', isBatch ? item.batch_id : '');
        row.setAttribute('data-item-code', item.item_code || '');
        row.setAttribute('data-item-name', item.display_name || item.item_name);
        row.setAttribute('data-unit', item.measure_unit || '');
        row.setAttribute('data-price', item.sales_price || 0);
        row.setAttribute('data-tax', item.gst_rate || 0);
        row.setAttribute('data-discount', item.discount || 0);
        row.setAttribute('data-stock', item.current_stock || 0);
        row.setAttribute('data-item-type', item.item_type || 'regular');
        row.setAttribute('data-barcode', item.barcode || '');
        row.setAttribute('data-batch-no', isBatch ? item.batch_no : '');
        
        let displayName = item.item_name;
        let batchInfo = '';
        
        if (isBatch) {
            displayName += ` (Batch: ${item.batch_no})`;
            const mfgDate = item.mfg_date ? new Date(item.mfg_date).toLocaleDateString() : '-';
            const expDate = item.exp_date ? new Date(item.exp_date).toLocaleDateString() : '-';
            batchInfo = `<div class="small"><div>MFG: ${mfgDate}</div><div>EXP: ${expDate}</div></div>`;
        }
        
        row.innerHTML = `
            <td>
                <span class="item-type-badge ${isBatch ? 'batch-badge' : 'regular-badge'}">
                    ${isBatch ? 'BATCH' : 'ITEM'}
                </span>
            </td>
            <td>${item.item_code || ''}</td>
            <td>
                ${item.item_name}
                ${isBatch ? `<div class="batch-info">Batch: ${item.batch_no}</div>` : ''}
            </td>
            <td>${item.measure_unit || ''}</td>
            <td>₹${parseFloat(item.sales_price || 0).toFixed(2)}</td>
            <td>
                ${item.current_stock || 0}
                ${isExpired ? '<div class="text-danger small">EXPIRED</div>' : ''}
                ${isExpiringSoon ? '<div class="text-warning small">EXPIRING SOON</div>' : ''}
            </td>
            <td>${isBatch ? item.batch_id : '-'}</td>
            <td>${isBatch ? batchInfo : '-'}</td>
            <td>
                <button type="button" class="btn btn-sm btn-primary select-item-btn" 
                        ${isExpired ? 'disabled title="Item is expired"' : ''}>
                    Select
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}
// Initialize Focus Tracking - NEW FUNCTION
function initializeFocusTracking() {
    // Track all input focuses to prevent auto-focus conflicts
    const inputs = document.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            currentFocusedInput = this;
        });
        
        input.addEventListener('blur', function() {
            // Small delay to check if focus moved to another input
            setTimeout(() => {
                if (document.activeElement.tagName !== 'INPUT' && 
                    document.activeElement.tagName !== 'TEXTAREA' && 
                    document.activeElement.tagName !== 'SELECT') {
                    currentFocusedInput = null;
                }
            }, 100);
        });
    });
}

// Initialize Discount Modal
function initializeDiscountModal() {
    const discountForm = document.getElementById('discountForm');
    const percentageInput = document.getElementById('percentage');
    const amountInput = document.getElementById('discamount');
    const typeRadios = document.querySelectorAll('input[name="discountType"]');
    const clearDiscountBtn = document.getElementById('clearDiscount');
    
    // Handle discount type change
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'percentage') {
                percentageInput.disabled = false;
                amountInput.disabled = true;
                amountInput.value = '';
            } else {
                percentageInput.disabled = true;
                amountInput.disabled = false;
                percentageInput.value = '';
            }
        });
    });
    
    // Handle form submission
    if (discountForm) {
        discountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyBillDiscount();
        });
    }
    
    // Handle clear discount
    if (clearDiscountBtn) {
        clearDiscountBtn.addEventListener('click', function() {
            clearBillDiscount();
        });
    }
    
    // Initialize form state
    const percentageRadio = document.querySelector('input[name="discountType"][value="percentage"]');
    if (percentageRadio) {
        percentageRadio.checked = true;
        percentageInput.disabled = false;
        amountInput.disabled = true;
    }
}

// FIXED Additional Charges Modal Functionality
function initializeAdditionalCharges() {
    const additionalChargesInputs = ['bag', 'packing', 'adjustment', 'wrapping', 'customise'];
    const adjustAmtInput = document.getElementById('adjustamt');
    const additionalForm = document.querySelector('#additional form');
    
    // Check if elements exist before adding listeners
    if (!adjustAmtInput || !additionalForm) {
        console.log('Additional charges elements not found, creating basic functionality');
        
        // If modal doesn't exist, create basic additional charges functionality
        const additionalChargesInput = document.getElementById('additionalChargesInput');
        if (additionalChargesInput) {
            additionalChargesInput.addEventListener('input', function() {
                additionalChargesAmount = parseFloat(this.value) || 0;
                document.getElementById('additionalCharges').textContent = `₹ ${additionalChargesAmount.toFixed(2)}`;
                updateTotals();
            });
        }
        return;
    }
    
    // Real-time calculation for additional charges
    additionalChargesInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', calculateAdditionalCharges);
        }
    });

    function calculateAdditionalCharges() {
        let total = 0;
        additionalChargesInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                const value = parseFloat(input.value) || 0;
                total += value;
            }
        });
        if (adjustAmtInput) {
            adjustAmtInput.value = total.toFixed(2);
        }
    }

    // Handle form submission
    additionalForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let totalCharges = 0;
        additionalChargesInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                totalCharges += parseFloat(input.value) || 0;
            }
        });
        
        // Update the global additionalChargesAmount variable
        additionalChargesAmount = totalCharges;
        
        // Update the display
        document.getElementById('additionalCharges').textContent = `₹ ${additionalChargesAmount.toFixed(2)}`;
        
        // Recalculate bill totals
        updateTotals();
        
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('additional'));
        if (modal) {
            modal.hide();
        }
        
        // Show success message
        showNotification('Additional charges updated successfully!');
        
        // Mark as saved
        document.getElementById('additional').dataset.saved = 'true';
    });

    // Reset form when modal is closed (only if not saved)
    const additionalModal = document.getElementById('additional');
    if (additionalModal) {
        additionalModal.addEventListener('hidden.bs.modal', function() {
            if (this.dataset.saved !== 'true') {
                additionalForm.reset();
                if (adjustAmtInput) {
                    adjustAmtInput.value = '0.00';
                }
            }
            this.dataset.saved = 'false';
        });
    }

    // Handle cancel button
    const cancelBtn = document.querySelector('#additional .cancelbtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('additional'));
            if (modal) {
                modal.hide();
            }
        });
    }
}

// Optional: Add a notification function
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to body
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Initialize Event Listeners - FIXED
function initializeEventListeners() {
    // Received amount change - FIXED to prevent focus conflicts
    const receivedAmountInput = document.getElementById('receivedAmount');
    if (receivedAmountInput) {
        receivedAmountInput.addEventListener('input', calculateChange);
        receivedAmountInput.addEventListener('focus', function() {
            currentFocusedInput = this;
        });
    }
    
    // Method of payment change
    const mopSelect = document.getElementById('mop');
    if (mopSelect) {
        mopSelect.addEventListener('focus', function() {
            currentFocusedInput = this;
        });
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Don't trigger shortcuts if user is typing in an input
        if (currentFocusedInput && (
            currentFocusedInput.tagName === 'INPUT' || 
            currentFocusedInput.tagName === 'TEXTAREA' || 
            currentFocusedInput.tagName === 'SELECT'
        )) {
            return;
        }
        
        // F5 - Open discount modal
        if (e.key === 'F5') {
            e.preventDefault();
            const discountModal = new bootstrap.Modal(document.getElementById('discount'));
            discountModal.show();
        }
        
        // F6 - Open additional charges modal (if exists)
        if (e.key === 'F6') {
            e.preventDefault();
            const additionalModal = document.getElementById('additional');
            if (additionalModal) {
                const modal = new bootstrap.Modal(additionalModal);
                modal.show();
            }
        }
        
        // F4 - Focus on barcode input
        if (e.key === 'F4') {
            e.preventDefault();
            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput) {
                barcodeInput.focus();
            }
        }
        
        // Escape - Clear barcode input or unfocus current input
        if (e.key === 'Escape') {
            if (document.activeElement === document.getElementById('barcodeInput')) {
                document.getElementById('barcodeInput').value = '';
                clearFeedback();
            } else if (currentFocusedInput) {
                currentFocusedInput.blur();
            }
        }
    });
}

// function processBarcode() {
//     const barcodeInput = document.getElementById('barcodeInput');
//     const barcode = barcodeInput.value.trim();
    
//     if (!barcode) {
//         showFeedback('Please scan or enter a barcode', 'error');
//         return;
//     }

//     console.log('Processing barcode:', barcode);

//     // Enhanced search logic for both regular items and batches
//     const item = itemsData.find(item => {
//         // Search by item code (case-insensitive)
//         if (item.item_code && item.item_code.toString().toLowerCase() === barcode.toLowerCase()) {
//             return true;
//         }
        
//         // Search by barcode field
//         if (item.barcode && item.barcode.toString().toLowerCase() === barcode.toLowerCase()) {
//             return true;
//         }
        
//         // Search by item ID
//         if (item.id && item.id.toString() === barcode) {
//             return true;
//         }
        
//         // For batch items, also search by batch number
//         if (item.item_type === 'batch' && item.batch_no && 
//             item.batch_no.toString().toLowerCase() === barcode.toLowerCase()) {
//             return true;
//         }
        
//         // Search by item name (partial match)
//         if (item.item_name && item.item_name.toLowerCase().includes(barcode.toLowerCase())) {
//             return true;
//         }
        
//         return false;
//     });

//     if (item) {
//         // Check if batch item is expired
//         if (item.item_type === 'batch' && item.exp_date) {
//             const expDate = new Date(item.exp_date);
//             const now = new Date();
            
//             if (expDate < now) {
//                 showFeedback(`✗ Cannot add expired batch item: ${item.item_name} (Batch: ${item.batch_no})`, 'error');
//                 barcodeInput.value = '';
//                 setTimeout(() => {
//                     barcodeInput.focus();
//                     clearFeedback();
//                 }, 3000);
//                 return;
//             }
//         }

//         // Item found, add to bill with quantity 1
//         addItemToBill(item);
//         barcodeInput.value = '';
        
//         const itemTypeText = item.item_type === 'batch' ? `batch item ${item.batch_no}` : 'item';
//         showFeedback(`✓ ${item.item_name} (${itemTypeText}) added successfully`, 'success');
        
//         setTimeout(() => {
//             barcodeInput.focus();
//             clearFeedback();
//         }, 1500);
//     } else {
//         showFeedback(`✗ Item not found: ${barcode}`, 'error');
//         setTimeout(() => {
//             barcodeInput.select();
//             clearFeedback();
//         }, 3000);
//     }
// }

// function addItemToBill(item) {
//     const qty = 1;
    
//     // For batch items, check if the specific batch already exists
//     let existingRowSelector;
//     if (item.item_type === 'batch') {
//         existingRowSelector = `#addedTable tbody tr[data-item-id="${item.id}"][data-batch-id="${item.batch_id}"]`;
//     } else {
//         existingRowSelector = `#addedTable tbody tr[data-item-id="${item.id}"][data-item-type="regular"]`;
//     }
    
//     const existingRow = document.querySelector(existingRowSelector);
    
//     if (existingRow) {
//         // Item/batch already exists, update quantity
//         const existingQtyCell = existingRow.cells[3];
//         const existingQty = parseFloat(existingQtyCell.textContent) || 0;
//         const newQty = existingQty + qty;
        
//         // Check stock for new quantity
//         const currentStock = parseFloat(item.current_stock) || 0;
//         if (currentStock < newQty) {
//             const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
//             showFeedback(`✗ Insufficient stock for ${itemTypeText}. Available: ${currentStock}, Requested: ${newQty}`, 'error');
//             return;
//         }
        
//         updateExistingItemRow(existingRow, item, newQty);
//         const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
//         showFeedback(`✓ ${item.item_name} (${itemTypeText}) quantity updated to ${newQty}`, 'success');
//     } else {
//         // New item/batch, check stock
//         const currentStock = parseFloat(item.current_stock) || 0;
//         if (currentStock < qty) {
//             const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
//             showFeedback(`✗ Insufficient stock for ${itemTypeText}. Available: ${currentStock}`, 'error');
//             return;
//         }
        
//         addNewItemRow(item, qty);
//         const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
//         showFeedback(`✓ ${item.item_name} (${itemTypeText}) added to bill`, 'success');
//     }

//     updateTotals();
    
//     // Ensure table is visible
//     const table = document.getElementById('addedTable');
//     if (table) {
//         table.style.display = 'table';
//     }
    
//     const footer = document.getElementById('tableFooter');
//     if (footer) {
//         footer.style.display = 'table-footer-group';
//     }
// }

function processBarcode() {
    const barcodeInput = document.getElementById('barcodeInput');
    const barcode = barcodeInput.value.trim();
    
    if (!barcode) {
        showFeedback('Please scan or enter a barcode', 'error');
        return;
    }

    console.log('Processing barcode:', barcode);

    // Normalize the search term for better matching
    const normalizedBarcode = barcode.toLowerCase().replace(/\s+/g, ' ').trim();

    // Enhanced search logic for both regular items and batches
    const item = itemsData.find(item => {
        // Search by item code (case-insensitive, exact match)
        if (item.item_code && item.item_code.toString().toLowerCase() === normalizedBarcode) {
            return true;
        }
        
        // Search by barcode field (case-insensitive, exact match)
        if (item.barcode && item.barcode.toString().toLowerCase() === normalizedBarcode) {
            return true;
        }
        
        // Search by item ID (exact match)
        if (item.id && item.id.toString() === barcode) {
            return true;
        }
        
        // For batch items, also search by batch number
        if (item.item_type === 'batch' && item.batch_no && 
            item.batch_no.toString().toLowerCase() === normalizedBarcode) {
            return true;
        }
        
        // Search by item name (normalized, case-insensitive, exact match first)
        if (item.item_name) {
            const normalizedItemName = item.item_name.toLowerCase().replace(/\s+/g, ' ').trim();
            
            // Exact match
            if (normalizedItemName === normalizedBarcode) {
                return true;
            }
            
            // Partial match (contains)
            if (normalizedItemName.includes(normalizedBarcode)) {
                return true;
            }
        }
        
        // Search by display_name if it exists
        if (item.display_name) {
            const normalizedDisplayName = item.display_name.toLowerCase().replace(/\s+/g, ' ').trim();
            
            // Exact match
            if (normalizedDisplayName === normalizedBarcode) {
                return true;
            }
            
            // Partial match (contains)
            if (normalizedDisplayName.includes(normalizedBarcode)) {
                return true;
            }
        }
        
        return false;
    });

    if (item) {
        // Check stock quantity first
        const currentStock = parseFloat(item.current_stock) || 0;
        if (currentStock <= 0) {
            const itemTypeText = item.item_type === 'batch' ? `(Batch: ${item.batch_no})` : '';
            showFeedback(`✗ Out of stock: ${item.item_name} ${itemTypeText}. Available: ${currentStock}`, 'error');
            barcodeInput.value = '';
            setTimeout(() => {
                barcodeInput.focus();
                clearFeedback();
            }, 3000);
            return;
        }
        
        // Check if batch item is expired
        if (item.item_type === 'batch' && item.exp_date) {
            const expDate = new Date(item.exp_date);
            const now = new Date();
            
            if (expDate < now) {
                showFeedback(`✗ Cannot add expired batch item: ${item.item_name} (Batch: ${item.batch_no})`, 'error');
                barcodeInput.value = '';
                setTimeout(() => {
                    barcodeInput.focus();
                    clearFeedback();
                }, 3000);
                return;
            }
        }

        // Item found, add to bill with quantity 1
        addItemToBill(item);
        barcodeInput.value = '';
        
        const itemTypeText = item.item_type === 'batch' ? `batch item ${item.batch_no}` : 'item';
        showFeedback(`✓ ${item.item_name} (${itemTypeText}) added successfully`, 'success');
        
        setTimeout(() => {
            barcodeInput.focus();
            clearFeedback();
        }, 1500);
    } else {
        showFeedback(`✗ Item not found: ${barcode}`, 'error');
        
        // Log available items for debugging
        console.log('Available items:', itemsData.map(i => i.item_name || i.display_name));
        
        setTimeout(() => {
            barcodeInput.select();
            clearFeedback();
        }, 3000);
    }
}

function addItemToBill(item) {
    const qty = 1;
    const currentStock = parseFloat(item.current_stock) || 0;
    
    // Check if item has stock
    if (currentStock <= 0) {
        const itemTypeText = item.item_type === 'batch' ? `(Batch: ${item.batch_no})` : '';
        showFeedback(`✗ Out of stock: ${item.item_name} ${itemTypeText}`, 'error');
        return;
    }
    
    // For batch items, check if the specific batch already exists
    let existingRowSelector;
    if (item.item_type === 'batch') {
        existingRowSelector = `#addedTable tbody tr[data-item-id="${item.id}"][data-batch-id="${item.batch_id}"]`;
    } else {
        existingRowSelector = `#addedTable tbody tr[data-item-id="${item.id}"][data-item-type="regular"]`;
    }
    
    const existingRow = document.querySelector(existingRowSelector);
    
    if (existingRow) {
        // Item/batch already exists, update quantity
        const existingQtyCell = existingRow.cells[3];
        const existingQty = parseFloat(existingQtyCell.textContent) || 0;
        const newQty = existingQty + qty;
        
        // Check stock for new quantity
        if (currentStock < newQty) {
            const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
            showFeedback(`✗ Insufficient stock for ${itemTypeText}. Available: ${currentStock}, Requested: ${newQty}`, 'error');
            return;
        }
        
        updateExistingItemRow(existingRow, item, newQty);
        const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
        showFeedback(`✓ ${item.item_name} (${itemTypeText}) quantity updated to ${newQty}`, 'success');
    } else {
        // New item/batch, check stock
        if (currentStock < qty) {
            const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
            showFeedback(`✗ Insufficient stock for ${itemTypeText}. Available: ${currentStock}`, 'error');
            return;
        }
        
        addNewItemRow(item, qty);
        const itemTypeText = item.item_type === 'batch' ? `batch ${item.batch_no}` : 'item';
        showFeedback(`✓ ${item.item_name} (${itemTypeText}) added to bill`, 'success');
    }

    updateTotals();
    
    // Ensure table is visible
    const table = document.getElementById('addedTable');
    if (table) {
        table.style.display = 'table';
    }
    
    const footer = document.getElementById('tableFooter');
    if (footer) {
        footer.style.display = 'table-footer-group';
    }
}


function addNewItemRow(item, qty) {
    const price = parseFloat(item.sales_price) || 0;
    const discount = parseFloat(item.discount) || 0;
    const tax = parseFloat(item.gst_rate) || 0;
    const totalAmount = price * qty;

    itemCount++;

    // Create display name for the table
    let displayName = item.item_name;
    let batchInfo = '';
    
    if (item.item_type === 'batch') {
        displayName += ` (Batch: ${item.batch_no})`;
        const mfgDate = item.mfg_date ? new Date(item.mfg_date).toLocaleDateString() : '-';
        const expDate = item.exp_date ? new Date(item.exp_date).toLocaleDateString() : '-';
        batchInfo = `<div class="batch-info">MFG: ${mfgDate} | EXP: ${expDate}</div>`;
    }

    const row = `
        <tr data-item-id="${item.id}" 
            data-batch-id="${item.item_type === 'batch' ? item.batch_id : ''}"
            data-item-code="${item.item_code || ''}" 
            data-stock="${item.current_stock || 0}"
            data-item-type="${item.item_type || 'regular'}"
            data-batch-no="${item.batch_no || ''}">
            <td class="item-sno">${itemCount}</td>
            <td class="item-name" title="${displayName}">
                ${displayName}
                <span class="item-type-badge ${item.item_type === 'batch' ? 'batch-badge' : 'regular-badge'}">
                    ${item.item_type === 'batch' ? 'BATCH' : 'ITEM'}
                </span>
                ${batchInfo}
            </td>
            <td class="item-unit">${item.measure_unit || 'PCS'}</td>
            <td class="item-qty" contenteditable="true" data-original-qty="${qty}">${qty}</td>
            <td class="item-price">₹ ${price.toFixed(2)}</td>
            <td class="item-discount">${discount}</td>
            <td class="item-tax">${tax}</td>
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
    if (tbody) {
        tbody.insertAdjacentHTML('beforeend', row);
        makeQuantityEditable(tbody.lastElementChild);

        const newRow = tbody.lastElementChild;
        newRow.classList.add('newly-added');
        setTimeout(() => newRow.classList.remove('newly-added'), 2000);
    }
}
function updateExistingItemRow(row, item, newQty) {
    const price = parseFloat(item.sales_price) || 0;
    const discount = parseFloat(item.discount) || 0;
    const tax = parseFloat(item.gst_rate) || 0;
    const totalAmount = price * newQty;

    // Update cells
    row.cells[3].textContent = newQty; // Quantity
    row.cells[5].textContent = discount; // Discount %
    row.cells[6].textContent = tax; // Tax %
    row.cells[7].textContent = `₹ ${totalAmount.toFixed(2)}`; // Amount

    // Keep original qty in data attribute
    const qtyCell = row.querySelector('.item-qty');
    if (qtyCell) {
        qtyCell.setAttribute('data-original-qty', newQty);
    }

    // Highlight change
    row.classList.add('newly-added');
    setTimeout(() => row.classList.remove('newly-added'), 2000);
}

// Enhanced manual item selection to handle both regular and batch items
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('select-item-btn')) {
        const row = e.target.closest('tr');
        const item = {
            id: row.getAttribute('data-item-id'),
            batch_id: row.getAttribute('data-batch-id'),
            item_code: row.getAttribute('data-item-code'),
            item_name: row.getAttribute('data-item-name'),
            display_name: row.getAttribute('data-item-name'),
            measure_unit: row.getAttribute('data-unit'),
            sales_price: parseFloat(row.getAttribute('data-price')),
            gst_rate: parseFloat(row.getAttribute('data-tax')),
            discount: parseFloat(row.getAttribute('data-discount')),
            current_stock: parseFloat(row.getAttribute('data-stock')),
            item_type: row.getAttribute('data-item-type'),
            batch_no: row.getAttribute('data-batch-no') || '',
            barcode: row.getAttribute('data-barcode') || ''
        };
        
        // For batch items, get additional info from the table
        if (item.item_type === 'batch') {
            // Extract dates from the batch info cell if needed
            const batchInfoCell = row.cells[6]; // Batch Info column
            // You might need to extract mfg_date and exp_date here if needed
        }
        
        addItemToBill(item);
        
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('itemSelectModal'));
        if (modal) {
            modal.hide();
        }
    }
});



function showFeedback(message, type) {
    const feedback = document.getElementById('scannerFeedback');
    if (feedback) {
        feedback.innerHTML = message;
        feedback.className = `scanner-feedback ${type}`;
    }
}

function clearFeedback() {
    const feedback = document.getElementById('scannerFeedback');
    if (feedback) {
        feedback.innerHTML = '';
        feedback.className = 'scanner-feedback';
    }
}




function updateExistingItemRow(row, item, newQty) {
    const price = parseFloat(item.sales_price) || 0;
    const discount = parseFloat(item.discount) || 0; // from DB
    const tax = parseFloat(item.gst_rate) || 0;      // from DB

    // ✅ Only qty × price (no tax or discount in amount calculation)
    const totalAmount = price * newQty;

    // Update cells
    row.cells[3].textContent = newQty; // Quantity
    row.cells[5].textContent = discount; // Discount %
    row.cells[6].textContent = tax;      // Tax %
    row.cells[7].textContent = `₹ ${totalAmount.toFixed(2)}`; // Amount

    // Keep original qty in data attribute
    const qtyCell = row.querySelector('.item-qty');
    if (qtyCell) {
        qtyCell.setAttribute('data-original-qty', newQty);
    }

    // Highlight change
    row.classList.add('newly-added');
    setTimeout(() => row.classList.remove('newly-added'), 2000);
}

// Enhanced makeQuantityEditable function to handle batch items properly
function makeQuantityEditable(row) {
    const qtyCell = row.querySelector('.item-qty');
    
    if (!qtyCell) return;
    
    qtyCell.addEventListener('focus', function() {
        currentFocusedInput = this;
    });
    
    qtyCell.addEventListener('blur', function() {
        const newQty = parseFloat(this.textContent) || 1;
        const itemId = row.getAttribute('data-item-id');
        const batchId = row.getAttribute('data-batch-id');
        const itemType = row.getAttribute('data-item-type');
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
        let item;
        if (itemType === 'batch') {
            item = itemsData.find(i => i.id.toString() === itemId && i.batch_id && i.batch_id.toString() === batchId);
        } else {
            item = itemsData.find(i => i.id.toString() === itemId && i.item_type !== 'batch');
        }
        
        if (item) {
            updateExistingItemRow(row, item, newQty);
            updateTotals();
        }
        
        currentFocusedInput = null;
    });
    
    qtyCell.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.blur();
        }
        if (!/[\d\.]/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
        }
    });
}

// Apply Bill Discount
function applyBillDiscount() {
    const discountType = document.querySelector('input[name="discountType"]:checked').value;
    const discountTiming = document.querySelector('input[name="discountTiming"]:checked').value;
    const percentageValue = parseFloat(document.getElementById('percentage').value) || 0;
    const amountValue = parseFloat(document.getElementById('discamount').value) || 0;
    
    // Validate input
    if (discountType === 'percentage' && (percentageValue <= 0 || percentageValue > 100)) {
        alert('Please enter a valid percentage between 0 and 100');
        return;
    }
    
    if (discountType === 'amount' && amountValue <= 0) {
        alert('Please enter a valid discount amount');
        return;
    }
    
    // Get current values
    const subtotal = parseFloat(document.getElementById('subTotal').textContent.replace('₹', '').trim()) || 0;
    const itemDiscounts = parseFloat(document.getElementById('itemDiscounts').textContent.replace('₹', '').trim()) || 0;
    const tax = parseFloat(document.getElementById('totalTax').textContent.replace('₹', '').trim()) || 0;
    
    // Get loyalty points and gift card amounts
    const loyaltyPointsDiscount = parseFloat(document.getElementById('appliedAmount')?.value) || 0;
    const giftCardDiscount = totalGiftCardDiscount || 0; // Use totalGiftCardDiscount from gift card system
    
    let maxDiscountAmount = 0;
    if (discountTiming === 'before') {
        // Before tax: subtotal - item discounts - loyalty points - gift cards
        maxDiscountAmount = subtotal - itemDiscounts - loyaltyPointsDiscount - giftCardDiscount;
    } else {
        // After tax: subtotal - item discounts + tax - loyalty points - gift cards
        maxDiscountAmount = subtotal - itemDiscounts + tax - loyaltyPointsDiscount - giftCardDiscount;
    }
    
    // Ensure maxDiscountAmount is not negative
    maxDiscountAmount = Math.max(0, maxDiscountAmount);
    
    if (discountType === 'amount' && amountValue > maxDiscountAmount) {
        alert(`Discount amount cannot exceed ₹${maxDiscountAmount.toFixed(2)}`);
        return;
    }
    
    // Update bill discount data
    billDiscountData = {
        type: discountType,
        value: discountType === 'percentage' ? percentageValue : amountValue,
        timing: discountTiming,
        amount: 0 // Will be calculated in updateTotals
    };
    
    // Update totals
    updateTotals();
    
    // Show current discount in modal
    updateCurrentDiscountDisplay();
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('discount'));
    if (modal) {
        modal.hide();
    }
    
    // Show success message
    showFeedback(`✓ Bill discount applied successfully`, 'success');
    setTimeout(clearFeedback, 2000);
}

// Clear Bill Discount
function clearBillDiscount() {
    billDiscountData = {
        type: null,
        value: 0,
        timing: 'before',
        amount: 0
    };
    
    // Clear form inputs
    document.getElementById('percentage').value = '';
    document.getElementById('discamount').value = '';
    const percentageRadio = document.querySelector('input[name="discountType"][value="percentage"]');
    const beforeRadio = document.querySelector('input[name="discountTiming"][value="before"]');
    
    if (percentageRadio) percentageRadio.checked = true;
    if (beforeRadio) beforeRadio.checked = true;
    
    // Reset form state
    document.getElementById('percentage').disabled = false;
    document.getElementById('discamount').disabled = true;
    
    // Hide current discount display
    const currentDiscountDisplay = document.getElementById('currentDiscountDisplay');
    if (currentDiscountDisplay) {
        currentDiscountDisplay.style.display = 'none';
    }
    
    // Update totals
    updateTotals();
    
    showFeedback('✓ Bill discount cleared', 'success');
    setTimeout(clearFeedback, 2000);
}

// Update Current Discount Display in Modal
function updateCurrentDiscountDisplay() {
    const displayDiv = document.getElementById('currentDiscountDisplay');
    const textSpan = document.getElementById('currentDiscountText');
    
    if (!displayDiv || !textSpan) return;
    
    if (billDiscountData.type && billDiscountData.value > 0) {
        let discountText = '';
        if (billDiscountData.type === 'percentage') {
            discountText = `${billDiscountData.value}% discount applied ${billDiscountData.timing} tax`;
        } else {
            discountText = `₹${billDiscountData.value} discount applied ${billDiscountData.timing} tax`;
        }
        textSpan.textContent = discountText;
        displayDiv.style.display = 'block';
    } else {
        displayDiv.style.display = 'none';
    }
}

// Enhanced Remove item functionality
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
        const row = e.target.closest('tr');
        const itemName = row.querySelector('.item-name').textContent;
        
        if (confirm(`Remove ${itemName} from bill?`)) {
            row.remove();
            updateTotals();
            
            // Hide table if no items left
            const remainingRows = document.querySelectorAll('#addedTable tbody tr').length;
            if (remainingRows === 0) {
                document.getElementById('addedTable').style.display = 'none';
                const footer = document.getElementById('tableFooter');
                if (footer) {
                    footer.style.display = 'none';
                }
                itemCount = 0; // Reset counter
                showFeedback('All items removed from bill', 'success');
                setTimeout(clearFeedback, 2000);
            }
        }
    }
});

function updateTotals() {
    let totalAmount = 0; // sum of Amount (₹) column
    let totalItems = 0;
    
    let totalItemDiscountAmount = 0;
    let totalDiscountPercent = 0;
    let totalItemTaxAmount = 0;
    let totalTaxPercent = 0;
    
    let itemCountForPercent = 0; // for averaging percentage
    
    // Get all table rows
    const rows = document.querySelectorAll('#addedTable tbody tr');
    
    rows.forEach(row => {
        const qty = parseFloat(row.cells[3].textContent) || 0;
        const amount = parseFloat(row.cells[7].textContent.replace('₹', '').trim()) || 0; // Amount column
        const discountPercent = parseFloat(row.cells[5].textContent) || 0;
        const taxPercent = parseFloat(row.cells[6].textContent) || 0;
        
        // Track totals
        totalAmount += amount;
        totalItems += qty;
        
        // Just for display
        totalItemDiscountAmount += (amount * discountPercent) / (100 - discountPercent); // reverse calc
        totalDiscountPercent += discountPercent;
        totalItemTaxAmount += (amount * taxPercent) / (100 + taxPercent); // reverse calc
        totalTaxPercent += taxPercent;
        
        itemCountForPercent++;
    });
    
    // Average percentages if needed
    const avgDiscountPercent = itemCountForPercent ? (totalDiscountPercent / itemCountForPercent) : 0;
    const avgTaxPercent = itemCountForPercent ? (totalTaxPercent / itemCountForPercent) : 0;
    
    // Calculate subtotal for bill discount calculation
    const subtotal = totalAmount;
    
    // Get loyalty points and gift card amounts
    const loyaltyPointsDiscount = parseFloat(document.getElementById('appliedAmount')?.value) || 0;
    const giftCardDiscount = totalGiftCardDiscount || 0; // Use totalGiftCardDiscount from gift card system
    
    // Calculate bill discount
    let billDiscountAmount = 0;
    if (billDiscountData.type && billDiscountData.value > 0) {
        let discountBase = 0;
        
        if (billDiscountData.timing === 'before') {
            // Apply discount before tax on (subtotal - item discounts - loyalty points - gift cards)
            discountBase = subtotal - totalItemDiscountAmount - loyaltyPointsDiscount - giftCardDiscount;
        } else {
            // Apply discount after tax on (full amount including tax - loyalty points - gift cards)
            discountBase = subtotal - totalItemDiscountAmount + totalItemTaxAmount - loyaltyPointsDiscount - giftCardDiscount;
        }
        
        // Ensure discountBase is not negative
        discountBase = Math.max(0, discountBase);
        
        if (billDiscountData.type === 'percentage') {
            billDiscountAmount = discountBase * (billDiscountData.value / 100);
        } else {
            billDiscountAmount = Math.min(billDiscountData.value, discountBase);
        }
    }
    
    // Store calculated bill discount amount
    billDiscountData.amount = billDiscountAmount;
    
    // Update UI
    document.getElementById('subTotal').textContent = `₹ ${totalAmount.toFixed(2)}`;
    document.getElementById('itemDiscounts').textContent = `₹ ${totalItemDiscountAmount.toFixed(2)} (${avgDiscountPercent.toFixed(2)}%)`;
    document.getElementById('totalTax').textContent = `₹ ${totalItemTaxAmount.toFixed(2)} (${avgTaxPercent.toFixed(2)}%)`;
    document.getElementById('totalItems').textContent = totalItems.toFixed(0);
    
    // Update bill discount display
    document.getElementById('billDiscount').textContent = `₹ ${billDiscountAmount.toFixed(2)}`;
    
    // Additional charges
    document.getElementById('additionalCharges').textContent = `₹ ${additionalChargesAmount.toFixed(2)}`;
    
    // Grand total: Amount + Additional Charges - Bill Discount - Loyalty Points - Gift Cards
    const grandTotal = totalAmount + additionalChargesAmount - billDiscountAmount - loyaltyPointsDiscount - giftCardDiscount;
    document.getElementById('grandTotal').textContent = `₹ ${Math.max(0, grandTotal).toFixed(2)}`;
    
    // Update gift card discount display
    if (typeof updateGiftCardDiscountDisplay === 'function') {
        updateGiftCardDiscountDisplay();
    }
    
    // Update bill discount info display (if needed)
    updateBillDiscountDisplay();
    calculateChange();
}

// Alternative version if your Amount column already includes all calculations
function updateTotalsSimplified() {
    let totalAmount = 0;
    let totalItems = 0;
    
    const additionalChargesAmount = parseFloat(document.getElementById('additionalChargesInput')?.value) || 0;
    const rows = document.querySelectorAll('#addedTable tbody tr');
    
    rows.forEach(row => {
        const qty = parseFloat(row.cells[3].textContent) || 0;
        const amount = parseFloat(row.cells[7].textContent.replace('₹', '').trim()) || 0;
        
        totalAmount += amount;
        totalItems += qty;
    });
    
    // If you just need basic totals without breaking down discounts/taxes
    document.getElementById('subTotal').textContent = `₹ ${totalAmount.toFixed(2)}`;
    document.getElementById('totalItems').textContent = totalItems.toFixed(0);
    document.getElementById('additionalCharges').textContent = `₹ ${additionalChargesAmount.toFixed(2)}`;
    
    const grandTotal = totalAmount + additionalChargesAmount;
    document.getElementById('grandTotal').textContent = `₹ ${grandTotal.toFixed(2)}`;
    
    if (typeof updateBillDiscountDisplay === 'function') {
        updateBillDiscountDisplay();
    }
    if (typeof calculateChange === 'function') {
        calculateChange();
    }
}

function updateBillDiscountDisplay() {
    const billDiscountRow = document.getElementById('billDiscountRow');
    const billDiscountInfo = document.getElementById('billDiscountInfo');
    
    if (!billDiscountRow || !billDiscountInfo) return;
    
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
        billDiscountRow.style.display = 'table-row'; // Always show the row
        billDiscountInfo.textContent = '';
    }
}






function initializePaymentMode() {
    const mopSelect = document.getElementById('mop');
    const singlePaymentInput = document.getElementById('singlePaymentInput');
    const splitPaymentInputs = document.getElementById('splitPaymentInputs');
    const receivedAmount = document.getElementById('receivedAmount');
    const cashAmount = document.getElementById('cashAmount');
    const onlineAmount = document.getElementById('onlineAmount');
    
    if (!mopSelect) return;
    
    // Handle mode of payment change
    mopSelect.addEventListener('change', function() {
        const selectedMode = this.value;
        
        if (selectedMode === 'Both') {
            // Show split payment inputs
            singlePaymentInput.style.display = 'none';
            splitPaymentInputs.style.display = 'block';
            
            // Set focus to cash amount
            setTimeout(() => cashAmount.focus(), 100);
            
            // Clear single payment input
            receivedAmount.value = '0';
        } else {
            // Show single payment input
            singlePaymentInput.style.display = 'block';
            splitPaymentInputs.style.display = 'none';
            
            // Clear split payment inputs
            cashAmount.value = '0';
            onlineAmount.value = '0';
            
            // Set focus to single payment input
            setTimeout(() => receivedAmount.focus(), 100);
        }
        
        // Recalculate change
        calculateChange();
    });
    
    // Handle input changes for all payment inputs
    receivedAmount.addEventListener('input', function() {
        calculateChange();
    });
    
    cashAmount.addEventListener('input', function() {
        calculateChange();
    });
    
    onlineAmount.addEventListener('input', function() {
        calculateChange();
    });
    
    // Track focus for all payment inputs
    receivedAmount.addEventListener('focus', function() {
        currentFocusedInput = this;
    });
    
    cashAmount.addEventListener('focus', function() {
        currentFocusedInput = this;
    });
    
    onlineAmount.addEventListener('focus', function() {
        currentFocusedInput = this;
    });
}

// FIXED: Update calculateChange function to handle both payment modes
function calculateChange() {
    const mopSelect = document.getElementById('mop');
    const receivedAmountInput = document.getElementById('receivedAmount');
    const cashAmountInput = document.getElementById('cashAmount');
    const onlineAmountInput = document.getElementById('onlineAmount');
    const grandTotalEl = document.getElementById('grandTotal');
    const changeToReturnEl = document.getElementById('changeToReturn');
    
    if (!grandTotalEl || !changeToReturnEl) return;
    
    const grandTotalText = grandTotalEl.textContent;
    const grandTotal = parseFloat(grandTotalText.replace('₹', '').replace(',', '').trim()) || 0;
    
    let totalReceived = 0;
    
    // Calculate total received based on payment mode
    if (mopSelect && mopSelect.value === 'Both') {
        const cashAmount = parseFloat(cashAmountInput.value) || 0;
        const onlineAmount = parseFloat(onlineAmountInput.value) || 0;
        totalReceived = cashAmount + onlineAmount;
    } else {
        totalReceived = parseFloat(receivedAmountInput.value) || 0;
    }
    
    const change = totalReceived - grandTotal;
    changeToReturnEl.textContent = `₹ ${change.toFixed(2)}`;
}

function shouldAutoFocusBarcode() {
    // Don't auto-focus if:
    // 1. User is currently typing in an input
    // 2. A modal is open
    // 3. User is editing a contenteditable element
    
    if (currentFocusedInput) return false;
    if (document.querySelector('.modal.show')) return false;
    if (document.activeElement && (
        document.activeElement.tagName === 'INPUT' ||
        document.activeElement.tagName === 'TEXTAREA' ||
        document.activeElement.tagName === 'SELECT' ||
        document.activeElement.contentEditable === 'true'
    )) return false;
    
    return true;
}

// FIXED Auto-focus on barcode input when appropriate
document.addEventListener('click', function(e) {
    // Only auto-focus if clicking outside of inputs and not in modals
    if (!e.target.closest('.modal') && 
        !e.target.closest('input') && 
        !e.target.closest('textarea') && 
        !e.target.closest('select') && 
        !e.target.closest('[contenteditable="true"]')) {
        
        setTimeout(() => {
            if (shouldAutoFocusBarcode()) {
                const barcodeInput = document.getElementById('barcodeInput');
                if (barcodeInput) {
                    barcodeInput.focus();
                }
            }
        }, 100);
    }
});

// Handle modal events to refocus on barcode input appropriately
document.addEventListener('hidden.bs.modal', function () {
    setTimeout(() => {
        if (shouldAutoFocusBarcode()) {
            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput) {
                barcodeInput.focus();
            }
        }
    }, 300);
});



// const itemSearchInput = document.getElementById('itemSearchInput');
// if (itemSearchInput) {
//     itemSearchInput.addEventListener('input', function() {
//         const searchTerm = this.value.toLowerCase();
//         const rows = document.querySelectorAll('#itemsTableBody tr');
        
//         rows.forEach(row => {
//             // Adjust the cell indexes to match your table
//             const itemCode = row.cells[1]?.textContent.toLowerCase() || '';
//             const itemName = row.cells[2]?.textContent.toLowerCase() || '';
            
//             if (itemCode.includes(searchTerm) || itemName.includes(searchTerm)) {
//                 row.style.display = '';
//             } else {
//                 row.style.display = 'none';
//             }
//         });
//     });
// }

const itemSearchInput = document.getElementById('itemSearchInput');
if (itemSearchInput) {
    itemSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#itemsTableBody tr');
        
        // If search is empty, show all rows
        if (searchTerm === '') {
            rows.forEach(row => {
                row.style.display = '';
            });
            return;
        }
        
        // Split search term into individual words/tokens
        const searchTokens = searchTerm.split(/\s+/).filter(token => token.length > 0);
        
        rows.forEach(row => {
            // Skip rows without enough cells (like loading rows)
            if (row.cells.length < 9) {
                return;
            }
            
            // Get all text content from the entire row (excluding the Action column)
            let rowText = '';
            for (let i = 0; i < row.cells.length - 1; i++) {
                rowText += row.cells[i].textContent.toLowerCase() + ' ';
            }
            
            // Check if ALL search tokens are found in the row text
            const allTokensFound = searchTokens.every(token => rowText.includes(token));
            
            if (allTokensFound) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}



// Add CSS for highlighting newly added items
const style = document.createElement('style');
style.textContent = `
    .newly-added {
        background-color: #d4edda !important;
        transition: background-color 2s ease-out;
    }
    
    .scanner-feedback {
        min-height: 20px;
    }
    
    .scanner-feedback.success {
        color: #28a745;
        font-weight: 500;
    }
    
    .scanner-feedback.error {
        color: #dc3545;
        font-weight: 500;
    }
    
    /* Prevent auto-focus conflicts */
    input:focus, textarea:focus, select:focus {
        outline: 2px solid #007bff;
        outline-offset: 1px;
    }
    
    [contenteditable="true"]:focus {
        outline: 2px solid #007bff;
        outline-offset: 1px;
        background-color: #f8f9fa;
    }
`;
document.head.appendChild(style);

// Enhanced Save & Print functionality






// Update the Save & Print functionality (replace the existing one)
document.addEventListener('click', function(e) {
    if (e.target.id === 'savePrintBtn' || e.target.closest('#savePrintBtn')) {
        e.preventDefault();
        
        // Validate if there are items in the bill
        const rows = document.querySelectorAll('#addedTable tbody tr');
        if (rows.length === 0) {
            alert('Please add at least one item to the bill');
            return;
        }
        
        const mopSelect = document.getElementById('mop');
        const mopValue = mopSelect.value;
        
        // Get received amounts based on payment mode
        let receivedAmount = 0;
        let cashAmount = 0;
        let onlineAmount = 0;
        
        if (mopValue === 'Both') {
            cashAmount = parseFloat(document.getElementById('cashAmount').value) || 0;
            onlineAmount = parseFloat(document.getElementById('onlineAmount').value) || 0;
            receivedAmount = cashAmount + onlineAmount;
            
            // Validate split payments
            if (cashAmount <= 0 && onlineAmount <= 0) {
                alert('Please enter cash or online amount for split payment.');
                return;
            }
        } else {
            receivedAmount = parseFloat(document.getElementById('receivedAmount').value) || 0;
        }
        
        const grandTotal = parseFloat(document.getElementById('grandTotal').textContent.replace('₹', '').trim()) || 0;
        
        // Validate received amount
        if (receivedAmount < grandTotal) {
            const proceed = confirm(`Received amount (₹${receivedAmount.toFixed(2)}) is less than bill total (₹${grandTotal.toFixed(2)}). Do you want to proceed?`);
            if (!proceed) return;
        }
        
        // Collect bill data with split payment information
        const billData = {
            customer_name: document.getElementById('customer_name')?.value || '',
            customer_contact: document.getElementById('customer_contact')?.value || '',
            items: [],
            sub_total: parseFloat(document.getElementById('subTotal').textContent.replace(/[₹,\s]/g, '')) || 0,
            total_discount: parseFloat(document.getElementById('itemDiscounts').textContent.replace(/[₹,\s]/g, '')) || 0,
            total_tax: parseFloat(document.getElementById('totalTax').textContent.replace(/[₹,\s]/g, '')) || 0,
            additional_charges: additionalChargesAmount || 0,
            grand_total: grandTotal,
            received_amount: receivedAmount,
            mode_of_payment: mopValue,
            
            // Split payment fields
            cash_amount: mopValue === 'Both' ? cashAmount : (mopValue === 'Cash' ? receivedAmount : 0),
            online_amount: mopValue === 'Both' ? onlineAmount : (mopValue === 'Online' ? receivedAmount : 0),
            
            loyalty_points_used: 0,
            applied_gift_cards: [],
            total_gift_card_discount: 0
        };
        
        // Collect items data
        rows.forEach(row => {
            const cells = row.cells;
            const itemData = {
                item_id: parseInt(row.getAttribute('data-item-id')),
                unit: cells[2].textContent.trim(),
                qty: parseFloat(cells[3].textContent) || 0,
                price: parseFloat(cells[4].textContent.replace('₹', '').trim()) || 0,
                discount: parseFloat(cells[5].textContent) || 0,
                tax: parseFloat(cells[6].textContent) || 0,
                amount: parseFloat(cells[7].textContent.replace('₹', '').trim()) || 0
            };
            
            const batchId = row.getAttribute('data-batch-id');
            if (batchId && batchId !== '') {
                itemData.batch_id = parseInt(batchId);
            }
            
            billData.items.push(itemData);
        });
        
        // Send data to server
        fetch('/save-bill', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(billData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                alert('Bill saved successfully!');
                console.log('Bill saved:', data);
                
                // Optionally reset the bill
                // resetBill();
            } else {
                alert('Error saving bill: ' + data.message);
                console.error('Error:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the bill');
        });
    }
});
function resetBill() {
    // Clear the table
    const tbody = document.querySelector('#addedTable tbody');
    if (tbody) {
        tbody.innerHTML = '';
    }
    
    // Hide the table
    document.getElementById('addedTable').style.display = 'none';
    document.getElementById('tableFooter').style.display = 'none';
    
    // Reset counters and data
    itemCount = 0;
    additionalChargesAmount = 0;
    billDiscountData = {
        type: null,
        value: 0,
        timing: 'before',
        amount: 0
    };
    
    // Clear inputs
    document.getElementById('receivedAmount').value = '0';
    document.getElementById('barcodeInput').value = '';
    
    // Reset totals
    updateTotals();
    
    // Focus back to barcode input
    setTimeout(() => {
        document.getElementById('barcodeInput').focus();
    }, 100);
    
    showFeedback('Bill reset successfully', 'success');
    setTimeout(clearFeedback, 2000);
}

// Initialize focus on page load
window.addEventListener('load', function() {
    setTimeout(() => {
        if (shouldAutoFocusBarcode()) {
            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput) {
                barcodeInput.focus();
            }
        }
    }, 500);
});

console.log('Enhanced POS system with fixed cursor focus and additional charges initialized');
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

const enhancedPrintStyles = `
<style media="print">
    .print-bill {
        font-family: 'Courier New', monospace;
        font-size: 14px;
        line-height: 1.3;
        width: 70mm;
        margin: 10px auto;
        background: white;
        color: black;
        margin-left: 6px;
    }
    
    .bill-header {
        text-align: center;
        border-bottom: 1px dashed #000;
        padding-bottom: 5px;
        margin-bottom: 8px;
    }
    
    .store-name {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 2px;
    }
    
    .bill-info {
        font-size: 9px;
        margin-bottom: 8px;
    }
    
    .bill-info-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1px;
    }
    
    .items-section {
        margin-bottom: 8px;
    }
    
    .item-block {
        margin-bottom: 6px;
        border-bottom: 1px dotted #ccc;
        padding-bottom: 3px;
    }
    
    .item-header {
        font-weight: bold;
        font-size: 12px;
        margin-bottom: 1px;
    }
    
    .item-calculation {
        font-size: 11px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1px;
    }
    
    .item-details {
        font-size: 10px;
        color: #666;
        margin-bottom: 1px;
    }
    
    .item-savings {
        font-size: 10px;
        color: #d63384;
        font-weight: bold;
    }
    
    .totals-section {
        border-top: 1px dashed #000;
        padding-top: 5px;
        margin-top: 8px;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
        font-size: 10px;
    }
    
    .total-label {
        flex: 1;
    }
    
    .total-value {
        text-align: right;
        min-width: 50px;
    }
    
    .grand-total {
        font-weight: bold;
        font-size: 14px;
        border-top: 1px solid #000;
        padding-top: 3px;
        margin-top: 5px;
    }
    
    .savings-section {
        background-color: #f8f9fa;
        border: 1px dashed #d63384;
        padding: 3px;
        margin: 8px 0;
        text-align: center;
    }
    
    .total-savings {
        font-weight: bold;
        font-size: 12px;
        color: #d63384;
    }
    
    .payment-info {
        margin-top: 8px;
        border-top: 1px dashed #000;
        padding-top: 5px;
    }
    
    .footer-message {
        text-align: center;
        margin-top: 10px;
        font-size: 9px;
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

// Enhanced Function to generate supermarket-style bill HTML
function generateEnhancedBillHTML(billData) {
    const currentDate = new Date();
    const formattedDate = currentDate.toLocaleDateString('en-IN');
    const formattedTime = currentDate.toLocaleTimeString('en-IN', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
    
    let itemsHTML = '';
    let totalSavings = 0;
    
    // Process each item with detailed calculations
    billData.items.forEach((item, index) => {
        // Get MRP from the item data (you may need to add this to your item data)
        const mrp = item.mrp || item.price;// Default MRP as 10% above selling price if not available
        const sellingPrice = item.price;
        const quantity = item.qty;
        const discountPercent = item.discount || 0;
        const taxPercent = item.tax || 0;
        
        // Calculations
        const baseAmount = quantity * sellingPrice;
        const discountAmount = baseAmount * (discountPercent / 100);
        const taxableAmount = baseAmount - discountAmount;
        const taxAmount = taxableAmount * (taxPercent / 100);
        const finalAmount = taxableAmount + taxAmount;
        
        // MRP savings calculation
        const mrpTotal = quantity * mrp;
        const itemSavings = mrpTotal - baseAmount;
        totalSavings += itemSavings;
        
        itemsHTML += `
            <div class="item-block">
                <div class="item-header">${item.name}</div>
                <div class="item-calculation">
                    <span>${quantity} × ₹${sellingPrice.toFixed(2)} (MRP. ₹${mrpTotal.toFixed(2)})</span>
                    <span>₹${baseAmount.toFixed(2)}</span>
                </div>

            </div>
        `;
    });

    // Check if customer info is available and not null
    const hasCustomer = billData.customer_name && 
                       billData.customer_contact && 
                       billData.customer_name !== 'null' && 
                       billData.customer_contact !== 'null' &&
                       billData.customer_name.trim() !== '' && 
                       billData.customer_contact.trim() !== '';

    // Generate customer info section only if customer exists
    let customerInfoHTML = '';
    if (hasCustomer) {
        customerInfoHTML = `
            <div class="bill-info">
                <div class="bill-info-line">
                    <span><strong>Customer:</strong></span>
                    <span>${billData.customer_name}</span>
                </div>
                <div class="bill-info-line">
                    <span><strong>Contact:</strong></span>
                    <span>${billData.customer_contact}</span>
                </div>
                ${billData.loyalty_points_used > 0 ? `
                <div class="bill-info-line">
                    <span><strong>Loyalty Points Used:</strong></span>
                    <span>${billData.loyalty_points_used}</span>
                </div>
                ` : ''}
            </div>
        `;
    }

    // Calculate total bill discount and other savings
    const totalBillDiscount = (billData.bill_discount || 0) + (billData.item_discounts || 0);
    const totalDiscountSavings = totalBillDiscount + (billData.total_gift_card_discount || 0);
    const grandTotalSavings = totalSavings + totalDiscountSavings;

    return `
        <div class="print-bill">
            <div class="bill-header">
                <div class="store-name">SUKOYO STORE</div>
                <div style="font-size: 10px; margin-bottom: 5px;">Retail POS System</div>
                <div style="font-size: 9px;">
                    <div class="bill-info-line">
                        <span style="font-weight:500">Date: ${formattedDate}</span>
                        <span style="font-weight:500">Time: ${formattedTime}</span>
                    </div>
                     <div style="margin-left: -120px; font-weight:500">
                        Bill No: ${billData.billNumber || 'POS' + Date.now()}<br>
                    </div>
                    <div style="margin-left: -90px;">
                    GST NO:32AAJFF3746P2Z0
                    </div>
                </div>
            </div>
            
            ${customerInfoHTML}
            
            <div class="items-section">
                <div style="text-align: center; font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #000; padding-bottom: 2px;">
                    ITEM DETAILS
                </div>
                ${itemsHTML}
            </div>
            
        
            <div class="totals-section">
                <div style="text-align: center; font-weight: bold; margin-bottom: 3px;">
                    BILL SUMMARY
                </div>
                
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Sub Total:</span>
                    <span class="total-value" style="font-weight: bold;">₹${billData.sub_total.toFixed(2)}</span>
                </div>
                
                ${billData.item_discounts > 0 ? `
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Item Discounts:</span>
                    <span class="total-value" style="font-weight: bold;">-₹${billData.item_discounts.toFixed(2)}</span>
                </div>
                ` : ''}
                
                ${billData.bill_discount > 0 ? `
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Bill Discount:</span>
                    <span class="total-value" style="font-weight: bold;">-₹${billData.bill_discount.toFixed(2)}</span>
                </div>
                ` : ''}
                
                ${billData.total_gift_card_discount > 0 ? `
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Gift Card/Voucher:</span>
                    <span class="total-value" style="font-weight: bold;">-₹${billData.total_gift_card_discount.toFixed(2)}</span>
                </div>
                ` : ''}
                
                ${billData.total_tax > 0 ? `
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Total Tax:</span>
                    <span class="total-value" style="font-weight: bold;">₹${billData.total_tax.toFixed(2)}</span>
                </div>
                ` : ''}
                
                ${billData.additional_charges > 0 ? `
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Additional Charges:</span>
                    <span class="total-value" style="font-weight: bold;">₹${billData.additional_charges.toFixed(2)}</span>
                </div>
                ` : ''}
                
                <div class="total-row grand-total">
                    <span class="total-label">GRAND TOTAL:</span>
                    <span class="total-value">₹${billData.grand_total.toFixed(2)}</span>
                </div>
            </div>
            
            <div class="payment-info">
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Payment Mode:</span>
                    <span class="total-value" style="font-weight: bold;">${billData.mode_of_payment}</span>
                </div>
                
                <div class="total-row">
                    <span class="total-label" style="font-weight: bold;">Amount Received:</span>
                    <span class="total-value" style="font-weight: bold;">₹${billData.received_amount.toFixed(2)}</span>
                </div>
                
                ${billData.received_amount - billData.grand_total !== 0 ? `
                <div class="total-row">
                    <span class="total-label">Change:</span>
                    <span class="total-value">₹${(billData.received_amount - billData.grand_total).toFixed(2)}</span>
                </div>
                ` : ''}
            </div>
                ${grandTotalSavings > 0 ? `
            <div class="savings-section">
                <div class="total-savings">★ YOU SAVED: ₹${grandTotalSavings.toFixed(2)} ★</div>
                ${totalSavings > 0 ? `<div style="font-size: 8px; font-weight: bold;">MRP Savings: ₹${totalSavings.toFixed(2)}</div>` : ''}
                ${totalDiscountSavings > 0 ? `<div style="font-size: 8px; font-weight: bold">Discount Savings: ₹${totalDiscountSavings.toFixed(2)}</div>` : ''}
            </div>
            ` : ''}
            
            <div class="footer-message">
                <div style="margin-bottom: 5px; font-weight: bold;">*** THANK YOU FOR SHOPPING ***</div>
                <div style="font-size: 8px; margin-bottom: 3px; font-weight: bold;">
                    Items: ${billData.items.length} | Qty: ${billData.items.reduce((sum, item) => sum + item.qty, 0)}
                </div>
                <div style="font-size: 8px; font-weight: bold;">
                    This is a computer generated bill<br>
                    Visit Again!
                </div>
            </div>
        </div>
    `;
}


function printEnhancedBill(billData) {
    const printContent = generateEnhancedBillHTML(billData);

    // Open a new tab for printing
    const printWindow = window.open('', '_blank');

    // Add the full HTML with print styles
    printWindow.document.open();
    printWindow.document.write(`
        <html>
        <head>
            <title>Print Bill</title>
            <style>

                ${enhancedPrintStyles.replace(/<style.*?>|<\/style>/g, '')}
            </style>
        </head>
        <body>
            ${printContent}
            <script>
                // Auto print when the new tab loads
                window.onload = function() {
                    window.print();
                    // Close tab after short delay (optional)
                    setTimeout(() => window.close(), 2000);
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}



// FIXED: Enhanced Save & Print functionality with split payment support
$(document).ready(function () {
    
    // Initialize payment mode functionality
    initializePaymentMode();
    
    // Remove all existing event handlers first
    $('#savePrintBtn').off('click.saveBill');
    
    // Use event namespace to prevent multiple handlers
    $('#savePrintBtn').on('click.saveBill', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Save & Print button clicked');
        
        // Prevent double clicks
        const $btn = $(this);
        if ($btn.prop('disabled')) {
            console.log('Button already disabled, preventing duplicate submission');
            return false;
        }
        
        // Disable button immediately
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Processing...');
        
        // Validate that there are items in the bill
        const tableRows = $('#addedTable tbody tr').length;
        if (tableRows === 0) {
            alert("Please add at least one item to the bill.");
            $btn.prop('disabled', false).text(originalText);
            return false;
        }
        
        // Get mode of payment
        const mopValue = $('#mop').val() || 'Cash';
        
        // Get received amounts based on payment mode
        let receivedAmount = 0;
        let cashAmount = 0;
        let onlineAmount = 0;
        
        if (mopValue === 'Both') {
            cashAmount = parseFloat($('#cashAmount').val()) || 0;
            onlineAmount = parseFloat($('#onlineAmount').val()) || 0;
            receivedAmount = cashAmount + onlineAmount;
            
            // Validate split payments
            if (cashAmount <= 0 && onlineAmount <= 0) {
                alert('Please enter cash or online amount for split payment.');
                $btn.prop('disabled', false).text(originalText);
                return false;
            }
        } else {
            receivedAmount = parseFloat($('#receivedAmount').val()) || 0;
            
            // Set cash or online amount based on mode
            if (mopValue === 'Cash') {
                cashAmount = receivedAmount;
                onlineAmount = 0;
            } else if (mopValue === 'Online') {
                cashAmount = 0;
                onlineAmount = receivedAmount;
            }
        }
        
        // Get customer information
        const selectedCustomerText = $('#selectedCustomer').text().trim();
        let customerName = '';
        let customerContact = '';
        
        if (selectedCustomerText && selectedCustomerText !== 'No customer selected' && selectedCustomerText !== 'Sukoyo Store') {
            if (selectedCustomerText.includes(' - ')) {
                const parts = selectedCustomerText.split(' - ');
                customerName = parts[0].trim();
                customerContact = parts[1].trim();
            }
        }
        
        if (!customerName) customerName = $('#customer_name').val() || '';
        if (!customerContact) customerContact = $('#customer_contact').val() || '';
        
        // Collect items data
        let items = [];
        let hasValidItems = false;
        
        $('#addedTable tbody tr').each(function() {
            const row = $(this);
            const cells = row.find('td');
            
            if (cells.length >= 8) {
                const itemId = row.attr('data-item-id');
                const batchId = row.attr('data-batch-id');
                const itemName = cells.eq(1).text().trim();
                const unit = cells.eq(2).text().trim();
                const qty = parseFloat(cells.eq(3).text().trim()) || 0;
                const price = parseFloat(cells.eq(4).text().replace('₹', '').trim()) || 0;
                const discount = parseFloat(cells.eq(5).text().trim()) || 0;
                const tax = parseFloat(cells.eq(6).text().trim()) || 0;
                const amount = parseFloat(cells.eq(7).text().replace('₹', '').trim()) || 0;
                
                if (itemId && qty > 0 && price > 0) {
                    const actualItem = itemsData.find(i => i.id.toString() === itemId.toString());
                    const itemData = {
                        item_id: parseInt(itemId),
                        name: itemName,
                        unit: unit,
                        qty: qty,
                        price: price,
                        discount: discount,
                        tax: tax,
                        amount: amount,
                        mrp: actualItem ? parseFloat(actualItem.mrp || actualItem.sales_price) : price
                    };
                    
                    if (batchId && batchId.trim() !== '') {
                        itemData.batch_id = parseInt(batchId);
                    }
                    
                    items.push(itemData);
                    hasValidItems = true;
                }
            }
        });
        
        if (!hasValidItems) {
            alert("No valid items found in the bill.");
            $btn.prop('disabled', false).text(originalText);
            return false;
        }
        
        // Get financial totals
        const subTotal = parseFloat($('#subTotal').text().replace(/₹|,|\s/g, '')) || 0;
        const itemDiscounts = parseFloat($('#itemDiscounts').text().replace(/₹|,|\s/g, '')) || 0;
        const billDiscount = parseFloat($('#billDiscount').text().replace(/₹|,|\s/g, '')) || 0;
        const totalTax = parseFloat($('#totalTax').text().replace(/₹|,|\s/g, '')) || 0;
        const additionalCharges = parseFloat($('#additionalCharges').text().replace(/₹|,|\s/g, '')) || 0;
        const grandTotal = parseFloat($('#grandTotal').text().replace(/₹|,|\s/g, '')) || 0;
        
        if (grandTotal <= 0) {
            alert("Grand total must be greater than 0.");
            $btn.prop('disabled', false).text(originalText);
            return false;
        }
        
        // Validate received amount
        if (receivedAmount < grandTotal) {
            if (!confirm(`Received amount (₹${receivedAmount.toFixed(2)}) is less than Grand Total (₹${grandTotal.toFixed(2)}). Continue anyway?`)) {
                $btn.prop('disabled', false).text(originalText);
                return false;
            }
        }
        
        // Get loyalty points if used
        let loyaltyPointsUsed = 0;
        if (customerName && customerContact) {
            const usedPointsElement = $('#usedPointsDisplay');
            if (usedPointsElement.length && usedPointsElement.is(':visible')) {
                const usedPointsText = usedPointsElement.text().trim();
                loyaltyPointsUsed = parseInt(usedPointsText.replace(/[^\d]/g, '')) || 0;
            }
        }
        
        // Get gift card data
        const giftCardData = typeof getGiftCardDataForSaving === 'function' ? getGiftCardDataForSaving() : {
            applied_gift_cards: [],
            total_gift_card_discount: 0
        };
        
        // Generate unique request ID
        const requestId = 'bill_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Prepare payload
        const payload = {
            request_id: requestId,
            customer_name: customerName || null,
            customer_contact: customerContact || null,
            items: items,
            sub_total: subTotal,
            item_discounts: itemDiscounts,
            bill_discount: billDiscount,
            total_discount: itemDiscounts + billDiscount,
            total_tax: totalTax,
            additional_charges: additionalCharges,
            grand_total: grandTotal,
            received_amount: receivedAmount,
            mode_of_payment: mopValue,
            
            // Split payment fields
            cash_amount: cashAmount,
            online_amount: onlineAmount,
            
            loyalty_points_used: loyaltyPointsUsed,
            applied_gift_cards: giftCardData.applied_gift_cards,
            total_gift_card_discount: giftCardData.total_gift_card_discount,
            
            bill_date: new Date().toISOString().split('T')[0],
            bill_time: new Date().toLocaleTimeString('en-GB', { hour12: false })
        };
        
        console.log('Final payload:', payload);
        
        $btn.text('Saving & Printing...');
        
        const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
        
        if (!csrfToken) {
            alert('Security token not found. Please refresh the page.');
            $btn.prop('disabled', false).text(originalText);
            return false;
        }
        
        // Send request
        $.ajax({
            url: '/save-bill',
            method: 'POST',
            timeout: 30000,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            data: JSON.stringify(payload),
            success: function(response) {
                console.log('Success response:', response);
                
                if (response.status || response.success) {
                    if (response.data && response.data.invoice_id) {
                        payload.billNumber = 'BILL-' + response.data.invoice_id;
                    } else {
                        payload.billNumber = 'BILL-' + Date.now();
                    }
                    
                    if (typeof printEnhancedBill === 'function') {
                        printEnhancedBill(payload);
                    }
                    
                    let successMessage = "Bill saved successfully and sent to printer!";
                    if (mopValue === 'Both') {
                        successMessage += `\nCash: ₹${cashAmount.toFixed(2)} | Online: ₹${onlineAmount.toFixed(2)}`;
                    }
                    alert(successMessage);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    alert("Failed to save bill: " + (response.message || "Unknown error"));
                    $btn.prop('disabled', false).text(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr, status, error});
                
                let errorMessage = "Something went wrong while saving the bill.";
                
                if (status === 'timeout') {
                    errorMessage = "Request timed out. Please check and refresh the page.";
                } else if (xhr.status === 419) {
                    errorMessage = "Session expired. Please refresh the page.";
                } else if (xhr.status === 422) {
                    errorMessage = "Validation error. Please check the data format.";
                }
                
                alert(errorMessage);
                $btn.prop('disabled', false).text(originalText);
            }
        });
        
        return false;
    });
});



$(document).ready(function() {
    $('#posForm').off('submit').on('submit', function(e) {
        e.preventDefault(); // Prevent any form submission
        return false;
    });
});


// Clean customer data function for enhanced print
function cleanCustomerDataForEnhancedPrint(payload) {
    const cleanPayload = { ...payload };
    
    if (!cleanPayload.customer_name || 
        cleanPayload.customer_name === 'null' || 
        cleanPayload.customer_name.trim() === '') {
        cleanPayload.customer_name = null;
    }
    
    if (!cleanPayload.customer_contact || 
        cleanPayload.customer_contact === 'null' || 
        cleanPayload.customer_contact.trim() === '') {
        cleanPayload.customer_contact = null;
    }
    
    return cleanPayload;
}

console.log('Enhanced POS Print Bill Script with supermarket-style details loaded successfully!');
</script>
<script>
    // Enhanced Multiple Screen POS System with Gift Card Modal Fix
// This script properly handles gift card modal refreshing between screens

(function() {
    'use strict';
    
    // Global variables for multiple screens
    let screens = {};
    let currentScreenId = 'screen-1';
    let screenCounter = 1;
    
    // Store original global variables per screen
    let screenSpecificData = {};
    
    // Initialize multiple screens functionality
    function initMultipleScreens() {
        // Create the screen tabs container
        createScreenTabsContainer();
        
        // Initialize first screen with current data
        initializeFirstScreen();
        
        // Add event listeners
        setupEventListeners();
        
        // Setup keyboard shortcuts
        setupKeyboardShortcuts();
        
        console.log('Enhanced Multiple Screens POS initialized');
    }
    
    // Create the tabs container at the top
    function createScreenTabsContainer() {
        const billContainer = document.querySelector('.container-fluid');
        if (!billContainer) return;
        
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
            position: sticky;
            top: 0;
            z-index: 100;
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
        
        // Insert at the beginning of the container
        billContainer.insertBefore(tabsContainer, billContainer.firstChild);
    }
    
    // Initialize first screen with current data
    function initializeFirstScreen() {
        screens['screen-1'] = captureCurrentScreenData();
        screens['screen-1'].id = 'screen-1';
        screens['screen-1'].name = 'Bill #1';
        screens['screen-1'].isActive = true;
        
        // Initialize screen-specific data
        screenSpecificData['screen-1'] = {
            billDiscountData: typeof billDiscountData !== 'undefined' ? {...billDiscountData} : {
                type: null,
                value: 0,
                timing: 'before',
                amount: 0
            },
            additionalChargesAmount: typeof additionalChargesAmount !== 'undefined' ? additionalChargesAmount : 0,
            usedLoyaltyPoints: 0,
            remainingLoyaltyPoints: 0,
            appliedGiftCards: [],
            giftCardDiscount: 0,
            itemCount: typeof itemCount !== 'undefined' ? itemCount : 0,
            // Gift card modal state
            giftCardModalData: {
                availableCards: [],
                selectedCards: [],
                totalApplied: 0,
                remainingBalance: 0
            }
        };
    }
    
    // Capture current screen data including all modals and forms
    function captureCurrentScreenData() {
        const items = [];
        
        // Capture items from table
        document.querySelectorAll('#addedTable tbody tr').forEach(row => {
            const cells = row.cells;
            if (cells.length >= 8) {
                items.push({
                    itemId: row.getAttribute('data-item-id') || '',
                    itemCode: row.getAttribute('data-item-code') || '',
                    name: cells[1].textContent.trim(),
                    unit: cells[2].textContent.trim(),
                    qty: cells[3].textContent.trim(),
                    price: cells[4].textContent.trim(),
                    discount: cells[5].textContent.trim(),
                    tax: cells[6].textContent.trim(),
                    amount: cells[7].textContent.trim(),
                    stock: row.getAttribute('data-stock') || '0'
                });
            }
        });
        
        // Capture customer data including loyalty points
        const selectedCustomerText = document.getElementById('selectedCustomer')?.textContent || 'No customer selected';
        let customerName = '';
        let customerContact = '';
        
        if (selectedCustomerText && selectedCustomerText !== 'No customer selected') {
            if (selectedCustomerText.includes(' - ')) {
                const parts = selectedCustomerText.split(' - ');
                customerName = parts[0].trim();
                customerContact = parts[1].trim();
            } else {
                customerName = selectedCustomerText;
            }
        }
        
        return {
            items: items,
            customer: {
                name: selectedCustomerText,
                actualName: customerName,
                contact: customerContact,
                loyaltyPoints: document.getElementById('loyaltyPointsDisplay')?.textContent || '0 Points',
                usedPointsVisible: document.getElementById('usedPointsContainer')?.style.display !== 'none',
                usedPointsValue: document.getElementById('usedPointsDisplay')?.textContent || '0',
                remainingPointsVisible: document.getElementById('remainingPointsContainer')?.style.display !== 'none',
                remainingPointsValue: document.getElementById('remainingPoints')?.textContent || '0'
            },
            totals: {
                subTotal: document.getElementById('subTotal')?.textContent || '₹ 0.00',
                itemDiscounts: document.getElementById('itemDiscounts')?.textContent || '₹ 0.00',
                billDiscount: document.getElementById('billDiscount')?.textContent || '₹ 0.00',
                totalTax: document.getElementById('totalTax')?.textContent || '₹ 0.00',
                additionalCharges: document.getElementById('additionalCharges')?.textContent || '₹ 0.00',
                grandTotal: document.getElementById('grandTotal')?.textContent || '₹ 0.00'
            },
            payment: {
                receivedAmount: document.getElementById('receivedAmount')?.value || '0',
                modeOfPayment: document.getElementById('mop')?.value || 'Cash',
                changeToReturn: document.getElementById('changeToReturn')?.textContent || '₹ 0.00'
            },
            additionalChargesInput: document.getElementById('additionalChargesInput')?.value || '0',
            billDiscountInfo: document.getElementById('billDiscountInfo')?.textContent || '',
            tableVisible: document.getElementById('addedTable')?.style.display !== 'none',
            // Capture gift card display data
            giftCardDisplay: {
                visible: document.getElementById('giftCardDiscount')?.textContent !== '₹ 0.00',
                amount: document.getElementById('giftCardDiscount')?.textContent || '₹ 0.00'
            }
        };
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Add new screen button
        document.getElementById('addNewScreen').addEventListener('click', addNewScreen);
        
        // Tab click events
        document.getElementById('screenTabs').addEventListener('click', handleTabClick);
        
        // Setup auto-save functionality
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
        
        // Initialize fresh screen-specific data
        screenSpecificData[newScreenId] = {
            billDiscountData: {
                type: null,
                value: 0,
                timing: 'before',
                amount: 0
            },
            additionalChargesAmount: 0,
            usedLoyaltyPoints: 0,
            remainingLoyaltyPoints: 0,
            appliedGiftCards: [],
            giftCardDiscount: 0,
            itemCount: 0,
            // Fresh gift card modal state
            giftCardModalData: {
                availableCards: [],
                selectedCards: [],
                totalApplied: 0,
                remainingBalance: 0
            }
        };
        
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
                actualName: '',
                contact: '',
                loyaltyPoints: '0 Points',
                usedPointsVisible: false,
                usedPointsValue: '0',
                remainingPointsVisible: false,
                remainingPointsValue: '0'
            },
            totals: {
                subTotal: '₹ 0.00',
                itemDiscounts: '₹ 0.00',
                billDiscount: '₹ 0.00',
                totalTax: '₹ 0.00',
                additionalCharges: '₹ 0.00',
                grandTotal: '₹ 0.00'
            },
            payment: {
                receivedAmount: '0',
                modeOfPayment: 'Cash',
                changeToReturn: '₹ 0.00'
            },
            additionalChargesInput: '0',
            billDiscountInfo: '',
            tableVisible: false,
            giftCardDisplay: {
                visible: false,
                amount: '₹ 0.00'
            }
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
        
        // Save current screen data before switching (including gift card state)
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
    
    // Save current screen data including gift card modal state
    function saveCurrentScreenData() {
        if (screens[currentScreenId]) {
            screens[currentScreenId] = {
                ...screens[currentScreenId],
                ...captureCurrentScreenData()
            };
            
            // Save screen-specific global variables
            if (screenSpecificData[currentScreenId]) {
                screenSpecificData[currentScreenId].billDiscountData = typeof billDiscountData !== 'undefined' ? {...billDiscountData} : {
                    type: null,
                    value: 0,
                    timing: 'before',
                    amount: 0
                };
                screenSpecificData[currentScreenId].additionalChargesAmount = typeof additionalChargesAmount !== 'undefined' ? additionalChargesAmount : 0;
                screenSpecificData[currentScreenId].itemCount = typeof itemCount !== 'undefined' ? itemCount : 0;
                
                // Save gift card modal state if available
                saveGiftCardModalState();
            }
        }
    }
    
    // Save gift card modal state
    function saveGiftCardModalState() {
        if (!screenSpecificData[currentScreenId]) return;
        
        // Save applied gift cards from the display or global variables
        const giftCardDisplay = document.getElementById('giftCardDiscount');
        const appliedAmount = giftCardDisplay ? parseFloat(giftCardDisplay.textContent.replace(/₹|,|\s/g, '')) || 0 : 0;
        
        screenSpecificData[currentScreenId].giftCardDiscount = appliedAmount;
        
        // Try to capture gift card data from modal if it exists
        const giftCardModal = document.getElementById('giftcards');
        if (giftCardModal) {
            const selectedCards = [];
            const cardSelections = giftCardModal.querySelectorAll('input[type="checkbox"]:checked');
            
            cardSelections.forEach(checkbox => {
                const cardRow = checkbox.closest('tr') || checkbox.closest('.gift-card-item');
                if (cardRow) {
                    const cardData = {
                        id: checkbox.value,
                        code: cardRow.querySelector('.card-code')?.textContent || '',
                        balance: parseFloat(cardRow.querySelector('.card-balance')?.textContent?.replace(/₹|,|\s/g, '')) || 0,
                        appliedAmount: parseFloat(cardRow.querySelector('.applied-amount')?.value || cardRow.querySelector('.applied-amount')?.textContent || '0')
                    };
                    selectedCards.push(cardData);
                }
            });
            
            screenSpecificData[currentScreenId].appliedGiftCards = selectedCards;
            
            // Save modal form data
            screenSpecificData[currentScreenId].giftCardModalData = {
                availableCards: screenSpecificData[currentScreenId].giftCardModalData?.availableCards || [],
                selectedCards: selectedCards,
                totalApplied: appliedAmount,
                remainingBalance: screenSpecificData[currentScreenId].giftCardModalData?.remainingBalance || 0
            };
        }
    }
    
    // Load screen data and restore all UI states
    function loadScreenData(screenId) {
        const screenData = screens[screenId];
        if (!screenData) return;
        
        // Restore global variables for this screen
        if (screenSpecificData[screenId]) {
            if (typeof billDiscountData !== 'undefined') {
                billDiscountData = {...screenSpecificData[screenId].billDiscountData};
            }
            if (typeof additionalChargesAmount !== 'undefined') {
                additionalChargesAmount = screenSpecificData[screenId].additionalChargesAmount;
            }
            if (typeof itemCount !== 'undefined') {
                itemCount = screenSpecificData[screenId].itemCount;
            }
        }
        
        // Clear current items
        const tableBody = document.querySelector('#addedTable tbody');
        if (tableBody) {
            tableBody.innerHTML = '';
        }
        
        // Show/hide table based on items
        const table = document.getElementById('addedTable');
        const tableFooter = document.getElementById('tableFooter');
        
        if (screenData.items.length === 0) {
            if (table) table.style.display = 'none';
            if (tableFooter) tableFooter.style.display = 'none';
        } else {
            if (table) table.style.display = 'table';
            if (tableFooter) tableFooter.style.display = 'table-footer-group';
            
            // Load items with all attributes
            screenData.items.forEach((item, index) => {
                const row = document.createElement('tr');
                row.setAttribute('data-item-id', item.itemId);
                row.setAttribute('data-item-code', item.itemCode);
                row.setAttribute('data-stock', item.stock);
                
                row.innerHTML = `
                    <td class="item-sno">${index + 1}</td>
                    <td class="item-name" title="${item.name}">${item.name}</td>
                    <td class="item-unit">${item.unit}</td>
                    <td class="item-qty" contenteditable="true" data-original-qty="${item.qty.replace(/[^\d.]/g, '')}">${item.qty}</td>
                    <td class="item-price">${item.price}</td>
                    <td class="item-discount">${item.discount}</td>
                    <td class="item-tax">${item.tax}</td>
                    <td class="item-amount">${item.amount}</td>
                    <td class="item-actions">
                        <button type="button" class="btn btn-danger btn-sm remove-item" title="Remove Item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                
                if (tableBody) {
                    tableBody.appendChild(row);
                    // Re-enable quantity editing for the row
                    makeQuantityEditable(row);
                }
            });
        }
        
        // Load customer data
        const selectedCustomer = document.getElementById('selectedCustomer');
        if (selectedCustomer) {
            selectedCustomer.textContent = screenData.customer.name;
        }
        
        const customerNameInput = document.getElementById('customer_name');
        if (customerNameInput) {
            customerNameInput.value = screenData.customer.actualName;
        }
        
        const customerContact = document.getElementById('customer_contact');
        if (customerContact) {
            customerContact.value = screenData.customer.contact;
        }
        
        const loyaltyPoints = document.getElementById('loyaltyPointsDisplay');
        if (loyaltyPoints) {
            loyaltyPoints.textContent = screenData.customer.loyaltyPoints;
        }
        
        // Restore loyalty points containers visibility
        const usedPointsContainer = document.getElementById('usedPointsContainer');
        if (usedPointsContainer) {
            usedPointsContainer.style.display = screenData.customer.usedPointsVisible ? 'block' : 'none';
        }
        
        const usedPointsDisplay = document.getElementById('usedPointsDisplay');
        if (usedPointsDisplay) {
            usedPointsDisplay.textContent = screenData.customer.usedPointsValue;
        }
        
        const remainingPointsContainer = document.getElementById('remainingPointsContainer');
        if (remainingPointsContainer) {
            remainingPointsContainer.style.display = screenData.customer.remainingPointsVisible ? 'block' : 'none';
        }
        
        const remainingPoints = document.getElementById('remainingPoints');
        if (remainingPoints) {
            remainingPoints.textContent = screenData.customer.remainingPointsValue;
        }
        
        // Load totals
        const subTotal = document.getElementById('subTotal');
        if (subTotal) subTotal.textContent = screenData.totals.subTotal;
        
        const itemDiscounts = document.getElementById('itemDiscounts');
        if (itemDiscounts) itemDiscounts.textContent = screenData.totals.itemDiscounts;
        
        const billDiscount = document.getElementById('billDiscount');
        if (billDiscount) billDiscount.textContent = screenData.totals.billDiscount;
        
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
        
        // Restore bill discount info
        const billDiscountInfo = document.getElementById('billDiscountInfo');
        if (billDiscountInfo) {
            billDiscountInfo.textContent = screenData.billDiscountInfo;
        }
        
        // Restore gift card display
        const giftCardDiscount = document.getElementById('giftCardDiscount');
        if (giftCardDiscount) {
            giftCardDiscount.textContent = screenData.giftCardDisplay.amount;
        }
        
        // Reset form selections
        const barcodeInput = document.getElementById('barcodeInput');
        if (barcodeInput) barcodeInput.value = '';
        
        // Clear all modal form states and restore gift card modal
        resetAllModalForms();
        restoreGiftCardModal();
        
        // Update bill discount display
        if (typeof updateBillDiscountDisplay === 'function') {
            updateBillDiscountDisplay();
        }
        
        // Restore discount modal state if bill discount exists
        if (screenSpecificData[screenId] && screenSpecificData[screenId].billDiscountData) {
            const discountData = screenSpecificData[screenId].billDiscountData;
            if (discountData.type && discountData.value > 0) {
                // Update discount form in modal
                const percentageInput = document.getElementById('percentage');
                const amountInput = document.getElementById('discamount');
                const typeRadios = document.querySelectorAll('input[name="discountType"]');
                const timingRadios = document.querySelectorAll('input[name="discountTiming"]');
                
                if (discountData.type === 'percentage' && percentageInput) {
                    percentageInput.value = discountData.value;
                    typeRadios.forEach(radio => {
                        if (radio.value === 'percentage') radio.checked = true;
                    });
                } else if (discountData.type === 'amount' && amountInput) {
                    amountInput.value = discountData.value;
                    typeRadios.forEach(radio => {
                        if (radio.value === 'amount') radio.checked = true;
                    });
                }
                
                timingRadios.forEach(radio => {
                    if (radio.value === discountData.timing) radio.checked = true;
                });
                
                // Update current discount display in modal
                if (typeof updateCurrentDiscountDisplay === 'function') {
                    updateCurrentDiscountDisplay();
                }
            }
        }
        
        // Focus on barcode input
        setTimeout(() => {
            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput && typeof shouldAutoFocusBarcode === 'function' && shouldAutoFocusBarcode()) {
                barcodeInput.focus();
            }
        }, 100);
    }
    
    // Reset all modal forms to default state
    function resetAllModalForms() {
        // Reset discount modal
        const discountForm = document.getElementById('discountForm');
        if (discountForm) {
            discountForm.reset();
            const percentageRadio = document.querySelector('input[name="discountType"][value="percentage"]');
            const beforeRadio = document.querySelector('input[name="discountTiming"][value="before"]');
            if (percentageRadio) percentageRadio.checked = true;
            if (beforeRadio) beforeRadio.checked = true;
            
            const percentageInput = document.getElementById('percentage');
            const amountInput = document.getElementById('discamount');
            if (percentageInput) percentageInput.disabled = false;
            if (amountInput) amountInput.disabled = true;
            
            const currentDiscountDisplay = document.getElementById('currentDiscountDisplay');
            if (currentDiscountDisplay) currentDiscountDisplay.style.display = 'none';
        }
        
        // Reset additional charges modal
        const additionalForm = document.querySelector('#additional form');
        if (additionalForm) {
            additionalForm.reset();
        }
        
        // Reset loyalty points modal
        const loyaltyForm = document.getElementById('loyaltyForm');
        if (loyaltyForm) {
            loyaltyForm.reset();
        }
        
        // Reset gift cards modal - this is the key fix
        resetGiftCardModal();
    }
    
    // Reset gift card modal completely
    function resetGiftCardModal() {
        const giftCardModal = document.getElementById('giftcards');
        if (!giftCardModal) return;
        
        // Reset all checkboxes
        const checkboxes = giftCardModal.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset all input fields
        const inputs = giftCardModal.querySelectorAll('input[type="number"], input[type="text"]');
        inputs.forEach(input => {
            if (input.type === 'number') {
                input.value = '0';
            } else {
                input.value = '';
            }
        });
        
        // Clear any applied amounts or totals
        const appliedAmountInputs = giftCardModal.querySelectorAll('.applied-amount, input[name*="applied"]');
        appliedAmountInputs.forEach(input => {
            input.value = '0';
        });
        
        // Clear total applied display
        const totalAppliedDisplay = giftCardModal.querySelector('.total-applied, #totalGiftCardApplied');
        if (totalAppliedDisplay) {
            totalAppliedDisplay.textContent = '₹ 0.00';
        }
        
        // Clear remaining balance display
        const remainingBalanceDisplay = giftCardModal.querySelector('.remaining-balance, #remainingGiftCardBalance');
        if (remainingBalanceDisplay) {
            remainingBalanceDisplay.textContent = '₹ 0.00';
        }
        
        // Reset any form inside gift card modal
        const giftCardForm = giftCardModal.querySelector('form');
        if (giftCardForm) {
            giftCardForm.reset();
        }
        
        // Clear any dynamic content or lists
        const cardLists = giftCardModal.querySelectorAll('.gift-card-list, .selected-cards-list');
        cardLists.forEach(list => {
            if (list.classList.contains('dynamic-content')) {
                list.innerHTML = '';
            }
        });
        
        // Reset table rows if gift cards are displayed in a table
        const giftCardTable = giftCardModal.querySelector('table tbody');
        if (giftCardTable) {
            const rows = giftCardTable.querySelectorAll('tr');
            rows.forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"]');
                const appliedInput = row.querySelector('.applied-amount, input[name*="applied"]');
                
                if (checkbox) checkbox.checked = false;
                if (appliedInput) appliedInput.value = '0';
                
                // Remove any selected/active classes
                row.classList.remove('selected', 'active', 'applied');
            });
        }
        
        console.log('Gift card modal reset for screen:', currentScreenId);
    }
    
    // Restore gift card modal state for current screen
    function restoreGiftCardModal() {
        if (!screenSpecificData[currentScreenId]) return;
        
        const screenGiftCardData = screenSpecificData[currentScreenId];
        const giftCardModal = document.getElementById('giftcards');
        
        if (!giftCardModal || !screenGiftCardData.appliedGiftCards || screenGiftCardData.appliedGiftCards.length === 0) {
            return;
        }
        
        // Restore applied gift cards
        screenGiftCardData.appliedGiftCards.forEach(cardData => {
            const checkbox = giftCardModal.querySelector(`input[type="checkbox"][value="${cardData.id}"]`);
            if (checkbox) {
                checkbox.checked = true;
                
                // Find and set applied amount
                const cardRow = checkbox.closest('tr') || checkbox.closest('.gift-card-item');
                if (cardRow) {
                    const appliedAmountInput = cardRow.querySelector('.applied-amount, input[name*="applied"]');
                    if (appliedAmountInput) {
                        appliedAmountInput.value = cardData.appliedAmount || 0;
                    }
                    
                    // Add selected class if available
                    cardRow.classList.add('selected', 'applied');
                }
            }
        });
        
        // Restore total applied amount
        const totalAppliedDisplay = giftCardModal.querySelector('.total-applied, #totalGiftCardApplied');
        if (totalAppliedDisplay && screenGiftCardData.giftCardDiscount > 0) {
            totalAppliedDisplay.textContent = `₹ ${screenGiftCardData.giftCardDiscount.toFixed(2)}`;
        }
        
        // Restore remaining balance if available
        const remainingBalanceDisplay = giftCardModal.querySelector('.remaining-balance, #remainingGiftCardBalance');
        if (remainingBalanceDisplay && screenGiftCardData.giftCardModalData) {
            const remainingBalance = screenGiftCardData.giftCardModalData.remainingBalance || 0;
            if (remainingBalance > 0) {
                remainingBalanceDisplay.textContent = `₹ ${remainingBalance.toFixed(2)}`;
            }
        }
        
        console.log('Gift card modal restored for screen:', currentScreenId, screenGiftCardData.appliedGiftCards);
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
        
        // Remove from screens and screen-specific data
        delete screens[screenId];
        delete screenSpecificData[screenId];
        
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
        // Save data when barcode is processed
        const originalProcessBarcode = window.processBarcode;
        if (typeof originalProcessBarcode === 'function') {
            window.processBarcode = function() {
                originalProcessBarcode.apply(this, arguments);
                setTimeout(() => saveCurrentScreenData(), 100);
            };
        }
        
        // Save when customer is changed
        const customerModal = document.getElementById('customer');
        if (customerModal) {
            customerModal.addEventListener('hidden.bs.modal', function() {
                setTimeout(() => saveCurrentScreenData(), 100);
            });
        }
        
        // Save when payment details change
        const receivedAmount = document.getElementById('receivedAmount');
        if (receivedAmount) {
            receivedAmount.addEventListener('input', function() {
                setTimeout(() => saveCurrentScreenData(), 100);
            });
        }
        
        const mopSelect = document.getElementById('mop');
        if (mopSelect) {
            mopSelect.addEventListener('change', function() {
                setTimeout(() => saveCurrentScreenData(), 100);
            });
        }
        
        // Save when modals are closed - including gift card modal
        const modals = ['discount', 'additional', 'lp', 'giftcards'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    setTimeout(() => {
                        saveCurrentScreenData();
                        // Special handling for gift card modal
                        if (modalId === 'giftcards') {
                            console.log('Gift card modal closed, data saved for screen:', currentScreenId);
                        }
                    }, 100);
                });
                
                // Also save when gift card modal is shown (to capture any changes)
                if (modalId === 'giftcards') {
                    modal.addEventListener('shown.bs.modal', function() {
                        setTimeout(() => restoreGiftCardModal(), 100);
                    });
                }
            }
        });
    }
    
    // Setup keyboard shortcuts
    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Only trigger if not typing in an input
            if (document.activeElement && (
                document.activeElement.tagName === 'INPUT' ||
                document.activeElement.tagName === 'TEXTAREA' ||
                document.activeElement.tagName === 'SELECT' ||
                document.activeElement.contentEditable === 'true'
            )) {
                return;
            }
            
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
            
            // Ctrl + 1-9 to switch to specific screen
            if (e.ctrlKey && e.key >= '1' && e.key <= '9') {
                e.preventDefault();
                const screenIndex = parseInt(e.key) - 1;
                const screenIds = Object.keys(screens);
                if (screenIds[screenIndex]) {
                    switchToScreen(screenIds[screenIndex]);
                }
            }
        });
    }
    
    // Override existing functions to work with multiple screens
    function overrideExistingFunctions() {
        // Override updateTotals to save screen data
        const originalUpdateTotals = window.updateTotals;
        if (typeof originalUpdateTotals === 'function') {
            window.updateTotals = function() {
                originalUpdateTotals.apply(this, arguments);
                setTimeout(() => saveCurrentScreenData(), 50);
            };
        }
        
        // Override applyBillDiscount to work per screen
        const originalApplyBillDiscount = window.applyBillDiscount;
        if (typeof originalApplyBillDiscount === 'function') {
            window.applyBillDiscount = function() {
                originalApplyBillDiscount.apply(this, arguments);
                // Save the updated discount data for current screen
                if (screenSpecificData[currentScreenId] && typeof billDiscountData !== 'undefined') {
                    screenSpecificData[currentScreenId].billDiscountData = {...billDiscountData};
                }
            };
        }
        
        // Override clearBillDiscount to work per screen
        const originalClearBillDiscount = window.clearBillDiscount;
        if (typeof originalClearBillDiscount === 'function') {
            window.clearBillDiscount = function() {
                originalClearBillDiscount.apply(this, arguments);
                // Clear discount data for current screen
                if (screenSpecificData[currentScreenId]) {
                    screenSpecificData[currentScreenId].billDiscountData = {
                        type: null,
                        value: 0,
                        timing: 'before',
                        amount: 0
                    };
                }
            };
        }
        
        // Override addItemToBill to work with screen isolation
        const originalAddItemToBill = window.addItemToBill;
        if (typeof originalAddItemToBill === 'function') {
            window.addItemToBill = function() {
                originalAddItemToBill.apply(this, arguments);
                // Update item count for current screen
                if (screenSpecificData[currentScreenId] && typeof itemCount !== 'undefined') {
                    screenSpecificData[currentScreenId].itemCount = itemCount;
                }
                setTimeout(() => saveCurrentScreenData(), 100);
            };
        }
        
        // Override gift card related functions if they exist
        const originalApplyGiftCard = window.applyGiftCard;
        if (typeof originalApplyGiftCard === 'function') {
            window.applyGiftCard = function() {
                originalApplyGiftCard.apply(this, arguments);
                // Save gift card data for current screen
                setTimeout(() => {
                    saveGiftCardModalState();
                    saveCurrentScreenData();
                }, 100);
            };
        }
        
        const originalRemoveGiftCard = window.removeGiftCard;
        if (typeof originalRemoveGiftCard === 'function') {
            window.removeGiftCard = function() {
                originalRemoveGiftCard.apply(this, arguments);
                // Update gift card data for current screen
                setTimeout(() => {
                    saveGiftCardModalState();
                    saveCurrentScreenData();
                }, 100);
            };
        }
    }
    
    // Enhanced save bill functionality that works with multiple screens
    function enhancedSaveBillOverride() {
        // Remove existing save bill handlers
        $(document).off('click', '#saveBillBtn, #savePrintBtn');
        
        // New save bill handler that clears only current screen
        $(document).on('click', '#saveBillBtn, #savePrintBtn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isPrintAndSave = $(this).attr('id') === 'savePrintBtn';
            const buttonText = isPrintAndSave ? 'Save & Print [F12]' : 'Save Bill [F11]';
            const processingText = isPrintAndSave ? 'Saving & Printing...' : 'Saving...';
            
            // Get customer information from current screen
            const screenData = screens[currentScreenId];
            if (!screenData) {
                alert('Screen data not found');
                return;
            }
            
            const customerName = screenData.customer.actualName;
            const customerContact = screenData.customer.contact;
            
            // Validate customer
            if (!customerName || customerName === 'Sukoyo Store' || !customerContact) {
                alert("Please select a valid customer before saving the bill.");
                return;
            }
            
            // Collect items from current screen
            let items = [];
            if (screenData.items && screenData.items.length > 0) {
                screenData.items.forEach(item => {
                    if (item.itemId) {
                        items.push({
                            item_id: parseInt(item.itemId),
                            name: item.name,
                            unit: item.unit,
                            qty: parseFloat(item.qty.replace(/[^\d.]/g, '')) || 0,
                            price: parseFloat(item.price.replace(/₹|,|\s/g, '')) || 0,
                            discount: parseFloat(item.discount) || 0,
                            tax: parseFloat(item.tax) || 0,
                            amount: parseFloat(item.amount.replace(/₹|,|\s/g, '')) || 0
                        });
                    }
                });
            }
            
            if (items.length === 0) {
                alert("Please add at least one item to the bill.");
                return;
            }
            
            // Get financial totals from current screen
            const subTotal = parseFloat(screenData.totals.subTotal.replace(/₹|,|\s/g, '')) || 0;
            const itemDiscounts = parseFloat(screenData.totals.itemDiscounts.replace(/₹|,|\s/g, '')) || 0;
            const billDiscount = parseFloat(screenData.totals.billDiscount.replace(/₹|,|\s/g, '')) || 0;
            const totalTax = parseFloat(screenData.totals.totalTax.replace(/₹|,|\s/g, '')) || 0;
            const additionalCharges = parseFloat(screenData.totals.additionalCharges.replace(/₹|,|\s/g, '')) || 0;
            const grandTotal = parseFloat(screenData.totals.grandTotal.replace(/₹|,|\s/g, '')) || 0;
            const receivedAmount = parseFloat(screenData.payment.receivedAmount) || 0;
            const modeOfPayment = screenData.payment.modeOfPayment || 'Cash';
            
            const totalDiscount = itemDiscounts + billDiscount;
            
            if (grandTotal <= 0) {
                alert("Grand total must be greater than 0.");
                return;
            }
            
            if (receivedAmount < grandTotal) {
                if (!confirm(`Received amount (₹${receivedAmount.toFixed(2)}) is less than Grand Total (₹${grandTotal.toFixed(2)}). Continue anyway?`)) {
                    return;
                }
            }
            
            // Get loyalty points used from screen-specific data
            let loyaltyPointsUsed = 0;
            if (screenSpecificData[currentScreenId]) {
                loyaltyPointsUsed = screenSpecificData[currentScreenId].usedLoyaltyPoints || 0;
            }
            
            // Get gift card data from screen-specific data
            let appliedGiftCards = [];
            let totalGiftCardDiscount = 0;
            if (screenSpecificData[currentScreenId]) {
                appliedGiftCards = screenSpecificData[currentScreenId].appliedGiftCards || [];
                totalGiftCardDiscount = screenSpecificData[currentScreenId].giftCardDiscount || 0;
            }
            
            // Prepare payload
            const payload = {
                customer_name: customerName,
                customer_contact: customerContact,
                items: items,
                sub_total: subTotal,
                item_discounts: itemDiscounts,
                bill_discount: billDiscount,
                total_discount: totalDiscount,
                total_tax: totalTax,
                additional_charges: additionalCharges,
                grand_total: grandTotal,
                received_amount: receivedAmount,
                mode_of_payment: modeOfPayment,
                loyalty_points_used: loyaltyPointsUsed,
                applied_gift_cards: appliedGiftCards,
                total_gift_card_discount: totalGiftCardDiscount,
                bill_date: new Date().toISOString().split('T')[0],
                bill_time: new Date().toLocaleTimeString('en-GB', { hour12: false })
            };
            
            console.log('Saving bill for screen:', currentScreenId, payload);
            
            // Disable button
            $(this).prop('disabled', true).text(processingText);
            const currentButton = $(this);
            
            // Get CSRF token
            const csrfToken = $('meta[name="csrf-token"]').attr('content') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            // Send request
            $.ajax({
                url: '/save-bill',
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                data: JSON.stringify(payload),
                success: function(response) {
                    console.log('Success response:', response);
                    
                    if (response.status || response.success) {
                        // Show success message
                        showScreenNotification("Bill saved successfully!", 'success');
                        
                        // Handle printing if needed
                        if (isPrintAndSave && typeof printBill === 'function') {
                            if (response.data && response.data.invoice_id) {
                                payload.billNumber = 'BILL-' + response.data.invoice_id;
                            } else {
                                payload.billNumber = 'BILL-' + Date.now();
                            }
                            printBill(payload);
                            showScreenNotification("Bill sent to printer!", 'info');
                        }
                        
                        // Clear ONLY current screen - don't affect other screens
                        clearCurrentScreenData();
                        
                        // Re-enable button
                        currentButton.prop('disabled', false).text(buttonText);
                        
                    } else {
                        currentButton.prop('disabled', false).text(buttonText);
                        showScreenNotification("Failed to save bill: " + (response.message || response.error || "Unknown error"), 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    
                    let errorMessage = "Something went wrong while saving the bill.";
                    
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.errors) {
                            const messages = Object.values(errorResponse.errors).flat();
                            errorMessage = "Validation errors:\n" + messages.join("\n");
                        } else if (errorResponse.message) {
                            errorMessage = "Error: " + errorResponse.message;
                        }
                    } catch (e) {
                        if (xhr.status === 500) {
                            errorMessage = "Server error (500). Please check server logs.";
                        } else if (xhr.status === 404) {
                            errorMessage = "Endpoint not found (404).";
                        } else if (xhr.status === 419) {
                            errorMessage = "Session expired (419). Please refresh the page.";
                        }
                    }
                    
                    showScreenNotification(errorMessage, 'error');
                    currentButton.prop('disabled', false).text(buttonText);
                }
            });
        });
    }
    
    // Clear only current screen data
    function clearCurrentScreenData() {
        // Reset screen data to empty
        screens[currentScreenId] = createEmptyScreenData();
        screenSpecificData[currentScreenId] = {
            billDiscountData: {
                type: null,
                value: 0,
                timing: 'before',
                amount: 0
            },
            additionalChargesAmount: 0,
            usedLoyaltyPoints: 0,
            remainingLoyaltyPoints: 0,
            appliedGiftCards: [],
            giftCardDiscount: 0,
            itemCount: 0,
            giftCardModalData: {
                availableCards: [],
                selectedCards: [],
                totalApplied: 0,
                remainingBalance: 0
            }
        };
        
        // Reset global variables for current screen
        if (typeof billDiscountData !== 'undefined') {
            billDiscountData = {
                type: null,
                value: 0,
                timing: 'before',
                amount: 0
            };
        }
        if (typeof additionalChargesAmount !== 'undefined') {
            additionalChargesAmount = 0;
        }
        if (typeof itemCount !== 'undefined') {
            itemCount = 0;
        }
        
        // Reload the empty screen data
        loadScreenData(currentScreenId);
        
        console.log('Current screen cleared after successful bill save');
    }
    
    // Show notification for screen operations
    function showScreenNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 100px;
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
            max-width: 350px;
            word-wrap: break-word;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
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
    
    // Public API for external access
    window.POSMultiScreen = {
        addNewScreen: addNewScreen,
        switchToScreen: switchToScreen,
        closeScreen: closeScreen,
        getCurrentScreen: () => currentScreenId,
        getAllScreens: () => screens,
        saveCurrentScreen: saveCurrentScreenData,
        getScreenData: (screenId) => screenSpecificData[screenId] || null,
        updateScreenData: (screenId, data) => {
            if (screenSpecificData[screenId]) {
                screenSpecificData[screenId] = {...screenSpecificData[screenId], ...data};
            }
        },
        showNotification: showScreenNotification,
        clearCurrentScreen: clearCurrentScreenData,
        resetGiftCardModal: resetGiftCardModal,
        restoreGiftCardModal: restoreGiftCardModal
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initMultipleScreens();
            setTimeout(() => {
                overrideExistingFunctions();
                enhancedSaveBillOverride();
            }, 1000);
        });
    } else {
        initMultipleScreens();
        setTimeout(() => {
            overrideExistingFunctions();
            enhancedSaveBillOverride();
        }, 1000);
    }
    
    // Add helpful styles
    const style = document.createElement('style');
    style.textContent = `
        .screen-tab:hover {
            background: #5a6268 !important;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        
        .screen-tab.active:hover {
            background: #0056b3 !important;
        }
        
        .close-tab:hover {
            background: rgba(255,255,255,0.6) !important;
            transform: scale(1.1);
        }
        
        #screen-tabs-container {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-bottom: 3px solid #007bff;
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
        
        /* Screen transition effects */
        .bill-div {
            transition: opacity 0.2s ease;
        }
        
        /* Screen indicator */
        .screen-tab::before {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            right: 0;
            height: 3px;
            background: transparent;
            transition: background 0.2s ease;
        }
        
        .screen-tab.active::before {
            background: #ffc107;
        }
        
        /* Loading state for screen switches */
        .screen-switching {
            pointer-events: none;
            opacity: 0.7;
        }
        
        /* Enhanced notification styles */
        .screen-notification {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            border-left: 4px solid currentColor;
        }
        
        /* Gift card modal enhancements */
        .gift-card-item.selected,
        .gift-card-item.applied {
            background-color: #e3f2fd;
            border-left: 3px solid #2196f3;
        }
        
        .gift-card-modal .table tr.selected {
            background-color: #e8f5e8;
        }
        
        .gift-card-modal .table tr.applied {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
        }
    `;
    document.head.appendChild(style);
    
    console.log('Enhanced Multiple Screen POS System with Gift Card Fix Loaded Successfully');

    
})();
</script>







@include('layouts.footer')