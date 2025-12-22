<?php $__env->startSection('content'); ?>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Return Vouchers
                    </h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productReturnModal">
                        <i class="fas fa-undo"></i> Product Return
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" id="searchVoucher" class="form-control" placeholder="Search by voucher code...">
                        </div>
                        <div class="col-md-3">
                            <select id="filterStatus" class="form-control">
                                <option value="">All Status</option>
                                <option value="valid">Valid</option>
                                <option value="used">Used</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="filterDate" class="form-control" placeholder="Filter by date">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" id="clearFilters">
                                <i class="fas fa-redo"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <!-- Vouchers Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="vouchersTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 15%;">Voucher Code</th>
                                    <th style="width: 10%;">Amount</th>
                                    <th style="width: 12%;">Bill Number</th>
                                    <th style="width: 15%;">Customer</th>
                                    <th style="width: 12%;">Store</th>
                                    <th style="width: 10%;">Issue Date</th>
                                    <th style="width: 10%;">Expiry Date</th>
                                    <th style="width: 11%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr data-status="<?php echo e($voucher->isValid() ? 'valid' : ($voucher->is_used ? 'used' : 'expired')); ?>">
                                    <td><?php echo e($index + 1); ?></td>
                                    <td>
                                        <strong class="text-primary"><?php echo e($voucher->voucher_code); ?></strong>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">₹<?php echo e(number_format($voucher->amount, 2)); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('sales.profile', ['id' => $voucher->sales_invoice_id])); ?>" 
                                           class="text-decoration-none">
                                            BILL-<?php echo e($voucher->sales_invoice_id); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <?php echo e($voucher->salesInvoice->customer->name ?? 'Walk-in Customer'); ?>

                                        <?php if($voucher->salesInvoice->customer): ?>
                                            <br><small class="text-muted"><?php echo e($voucher->salesInvoice->customer->contact); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($voucher->salesInvoice->store->store_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($voucher->created_at->format('d-m-Y')); ?></td>
                                    <td><?php echo e($voucher->expiry_date->format('d-m-Y')); ?></td>
                                    <td>
                                        <?php if($voucher->is_used): ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-check-circle"></i> Used
                                            </span>
                                        <?php elseif($voucher->isExpired()): ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle"></i> Expired
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Valid
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No return vouchers found</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Return Modal - Step 1: Enter Bill Number -->
<div class="modal fade" id="productReturnModal" tabindex="-1" aria-labelledby="productReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productReturnModalLabel">Product Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="billNumber" class="form-label fw-semibold">Enter Bill Number</label>
                    <input type="text" class="form-control" id="billNumber" placeholder="Enter bill number (e.g., BILL-123 or 123)">
                    <small class="text-muted">Enter the bill number or invoice ID</small>
                </div>
                <div class="d-flex justify-content-between gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="searchBillBtn">
                        <i class="fas fa-search"></i> Search Bill
                    </button>
                 </div>
                <div id="billSearchFeedback" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Product Return Modal - Step 2: Show Bill Details & Select Items -->
<div class="modal fade" id="returnDetailsModal" tabindex="-1" aria-labelledby="returnDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnDetailsModalLabel">Process Product Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Bill Information -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Bill Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Bill Number:</strong> <span id="returnBillNumber"></span></p>
                                <p class="mb-1"><strong>Date:</strong> <span id="returnBillDate"></span></p>
                                <p class="mb-1"><strong>Customer:</strong> <span id="returnCustomer"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Grand Total:</strong> ₹<span id="returnGrandTotal"></span></p>
                                <p class="mb-1"><strong>Payment Method:</strong> <span id="returnPaymentMethod"></span></p>
                                <p class="mb-1"><strong>Store:</strong> <span id="returnStore"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Items Table -->
                <form id="returnForm">
                    <input type="hidden" id="returnInvoiceId" name="invoice_id">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">
                                        <input type="checkbox" id="selectAllItems" title="Select All">
                                    </th>
                                    <th style="width: 30%;">Item Name</th>
                                    <th style="width: 10%;">Unit</th>
                                    <th style="width: 10%;">Original Qty</th>
                                    <th style="width: 15%;">Return Qty</th>
                                    <th style="width: 10%;">Price</th>
                                    <th style="width: 10%;">Discount (%)</th>
                                    <th style="width: 10%;">Tax (%)</th>
                                </tr>
                            </thead>
                            <tbody id="returnItemsTable">
                                <!-- Items will be populated here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <!-- Return Summary -->
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Return Summary</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1">Selected Items: <strong id="returnItemCount">0</strong></p>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <p class="mb-0">Refund Amount (Excluding Tax): <strong class="text-success fs-5">₹<span id="returnAmount">0.00</span></strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="processReturnBtn">
                            <i class="fas fa-check"></i> Process Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card-body h3 {
    font-size: 2rem;
    margin-bottom: 0;
}

#vouchersTable tbody tr {
    cursor: default;
}

