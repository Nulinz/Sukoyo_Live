@php
    $layout = session('role') === 'employee' ? 'layouts.app_pos' : 'layouts.app';
@endphp

@extends($layout)

@section('content')
<div class="body-div p-3">
    <div class="body-head">
        <h4>Transfer Items to Store</h4>
        <a href="{{ route('inventory.transferlist') }}">
            <button class="listbtn"><i class="fas fa-list pe-2"></i>Transfer History</button>
        </a>
    </div>

    <div class="container-fluid mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('inventory.storetransfer') }}" method="POST" id="transferForm">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <label>Store <span class="text-danger">*</span></label>
                    <select name="store_id" id="store_id" class="form-select" required>
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Transfer Date <span class="text-danger">*</span></label>
                    <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label>Select Items <span class="text-danger">*</span></label>
                <div class="dropdown w-100">
                    <button class="form-control text-start dropdown-toggle" type="button" id="itemDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span id="selectedItemsText">Search and select items...</span>
                    </button>
                    <div class="dropdown-menu w-100 p-3" style="max-height: 400px; overflow-y: auto;" onclick="event.stopPropagation();">
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" id="searchItems" placeholder="Search items by name or code...">
                        </div>
                        <div class="form-check mb-2 border-bottom pb-2">
                            <input class="form-check-input" type="checkbox" id="selectAllItems">
                            <label class="form-check-label fw-bold" for="selectAllItems">
                                Select All
                            </label>
                        </div>
                        <div id="itemCheckboxList">
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0 mt-2">Loading items...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-wrapper mt-3">
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Available Stock</th>
                            <th>Transfer Qty <span class="text-danger">*</span></th>
                            <th>Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <tr id="noItemsRow">
                            <td colspan="7" class="text-center">No items selected</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-start gap-2 mt-4">
                <button type="submit" class="modalbtn">
                    <i class="fas fa-exchange-alt pe-2"></i>Transfer Items
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.dropdown-menu {
    min-width: 100%;
}

.item-checkbox-wrapper {
    transition: background-color 0.2s;
    padding-left: 1.5rem !important;
}

.item-checkbox-wrapper:hover {
    background-color: #f8f9fa;
}

.form-check-input:checked ~ .form-check-label {
    font-weight: 500;
}

#itemsTable tbody tr td {
    vertical-align: middle;
}

.qty-display {
    cursor: pointer;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: #f8f9fa;
    transition: all 0.2s;
}

.qty-display:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.qty-input {
    display: none;
}

.editing .qty-display {
    display: none;
}

