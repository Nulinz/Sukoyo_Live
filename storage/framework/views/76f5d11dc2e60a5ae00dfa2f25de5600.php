<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate Invoice - <?php echo e($salesInvoice->id); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
        }

        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
            color: #333;
            text-transform: uppercase;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .invoice-info div {
            flex: 1;
        }

        .invoice-info h4 {
            font-size: 13px;
            margin-bottom: 8px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        .invoice-info p {
            margin: 3px 0;
            font-size: 11px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .items-table td.text-center {
            text-align: center;
        }

        .totals-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .totals-table td {
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        .totals-table .total-label {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .totals-table .grand-total {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .gst-section {
            clear: both;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .gst-details {
            display: flex;
            justify-content: space-between;
        }

        .gst-box {
            flex: 1;
            margin-right: 20px;
        }

        .gst-box:last-child {
            margin-right: 0;
        }

        .gst-box h4 {
            font-size: 13px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        .gst-info {
            font-size: 11px;
            margin: 3px 0;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-size: 11px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .invoice-container {
                max-width: none;
                margin: 0;
                padding: 10px;
                box-shadow: none;
            }
            
            .no-print {
                display: none !important;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Invoice</button>

    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">SUKOYO</div>
            <div class="company-details">
                <?php if($salesInvoice->store): ?>
                    <?php echo e($salesInvoice->store->address ?? 'Company Address'); ?><br>
                    Phone: <?php echo e($salesInvoice->store->phone ?? 'Phone Number'); ?> | 
                    Email: <?php echo e($salesInvoice->store->email ?? 'email@company.com'); ?>

                <?php else: ?>
                    GST NO: 32AAJFF3746P2Z0<br>
                    Phone: 9876543210 | Email: sukoyo@company.com
                <?php endif; ?>
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">Tax Invoice (Corporate)</div>

        <!-- Invoice Information -->
        <div class="invoice-info">
            <div>
                <h4>Invoice Details</h4>
                <p><strong>Invoice No:</strong> INV-<?php echo e(str_pad($salesInvoice->id, 6, '0', STR_PAD_LEFT)); ?></p>
                <p><strong>Date:</strong> <?php echo e($salesInvoice->invoice_date->format('d/m/Y')); ?></p>
                <p><strong>Time:</strong> <?php echo e($salesInvoice->invoice_date->format('h:i A')); ?></p>
                <p><strong>Cashier:</strong> <?php echo e($salesInvoice->employee->empname ?? 'N/A'); ?></p>
                <p><strong>Payment Mode:</strong> <?php echo e(ucfirst($salesInvoice->mode_of_payment)); ?></p>
            </div>

            <div>
                <h4>Bill To</h4>
                <?php if($salesInvoice->customer): ?>
                    <p><strong><?php echo e($salesInvoice->customer->name); ?></strong></p>
                    <p>Contact: <?php echo e($salesInvoice->customer->contact); ?></p>
                <?php else: ?>
                    <p><strong>Walk-in Customer</strong></p>
                <?php endif; ?>
                <?php if($salesInvoice->gstDetail): ?>
                    <p><strong>GST No:</strong> <?php echo e($salesInvoice->gstDetail->gst_number); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 35%">Item Description</th>
                    <th style="width: 8%">Unit</th>
                    <th style="width: 10%">Qty</th>
                    <th style="width: 12%">Unit Price</th>
                    <th style="width: 10%">Discount</th>
                    <th style="width: 10%">Tax Rate</th>
                    <th style="width: 10%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $salesInvoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item->item->item_name ?? 'Unknown Item'); ?></td>
                    <td class="text-center"><?php echo e($item->unit ?? 'pcs'); ?></td>
                    <td class="text-center"><?php echo e(number_format($item->qty, 2)); ?></td>
                    <td class="text-right">₹ <?php echo e(number_format($item->price, 2)); ?></td>
                    <td class="text-right">
                        <?php echo e($item->discount); ?>%
                        <?php if($item->discount > 0): ?>
                            <?php
                                $discountAmount = ($item->price * $item->qty * $item->discount) / 100;
                            ?>
                            <br>(₹ <?php echo e(number_format($discountAmount, 2)); ?>)
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php echo e($item->tax); ?>%
                        <?php if($item->tax > 0): ?>
                            <?php
                                $baseAmount = $item->price * $item->qty;
                                $discountAmount = ($baseAmount * $item->discount) / 100;
                                $taxableAmount = $baseAmount - $discountAmount;
                                $taxAmount = ($taxableAmount * $item->tax) / 100;
                            ?>
                            <br>(₹ <?php echo e(number_format($taxAmount, 2)); ?>)
                        <?php endif; ?>
                    </td>
                    <td class="text-right">₹ <?php echo e(number_format($item->amount, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
<?php if($salesInvoice->total_tax > 0): ?>
<div style="display: flex; justify-content: space-between;  gap: 20px; flex-wrap: wrap;">
    
    <!-- Tax Summary (Left Side) -->
    <div style="flex: 1; min-width: 300px;">
        <h4>Tax Summary</h4>
        <table class="items-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Tax Rate</th>
                    <th>Taxable Amount</th>
                    <th>Tax Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $taxSummary = [];
                    foreach($salesInvoice->items as $item) {
                        $rate = $item->tax ?? 0;
                        if (!isset($taxSummary[$rate])) {
                            $taxSummary[$rate] = ['taxable' => 0, 'tax' => 0];
                        }
                        $baseAmount = $item->price * $item->qty;
                        $discountAmount = ($baseAmount * ($item->discount ?? 0)) / 100;
                        $taxable = $baseAmount - $discountAmount;
                        $taxAmount = ($taxable * $rate) / 100;

                        $taxSummary[$rate]['taxable'] += $taxable;
                        $taxSummary[$rate]['tax'] += $taxAmount;
                    }
                ?>
                <?php $__currentLoopData = $taxSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($rate); ?>%</td>
                    <td class="text-right">₹ <?php echo e(number_format($data['taxable'], 2)); ?></td>
                    <td class="text-right">₹ <?php echo e(number_format($data['tax'], 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <!-- Totals Section (Right Side) -->
    <div style="flex: 1; min-width: 300px; margin-top: 3px;">
        <h4>Totals</h4><br>
        <table class="totals-table" style="width: 100%;">
            <tr>
                <td class="total-label">Sub Total:</td>
                <td class="text-right">₹ <?php echo e(number_format($salesInvoice->sub_total, 2)); ?></td>
            </tr>
            <?php if($salesInvoice->total_discount > 0): ?>
            <tr>
                <td class="total-label">Total Discount:</td>
                <td class="text-right">- ₹ <?php echo e(number_format($salesInvoice->total_discount, 2)); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($salesInvoice->total_tax > 0): ?>
            <tr>
                <td class="total-label">Total Tax:</td>
                <td class="text-right">₹ <?php echo e(number_format($salesInvoice->total_tax, 2)); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($salesInvoice->additional_charges > 0): ?>
            <tr>
                <td class="total-label">Additional Charges:</td>
                <td class="text-right">₹ <?php echo e(number_format($salesInvoice->additional_charges, 2)); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($salesInvoice->loyalty_points_used > 0): ?>
            <tr>
                <td class="total-label">Loyalty Points Used:</td>
                <td class="text-right"><?php echo e($salesInvoice->loyalty_points_used); ?> points</td>
            </tr>
            <?php endif; ?>
            <?php if($salesInvoice->gift_card_amount > 0): ?>
            <tr>
                <td class="total-label">Gift Card:</td>
                <td class="text-right">- ₹ <?php echo e(number_format($salesInvoice->gift_card_amount, 2)); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($salesInvoice->voucher_amount > 0): ?>
            <tr>
                <td class="total-label">Voucher Discount:</td>
                <td class="text-right">- ₹ <?php echo e(number_format($salesInvoice->voucher_amount, 2)); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td class="text-right">₹ <?php echo e(number_format($salesInvoice->grand_total, 2)); ?></td>
            </tr>
            <tr>
                <td class="total-label">Received Amount:</td>
                <td class="text-right">₹ <?php echo e(number_format($salesInvoice->received_amount, 2)); ?></td>
            </tr>
            <tr>
                <td class="total-label">Change:</td>
                <td class="text-right">₹ <?php echo e(number_format($salesInvoice->received_amount - $salesInvoice->grand_total, 2)); ?></td>
            </tr>
        </table>
    </div>

</div>
<?php endif; ?>


        <!-- GST Details Section -->
        <?php if($salesInvoice->gstDetail): ?>
        <div class="gst-section">
            <div class="gst-details">
                <div class="gst-box">
                    <h4>GST Details</h4>
                    <div class="gst-info"><strong>GST Number:</strong> <?php echo e($salesInvoice->gstDetail->gst_number); ?></div>
                    <div class="gst-info"><strong>Business Name:</strong> <?php echo e($salesInvoice->gstDetail->business_legal); ?></div>
                    <div class="gst-info"><strong>PAN Number:</strong> <?php echo e($salesInvoice->gstDetail->pan_no); ?></div>
                    <div class="gst-info"><strong>Registration Date:</strong> <?php echo e($salesInvoice->gstDetail->register_date ? \Carbon\Carbon::parse($salesInvoice->gstDetail->register_date)->format('d/m/Y') : 'N/A'); ?></div>
                </div>

                <div class="gst-box">
                    <h4>Contact Information</h4>
                    <div class="gst-info"><strong>Contact Person:</strong> <?php echo e($salesInvoice->gstDetail->name); ?></div>
                    <div class="gst-info"><strong>Phone:</strong> <?php echo e($salesInvoice->gstDetail->contact_no); ?></div>
                    <div class="gst-info"><strong>Email:</strong> <?php echo e($salesInvoice->gstDetail->email_id); ?></div>
                    <div class="gst-info"><strong>Nature of Business:</strong> <?php echo e($salesInvoice->gstDetail->nature_business); ?></div>
                </div>
            </div>

            <?php if($salesInvoice->gstDetail->gstaddress): ?>
            <div style="margin-top: 15px;">
                <h4>Billing Address</h4>
                <div class="gst-info"><?php echo e($salesInvoice->gstDetail->gstaddress); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>



        <!-- Signature Section -->
        <!-- <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Customer Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div> -->

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <!--<p>This is a computer-generated invoice and does not require a physical signature.</p>-->
            <!--<p>For any queries, please contact us at <?php echo e($salesInvoice->store->phone ?? 'your-phone-number'); ?></p>-->
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.addEventListener('load', function() {
        //     window.print();
        // });
        
        // Close window after printing (optional)
        window.addEventListener('afterprint', function() {
            // window.close();
        });
    </script>
</body>
</html><?php /**PATH /var/www/sukoyo/resources/views/cororate_pdf.blade.php ENDPATH**/ ?>