#vouchersTable tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    padding: 0.5em 0.75em;
    font-size: 0.875rem;
}

.item-select-checkbox {
    cursor: pointer;
}

.return-qty-input:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
}

#returnItemsTable tr:hover {
    background-color: #f8f9fa;
}
</style>

<script>
$(document).ready(function() {
    // ========== VOUCHERS LIST FUNCTIONALITY ==========
    
    // Search functionality
    $('#searchVoucher').on('keyup', function() {
        filterTable();
    });

    // Status filter
    $('#filterStatus').on('change', function() {
        filterTable();
    });

    // Date filter
    $('#filterDate').on('change', function() {
        filterTable();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#searchVoucher').val('');
        $('#filterStatus').val('');
        $('#filterDate').val('');
        filterTable();
    });

    function filterTable() {
        const searchTerm = $('#searchVoucher').val().toLowerCase();
        const statusFilter = $('#filterStatus').val();
        const dateFilter = $('#filterDate').val();

        $('#vouchersTable tbody tr').each(function() {
            const row = $(this);
            const voucherCode = row.find('td:eq(1)').text().toLowerCase();
            const rowStatus = row.data('status');
            const issueDate = row.find('td:eq(6)').text();

            let showRow = true;

            // Search filter
            if (searchTerm && !voucherCode.includes(searchTerm)) {
                showRow = false;
            }

            // Status filter
            if (statusFilter && rowStatus !== statusFilter) {
                showRow = false;
            }

            // Date filter
            if (dateFilter && issueDate) {
                const rowDate = issueDate.split('-').reverse().join('-');
                if (rowDate !== dateFilter) {
                    showRow = false;
                }
            }

            row.toggle(showRow);
        });
    }

    // ========== PRODUCT RETURN FUNCTIONALITY ==========
    
    let currentBillData = null;

    // Search bill by bill number - Button Click Event
    $(document).on('click', '#searchBillBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const billNumber = $('#billNumber').val().trim();
        
        if (!billNumber) {
            $('#billSearchFeedback').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Please enter a bill number</div>');
            return false;
        }

        // Show loading
        $('#searchBillBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');
        $('#billSearchFeedback').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Searching for bill...</div>');

        $.ajax({
            url: '/pos/get-bill-details',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                bill_number: billNumber
            },
            success: function(response) {
                if (response.status) {
                    currentBillData = response.data;
                    
                    // Hide search modal
                    $('#productReturnModal').modal('hide');
                    
                    // Populate and show details modal
                    populateReturnDetails(response.data);
                    
                    // Small delay to ensure first modal is closed
                    setTimeout(function() {
                        $('#returnDetailsModal').modal('show');
                    }, 300);
                    
                    // Clear search form
                    $('#billNumber').val('');
                    $('#billSearchFeedback').html('');
                } else {
                    $('#billSearchFeedback').html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Error retrieving bill details';
                $('#billSearchFeedback').html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ' + errorMsg + '</div>');
            },
            complete: function() {
                $('#searchBillBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Search Bill');
            }
        });
        
        return false;
    });

    // Allow Enter key to search
    $(document).on('keypress', '#billNumber', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#searchBillBtn').click();
            return false;
        }
    });

    // Populate return details modal
    function populateReturnDetails(data) {
        // Populate bill information
        $('#returnBillNumber').text(data.bill_number);
        $('#returnBillDate').text(data.invoice_date);
        $('#returnCustomer').text(data.customer_name + ' - ' + data.customer_contact);
        $('#returnGrandTotal').text(parseFloat(data.grand_total).toFixed(2));
        $('#returnPaymentMethod').text(data.mode_of_payment);
        $('#returnStore').text(data.store_name);
        $('#returnInvoiceId').val(data.invoice_id);

        // Populate items table
        const itemsTable = $('#returnItemsTable');
        itemsTable.empty();

        data.items.forEach((item, index) => {
            const batchInfo = item.batch_no ? ` (Batch: ${item.batch_no})` : '';
            const row = `
                <tr data-item-index="${index}">
                    <td>
                        <input type="checkbox" class="item-select-checkbox" data-index="${index}">
                    </td>
                    <td>${item.item_name}${batchInfo}</td>
                    <td>${item.unit}</td>
                    <td>${item.qty}</td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm return-qty-input" 
                               data-index="${index}"
                               min="0.01" 
                               max="${item.qty}" 
                               step="0.01" 
                               value="${item.return_qty}"
                               disabled>
                    </td>
                    <td>₹${parseFloat(item.price).toFixed(2)}</td>
                    <td>${item.discount || 0}%</td>
                    <td>${item.tax || 0}%</td>
                    <input type="hidden" class="item-data" data-index="${index}" value='${JSON.stringify(item)}'>
                </tr>
            `;
            itemsTable.append(row);
        });

        // Reset form
        $('#selectAllItems').prop('checked', false);
        updateReturnSummary();
    }

    // Select all items checkbox
    $(document).on('change', '#selectAllItems', function() {
        const isChecked = $(this).is(':checked');
        $('.item-select-checkbox').prop('checked', isChecked);
        $('.return-qty-input').prop('disabled', !isChecked);
        updateReturnSummary();
    });

    // Individual item checkbox
    $(document).on('change', '.item-select-checkbox', function() {
        const index = $(this).data('index');
        const isChecked = $(this).is(':checked');
        $(`.return-qty-input[data-index="${index}"]`).prop('disabled', !isChecked);
        
        // Update select all checkbox
        const totalCheckboxes = $('.item-select-checkbox').length;
        const checkedCheckboxes = $('.item-select-checkbox:checked').length;
        $('#selectAllItems').prop('checked', totalCheckboxes === checkedCheckboxes);
        
        updateReturnSummary();
    });

    // Return quantity change
    $(document).on('input', '.return-qty-input', function() {
        updateReturnSummary();
    });

    // Update return summary
    function updateReturnSummary() {
        let totalAmount = 0;
        let itemCount = 0;

        $('.item-select-checkbox:checked').each(function() {
            const index = $(this).data('index');
            const itemDataStr = $(`.item-data[data-index="${index}"]`).val();
            const itemData = JSON.parse(itemDataStr);
            const returnQty = parseFloat($(`.return-qty-input[data-index="${index}"]`).val()) || 0;

            if (returnQty > 0) {
                itemCount++;
                const itemAmount = returnQty * parseFloat(itemData.price);
                totalAmount += itemAmount;
            }
        });

        $('#returnItemCount').text(itemCount);
        $('#returnAmount').text(totalAmount.toFixed(2));
    }

    // Process return
    $(document).on('click', '#processReturnBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const selectedItems = $('.item-select-checkbox:checked').length;
        if (selectedItems === 0) {
            alert('Please select at least one item to return');
            return false;
        }

        // Collect return items data
        const returnItems = [];
        $('.item-select-checkbox:checked').each(function() {
            const index = $(this).data('index');
            const itemDataStr = $(`.item-data[data-index="${index}"]`).val();
            const itemData = JSON.parse(itemDataStr);
            const returnQty = parseFloat($(`.return-qty-input[data-index="${index}"]`).val()) || 0;

            if (returnQty > 0 && returnQty <= itemData.qty) {
                returnItems.push({
                    sales_invoice_item_id: itemData.id,
                    item_id: itemData.item_id,
                    batch_id: itemData.batch_id || null,
                    return_qty: returnQty,
                    qty: itemData.qty,
                    price: itemData.price,
                    discount: itemData.discount || 0,
                    tax: itemData.tax || 0
                });
            }
        });

        if (returnItems.length === 0) {
            alert('Please enter valid return quantities');
            return false;
        }

        $('#processReturnBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: '/pos/process-return',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                invoice_id: $('#returnInvoiceId').val(),
                items: returnItems
            },
            success: function(response) {
                if (response.status) {
                    $('#returnDetailsModal').modal('hide');
                    
                    printVoucherDirect(
                        response.data.voucher_code,
                        response.data.refund_amount,
                        response.data.expiry_date
                    );
                    
                    $('#returnForm')[0].reset();
                    currentBillData = null;
                    
                    // Reload page to show new voucher
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Error processing return';
                alert('Error: ' + errorMsg);
            },
            complete: function() {
                $('#processReturnBtn').prop('disabled', false).html('<i class="fas fa-check"></i> Process Return');
            }
        });
        
        return false;
    });

    // Reset modals when closed
    $('#productReturnModal').on('hidden.bs.modal', function() {
        $('#billNumber').val('');
        $('#billSearchFeedback').html('');
    });

    $('#returnDetailsModal').on('hidden.bs.modal', function() {
        $('#returnForm')[0].reset();
        $('#returnItemsTable').empty();
        $('#selectAllItems').prop('checked', false);
        currentBillData = null;
    });
});

