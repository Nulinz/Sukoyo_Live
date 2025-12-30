<?php $__env->startSection('content'); ?>

<div class="body-div p-3">
    <div class="body-head mb-3">
        <h4>Add Purchase Invoice</h4>
    </div>

    <form method="POST" action="<?php echo e(route('purchase.invoice.store')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="pos_id" id="selected_pos_id" value="">

        <div class="container-fluid form-div">
            <div class="body-head mb-3">
                <h5>Item Details</h5>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label for="posid">PO ID <span>*</span></label>

                    <?php if($purchaseOrders->isEmpty()): ?>
                        <div class="alert alert-warning p-2">
                            No Purchase Orders available for your store.
                        </div>
                    <?php else: ?>
                        <select class="form-select" id="posid" name="pos_id" required>
                            <option value="" selected disabled>Select Option</option>
                            <?php $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($order->id); ?>"><?php echo e($order->id ?? 'POS-' . $order->id); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Contact Number</label>
                    <input type="text" class="form-control" id="contact" name="contact" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Billing Address</label>
                    <textarea rows="1" class="form-control" id="billaddress" name="billaddress" readonly></textarea>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Bill No</label>
                    <input type="text" class="form-control" id="billno" name="billno" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Due Date</label>
                    <input type="date" class="form-control" id="duedate" name="due_date" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Bill Date</label>
                    <input type="date" class="form-control" id="billdate" name="billdate" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Transport</label>
                    <input type="text" class="form-control" id="transport" name="transport" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Packaging</label>
                    <input type="text" class="form-control" id="packaging" name="packaging" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Store / Warehouse</label>
                    <input type="text" class="form-control" id="warehouse" name="warehouse" readonly>
                </div>
            </div>

            <div class="container-fluid listtable">
                <div class="table-wrapper">
                    <table class="example table table-bordered" id="itemTable">
<thead>
    <tr>
        <th style="width: 50px;">S.No</th>
        <th style="width: 50px;">
            <input type="checkbox" id="selectAll">
        </th>
        <th style="width: 300px;">Item</th>
        <th style="width: 150px;">Unit</th>
        <th style="width: 100px">Qty</th>
        <th style="width: 100px">Price/Unit</th>
        <th style="width: 100px">Discount</th>
        <th style="width: 100px">Tax</th>
        <th style="width: 100px">Amount</th>
    </tr>
</thead>

                        <tbody id="invoiceItemsBody">
                            <!-- loaded by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Payment Type</label>
                    <input type="text" class="form-control" id="paytype" name="paytype" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Reference No</label>
                    <input type="text" class="form-control" id="refno" name="refno" readonly>
                </div>
                <div class="col-sm-12 col-md-8 col-xl-6 mb-3">
                    <label>Description</label>
                    <textarea rows="1" class="form-control" id="descp" name="descp" readonly></textarea>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Total</label>
                    <input type="number" class="form-control" id="total" name="total" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Paid Amount</label>
                    <input type="number" class="form-control" id="paidamt" name="paidamt" readonly>
                </div>
                <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                    <label>Balance Amount</label>
                    <input type="number" class="form-control" id="balanceamt" name="balanceamt" readonly>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 mt-3 d-flex justify-content-center align-items-center">
                <button type="submit" class="formbtn">Add Purchase Invoice</button>
            </div>
        </div>
    </form>
</div>


<script>
document.getElementById('posid').addEventListener('change', function () {
    let posId = this.value;
    if (!posId) return;

    fetch('/purchase-order-data/' + posId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('selected_pos_id').value = posId;
            document.getElementById('contact').value = data.contact || '';
            document.getElementById('billaddress').value = data.billaddress || '';
            document.getElementById('billno').value = data.bill_no || '';
            document.getElementById('billdate').value = data.bill_date || '';
            document.getElementById('duedate').value = data.due_date || '';
            document.getElementById('transport').value = data.transport || '';
            document.getElementById('packaging').value = data.packaging || '';
            document.getElementById('warehouse').value = data.warehouse || '';
            document.getElementById('paytype').value = data.payment_type || '';
            document.getElementById('refno').value = data.reference_no || '';
            document.getElementById('descp').value = data.description || '';
            document.getElementById('total').value = data.total || 0;
            document.getElementById('paidamt').value = data.paid_amount || 0;
            document.getElementById('balanceamt').value = data.balance_amount || 0;

            let tbody = document.getElementById('invoiceItemsBody');
            tbody.innerHTML = '';

            data.items.forEach((itemData, index) => {
                // Try different possible paths for the item name based on your relationship structure
                let itemName = '';
                let itemId = '';
                
                if (itemData.item && itemData.item.item_name) {
                    // If item relationship exists and has item_name field
                    itemName = itemData.item.item_name;
                    itemId = itemData.item.id || itemData.item_id;
                } else if (itemData.item && itemData.item.name) {
                    // If item relationship exists and has name field
                    itemName = itemData.item.name;
                    itemId = itemData.item.id || itemData.item_id;
                } else if (itemData.item_name) {
                    // If item_name is directly available
                    itemName = itemData.item_name;
                    itemId = itemData.item_id;
                } else {
                    // Fallback to item_id if name is not available
                    itemName = itemData.item_id || 'Unknown Item';
                    itemId = itemData.item_id;
                }

                let row = `
                    <tr>
                     <td>${index + 1}</td>
                        <td>
                            <input type="checkbox" name="items[${index}][selected]" value="1">
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][item_id]" value="${itemId || ''}">
                            ${itemName || 'Unknown Item'}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][unit]" value="${itemData.unit || ''}">
                            ${itemData.unit || ''}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][qty]" value="${itemData.qty || 0}">
                            ${itemData.qty || 0}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][price]" value="${itemData.price || 0}">
                            ${itemData.price || 0}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][discount]" value="${itemData.discount || 0}">
                            ${itemData.discount || 0}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][tax]" value="${itemData.tax || 0}">
                            ${itemData.tax || 0}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][amount]" value="${itemData.amount || 0}">
                            ${itemData.amount || 0}
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(err => {
            console.error(err);
            alert('Failed to load data!');
        });
});
</script>
<script>
    // Select/Deselect all items
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    
    selectAll.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('#invoiceItemsBody input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });
});

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/purchase/inv_add.blade.php ENDPATH**/ ?>