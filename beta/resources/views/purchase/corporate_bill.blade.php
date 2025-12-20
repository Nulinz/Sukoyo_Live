<div class="container">

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
                        @csrf
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
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>GST Number</label>
                                <input type="text" class="form-control" name="gst_number" id="gst_number" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Name <span class="text-danger">*</span></label>
                                <select class="form-select" name="name" id="name" required>
                                    <option value="" disabled selected>Select Name</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Business Legal Name</label>
                                <input type="text" class="form-control" name="business_legal" id="business_legal" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Contact Number</label>
                                <input type="text" class="form-control" name="contact_no" id="contact_no" readonly>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Email ID</label>
                                <input type="email" class="form-control" name="email_id" id="email_id" readonly>
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
                                <label>Address <span class="text-danger">*</span></label>
                                <select class="form-select" name="gstaddress" id="gstaddress" required>
                                    <option value="" disabled selected>Select Address</option>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let corporateGstData = null; // Global variable to store GST data

    $(document).ready(function () {
        // GST Verification
        $('#gstForm').on('submit', function (e) {
            e.preventDefault();

            let gstNumber = $('#gst_no').val().trim();
            
            if (!gstNumber) {
                alert('Please enter GST number');
                return;
            }

            // Show loading
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.text('Verifying...').prop('disabled', true);

            $.ajax({
                url: "{{ route('gst.add') }}",
                type: 'POST',
                data: {
                    gst_no: gstNumber,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        const details = response.data[0]?.result?.details ?? {};
                        const promoters = details.promoters ?? [];

                        // Populate fields
                        $('#gst_number').val(details.gstin ?? '');
                        $('#business_legal').val(details.legal_name ?? '');
                        $('#pan_no').val(details.pan_number ?? '');
                        $('#annual_turnover').val(details.annual_turnover ?? '');
                        $('#register_date').val(details.date_of_registration ?? '');
                        $('#email_id').val(response.data[0]?.result?.email ?? '');
                        $('#contact_no').val(response.data[0]?.result?.mobile ?? '');
                        $('#nature_business').val(details.contact_details?.principal?.nature_of_business ?? '');

                        // Populate promoter dropdown
                        const $nameDropdown = $('#name');
                        $nameDropdown.empty().append('<option value="" disabled selected>Select Name</option>');
                        if (promoters.length > 0) {
                            promoters.forEach(name => {
                                if (name && name.trim()) {
                                    $nameDropdown.append(`<option value="${name.trim()}">${name.trim()}</option>`);
                                }
                            });
                        }

                        // Populate addresses
                        const contactDetails = details.contact_details ?? {};
                        const principaladd = contactDetails.principal ? [contactDetails.principal] : [];
                        const additional = contactDetails.additional ?? [];
                        const allAddresses = [...principaladd, ...additional];

                        const $addressDropdown = $('#gstaddress');
                        $addressDropdown.empty().append('<option value="" disabled selected>Select Address</option>');
                        if (allAddresses.length > 0) {
                            allAddresses.forEach(entry => {
                                if (entry && entry.address) {
                                    const formatted = `${entry.address}${entry.nature_of_business ? ', ' + entry.nature_of_business : ''}`;
                                    $addressDropdown.append(`<option value="${formatted}">${formatted}</option>`);
                                }
                            });
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
                        alert('GST Verification Failed: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr) {
                    console.error('GST Verification Error:', xhr);
                    let errorMsg = 'Something went wrong. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    submitBtn.text(originalText).prop('disabled', false);
                }
            });
        });
    });

// Updated function to handle save with print option - customer is optional
function saveCorporateBill(shouldPrint = false) {
    console.log('Save Corporate Bill called with print:', shouldPrint);
    
    // GST fields are mandatory for corporate bills
    if (!$('#gst_number').val()) {
        alert('GST Number is required for corporate bills');
        return;
    }
    
    if (!$('#name').val()) {
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
        corporateGstData.name = $('#name').val();
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
                    }, 500);
                } else {
                    alert(message);
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
        let unit = $row.data('unit') || $row.find('[data-unit]').data('unit') || 'pcs';
        let qty = parseFloat($row.find('input[type="number"]').val()) || parseFloat($row.find('td').eq(3).text()) || 1;
        let price = parseFloat($row.data('price')) || parseFloat($row.find('td').eq(4).text().replace(/[₹,\s]/g, '')) || 0;
        let discount = parseFloat($row.data('discount')) || parseFloat($row.find('td').eq(5).text()) || 0;
        let tax = parseFloat($row.data('tax')) || parseFloat($row.find('td').eq(6).text()) || 0;
        let amount = parseFloat($row.find('td').eq(7).text().replace(/[₹,\s]/g, '')) || (qty * price);
        
        if (itemId) {
            items.push({
                item_id: itemId,
                unit: unit,
                qty: qty,
                price: price,
                discount: discount,
                tax: tax,
                amount: amount
            });
        }
    });
    
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
        total_discount: parseFloat($('#billDiscount').text().replace(/[₹,\s]/g, '') || 0) + 
                       parseFloat($('#itemDiscounts').text().replace(/[₹,\s]/g, '') || 0),
        total_tax: parseFloat($('#totalTax').text().replace(/[₹,\s]/g, '') || 0),
        additional_charges: parseFloat($('#additionalChargesInput').val() || 0),
        grand_total: parseFloat($('#grandTotal').text().replace(/[₹,\s]/g, '') || 0),
        received_amount: parseFloat($('#receivedAmount').val() || $('#grandTotal').text().replace(/[₹,\s]/g, '') || 0),
        mode_of_payment: $('#mop').val() || 'Cash',
        
        // Loyalty points (only relevant if customer exists)
        loyalty_points_used: customerContact ? parseInt($('#usedPointsDisplay').text() || 0) : 0,
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
            window.open(`{{ url('/corporate-invoice/print') }}/${invoiceId}`, '_blank');
        }
    }
</script>

</div>