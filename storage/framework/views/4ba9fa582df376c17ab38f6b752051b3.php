<div class="container">
<!-- Corporate Bill Options Modal -->
<div class="modal fade" id="corporateBillOptions" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Corporate Bill Options</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-4">Choose the type of corporate bill:</p>
                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-primary btn-lg" onclick="selectCorporateBillType('with_gst')">
                        <i class="fas fa-file-invoice-dollar"></i> With GST
                        <small class="d-block text-light">Generate corporate bill with GST details</small>
                    </button>
                    <button type="button" class="btn btn-success btn-lg" onclick="selectCorporateBillType('without_gst')">
                        <i class="fas fa-file-invoice"></i> Without GST
                        <small class="d-block text-light">Generate simple corporate bill</small>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- GST Number Verification Modal -->
   <div class="modal fade" id="addGST" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Verify GST Number</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="gstForm">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="gst_no">GST Number</label>
                            <input type="text" class="form-control" id="gst_no" name="gst_no" required>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Verify</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- GST Details Modal -->
    <div class="modal fade" id="addGSTDetails" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add GST Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="gstDetailsForm">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>GST Number</label>
                                <input type="text" class="form-control" name="gst_number" id="gst_number" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Contact Person Name <span class="text-danger">*</span></label>
                                <select class="form-select" name="gst_contact_person" id="gst_contact_person" required>
                                    <option value="" disabled selected>Select Contact Person</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Business Legal Name</label>
                                <input type="text" class="form-control" name="business_legal" id="business_legal" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Contact Number</label>
                                <input type="text" class="form-control" name="gst_contact_no" id="gst_contact_no" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Email ID</label>
                                <input type="email" class="form-control" name="gst_email_id" id="gst_email_id" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>PAN Number</label>
                                <input type="text" class="form-control" name="pan_no" id="pan_no" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Date Of Registration</label>
                                <input type="date" class="form-control" name="register_date" id="register_date" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Billing Address <span class="text-danger">*</span></label>
                                <select class="form-select" name="gstaddress" id="gstaddress" required>
                                    <option value="" disabled selected>Select Billing Address</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Nature Of Business</label>
                                <input type="text" class="form-control" name="nature_business" id="nature_business" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Annual Turnover</label>
                                <input type="text" class="form-control" name="annual_turnover" id="annual_turnover" readonly>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-success" onclick="saveCorporateBill(false)">
                                <i class="fas fa-save"></i> Save Only
                            </button>
                            <button type="button" class="btn btn-primary" onclick="saveCorporateBill(true)">
                                <i class="fas fa-print"></i> Save & Print
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
    function selectCorporateBillType(type) {
    $('#corporateBillOptions').modal('hide');
    
    if (type === 'with_gst') {
        // Open GST verification modal for usual flow
        $('#addGST').modal('show');
    } else if (type === 'without_gst') {
        // Generate corporate bill without GST
        saveCorporateBillWithoutGST();
    }
function saveCorporateBillWithoutGST(shouldPrint = false) {
    console.log('Save Corporate Bill Without GST called');
    
    // Check if items exist in the bill
    if ($('#addedTable tbody tr').length === 0) {
        alert('Please add items to the bill before submitting.');
        return;
    }

    // Collect all bill data (this now includes proper payment mode handling)
    const billData = collectCorporateBillData();
    
    // Validate bill data
    if (!billData.items || billData.items.length === 0) {
        alert('No items found in the bill');
        return;
    }
    
    // Validate payment data
    if (!billData.mode_of_payment || billData.received_amount <= 0) {
        alert('Please enter a valid payment amount');
        return;
    }
    
    // Add flags for without GST
    billData.without_gst = true;
    billData.print_bill = shouldPrint;
    
    // Add CSRF token
    billData._token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
    
    console.log('Corporate Bill Data (Without GST) to send:', billData);
    
    // Show confirmation dialog
    const confirmMsg = `Generate corporate bill without GST?\n\nTotal Amount: ₹${billData.grand_total.toFixed(2)}\nPayment Mode: ${billData.mode_of_payment}\nReceived: ₹${billData.received_amount.toFixed(2)}\nCustomer: ${billData.customer_name || 'Walk-in Customer'}`;
    
    if (confirm(confirmMsg)) {
        // Ask user if they want to print
        const printChoice = confirm('Do you want to print the invoice after saving?');
        billData.print_bill = printChoice;
        
        // Disable the button to prevent double submission
        const $btn = $('button[onclick*="saveCorporateBillWithoutGST"]');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Use the existing route
        $.ajax({
            url: '/save-corporate-bill',
            type: 'POST',
            data: billData,
            success: function(response) {
                console.log('Success Response:', response);
                
                if (response.success) {
                    let message = 'Corporate bill (without GST) saved successfully!\nInvoice ID: ' + response.data.invoice_id;
                    
                    if (response.data.customer_name) {
                        message += '\nCustomer: ' + response.data.customer_name;
                    } else {
                        message += '\nWalk-in Customer';
                    }
                    
                    message += '\nPayment Mode: ' + billData.mode_of_payment;
                    message += '\nAmount Received: ₹' + billData.received_amount.toFixed(2);
                    
                    if (printChoice && response.print_url) {
                        setTimeout(function() {
                            const printWindow = window.open(response.print_url, '_blank', 'width=800,height=600');
                            if (printWindow) {
                                printWindow.focus();
                                alert(message + '\n\nPrint window opened successfully!');
                            } else {
                                alert(message + '\n\nPlease allow popups to open the print window.');
                            }
                            window.location.reload();
                        }, 500);
                    } else {
                        alert(message);
                        window.location.reload();
                    }
                    
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                    $btn.prop('disabled', false).html('Without GST');
                }
            },
            error: function(xhr) {
                console.error('Save Error:', xhr);
                let errorMsg = 'Error saving corporate bill without GST.';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                    if (response.errors) {
                        const errors = Object.values(response.errors).flat();
                        errorMsg += '\n\nValidation Errors:\n' + errors.join('\n');
                    }
                } catch (e) {
                    errorMsg += '\n\nPlease check the console for more details.';
                }
                
                alert(errorMsg);
                $btn.prop('disabled', false).html('Without GST');
            }
        });
    }
}
}
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let corporateGstData = null; // Global variable to store GST data

    // Enhanced GST Verification with improved error handling
