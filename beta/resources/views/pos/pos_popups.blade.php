<!-- <div class="modal fade" id="discount" tabindex="-1" aria-labelledby="discountLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Discounts</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <div class="d-flex align-items-center mt-1 gap-3">
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="checkbox" value="" id="before">
                                    <label class="form-check-label mb-0" for="before">Before Tax</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="checkbox" value="" id="after">
                                    <label class="form-check-label mb-0" for="after">After Tax</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="percentage">Percentage</label>
                            <input type="number" class="form-control" name="" id="percentage" min="0">
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="discamount">Amount</label>
                            <input type="number" class="form-control" name="" id="discamount" min="0">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mx-auto mt-3 gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
 -->

<!-- Additional Modal -->
<div class="modal fade" id="additional" tabindex="-1" aria-labelledby="additionalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Additional Charges</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="bag">Bag</label>
                            <input type="number" class="form-control" name="" id="bag" min="0">
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="packing">Packing</label>
                            <input type="number" class="form-control" name="" id="packing" min="0">
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="adjustment">Adjustment</label>
                            <input type="number" class="form-control" name="" id="adjustment" min="0">
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="wrapping">Gift Wrapping</label>
                            <input type="number" class="form-control" name="" id="wrapping" min="0">
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="customise">Customization</label>
                            <input type="number" class="form-control" name="" id="customise" min="0">
                        </div>
                        <div class="col-md-12">
                            <label for="adjustamt">Amount</label>
                            <input type="number" class="form-control" name="" id="adjustamt" min="0">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mx-auto mt-3 gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="lp" tabindex="-1" aria-labelledby="lpLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Loyalty Points</h4>
            </div>
            <div class="modal-body">
                <form id="loyaltyForm">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h6>Bill Total : ₹ <span id="billTotalDisplay">0.00</span></h6>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Available Points</label>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('assets/images/icon_7.png') }}" height="15px">
                                <h6 id="availablePoints">0</h6>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Maximum Redeemable</label>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('assets/images/icon_7.png') }}" height="15px">
                                <h6 id="maxRedeemPoints">0 (₹ 0.00)</h6>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <label>Enter Points / Amount to Redeem</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control" id="redeemPoints" min="0">
                                <input type="number" class="form-control" id="redeemAmount" min="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Remaining Amount (Balance)</label>
                            <h6>₹ <span id="remainingAmount">0.00</span></h6>
                        </div>
                        <div class="d-flex justify-content-between mx-auto mt-3 gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let billTotal = 0;
    let availablePoints = 0;
    let redeemRate = 1;
    let usedPointsInSession = 0;
    let currentCustomerContact = '';

    let maxRedeemablePoints = 0;
    let maxRedeemableAmount = 0;

    // Enhanced function with online/offline support
    function openLoyaltyModal() {
        const grandTotalElement = document.getElementById('grandTotal');
        billTotal = grandTotalElement ? parseFloat(grandTotalElement.textContent.replace(/[₹,]/g, '').trim()) || 0 : 0;

        document.getElementById('billTotalDisplay').textContent = billTotal.toFixed(2);

        let contact = '';
        const contactInput = document.getElementById('contact');
        if (contactInput?.value) contact = contactInput.value;

        if (!contact) {
            const customerContactInput = document.getElementById('customer_contact');
            if (customerContactInput?.value) contact = customerContactInput.value;
        }

        if (!contact) {
            const selectedCustomerElement = document.getElementById('selectedCustomer');
            if (selectedCustomerElement?.textContent) {
                const match = selectedCustomerElement.textContent.match(/(\d{10,})$/);
                if (match) contact = match[1];
            }
        }

        if (!contact) {
            alert('Please select a customer first to use loyalty points.');
            return;
        }

        currentCustomerContact = contact;

        // Enhanced: Check online/offline mode using posOfflineManager
        if (typeof posOfflineManager !== 'undefined' && posOfflineManager.isOnline) {
            // Online mode: fetch from server
            fetchLoyaltyPointsOnline(contact);
        } else {
            // Offline mode: get from local storage/cache
            fetchLoyaltyPointsOffline(contact);
        }
    }

    // Online fetch function
    function fetchLoyaltyPointsOnline(contact) {
        console.log('Fetching loyalty points ONLINE for:', contact);

        fetch(`/check-customer/${contact}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    availablePoints = data.loyalty_points || 0;
                    redeemRate = parseFloat(data.redeem_amt) || 1;

                    // Cache the data for offline use
                    if (typeof posOfflineManager !== 'undefined') {
                        const customerData = posOfflineManager.customerManager.customerCache.get(contact) || {};
                        customerData.loyalty_points = availablePoints;
                        customerData.redeem_amt = redeemRate;
                        posOfflineManager.customerManager.customerCache.set(contact, customerData);
                        posOfflineManager.customerManager.saveCustomerCache();
                    }

                    setupLoyaltyModal();

                    // Show online indicator
                    if (typeof posOfflineManager !== 'undefined') {
                        posOfflineManager.showNotification('Loyalty points loaded (Online)', 'info');
                    }
                } else {
                    alert('Customer not found. Please add the customer first.');
                }
            })
            .catch(err => {
                console.error('Online loyalty fetch failed:', err);

                // Fallback to offline mode if online fails
                if (typeof posOfflineManager !== 'undefined') {
                    posOfflineManager.showNotification('Connection failed. Trying offline data...', 'warning');
                    fetchLoyaltyPointsOffline(contact);
                } else {
                    alert('Error loading customer loyalty points.');
                }
            });
    }

    // Offline fetch function
    function fetchLoyaltyPointsOffline(contact) {
        console.log('Fetching loyalty points OFFLINE for:', contact);

        let foundCustomer = null;

        // Check offline manager cache first
        if (typeof posOfflineManager !== 'undefined') {
            // Check customer cache
            if (posOfflineManager.customerManager.customerCache.has(contact)) {
                foundCustomer = posOfflineManager.customerManager.customerCache.get(contact);
                console.log('Found customer in cache:', foundCustomer);
            }

            // Check offline customers
            if (!foundCustomer) {
                foundCustomer = posOfflineManager.customerManager.findOfflineCustomer(contact);
                console.log('Found customer in offline storage:', foundCustomer);
            }
        }

        // Fallback to localStorage
        if (!foundCustomer) {
            try {
                const stored = localStorage.getItem('pos_customer_cache');
                if (stored) {
                    const cacheArray = JSON.parse(stored);
                    const cache = new Map(cacheArray);
                    if (cache.has(contact)) {
                        foundCustomer = cache.get(contact);
                        console.log('Found customer in localStorage cache:', foundCustomer);
                    }
                }
            } catch (error) {
                console.error('Error reading localStorage:', error);
            }
        }

        if (foundCustomer) {
            availablePoints = foundCustomer.loyalty_points || 0;
            redeemRate = parseFloat(foundCustomer.redeem_amt) || 1;

            setupLoyaltyModal();

            // Show offline indicator
            if (typeof posOfflineManager !== 'undefined') {
                posOfflineManager.showNotification('Loyalty points loaded (Offline)', 'warning');
            }
        } else {
            // Customer not found offline
            availablePoints = 0;
            redeemRate = 1;

            setupLoyaltyModal();

            const message = 'Customer found but no loyalty data available offline. Points set to 0.';
            if (typeof posOfflineManager !== 'undefined') {
                posOfflineManager.showNotification(message, 'warning');
            } else {
                alert(message);
            }
        }
    }

    // Setup loyalty modal data
    function setupLoyaltyModal() {
        document.getElementById('availablePoints').textContent = availablePoints;

        const maxAmt = Math.floor(billTotal / 2);
        maxRedeemablePoints = Math.min(Math.floor(maxAmt / redeemRate), availablePoints);
        maxRedeemableAmount = maxRedeemablePoints * redeemRate;

        document.getElementById('maxRedeemPoints').textContent = `${maxRedeemablePoints} (₹ ${maxRedeemableAmount.toFixed(2)})`;

        document.getElementById('redeemPoints').value = '';
        document.getElementById('redeemAmount').value = '';
        document.getElementById('remainingAmount').textContent = billTotal.toFixed(2);

        updateUsedPointsDisplay();
        updateRemainingPointsDisplay();
    }

    function updateUsedPointsDisplay() {
        const usedPointsElement = document.getElementById('usedPointsDisplay');
        const usedPointsContainer = document.getElementById('usedPointsContainer');

        if (usedPointsElement && usedPointsContainer) {
            if (usedPointsInSession > 0) {
                usedPointsElement.textContent = `${usedPointsInSession} Points`;
                usedPointsContainer.style.display = 'block';
            } else {
                usedPointsContainer.style.display = 'none';
            }
        }
    }

    function updateRemainingPointsDisplay() {
        const remainingPointsElement = document.getElementById('remainingPoints');
        const remainingPointsContainer = document.getElementById('remainingPointsContainer');

        if (remainingPointsElement && remainingPointsContainer) {
            const remaining = availablePoints - usedPointsInSession;
            if (usedPointsInSession > 0) {
                remainingPointsElement.textContent = remaining;
                remainingPointsContainer.style.display = 'block';
            } else {
                remainingPointsContainer.style.display = 'none';
            }
        }
    }

    function setupRedeemPointsListener() {
        const redeemPointsInput = document.getElementById('redeemPoints');
        redeemPointsInput?.addEventListener('input', function() {
            let pts = parseInt(this.value) || 0;

            if (pts > maxRedeemablePoints) {
                alert(`You can redeem up to ${maxRedeemablePoints} points only.`);
                pts = maxRedeemablePoints;
                this.value = pts;
            }

            const amt = pts * redeemRate;
            document.getElementById('redeemAmount').value = amt.toFixed(2);
            document.getElementById('remainingAmount').textContent = (billTotal - amt).toFixed(2);
        });
    }

    function setupRedeemAmountListener() {
        const redeemAmountInput = document.getElementById('redeemAmount');
        redeemAmountInput?.addEventListener('input', function() {
            let amt = parseFloat(this.value) || 0;

            if (amt > maxRedeemableAmount) {
                alert(`You can redeem up to ₹${maxRedeemableAmount.toFixed(2)} only.`);
                amt = maxRedeemableAmount;
                this.value = amt.toFixed(2);
            }

            const pts = Math.floor(amt / redeemRate);
            document.getElementById('redeemPoints').value = pts;
            document.getElementById('remainingAmount').textContent = (billTotal - amt).toFixed(2);
        });
    }

    // Enhanced form submission with offline support
    function setupLoyaltyFormListener() {
        const form = document.getElementById('loyaltyForm');
        form?.addEventListener('submit', function(e) {
            e.preventDefault();

            const usedPoints = parseInt(document.getElementById('redeemPoints').value) || 0;
            const usedAmount = parseFloat(document.getElementById('redeemAmount').value) || 0;
            const remaining = parseFloat(document.getElementById('remainingAmount').textContent) || billTotal;

            if (usedPoints > maxRedeemablePoints || usedAmount > maxRedeemableAmount) {
                alert(`You cannot redeem more than ${maxRedeemablePoints} points or ₹${maxRedeemableAmount.toFixed(2)}.`);
                return;
            }

            usedPointsInSession = usedPoints;

            updateUsedPointsDisplay();
            updateRemainingPointsDisplay();
            updateMainBillDisplay(usedAmount, remaining);

            // Save values as hidden inputs for bill processing
            document.getElementById('appliedPoints')?.remove();
            document.getElementById('appliedAmount')?.remove();

            const hiddenPoints = document.createElement('input');
            hiddenPoints.type = 'hidden';
            hiddenPoints.id = 'appliedPoints';
            hiddenPoints.name = 'loyalty_points_used';
            hiddenPoints.value = usedPoints;

            const hiddenAmt = document.createElement('input');
            hiddenAmt.type = 'hidden';
            hiddenAmt.id = 'appliedAmount';
            hiddenAmt.name = 'loyalty_amount_used';
            hiddenAmt.value = usedAmount;

            document.body.appendChild(hiddenPoints);
            document.body.appendChild(hiddenAmt);

            // Store loyalty data for offline bill saving
            if (typeof posOfflineManager !== 'undefined') {
                // Store in session for bill creation
                sessionStorage.setItem('loyaltyPointsUsed', usedPoints.toString());
                sessionStorage.setItem('loyaltyAmountUsed', usedAmount.toString());
                sessionStorage.setItem('loyaltyCustomerContact', currentCustomerContact);

                console.log('Loyalty points stored for offline bill:', {
                    points: usedPoints,
                    amount: usedAmount,
                    contact: currentCustomerContact
                });
            }

            const remainingPoints = availablePoints - usedPoints;
            document.getElementById('remainingPoints').textContent = remainingPoints;
            document.getElementById('remainingPointsContainer').style.display = 'block';

            const connectionStatus = (typeof posOfflineManager !== 'undefined' && !posOfflineManager.isOnline) ? ' (Offline)' : '';
            alert(
                `Loyalty Points Applied${connectionStatus}!\nPoints Used: ${usedPoints}\nAmount Redeemed: ₹${usedAmount.toFixed(2)}\nRemaining Amount: ₹${remaining.toFixed(2)}`
            );

            // Show notification
            if (typeof posOfflineManager !== 'undefined') {
                const notificationMessage = posOfflineManager.isOnline ?
                    'Loyalty points applied successfully!' :
                    'Loyalty points applied offline!';
                posOfflineManager.showNotification(notificationMessage, 'success');
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('lp'));
            modal?.hide();
        });
    }

    function updateMainBillDisplay(discountAmount, remainingAmount) {
        const loyaltyDiscountElement = document.getElementById('loyaltyDiscount');
        if (loyaltyDiscountElement) {
            loyaltyDiscountElement.textContent = `₹${discountAmount.toFixed(2)}`;
        }

        const finalTotalElement = document.getElementById('finalTotal');
        if (finalTotalElement) {
            finalTotalElement.textContent = `₹${remainingAmount.toFixed(2)}`;
        }

        const grandTotalElement = document.getElementById('grandTotal');
        if (grandTotalElement) {
            grandTotalElement.textContent = `₹ ${remainingAmount.toFixed(2)}`;
        }
    }

    function setupLoyaltyModalTrigger() {
        const btn = document.querySelector('[data-bs-target="#lp"]');
        btn?.addEventListener('click', openLoyaltyModal);
    }

    // Enhanced customer change listener with offline support
    function setupCustomerChangeListener() {
        const contactInput = document.getElementById('contact');
        contactInput?.addEventListener('change', function() {
            if (this.value !== currentCustomerContact) {
                usedPointsInSession = 0;
                updateUsedPointsDisplay();
                updateRemainingPointsDisplay();
                currentCustomerContact = this.value;

                // Clear loyalty session data when customer changes
                sessionStorage.removeItem('loyaltyPointsUsed');
                sessionStorage.removeItem('loyaltyAmountUsed');
                sessionStorage.removeItem('loyaltyCustomerContact');
            }
        });
    }

    // Function to get loyalty data for bill saving (used by the main POS system)
    function getLoyaltyDataForBillSaving() {
        const loyaltyPointsUsed = parseInt(sessionStorage.getItem('loyaltyPointsUsed')) || 0;
        const loyaltyAmountUsed = parseFloat(sessionStorage.getItem('loyaltyAmountUsed')) || 0;
        const loyaltyCustomerContact = sessionStorage.getItem('loyaltyCustomerContact') || '';

        return {
            loyalty_points_used: loyaltyPointsUsed,
            loyalty_amount_used: loyaltyAmountUsed,
            loyalty_customer_contact: loyaltyCustomerContact
        };
    }

    // Enhanced loyalty points sync function for offline bills
    function syncLoyaltyPointsWithBill(billData) {
        if (typeof posOfflineManager === 'undefined') {
            return billData;
        }

        const loyaltyData = getLoyaltyDataForBillSaving();

        // Add loyalty data to bill payload
        if (loyaltyData.loyalty_points_used > 0) {
            billData.loyalty_points_used = loyaltyData.loyalty_points_used;
            billData.loyalty_amount_used = loyaltyData.loyalty_amount_used;
            billData.loyalty_customer_contact = loyaltyData.loyalty_customer_contact;

            console.log('Loyalty data synced with bill:', loyaltyData);
        }

        return billData;
    }

    // Clear loyalty data after successful bill save
    function clearLoyaltySessionData() {
        sessionStorage.removeItem('loyaltyPointsUsed');
        sessionStorage.removeItem('loyaltyAmountUsed');
        sessionStorage.removeItem('loyaltyCustomerContact');
        usedPointsInSession = 0;
        updateUsedPointsDisplay();
        updateRemainingPointsDisplay();
    }

    // Init all listeners
    document.addEventListener('DOMContentLoaded', function() {
        setupRedeemPointsListener();
        setupRedeemAmountListener();
        setupLoyaltyFormListener();
        setupLoyaltyModalTrigger();
        setupCustomerChangeListener();

        console.log('Enhanced Loyalty Points system with Online/Offline support initialized');
    });

    // Export functions for global access
    window.getLoyaltyDataForBillSaving = getLoyaltyDataForBillSaving;
    window.syncLoyaltyPointsWithBill = syncLoyaltyPointsWithBill;
    window.clearLoyaltySessionData = clearLoyaltySessionData;
    window.openLoyaltyModal = openLoyaltyModal;
</script>

<script>
    // Enhanced loyalty form submission with offline support
    document.getElementById('loyaltyForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const usedPoints = parseInt(document.getElementById('redeemPoints').value) || 0;
        const usedAmount = parseFloat(document.getElementById('redeemAmount').value) || 0;
        const remaining = parseFloat(document.getElementById('remainingAmount').textContent) || billTotal;

        // Update bill details total
        document.getElementById('grandTotal').textContent = `₹ ${remaining.toFixed(2)}`;

        // Store hidden values for submission
        document.getElementById('appliedPoints')?.remove();
        document.getElementById('appliedAmount')?.remove();

        const hiddenPoints = document.createElement('input');
        hiddenPoints.type = 'hidden';
        hiddenPoints.id = 'appliedPoints';
        hiddenPoints.name = 'loyalty_points_used';
        hiddenPoints.value = usedPoints;

        const hiddenAmt = document.createElement('input');
        hiddenAmt.type = 'hidden';
        hiddenAmt.id = 'appliedAmount';
        hiddenAmt.name = 'loyalty_amount_used';
        hiddenAmt.value = usedAmount;

        document.body.appendChild(hiddenPoints);
        document.body.appendChild(hiddenAmt);

        // Enhanced: Store for offline bill processing
        if (typeof posOfflineManager !== 'undefined') {
            sessionStorage.setItem('loyaltyPointsUsed', usedPoints.toString());
            sessionStorage.setItem('loyaltyAmountUsed', usedAmount.toString());
            sessionStorage.setItem('loyaltyCustomerContact', currentCustomerContact);
        }

        // Show Remaining Points = Available - Redeemed
        const availablePointsText = document.getElementById('loyaltyPointsDisplay')?.textContent;
        const availablePointsFromDisplay = availablePointsText ? parseInt(availablePointsText.replace('Points', '').trim()) || 0 : availablePoints;

        if (usedPoints > availablePointsFromDisplay) {
            alert("You cannot redeem more points than available.");
            return;
        }

        const remainingPoints = availablePointsFromDisplay - usedPoints;
        if (document.getElementById('remainingPoints')) {
            document.getElementById('remainingPoints').textContent = remainingPoints;
            document.getElementById('remainingPointsContainer').style.display = 'block';
        }

        // Enhanced success message with online/offline status
        const connectionStatus = (typeof posOfflineManager !== 'undefined' && !posOfflineManager.isOnline) ? ' (Offline Mode)' : '';
        alert(`Loyalty Applied${connectionStatus}!\nPoints: ${usedPoints}\nRedeemed ₹${usedAmount}\nRemaining ₹${remaining}`);

        // Show notification if offline manager available
        if (typeof posOfflineManager !== 'undefined') {
            const notificationMessage = posOfflineManager.isOnline ?
                'Loyalty points applied successfully!' :
                'Loyalty points applied in offline mode!';
            posOfflineManager.showNotification(notificationMessage, 'success');
        }

        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('lp'));
        modal.hide();
    });
</script>

<div class="modal fade" id="giftcards" tabindex="-1" aria-labelledby="giftcardsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Gift Cards & Vouchers</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="giftCardForm">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <div class="d-flex align-items-center mt-1 gap-3">
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="radio" name="cardType" value="gift_card" id="gifts">
                                    <label class="form-check-label mb-0" for="gifts">Gift Cards</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="radio" name="cardType" value="voucher" id="vouchers">
                                    <label class="form-check-label mb-0" for="vouchers">Vouchers</label>
                                </div>
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input mb-auto" type="radio" name="cardType" value="return_voucher" id="returnVouchers">
                                    <label class="form-check-label mb-0" for="returnVouchers">Return Voucher</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="cardno">Card/Voucher Number</label>
                            <div class="inpselectflex">
                                <input type="text" class="form-control border-0" name="" id="cardno" placeholder="Enter card/voucher number">
                                <h6 class="text-center">Check Balance</h6>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label>Available Amount</label>
                            <h6>₹ 0</h6>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label>Maximum Redeemable Amount</label>
                            <h6>₹ 0</h6>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label>Card Status</label>
                            <h6>Invalid</h6>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label>Expiry Date</label>
                            <h6>N/A</h6>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="redeemamt">Enter Amount to Redeem</label>
                            <input type="number" class="form-control" name="" id="redeemamt" min="0" disabled>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-2">
                            <label for="remainamt">Remaining Amount (Balance)</label>
                            <input type="number" class="form-control" name="" id="remainamt" min="0" readonly>
                        </div>

                        <div class="col-sm-12 col-md-12 my-3 text-center">
                            <h6>Discount Only Applicable for Mamaearth Products</h6>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mx-auto mt-3 gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Apply</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Gift Cards & Vouchers System
    let appliedGiftCards = []; // Store applied gift cards/vouchers
    let totalGiftCardDiscount = 0; // Total discount from gift cards/vouchers

    // Initialize Gift Card/Voucher functionality
    function initializeGiftCardSystem() {
        const cardNumberInput = document.getElementById('cardno');
        const checkBalanceBtn = document.querySelector('.inpselectflex h6');
        const redeemAmountInput = document.getElementById('redeemamt');
        const giftCardForm = document.querySelector('#giftcards form');

        // Check balance when card number is entered
        if (checkBalanceBtn) {
            checkBalanceBtn.addEventListener('click', checkGiftCardBalance);
        }

        // Auto-calculate remaining balance when redeem amount is entered
        if (redeemAmountInput) {
            redeemAmountInput.addEventListener('input', updateGiftCardBalance);
        }

        // Handle form submission
        if (giftCardForm) {
            giftCardForm.addEventListener('submit', applyGiftCard);
        }

        // Reset gift cards when customer changes
        const contactInput = document.getElementById('contact');
        if (contactInput) {
            contactInput.addEventListener('change', resetGiftCards);
        }
    }

    // Check Gift Card/Voucher Balance
    function checkGiftCardBalance() {
        const cardNumber = document.getElementById('cardno').value.trim();
        const cardType = document.querySelector('input[name="cardType"]:checked')?.value;

        if (!cardNumber) {
            alert('Please enter a card/voucher number');
            return;
        }

        if (!cardType) {
            alert('Please select a card type (Gift Card, Voucher, or Return Voucher)');
            return;
        }

        // Show loading state
        updateGiftCardDisplay('Checking...', '0', 'Checking...', 'Checking...');

        // Determine the appropriate endpoint based on card type
        const endpoint = cardType === 'return_voucher' ?
            '/check-return-voucher-balance' :
            '/check-gift-card-balance';

        // Make AJAX request to check balance
        fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    card_number: cardNumber,
                    card_type: cardType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    // Update display with card details
                    updateGiftCardDisplay(
                        data.available_amount,
                        data.max_redeemable,
                        data.card_status,
                        data.expiry_date
                    );

                    // Enable redeem amount input
                    document.getElementById('redeemamt').disabled = false;
                    document.getElementById('redeemamt').max = data.max_redeemable;

                    // Show applicable products info if available
                    if (data.applicable_products) {
                        const infoDiv = document.querySelector('.col-sm-12.col-md-12.my-3.text-center h6');
                        infoDiv.textContent = data.applicable_products;
                    }
                } else {
                    alert(data.message || 'Invalid card/voucher number');
                    resetGiftCardDisplay();
                }
            })
            .catch(error => {
                console.error('Error checking balance:', error);
                alert('Error checking card balance. Please try again.');
                resetGiftCardDisplay();
            });
    }

    // Update Gift Card Display
    function updateGiftCardDisplay(availableAmount, maxRedeemable, status, expiryDate) {
        const elements = {
            available: document.querySelector('.col-sm-12.col-md-6.mb-2:nth-of-type(3) h6'),
            maxRedeemable: document.querySelector('.col-sm-12.col-md-6.mb-2:nth-of-type(4) h6'),
            status: document.querySelector('.col-sm-12.col-md-6.mb-2:nth-of-type(5) h6'),
            expiry: document.querySelector('.col-sm-12.col-md-6.mb-2:nth-of-type(6) h6')
        };

        if (elements.available) elements.available.textContent = `₹ ${availableAmount}`;
        if (elements.maxRedeemable) elements.maxRedeemable.textContent = `₹ ${maxRedeemable}`;
        if (elements.status) elements.status.textContent = status;
        if (elements.expiry) elements.expiry.textContent = expiryDate;
    }

    // Reset Gift Card Display
    function resetGiftCardDisplay() {
        updateGiftCardDisplay('0', '0', 'Invalid', 'N/A');
        document.getElementById('redeemamt').disabled = true;
        document.getElementById('redeemamt').value = '';
        document.getElementById('remainamt').value = '';
    }

    // Simple fix for the updateGiftCardBalance function
    function updateGiftCardBalance() {
        const redeemAmountInput = document.getElementById('redeemamt');
        const remainingAmountInput = document.getElementById('remainamt');

        if (!redeemAmountInput || !remainingAmountInput) {
            return;
        }

        const redeemAmount = parseFloat(redeemAmountInput.value) || 0;

        // Get available amount from the displayed text
        // Look for the Available Amount field in the modal
        const availableAmountElements = document.querySelectorAll('.col-sm-12.col-md-6.mb-2 h6');
        let availableAmount = 0;

        // The Available Amount is typically the first h6 element in the gift card details
        // Check each h6 element to find the one with rupee symbol and amount
        for (let element of availableAmountElements) {
            const text = element.textContent || element.innerText || '';
            if (text.includes('₹') && !text.includes('Invalid') && !text.includes('N/A') && !text.includes('Checking')) {
                // Extract numeric value
                const numericValue = parseFloat(text.replace(/₹|,|\s/g, ''));
                if (!isNaN(numericValue) && numericValue > 0) {
                    availableAmount = numericValue;
                    break;
                }
            }
        }

        // Alternative method: Look specifically in the Available Amount row
        const availableAmountRow = document.querySelector('label:contains("Available Amount")');
        if (!availableAmount && availableAmountRow) {
            const nextH6 = availableAmountRow.parentElement.querySelector('h6');
            if (nextH6) {
                const text = nextH6.textContent || nextH6.innerText || '';
                availableAmount = parseFloat(text.replace(/₹|,|\s/g, '')) || 0;
            }
        }

        // If still not found, try a more direct approach
        if (!availableAmount) {
            // Look for any element containing "10,000.00" or similar pattern
            const allElements = document.querySelectorAll('#giftcards h6');
            for (let element of allElements) {
                const text = element.textContent || element.innerText || '';
                const cleanText = text.replace(/₹|,|\s/g, '');
                const numericValue = parseFloat(cleanText);

                // Check if this looks like an amount (positive number, reasonable range)
                if (!isNaN(numericValue) && numericValue > 0 && numericValue >= redeemAmount) {
                    availableAmount = numericValue;
                    break;
                }
            }
        }

        console.log('Available Amount:', availableAmount);
        console.log('Redeem Amount:', redeemAmount);

        // Calculate remaining balance
        const remainingBalance = Math.max(0, availableAmount - redeemAmount);

        console.log('Remaining Balance:', remainingBalance);

        // Update the remaining amount field
        remainingAmountInput.value = remainingBalance.toFixed(2);
    }

    // Enhanced version that works with your specific HTML structure
    function updateGiftCardBalanceEnhanced() {
        const redeemAmountInput = document.getElementById('redeemamt');
        const remainingAmountInput = document.getElementById('remainamt');

        if (!redeemAmountInput || !remainingAmountInput) {
            return;
        }

        const redeemAmount = parseFloat(redeemAmountInput.value) || 0;

        // Method 1: Get from the specific Available Amount field
        let availableAmount = 0;

        // Find the Available Amount label and get its corresponding value
        const labels = document.querySelectorAll('#giftcards label');
        for (let label of labels) {
            if (label.textContent.includes('Available Amount')) {
                // Get the h6 element in the same parent container
                const parent = label.closest('.col-sm-12, .col-md-6');
                if (parent) {
                    const h6Element = parent.querySelector('h6');
                    if (h6Element) {
                        const text = h6Element.textContent || h6Element.innerText || '';
                        const numericValue = parseFloat(text.replace(/₹|,|\s/g, ''));
                        if (!isNaN(numericValue)) {
                            availableAmount = numericValue;
                            break;
                        }
                    }
                }
            }
        }

        // Method 2: If method 1 fails, try to find by position (third h6 element as per your original code)
        if (!availableAmount) {
            const h6Elements = document.querySelectorAll('#giftcards .col-sm-12.col-md-6.mb-2 h6');
            if (h6Elements.length >= 1) {
                const text = h6Elements[0].textContent || h6Elements[0].innerText || '';
                const numericValue = parseFloat(text.replace(/₹|,|\s/g, ''));
                if (!isNaN(numericValue)) {
                    availableAmount = numericValue;
                }
            }
        }

        // Method 3: Look for any h6 with rupee symbol and reasonable amount
        if (!availableAmount) {
            const allH6 = document.querySelectorAll('#giftcards h6');
            for (let h6 of allH6) {
                const text = h6.textContent || h6.innerText || '';
                if (text.includes('₹') && !text.includes('Invalid') && !text.includes('N/A')) {
                    const numericValue = parseFloat(text.replace(/₹|,|\s/g, ''));
                    if (!isNaN(numericValue) && numericValue > 0) {
                        availableAmount = numericValue;
                        break;
                    }
                }
            }
        }

        console.log('Debug - Available Amount:', availableAmount, 'Redeem Amount:', redeemAmount);

        // Calculate remaining balance
        const remainingBalance = Math.max(0, availableAmount - redeemAmount);

        // Update the remaining amount field
        remainingAmountInput.value = remainingBalance.toFixed(2);

        // Add visual feedback
        if (redeemAmount > 0 && availableAmount > 0) {
            remainingAmountInput.style.backgroundColor = '#e8f5e8';
        } else {
            remainingAmountInput.style.backgroundColor = '';
        }
    }

    // Replace the original function
    window.updateGiftCardBalance = updateGiftCardBalanceEnhanced;

    // Also add event listeners directly to ensure they work
    document.addEventListener('DOMContentLoaded', function() {
        const redeemInput = document.getElementById('redeemamt');
        if (redeemInput) {
            // Remove any existing listeners and add new ones
            redeemInput.removeEventListener('input', updateGiftCardBalance);
            redeemInput.removeEventListener('keyup', updateGiftCardBalance);
            redeemInput.removeEventListener('change', updateGiftCardBalance);

            redeemInput.addEventListener('input', updateGiftCardBalanceEnhanced);
            redeemInput.addEventListener('keyup', updateGiftCardBalanceEnhanced);
            redeemInput.addEventListener('change', updateGiftCardBalanceEnhanced);

            console.log('Gift card balance calculation listeners added');
        }
    });

    // For immediate testing, you can also call this function manually
    // updateGiftCardBalanceEnhanced();

    // Apply Gift Card/Voucher
    function applyGiftCard(e) {
        e.preventDefault();

        const cardNumber = document.getElementById('cardno').value.trim();
        const redeemAmount = parseFloat(document.getElementById('redeemamt').value) || 0;
        const selectedRadio = document.querySelector('input[name="cardType"]:checked');

        if (!selectedRadio) {
            alert('Please select a card type');
            return;
        }

        const cardType = selectedRadio.value;

        if (!cardNumber) {
            alert('Please enter a card/voucher number');
            return;
        }

        if (redeemAmount <= 0) {
            alert('Please enter a valid redeem amount');
            return;
        }

        // Check if card is already applied
        const isAlreadyApplied = appliedGiftCards.some(card => card.card_number === cardNumber);
        if (isAlreadyApplied) {
            alert('This card/voucher has already been applied');
            return;
        }

        // Get current bill total
        const currentGrandTotal = parseFloat(document.getElementById('grandTotal').textContent.replace(/₹|,|\s/g, '')) || 0;

        // Validate redeem amount doesn't exceed bill total
        if (redeemAmount > currentGrandTotal) {
            alert('Redeem amount cannot exceed bill total');
            return;
        }

        // Apply the gift card/voucher/return voucher
        const giftCardData = {
            card_number: cardNumber,
            card_type: cardType,
            redeem_amount: redeemAmount,
            remaining_balance: parseFloat(document.getElementById('remainamt').value) || 0
        };

        // Add to applied gift cards array
        appliedGiftCards.push(giftCardData);
        totalGiftCardDiscount += redeemAmount;

        // Update bill totals
        updateBillTotalsWithGiftCard();

        // Show success message based on type
        const cardTypeLabel = cardType === 'gift_card' ? 'Gift Card' :
            cardType === 'voucher' ? 'Voucher' : 'Return Voucher';
        alert(`${cardTypeLabel} applied successfully!\nDiscount: ₹${redeemAmount.toFixed(2)}`);

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('giftcards'));
        if (modal) {
            modal.hide();
        }

        // Reset form
        resetGiftCardForm();

        // Update applied gift cards display
        updateAppliedGiftCardsDisplay();
    }


    // Update Bill Totals with Gift Card Discount
    function updateBillTotalsWithGiftCard() {
        // Get current totals
        const subTotal = parseFloat(document.getElementById('subTotal').textContent.replace(/₹|,|\s/g, '')) || 0;
        // const itemDiscounts = parseFloat(document.getElementById('itemDiscounts').textContent.replace(/₹|,|\s/g, '')) || 0;
        const billDiscount = parseFloat(document.getElementById('billDiscount').textContent.replace(/₹|,|\s/g, '')) || 0;
        // const totalTax = parseFloat(document.getElementById('totalTax').textContent.replace(/₹|,|\s/g, '')) || 0;
        const additionalCharges = parseFloat(document.getElementById('additionalCharges').textContent.replace(/₹|,|\s/g, '')) || 0;

        // Get loyalty points discount amount
        const loyaltyPointsDiscount = parseFloat(document.getElementById('appliedAmount')?.value) || 0;

        // Calculate new grand total with gift card discount and loyalty points discount
        const grandTotal = subTotal - billDiscount + additionalCharges - totalGiftCardDiscount - loyaltyPointsDiscount;

        // Update grand total display
        document.getElementById('grandTotal').textContent = `₹ ${Math.max(0, grandTotal).toFixed(2)}`;

        // Update gift card discount display (add this row if it doesn't exist)
        updateGiftCardDiscountDisplay();

        // Recalculate change
        if (typeof calculateChange === 'function') {
            calculateChange();
        }
    }

    // Update Gift Card Discount Display in Bill Details
    function updateGiftCardDiscountDisplay() {
        let giftCardRow = document.getElementById('giftCardDiscountRow');

        if (!giftCardRow && totalGiftCardDiscount > 0) {
            // Create gift card discount row if it doesn't exist
            const billDetailsTable = document.querySelector('.details .table tbody');
            if (billDetailsTable) {
                const newRow = document.createElement('tr');
                newRow.id = 'giftCardDiscountRow';
                newRow.style.color = '#28a745'; // Green color for gift card discount
                newRow.innerHTML = `
                <th class="text-start">Gift Cards & Vouchers</th>
                <td class="text-end" id="giftCardDiscount">₹ ${totalGiftCardDiscount.toFixed(2)}</td>
            `;

                // Insert before grand total row
                const grandTotalRow = billDetailsTable.parentElement.querySelector('tfoot tr');
                if (grandTotalRow) {
                    grandTotalRow.parentElement.insertBefore(newRow, grandTotalRow);
                } else {
                    billDetailsTable.appendChild(newRow);
                }
            }
        } else if (giftCardRow) {
            // Update existing row
            const giftCardDiscountEl = document.getElementById('giftCardDiscount');
            if (giftCardDiscountEl) {
                if (totalGiftCardDiscount > 0) {
                    giftCardDiscountEl.textContent = `₹ ${totalGiftCardDiscount.toFixed(2)}`;
                    giftCardRow.style.display = 'table-row';
                } else {
                    giftCardRow.style.display = 'none';
                }
            }
        }
    }

    // Update Applied Gift Cards Display
    function updateAppliedGiftCardsDisplay() {
        // Add a small display area to show applied gift cards
        let appliedCardsDisplay = document.getElementById('appliedGiftCardsDisplay');

        if (!appliedCardsDisplay && appliedGiftCards.length > 0) {
            // Create display area
            const billDetailsSection = document.querySelector('.bill-body-right .details:nth-of-type(2)');
            if (billDetailsSection) {
                const displayDiv = document.createElement('div');
                displayDiv.id = 'appliedGiftCardsDisplay';
                displayDiv.className = 'mt-2';
                displayDiv.innerHTML = `
                <small class="text-success">
                    <i class="fas fa-gift"></i> Applied Gift Cards/Vouchers: ${appliedGiftCards.length}
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearAllGiftCards()">
                        Clear All
                    </button>
                </small>
            `;
                billDetailsSection.appendChild(displayDiv);
            }
        } else if (appliedCardsDisplay) {
            if (appliedGiftCards.length > 0) {
                appliedCardsDisplay.innerHTML = `
                <small class="text-success">
                    <i class="fas fa-gift"></i> Applied Gift Cards/Vouchers: ${appliedGiftCards.length}
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearAllGiftCards()">
                        Clear All
                    </button>
                </small>
            `;
            } else {
                appliedCardsDisplay.remove();
            }
        }
    }

    // Clear All Gift Cards
    function clearAllGiftCards() {
        if (appliedGiftCards.length === 0) return;

        if (confirm('Are you sure you want to remove all applied gift cards/vouchers?')) {
            appliedGiftCards = [];
            totalGiftCardDiscount = 0;

            // Update bill totals
            updateBillTotalsWithGiftCard();

            // Update display
            updateAppliedGiftCardsDisplay();

            alert('All gift cards/vouchers have been removed');
        }
    }

    // Reset Gift Cards when customer changes
    function resetGiftCards() {
        appliedGiftCards = [];
        totalGiftCardDiscount = 0;
        updateBillTotalsWithGiftCard();
        updateAppliedGiftCardsDisplay();
    }

    // Reset Gift Card Form
    function resetGiftCardForm() {
        document.getElementById('cardno').value = '';
        document.getElementById('redeemamt').value = '';
        document.getElementById('remainamt').value = '';
        document.getElementById('gifts').checked = false;
        document.getElementById('vouchers').checked = false;
        resetGiftCardDisplay();
    }

    // Modify the existing updateTotals function to include gift card discount
    const originalUpdateTotals = window.updateTotals;
    window.updateTotals = function() {
        // Call original function first
        if (typeof originalUpdateTotals === 'function') {
            originalUpdateTotals();
        }

        // Then apply gift card discount
        if (totalGiftCardDiscount > 0) {
            updateBillTotalsWithGiftCard();
        }
    };

    // Get gift card data for saving to database
    function getGiftCardDataForSaving() {
        return {
            applied_gift_cards: appliedGiftCards,
            total_gift_card_discount: totalGiftCardDiscount
        };
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeGiftCardSystem();
    });

    // Add to the save bill functionality
    const originalSaveBill = document.getElementById('savePrintBtn');
    if (originalSaveBill) {
        originalSaveBill.addEventListener('click', function(e) {
            // Add gift card data to the bill data before saving
            const giftCardData = getGiftCardDataForSaving();

            // You can access this data in your save bill function
            // by adding it to the payload
            console.log('Gift Card Data to save:', giftCardData);

            // The existing save bill function will handle the rest
        });
    }
</script>
<!-- Corporate Bill Modal -->
<div class="modal fade" id="corporatebill" tabindex="-1" aria-labelledby="corporatebillLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Corporate Bill</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="gst">GST Number</label>
                            <input type="text" class="form-control" name="" id="gst">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mx-auto mt-3 gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<!-- Customer Modal -->
<div class="modal fade" id="customer" tabindex="-1" aria-labelledby="customerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="m-0">Add Customer</h4>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="contact">Contact Number</label>
                            <input type="number" class="form-control" name="contact" id="contact" oninput="checkCustomer()" required>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-2">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <!-- Button to add new customer (only if not found) -->
                        <div class="col-12 mb-2" id="addCustomerBtnDiv" style="display: none;">
                            <button type="button" class="btn btn-success btn-sm" onclick="addNewCustomer()">+ Add New Item</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mx-auto mt-3 gap-2">
                            <button type="button" data-bs-dismiss="modal" class="cancelbtn w-50">Cancel</button>
                            <button type="submit" class="modalbtn w-50">Save</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<script>
    function checkCustomer() {
        const contact = document.getElementById('contact').value;
        if (contact.length >= 6) {
            fetch(`/check-customer/${contact}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        document.getElementById('name').value = data.name;
                        document.getElementById('addCustomerBtnDiv').style.display = 'none';
                        updateCustomerDetails(data.name, contact, data.loyalty_points);
                    } else {
                        document.getElementById('name').value = '';
                        document.getElementById('addCustomerBtnDiv').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error checking customer:', error);
                });
        }
    }

    function addNewCustomer() {
        const name = document.getElementById('name').value;
        const contact = document.getElementById('contact').value;

        if (!name || !contact) {
            alert("Please enter both name and contact.");
            return;
        }

        // Fixed: Changed URL from '/store-customer' to '/add-customer' to match your Laravel route
        fetch('/add-customer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name,
                    contact
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    updateCustomerDetails(data.name, contact, data.loyalty_points);
                    document.getElementById('customerForm').reset();
                    bootstrap.Modal.getInstance(document.getElementById('customer')).hide();
                    alert('Customer added successfully!');
                } else {
                    // Handle validation errors or other issues
                    if (data.errors) {
                        let errorMessages = Object.values(data.errors).flat().join('\n');
                        alert('Validation Error:\n' + errorMessages);
                    } else {
                        alert(data.message || 'Customer could not be added.');
                    }
                }
            })
            .catch(error => {
                console.error('Error adding customer:', error);
                alert('An error occurred while adding the customer.');
            });
    }

    function updateCustomerDetails(name, contact, points) {
        document.getElementById('selectedCustomer').textContent = `${name} - ${contact}`;
        document.getElementById('loyaltyPointsDisplay').textContent = `${points} Points`;

        // Also set hidden inputs if they exist
        const customerNameInput = document.getElementById('customer_name');
        const customerContactInput = document.getElementById('customer_contact');

        if (customerNameInput) customerNameInput.value = name;
        if (customerContactInput) customerContactInput.value = contact;
    }

    function updateSelectedCustomer(name, contact) {
        document.getElementById('selectedCustomer').textContent = `${name} - ${contact}`;

        const customerNameInput = document.getElementById('customer_name');
        const customerContactInput = document.getElementById('customer_contact');

        if (customerNameInput) customerNameInput.value = name;
        if (customerContactInput) customerContactInput.value = contact;
    }

    // Form submission handler
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form submit + page reload

        let name = document.getElementById('name').value;
        let contact = document.getElementById('contact').value;

        if (!name || !contact) {
            alert("Please enter both name and contact.");
            return;
        }

        // First, check if this customer already exists
        fetch(`/check-customer/${contact}`)
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    // Existing customer: just update the UI
                    updateCustomerDetails(data.name, contact, data.loyalty_points);

                    // Reset form and hide modal
                    document.getElementById('customerForm').reset();
                    let modal = bootstrap.Modal.getInstance(document.getElementById('customer'));
                    modal.hide();
                } else {
                    // Not found — create new customer
                    addNewCustomer(); // This function already updates and hides modal
                }
            })
            .catch(error => {
                console.error('Error checking customer:', error);
                alert('An error occurred while checking the customer.');
            });
    });
</script>