// Print voucher function
function printVoucherDirect(voucherCode, voucherAmount, voucherExpiry) {
    const printWindow = window.open('', '_blank', 'width=600,height=700');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Return Voucher - ${voucherCode}</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                    color-adjust: exact;
                }
                body {
                    font-family: 'Arial', sans-serif;
                    padding: 20px;
                    background: #ffffff;
                    margin: 0;
                }
                .voucher {
                    display: flex;
                    background: linear-gradient(135deg, #ff8a3d 0%, #ff6a28 100%);
                    color: white;
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    overflow: visible;
                    width: 100%;
                    max-width: 700px;
                    height: 280px;
                    position: relative;
                    margin: 40px auto;
                }
                .voucher-left {
                    width: 35%;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    padding: 30px 20px;
                    position: relative;
                    border-right: 2px dashed rgba(255, 255, 255, 0.4);
                }
                .percentage {
                    font-size: 70px;
                    font-weight: bold;
                    line-height: 1;
                    margin-bottom: 10px;
                }
                .dots {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    position: absolute;
                    right: -6px;
                    top: 50%;
                    transform: translateY(-50%);
                }
                .dot {
                    width: 10px;
                    height: 10px;
                    background: white;
                    border-radius: 50%;
                }
                .voucher-right {
                    flex: 1;
                    padding: 30px 35px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }
                .voucher-title {
                    font-size: 42px;
                    font-weight: bold;
                    letter-spacing: 3px;
                    margin-bottom: 20px;
                    text-transform: uppercase;
                }
                .voucher-details {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                .cart-icon {
                    width: 50px;
                    height: 50px;
                    fill: white;
                    flex-shrink: 0;
                }
                .details-text {
                    flex: 1;
                }
                .voucher-code {
                    font-size: 22px;
                    font-weight: bold;
                    margin-bottom: 8px;
                    letter-spacing: 2px;
                }
                .voucher-amount {
                    font-size: 28px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .voucher-expiry {
                    font-size: 14px;
                    opacity: 0.9;
                }
                .voucher::before,
                .voucher::after {
                    content: '';
                    position: absolute;
                    width: 30px;
                    height: 30px;
                    background: #ffffff;
                    border-radius: 50%;
                    left: 33%;
                    transform: translateX(-50%);
                }
                .voucher::before {
                    top: -15px;
                }
                .voucher::after {
                    bottom: -15px;
                }
                @media print {
                    @page {
                        size: A4;
                        margin: 20mm;
                    }
                    body {
                        background: white;
                        padding: 0;
                        margin: 0;
                    }
                    .voucher {
                        box-shadow: none;
                        margin: 0 auto;
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div>
                <div class="voucher">
                    <div class="voucher-left">
                        <div class="percentage">%</div>
                        <div class="dots">
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                        </div>
                    </div>
                    <div class="voucher-right">
                        <div class="voucher-title">VOUCHER</div>
                        <div class="voucher-details">
                            <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                                <path d="M7 18c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2zm10 0c-1.1 0-2 0.9-2 2s0.9 2 2 2 2-0.9 2-2-0.9-2-2-2zm-9.8-3.2c0 0.1 0 0.2 0.1 0.2 0.1 0.1 0.1 0.1 0.2 0.1h11.4c0.6 0 1.1-0.4 1.3-0.9l2.4-6.8c0.1-0.2 0-0.4-0.1-0.6-0.1-0.1-0.3-0.2-0.5-0.2H6.54l-0.5-1.5C5.9 5 5.7 4.9 5.5 4.9H3c-0.3 0-0.5 0.2-0.5 0.5s0.2 0.5 0.5 0.5h2.2l2.3 6.8-0.9 1.6c-0.4 0.7-0.2 1.6 0.5 2 0.2 0.1 0.4 0.2 0.6 0.2h11.3c0.3 0 0.5-0.2 0.5-0.5s-0.2-0.5-0.5-0.5H7.2c-0.2 0-0.3-0.1-0.3-0.3 0-0.1 0-0.1 0-0.2l0.3-0.6z"/>
                            </svg>
                            <div class="details-text">
                                <div class="voucher-code">${voucherCode}</div>
                                <div class="voucher-amount">₹${parseFloat(voucherAmount).toFixed(2)}</div>
                                <div class="voucher-expiry">Valid: ${voucherExpiry}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    
    setTimeout(function() {
        printWindow.print();
    }, 250);
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/sales/return.blade.php ENDPATH**/ ?>