$('#gstForm').on('submit', function (e) {
    e.preventDefault();

    let gstNumber = $('#gst_no').val().trim();
    
    if (!gstNumber) {
        alert('Please enter GST number');
        return;
    }

    // Basic GST number format validation (15 characters)
    if (gstNumber.length !== 15) {
        alert('GST number must be exactly 15 characters long');
        return;
    }

    // GST format validation (basic pattern check)
    const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
    if (!gstPattern.test(gstNumber.toUpperCase())) {
        alert('Please enter a valid GST number format');
        return;
    }

    // Show loading
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.text();
    submitBtn.text('Verifying...').prop('disabled', true);

    $.ajax({
        url: "<?php echo e(route('gst.add')); ?>",
        type: 'POST',
        data: {
            gst_no: gstNumber,
            _token: "<?php echo e(csrf_token()); ?>"
        },
        success: function (response) {
            console.log('GST Response:', response); // Debug log
            
            // Check if response indicates success
            if (response.success && response.data && response.data.length > 0) {
                const details = response.data[0]?.result?.details ?? {};
                const promoters = details.promoters ?? [];

                // Additional validation - check if GST details are actually retrieved
                if (!details.gstin) {
                    alert('GST number verification failed: No details found for this GST number. Please check and try again.');
                    return;
                }

                // Check if the returned GST number matches what we entered
                if (details.gstin.toUpperCase() !== gstNumber.toUpperCase()) {
                    alert('GST number mismatch. Please verify the GST number and try again.');
                    return;
                }

                // Populate GST fields with unique IDs
                $('#gst_number').val(details.gstin ?? '');
                $('#business_legal').val(details.legal_name ?? '');
                $('#pan_no').val(details.pan_number ?? '');
                $('#annual_turnover').val(details.annual_turnover ?? '');
                $('#register_date').val(details.date_of_registration ?? '');
                $('#gst_email_id').val(response.data[0]?.result?.email ?? '');
                $('#gst_contact_no').val(response.data[0]?.result?.mobile ?? '');
                $('#nature_business').val(details.contact_details?.principal?.nature_of_business ?? '');

                // Populate contact person dropdown (using unique ID)
                const $contactPersonDropdown = $('#gst_contact_person');
                $contactPersonDropdown.empty().append('<option value="" disabled selected>Select Contact Person</option>');
                if (promoters.length > 0) {
                    promoters.forEach(promoterName => {
                        if (promoterName && promoterName.trim()) {
                            $contactPersonDropdown.append(`<option value="${promoterName.trim()}">${promoterName.trim()}</option>`);
                        }
                    });
                } else {
                    // If no promoters found, add a default option
                    $contactPersonDropdown.append('<option value="Business Owner">Business Owner</option>');
                }

                // Populate addresses
                const contactDetails = details.contact_details ?? {};
                const principaladd = contactDetails.principal ? [contactDetails.principal] : [];
                const additional = contactDetails.additional ?? [];
                const allAddresses = [...principaladd, ...additional];

                const $addressDropdown = $('#gstaddress');
                $addressDropdown.empty().append('<option value="" disabled selected>Select Billing Address</option>');
                if (allAddresses.length > 0) {
                    allAddresses.forEach(entry => {
                        if (entry && entry.address) {
                            const formatted = `${entry.address}${entry.nature_of_business ? ', ' + entry.nature_of_business : ''}`;
                            $addressDropdown.append(`<option value="${formatted}">${formatted}</option>`);
                        }
                    });
                } else {
                    // If no addresses found, show message
                    alert('Warning: No addresses found for this GST number. Please contact support if this is incorrect.');
                }

                // Store GST data globally
                corporateGstData = {
                    gst_number: details.gstin ?? '',
                    business_legal: details.legal_name ?? '',
                    pan_no: details.pan_number ?? '',
                    annual_turnover: details.annual_turnover ?? '',
                    register_date: details.date_of_registration ?? '',
                    email_id: response.data[0]?.result?.email ?? '',
                    contact_no: response.data[0]?.result?.mobile ?? '',
                    nature_business: details.contact_details?.principal?.nature_of_business ?? ''
                };

                // Switch modals
                $('#addGST').modal('hide');
                $('#addGSTDetails').modal('show');
                
            } else {
                // Handle various failure scenarios
                let errorMessage = 'GST number verification failed: ';
                
                if (response.message) {
                    errorMessage += response.message;
                } else if (!response.success) {
                    errorMessage += 'The GST number you entered appears to be invalid or not registered. Please check and try again.';
                } else if (!response.data || response.data.length === 0) {
                    errorMessage += 'No data found for this GST number. It may be invalid or inactive.';
                } else {
                    errorMessage += 'Unknown verification error. Please try again.';
                }
                
                alert(errorMessage);
            }
        },
        error: function (xhr) {
            console.error('GST Verification Error:', xhr);
            
            let errorMsg = 'GST verification failed: ';
            
            // Handle different HTTP error codes
            if (xhr.status === 404) {
                errorMsg += 'GST number not found. Please check if the GST number is correct and active.';
            } else if (xhr.status === 400) {
                errorMsg += 'Invalid GST number format. Please check and enter a valid 15-digit GST number.';
            } else if (xhr.status === 422) {
                // Validation errors
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg += errors.join(', ');
                } else {
                    errorMsg += 'Invalid GST number. Please check the format and try again.';
                }
            } else if (xhr.status === 500) {
                errorMsg += 'Server error occurred during verification. Please try again later.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg += xhr.responseJSON.message;
            } else {
                errorMsg += 'Network error or server unavailable. Please check your connection and try again.';
            }
            
            alert(errorMsg);
        },
        complete: function() {
            // Reset button state
            submitBtn.text(originalText).prop('disabled', false);
        }
    });
});