.editing .qty-input {
    display: block;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>





<script>
    $(document).ready(function() {
    let allItems = [];
    let selectedItems = {};
    let rowCounter = 0;
    let itemsLoaded = false;
    let bulkLoading = false;
    const MAX_ITEMS_PER_REQUEST = 100; // Process 100 items per batch

    $('#itemDropdown').on('click', function() {
        if (!itemsLoaded) {
            loadItems();
        }
    });

    function loadItems() {
        $.ajax({
            url: '/inventory/get-all-items',
            method: 'GET',
            success: function(response) {
                allItems = response.items;
                renderItemCheckboxes(allItems);
                itemsLoaded = true;
            },
            error: function() {
                $('#itemCheckboxList').html('<div class="text-center text-danger py-3">Error loading items</div>');
            }
        });
    }

    function renderItemCheckboxes(items) {
        if (items.length === 0) {
            $('#itemCheckboxList').html('<div class="text-center text-muted py-3">No items available</div>');
            return;
        }

        let html = '';
        items.forEach(function(item) {
            const isChecked = selectedItems[item.id] ? 'checked' : '';
            html += `
                <div class="form-check item-checkbox-wrapper py-2 px-2">
                    <input class="form-check-input item-checkbox" type="checkbox" value="${item.id}" 
                           id="item_${item.id}" ${isChecked}
                           data-name="${item.item_name}"
                           data-code="${item.item_code}"
                           data-unit="${item.opening_unit}">
                    <label class="form-check-label w-100" for="item_${item.id}">
                        <strong>${item.item_code}</strong> - ${item.item_name}
                    </label>
                </div>
            `;
        });
        $('#itemCheckboxList').html(html);
        updateSelectAllState();
    }

    $('#searchItems').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        if (searchTerm === '') {
            renderItemCheckboxes(allItems);
            return;
        }

        const filteredItems = allItems.filter(function(item) {
            return item.item_name.toLowerCase().includes(searchTerm) || 
                   item.item_code.toLowerCase().includes(searchTerm);
        });

        renderItemCheckboxes(filteredItems);
    });

    // Select All functionality
    $('#selectAllItems').on('change', function() {
        const isChecked = $(this).prop('checked');
        const visibleCheckboxes = $('.item-checkbox:visible');
        const totalVisible = visibleCheckboxes.length;
        
        if (isChecked) {
            if (totalVisible > 1000) {
                const confirmMsg = `You are about to select ${totalVisible} items. This may take a few moments. Continue?`;
                if (!confirm(confirmMsg)) {
                    $(this).prop('checked', false);
                    return;
                }
            }
            
            bulkLoading = true;
            const originalText = $('#selectAllItems').next('label').text();
            $('#selectAllItems').next('label').html('<span class="spinner-border spinner-border-sm me-2"></span>Loading items...');
            $('#selectAllItems').prop('disabled', true);
            
            const itemIds = [];
            visibleCheckboxes.each(function() {
                itemIds.push($(this).val());
            });
            
            processItemsInChunks(itemIds, originalText);
            
        } else {
            visibleCheckboxes.each(function() {
                if ($(this).prop('checked')) {
                    $(this).prop('checked', false);
                    removeItemRow($(this).val());
                }
            });
            updateSelectedItemsText();
        }
    });

    function processItemsInChunks(itemIds, originalText) {
        const CHUNK_SIZE = 100;
        let currentIndex = 0;
        let processedCount = 0;
        let failedCount = 0;
        
        function processNextChunk() {
            if (currentIndex >= itemIds.length) {
                $('#selectAllItems').next('label').text(originalText);
                $('#selectAllItems').prop('disabled', false);
                bulkLoading = false;
                updateSelectedItemsText();
                
                if (failedCount > 0) {
                    alert(`Loaded ${processedCount} items successfully. ${failedCount} items were skipped (no stock available).`);
                } else {
                    alert(`Successfully loaded all ${processedCount} items!`);
                }
                return;
            }
            
            const chunk = itemIds.slice(currentIndex, currentIndex + CHUNK_SIZE);
            currentIndex += CHUNK_SIZE;
            
            const progress = Math.min(100, Math.round((currentIndex / itemIds.length) * 100));
            $('#selectAllItems').next('label').html(
                `<span class="spinner-border spinner-border-sm me-2"></span>Loading ${progress}% (${processedCount}/${itemIds.length})...`
            );
            
            $.ajax({
                url: '/inventory/get-all-items-with-stock',
                method: 'GET',
                data: { item_ids: chunk },
                timeout: 60000,
                success: function(response) {
                    if (response.items && Array.isArray(response.items)) {
                        response.items.forEach(function(itemData) {
                            if (itemData && itemData.id && !selectedItems[itemData.id]) {
                                $(`#item_${itemData.id}`).prop('checked', true);
                                addItemRowFromData(itemData);
                                processedCount++;
                            }
                        });
                        
                        const successCount = response.items.length;
                        failedCount += (chunk.length - successCount);
                    }
                    
                    setTimeout(processNextChunk, 500);
                },
                error: function(xhr, status, error) {
                    console.error('Chunk error:', status, error);
                    failedCount += chunk.length;
                    
                    if (confirm(`Error loading chunk. Continue with remaining items?`)) {
                        setTimeout(processNextChunk, 1000);
                    } else {
                        $('#selectAllItems').prop('checked', false);
                        $('#selectAllItems').next('label').text(originalText);
                        $('#selectAllItems').prop('disabled', false);
                        bulkLoading = false;
                        updateSelectedItemsText();
                    }
                }
            });
        }
        
        processNextChunk();
    }

    function updateSelectAllState() {
        const totalVisible = $('.item-checkbox:visible').length;
        const totalChecked = $('.item-checkbox:visible:checked').length;
        
        if (totalVisible > 0 && totalVisible === totalChecked) {
            $('#selectAllItems').prop('checked', true);
            $('#selectAllItems').prop('indeterminate', false);
        } else if (totalChecked > 0) {
            $('#selectAllItems').prop('checked', false);
            $('#selectAllItems').prop('indeterminate', true);
        } else {
            $('#selectAllItems').prop('checked', false);
            $('#selectAllItems').prop('indeterminate', false);
        }
    }

    $(document).on('change', '.item-checkbox', function() {
        if (bulkLoading) return;
        
        const itemId = $(this).val();
        
        if ($(this).prop('checked')) {
            addItemRow(itemId);
        } else {
            removeItemRow(itemId);
        }
        
        updateSelectAllState();
        updateSelectedItemsText();
    });

    function updateSelectedItemsText() {
        const count = Object.keys(selectedItems).length;
        if (count === 0) {
            $('#selectedItemsText').text('Search and select items...');
        } else {
            $('#selectedItemsText').text(`${count} item(s) selected`);
        }
    }

    function addItemRowFromData(itemData) {
        const itemId = itemData.id;
        
        if (selectedItems[itemId]) {
            return;
        }

        rowCounter++;
        selectedItems[itemId] = {
            counter: rowCounter,
            stock: itemData.available_stock,
            qty: itemData.available_stock
        };

        const row = `
            <tr id="row_${itemId}" data-item-id="${itemId}">
                <td>${rowCounter}</td>
                <td>${itemData.item_code}</td>
                <td>${itemData.item_name}</td>
                <td>
                    <span class="badge bg-info">${itemData.available_stock} ${itemData.opening_unit}</span>
                    <input type="hidden" class="available-stock" value="${itemData.available_stock}">
                </td>
                <td class="qty-cell">
                    <div class="qty-display" data-item-id="${itemId}">
                        <i class="fas fa-edit text-muted me-2"></i>
                        <span class="qty-value">${itemData.available_stock}</span> ${itemData.opening_unit}
                    </div>
                    <input type="number" 
                           class="form-control qty-input transfer-qty" 
                           value="${itemData.available_stock}"
                           min="0.01" 
                           step="0.01" 
                           max="${itemData.available_stock}"
                           data-item-id="${itemId}"
                           required>
                </td>
                <td>${itemData.opening_unit}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item" data-item-id="${itemId}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#itemsTableBody').append(row);
        updateNoItemsRow();
    }

    function addItemRow(itemId) {
        if (selectedItems[itemId]) {
            return;
        }

        $.ajax({
            url: '/inventory/get-item-stock/' + itemId,
            method: 'GET',
            success: function(response) {
                rowCounter++;
                selectedItems[itemId] = {
                    counter: rowCounter,
                    stock: response.available_stock,
                    qty: response.available_stock
                };

                const row = `
                    <tr id="row_${itemId}" data-item-id="${itemId}">
                        <td>${rowCounter}</td>
                        <td>${response.item_code}</td>
                        <td>${response.item_name}</td>
                        <td>
                            <span class="badge bg-info">${response.available_stock} ${response.unit}</span>
                            <input type="hidden" class="available-stock" value="${response.available_stock}">
                        </td>
                        <td class="qty-cell">
                            <div class="qty-display" data-item-id="${itemId}">
                                <i class="fas fa-edit text-muted me-2"></i>
                                <span class="qty-value">${response.available_stock}</span> ${response.unit}
                            </div>
                            <input type="number" 
                                   class="form-control qty-input transfer-qty" 
                                   value="${response.available_stock}"
                                   min="0.01" 
                                   step="0.01" 
                                   max="${response.available_stock}"
                                   data-item-id="${itemId}"
                                   required>
                        </td>
                        <td>${response.unit}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item" data-item-id="${itemId}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#itemsTableBody').append(row);
                updateNoItemsRow();
            },
            error: function() {
                alert('Error fetching item details');
                $(`#item_${itemId}`).prop('checked', false);
                delete selectedItems[itemId];
            }
        });
    }

    function removeItemRow(itemId) {
        $(`#row_${itemId}`).remove();
        delete selectedItems[itemId];
        $(`#item_${itemId}`).prop('checked', false);
        updateNoItemsRow();
        updateSelectedItemsText();
        updateSerialNumbers(); 
    }

    function updateNoItemsRow() {
        if (Object.keys(selectedItems).length === 0) {
            $('#noItemsRow').show();
        } else {
            $('#noItemsRow').hide();
        }
    }

    function updateSerialNumbers() {
        $('#itemsTableBody tr[data-item-id]').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    $(document).on('click', '.qty-display', function() {
        const itemId = $(this).data('item-id');
        const cell = $(this).closest('.qty-cell');
        
        cell.addClass('editing');
        cell.find('.qty-input').focus().select();
    });

    $(document).on('blur', '.qty-input', function() {
        const itemId = $(this).data('item-id');
        const cell = $(this).closest('.qty-cell');
        const newQty = parseFloat($(this).val());
        
        cell.find('.qty-value').text(newQty);
        cell.removeClass('editing');
        
        if (selectedItems[itemId]) {
            selectedItems[itemId].qty = newQty;
        }
    });

    $(document).on('keypress', '.qty-input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).blur();
        }
    });

    $(document).on('input', '.qty-input', function() {
        const row = $(this).closest('tr');
        const availableStock = parseFloat(row.find('.available-stock').val());
        const transferQty = parseFloat($(this).val());

        if (transferQty > availableStock) {
            $(this).addClass('is-invalid');
            alert(`Transfer quantity cannot exceed available stock (${availableStock})`);
            $(this).val(availableStock);
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    $(document).on('click', '.remove-item', function() {
        const itemId = $(this).data('item-id');
        removeItemRow(itemId);
        updateSelectAllState();
    });

    $('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });

    // FIXED FORM SUBMISSION
    $('#transferForm').on('submit', function(e) {
        e.preventDefault();
        
        if (Object.keys(selectedItems).length === 0) {
            alert('Please select at least one item to transfer');
            return false;
        }

        const totalItems = Object.keys(selectedItems).length;
        
        if (totalItems > MAX_ITEMS_PER_REQUEST) {
            if (!confirm(`You have selected ${totalItems} items. This will be processed in multiple batches. Continue?`)) {
                return false;
            }
            
            submitInBatches();
        } else {
            submitSingleRequest();
        }
    });

    function submitSingleRequest() {
        $('.dynamic-item-input').remove();

        let index = 0;
        let isValid = true;
        
        $('#itemsTableBody tr[data-item-id]').each(function() {
            const itemId = $(this).data('item-id');
            const qty = $(this).find('.transfer-qty').val();
            const qtyVal = parseFloat(qty);
            
            if (qtyVal <= 0 || isNaN(qtyVal)) {
                isValid = false;
                $(this).find('.transfer-qty').addClass('is-invalid');
            }
            
            $('#transferForm').append(`
                <input type="hidden" name="items[${index}][item_id]" value="${itemId}" class="dynamic-item-input">
                <input type="hidden" name="items[${index}][transfer_qty]" value="${qty}" class="dynamic-item-input">
            `);
            
            index++;
        });

        if (!isValid) {
            $('.dynamic-item-input').remove();
            alert('Please enter valid transfer quantities for all items');
            return false;
        }

        $('#transferForm').find('button[type="submit"]').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin pe-2"></i>Processing...');
        
        $('#transferForm')[0].submit();
    }

    function submitInBatches() {
        const button = $('#transferForm').find('button[type="submit"]');
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin pe-2"></i>Preparing batches...');
        
        const allItemsData = [];
        let isValid = true;
        
        $('#itemsTableBody tr[data-item-id]').each(function() {
            const itemId = $(this).data('item-id');
            const qtyInput = $(this).find('.transfer-qty');
            const qty = parseFloat(qtyInput.val());
            
            if (isNaN(qty) || qty <= 0) {
                isValid = false;
                qtyInput.addClass('is-invalid');
                console.error('Invalid quantity for item:', itemId, qty);
            } else {
                allItemsData.push({
                    item_id: parseInt(itemId),
                    transfer_qty: qty
                });
            }
        });

        if (!isValid) {
            button.prop('disabled', false).html('<i class="fas fa-exchange-alt pe-2"></i>Transfer Items');
            alert('Please enter valid transfer quantities for all items');
            return;
        }

        const store_id = $('select[name="store_id"]').val();
        const transfer_date = $('input[name="transfer_date"]').val();
        const remarks = $('input[name="remarks"]').val() || '';
        
        if (!store_id) {
            button.prop('disabled', false).html('<i class="fas fa-exchange-alt pe-2"></i>Transfer Items');
            alert('Please select a store');
            return;
        }
        
        console.log('Starting batch transfer:', {
            totalItems: allItemsData.length,
            store_id: store_id,
            transfer_date: transfer_date
        });
        
        processBatch(allItemsData, store_id, transfer_date, remarks, 0, button, null);
    }

    function processBatch(allItems, store_id, transfer_date, remarks, batchIndex, button, transferId) {
        const totalBatches = Math.ceil(allItems.length / MAX_ITEMS_PER_REQUEST);
        const startIdx = batchIndex * MAX_ITEMS_PER_REQUEST;
        const endIdx = Math.min(startIdx + MAX_ITEMS_PER_REQUEST, allItems.length);
        const batchItems = allItems.slice(startIdx, endIdx);
        
        button.html(`<i class="fas fa-spinner fa-spin pe-2"></i>Processing batch ${batchIndex + 1}/${totalBatches} (${endIdx}/${allItems.length} items)...`);
        
        console.log(`Processing batch ${batchIndex + 1}:`, {
            startIdx: startIdx,
            endIdx: endIdx,
            itemCount: batchItems.length,
            transferId: transferId,
            batchNumber: batchIndex + 1,
            totalBatches: totalBatches
        });
        
        // Prepare the data object
        const requestData = {
            _token: $('input[name="_token"]').val(),
            store_id: parseInt(store_id),
            transfer_date: transfer_date,
            remarks: remarks,
            items: batchItems,
            batch_number: batchIndex + 1,
            total_batches: totalBatches
        };
        
        // Add transfer_id if it exists
        if (transferId) {
            requestData.transfer_id = parseInt(transferId);
        }
        
        console.log('Request data:', requestData);
        
        $.ajax({
            url: '/inventory/store-transfer-batch',
            method: 'POST',
            data: requestData,
            timeout: 120000,
            success: function(response) {
                console.log(`Batch ${batchIndex + 1} response:`, response);
                
                if (!response.success) {
                    button.prop('disabled', false).html('<i class="fas fa-exchange-alt pe-2"></i>Transfer Items');
                    alert(`Error in batch ${batchIndex + 1}: ${response.message || 'Unknown error'}`);
                    return;
                }
                
                // Store transfer_id from first batch
                if (!transferId && response.transfer_id) {
                    transferId = response.transfer_id;
                    console.log('Transfer ID set:', transferId);
                }
                
                // Check if there are more batches
                if (endIdx < allItems.length) {
                    setTimeout(function() {
                        processBatch(allItems, store_id, transfer_date, remarks, batchIndex + 1, button, transferId);
                    }, 1000);
                } else {
                    const totalProcessed = allItems.length;
                    alert(`Transfer completed successfully! ${totalProcessed} items transferred in ${totalBatches} batches.`);
                    window.location.href = '/inventory/transfer-items/list';
                }
            },
            error: function(xhr, status, error) {
                console.error(`Batch ${batchIndex + 1} error:`, {
                    status: xhr.status,
                    statusText: status,
                    error: error,
                    responseText: xhr.responseText,
                    response: xhr.responseJSON
                });
                
                button.prop('disabled', false).html('<i class="fas fa-exchange-alt pe-2"></i>Transfer Items');
                
                let errorMsg = `Error in batch ${batchIndex + 1}`;
                
                if (xhr.status === 422) {
                    try {
                        const response = xhr.responseJSON;
                        errorMsg += `: Validation error\n`;
                        if (response.errors) {
                            Object.keys(response.errors).forEach(function(key) {
                                errorMsg += `${key}: ${response.errors[key].join(', ')}\n`;
                            });
                        } else {
                            errorMsg += JSON.stringify(response.message || response);
                        }
                    } catch (e) {
                        errorMsg += `: ${xhr.responseText}`;
                    }
                } else if (xhr.status === 500) {
                    try {
                        const response = xhr.responseJSON;
                        errorMsg += `: ${response.message || 'Server error'}`;
                    } catch (e) {
                        errorMsg += `: Server error`;
                    }
                } else if (status === 'timeout') {
                    errorMsg += ': Request timeout. Please try with fewer items.';
                } else {
                    errorMsg += `: ${error}`;
                }
                
                alert(errorMsg + '\n\nCheck console for details.');
            }
        });
    }
});
</script>
@endsection