// Additional helper function to validate GST format before submission
function validateGSTFormat(gstNumber) {
    if (!gstNumber || gstNumber.length !== 15) {
        return false;
    }
    
    // GST number format: 2 digits state code + 10 characters PAN + 1 check digit + Z + 1 check digit
    const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
    return gstPattern.test(gstNumber.toUpperCase());
}

// You can call this function on input change for real-time validation
$('#gst_no').on('input', function() {
    const gstNumber = $(this).val().trim();
    if (gstNumber.length === 15) {
        if (!validateGSTFormat(gstNumber)) {
            $(this).addClass('is-invalid');
            // You can show a small error message below the input
            if ($('.gst-error-msg').length === 0) {
                $(this).after('<small class="text-danger gst-error-msg">Invalid GST number format</small>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $('.gst-error-msg').remove();
        }
    }
});

// Updated function to handle save with print option - customer is optional
function saveCorporateBill(shouldPrint = false) {
    console.log('Save Corporate Bill called with print:', shouldPrint);
    
    // GST fields are mandatory for corporate bills
    if (!$('#gst_number').val()) {
        alert('GST Number is required for corporate bills');
        return;
    }
    
    if (!$('#gst_contact_person').val()) {
        alert('Please select a contact person name');
        return;
    }
    
    if (!$('#gstaddress').val()) {
        alert('Please select a billing address');
        return;
    }
    
    // Check if items exist in the bill
    if ($('#addedTable tbody tr').length === 0) {
        alert('Please add items to the bill before submitting.');
        return;
    }

    // Update corporate GST data with selected values
    if (corporateGstData) {
        corporateGstData.name = $('#gst_contact_person').val();
        corporateGstData.gstaddress = $('#gstaddress').val();
    } else {
        alert('GST data not found. Please verify GST number again.');
        return;
    }
    
    // Collect all bill data
    const billData = collectCorporateBillData();
    
    // Validate bill data
    if (!billData.items || billData.items.length === 0) {
        alert('No items found in the bill');
        return;
    }
    
    // Add GST data to bill data (mandatory)
    Object.keys(corporateGstData).forEach(key => {
        billData[key] = corporateGstData[key];
    });
    
    // Add print flag as string
    billData.print_bill = shouldPrint ? 'true' : 'false';
    
    // Add CSRF token
    billData._token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
    
    console.log('Corporate Bill Data to send:', billData);
    
    // Show loading on buttons
    const $saveBtn = $('button:contains("Save Only")');
    const $printBtn = $('button:contains("Save & Print")');
    
    $saveBtn.prop('disabled', true);
    $printBtn.prop('disabled', true);
    
    if (shouldPrint) {
        $printBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving & Printing...');
    } else {
        $saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    }
    
    // Send AJAX request
    $.ajax({
        url: $('#save-corporate-bill-route').val() || '/save-corporate-bill',
        type: 'POST',
        data: billData,
        success: function(response) {
            console.log('Success Response:', response);
            
            if (response.success) {
                let message = 'Corporate bill saved successfully! Invoice ID: ' + response.data.invoice_id;
                
                // Add customer info to message if available
                if (response.data.customer_name) {
                    message += '\nCustomer: ' + response.data.customer_name;
                } else {
                    message += '\nWalk-in Customer (No customer details)';
                }
                
                message += '\nGST Number: ' + response.data.gst_number;
                
                // Close modal
                $('#addGSTDetails').modal('hide');
                
                if (shouldPrint && response.print_url) {
                    // Open print page in new window
                    setTimeout(function() {
                        const printWindow = window.open(response.print_url, '_blank', 'width=800,height=600');
                        if (printWindow) {
                            printWindow.focus();
                            message += '\nPrint window opened successfully!';
                        } else {
                            message += '\nPlease allow popups to open the print window.';
                        }
                        alert(message);
                        window.location.reload();
                    }, 500);
                } else {
                    alert(message);
                    window.location.reload();
                }
                
                // Reset form and clear data
                resetBillForm();
                
            } else {
                alert('Error: ' + (response.message || 'Unknown error occurred'));
            }
        },
        error: function(xhr) {
            console.error('Save Error:', xhr);
            let errorMsg = 'Error saving corporate bill.';
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMsg = response.message;
                }
                if (response.errors) {
                    const errors = Object.values(response.errors).flat();
                    errorMsg += '\n\nValidation Errors:\n' + errors.join('\n');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                errorMsg += '\n\nPlease check the console for more details.';
            }
            
            alert(errorMsg);
        },
        complete: function() {
            // Reset buttons
            $saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Only');
            $printBtn.prop('disabled', false).html('<i class="fas fa-print"></i> Save & Print');
        }
    });
}

function collectCorporateBillData() {
    const items = [];
    
    console.log('Collecting corporate bill data...');
    
    // Collect items from the table
    $('#addedTable tbody tr').each(function(index) {
        const $row = $(this);
        
        let itemId = $row.data('item-id') || $row.find('[data-item-id]').data('item-id');
        let batchId = $row.data('batch-id') || $row.find('[data-batch-id]').data('batch-id');
        let unit = $row.data('unit') || $row.find('[data-unit]').data('unit') || 'pcs';
        let qty = parseFloat($row.find('input[type="number"]').val()) || parseFloat($row.find('td').eq(3).text()) || 1;
        let price = parseFloat($row.data('price')) || parseFloat($row.find('td').eq(4).text().replace(/[₹,\s]/g, '')) || 0;
        let discount = parseFloat($row.data('discount')) || parseFloat($row.find('td').eq(5).text()) || 0;
        let tax = parseFloat($row.data('tax')) || parseFloat($row.find('td').eq(6).text()) || 0;
        let amount = parseFloat($row.find('td').eq(7).text().replace(/[₹,\s]/g, '')) || (qty * price);
        
        if (itemId) {
            const itemData = {
                item_id: itemId,
                unit: unit,
                qty: qty,
                price: price,
                discount: discount,
                tax: tax,
                amount: amount
            };
            
            // Add batch_id if it exists
            if (batchId && batchId !== '') {
                itemData.batch_id = parseInt(batchId);
            }
            
            items.push(itemData);
        }
    });
    
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
    
    // Get customer data - these are optional for corporate bills
    let customerName = $('#customer_name').val() || '';
    let customerContact = $('#customer_contact').val() || '';
    
    // Clean up empty customer data
    if (customerName.trim() === '' || customerName === 'null') {
        customerName = null;
    }
    if (customerContact.trim() === '' || customerContact === 'null') {
        customerContact = null;
    }

    const billData = {
        // Optional customer fields
        customer_name: customerName,
        customer_contact: customerContact,
        
        // Required fields
        items: items,
        sub_total: parseFloat($('#subTotal').text().replace(/[₹,\s]/g, '') || 0),
        item_discounts: parseFloat($('#itemDiscounts').text().replace(/[₹,\s]/g, '') || 0),
        bill_discount: parseFloat($('#billDiscount').text().replace(/[₹,\s]/g, '') || 0),
        total_discount: parseFloat($('#billDiscount').text().replace(/[₹,\s]/g, '') || 0) + 
                       parseFloat($('#itemDiscounts').text().replace(/[₹,\s]/g, '') || 0),
        total_tax: parseFloat($('#totalTax').text().replace(/[₹,\s]/g, '') || 0),
        additional_charges: parseFloat($('#additionalCharges').text().replace(/[₹,\s]/g, '') || 0),
        grand_total: parseFloat($('#grandTotal').text().replace(/[₹,\s]/g, '') || 0),
        
        // Payment details with proper mode handling
        received_amount: receivedAmount,
        mode_of_payment: mopValue,
        cash_amount: cashAmount,
        online_amount: onlineAmount,
        
        // Loyalty points (only relevant if customer exists)
        loyalty_points_used: customerContact ? (parseInt($('#usedPointsDisplay').text()) || 0) : 0,
        loyalty_points_earned: 0,
        
        // Gift card data
        applied_gift_cards: typeof getAppliedGiftCards === 'function' ? getAppliedGiftCards() : [],
        total_gift_card_discount: parseFloat($('#giftCardDiscount').text().replace(/[₹,\s]/g, '') || 0),
        
        // Additional fields
        gift_card_code: null,
        gift_card_amount: 0,
        voucher_code: null,
        voucher_amount: 0
    };
    
    console.log('Final corporate bill data:', billData);
    return billData;
}


function collectBillData() {
    const items = [];
    
    console.log('Collecting bill data...');
    console.log('Table rows found:', $('#addedTable tbody tr').length);
    
    // Collect items from the table
    $('#addedTable tbody tr').each(function(index) {
        const $row = $(this);
        console.log('Processing row:', index, $row);
        
        // Try multiple ways to get item data
        let itemId = $row.data('item-id') || 
                    $row.find('[data-item-id]').data('item-id') ||
                    $row.find('.item-id').val() ||
                    $row.find('td:first').data('id');
        
        let unit = $row.data('unit') ||
                   $row.find('[data-unit]').data('unit') ||
                   $row.find('.unit').val() ||
                   $row.find('td').eq(2).text().trim() || 'pcs';
        
        let qty = parseFloat($row.find('input[type="number"]').val()) ||
                  parseFloat($row.find('.quantity').val()) ||
                  parseFloat($row.find('td').eq(3).text()) || 1;
        
        let price = parseFloat($row.data('price')) ||
                   parseFloat($row.find('[data-price]').data('price')) ||
                   parseFloat($row.find('.price').val()) ||
                   parseFloat($row.find('td').eq(4).text().replace(/[₹,\s]/g, '')) || 0;
        
        let discount = parseFloat($row.find('input[name*="discount"]').val()) ||
                      parseFloat($row.data('discount')) ||
                      parseFloat($row.find('td').eq(5).text()) || 0;
        
        let tax = parseFloat($row.data('tax')) ||
                 parseFloat($row.find('[data-tax]').data('tax')) ||
                 parseFloat($row.find('.tax').val()) ||
                 parseFloat($row.find('td').eq(6).text()) || 0;
        
        let amount = parseFloat($row.find('td').eq(7).text().replace(/[₹,\s]/g, '')) ||
                    (qty * price);
        
        console.log('Item data:', { itemId, unit, qty, price, discount, tax, amount });
        
        if (itemId) {
            items.push({
                item_id: itemId,
                unit: unit,           // Include unit field
                qty: qty,            // Use qty (not quantity)
                price: price,
                discount: discount,
                tax: tax,            // Use tax (not tax_rate)
                amount: amount
            });
        }
    });
    
    console.log('Collected items:', items);

    const billData = {
        customer_name: $('#customer_name').val() || null,
        customer_contact: $('#customer_contact').val() || null,
        items: items,
        sub_total: parseFloat($('#subTotal').text().replace(/[₹,\s]/g, '') || 0),
        total_discount: parseFloat($('#billDiscount').text().replace(/[₹,\s]/g, '') || 0) + 
                       parseFloat($('#itemDiscounts').text().replace(/[₹,\s]/g, '') || 0),
        total_tax: parseFloat($('#totalTax').text().replace(/[₹,\s]/g, '') || 0),
        additional_charges: parseFloat($('#additionalChargesInput').val() || 0),
        grand_total: parseFloat($('#grandTotal').text().replace(/[₹,\s]/g, '') || 0),
        received_amount: parseFloat($('#receivedAmount').val() || $('#grandTotal').text().replace(/[₹,\s]/g, '') || 0),
        mode_of_payment: $('#mop').val() || 'Cash',
        loyalty_points_used: parseInt($('#usedPointsDisplay').text() || 0),
        loyalty_points_earned: 0,
        
        // Gift card data (if you have these functions)
        applied_gift_cards: typeof getAppliedGiftCards === 'function' ? getAppliedGiftCards() : [],
        total_gift_card_discount: parseFloat($('#giftCardDiscount').text().replace(/[₹,\s]/g, '') || 0),
        
        // Additional fields for consistency
        gift_card_code: null,
        gift_card_amount: 0,
        voucher_code: null,
        voucher_amount: 0
    };
    
    console.log('Final bill data:', billData);
    return billData;
}

function resetBillForm() {
    try {
        // Clear the items table
        $('#addedTable tbody').empty();
        $('#addedTable').hide();
        if ($('#tableFooter').length) {
            $('#tableFooter').hide();
        }
        
        // Reset totals
        $('#subTotal, #itemDiscounts, #billDiscount, #totalTax, #additionalCharges, #grandTotal, #changeToReturn').text('₹ 0.00');
        $('#receivedAmount').val('0');
        if ($('#totalItems').length) {
            $('#totalItems').text('0');
        }
        
        // Reset customer info
        $('#selectedCustomer').text('No customer selected');
        $('#loyaltyPointsDisplay').text('0 Points');
        $('#customer_name, #customer_contact').val('');
        
        // Reset GST data
        corporateGstData = null;
        $('#gstForm')[0].reset();
        $('#gstDetailsForm')[0].reset();
        
        // Focus back to barcode input if it exists
        if ($('#barcodeInput').length) {
            $('#barcodeInput').focus();
        }
        
        console.log('Bill form reset successfully');
    } catch (error) {
        console.error('Error resetting form:', error);
    }
}

// Optional: Function to print existing invoice
function printExistingInvoice(invoiceId) {
    if (invoiceId) {
        window.open(`<?php echo e(url('/corporate-invoice/print')); ?>/${invoiceId}`, '_blank');
    }
}
</script>

</div><?php /**PATH /var/www/sukoyo/resources/views/corporate_bill.blade.php ENDPATH**/ ?>