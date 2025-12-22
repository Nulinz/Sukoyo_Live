<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate Invoice - {{ $salesInvoice->id }}</title>
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
                @if($salesInvoice->store)
                    {{ $salesInvoice->store->address ?? 'Company Address' }}<br>
                    Phone: {{ $salesInvoice->store->phone ?? 'Phone Number' }} | 
                    Email: {{ $salesInvoice->store->email ?? 'email@company.com' }}
                @else
                    GST NO: 32AAJFF3746P2Z0<br>
                    Phone: 9876543210 | Email: sukoyo@company.com
                @endif
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">Tax Invoice (Corporate)</div>

        <!-- Invoice Information -->
        <div class="invoice-info">
            <div>
                <h4>Invoice Details</h4>
                <p><strong>Invoice No:</strong> INV-{{ str_pad($salesInvoice->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Date:</strong> {{ $salesInvoice->invoice_date->format('d/m/Y') }}</p>
                <p><strong>Time:</strong> {{ $salesInvoice->invoice_date->format('h:i A') }}</p>
                <p><strong>Cashier:</strong> {{ $salesInvoice->employee->empname ?? 'N/A' }}</p>
                <p><strong>Payment Mode:</strong> {{ ucfirst($salesInvoice->mode_of_payment) }}</p>
            </div>

            <div>
                <h4>Bill To</h4>
                @if($salesInvoice->customer)
                    <p><strong>{{ $salesInvoice->customer->name }}</strong></p>
                    <p>Contact: {{ $salesInvoice->customer->contact }}</p>
                @else
                    <p><strong>Walk-in Customer</strong></p>
                @endif
                @if($salesInvoice->gstDetail)
                    <p><strong>GST No:</strong> {{ $salesInvoice->gstDetail->gst_number }}</p>
                @endif
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
                @foreach($salesInvoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->item->item_name ?? 'Unknown Item' }}</td>
                    <td class="text-center">{{ $item->unit ?? 'pcs' }}</td>
                    <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                    <td class="text-right">₹ {{ number_format($item->price, 2) }}</td>
                    <td class="text-right">
                        {{ $item->discount }}%
                        @if($item->discount > 0)
                            @php
                                $discountAmount = ($item->price * $item->qty * $item->discount) / 100;
                            @endphp
                            <br>(₹ {{ number_format($discountAmount, 2) }})
                        @endif
                    </td>
                    <td class="text-right">
                        {{ $item->tax }}%
                        @if($item->tax > 0)
                            @php
                                $baseAmount = $item->price * $item->qty;
                                $discountAmount = ($baseAmount * $item->discount) / 100;
                                $taxableAmount = $baseAmount - $discountAmount;
                                $taxAmount = ($taxableAmount * $item->tax) / 100;
                            @endphp
                            <br>(₹ {{ number_format($taxAmount, 2) }})
                        @endif
                    </td>
                    <td class="text-right">₹ {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
@if($salesInvoice->total_tax > 0)
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
                @php
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
                @endphp
                @foreach($taxSummary as $rate => $data)
                <tr>
                    <td class="text-center">{{ $rate }}%</td>
                    <td class="text-right">₹ {{ number_format($data['taxable'], 2) }}</td>
                    <td class="text-right">₹ {{ number_format($data['tax'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals Section (Right Side) -->
    <div style="flex: 1; min-width: 300px; margin-top: 3px;">
        <h4>Totals</h4><br>
        <table class="totals-table" style="width: 100%;">
            <tr>
                <td class="total-label">Sub Total:</td>
                <td class="text-right">₹ {{ number_format($salesInvoice->sub_total, 2) }}</td>
            </tr>
            @if($salesInvoice->total_discount > 0)
            <tr>
                <td class="total-label">Total Discount:</td>
                <td class="text-right">- ₹ {{ number_format($salesInvoice->total_discount, 2) }}</td>
            </tr>
            @endif
            @if($salesInvoice->total_tax > 0)
            <tr>
                <td class="total-label">Total Tax:</td>
                <td class="text-right">₹ {{ number_format($salesInvoice->total_tax, 2) }}</td>
            </tr>
            @endif
            @if($salesInvoice->additional_charges > 0)
            <tr>
                <td class="total-label">Additional Charges:</td>
                <td class="text-right">₹ {{ number_format($salesInvoice->additional_charges, 2) }}</td>
            </tr>
            @endif
            @if($salesInvoice->loyalty_points_used > 0)
            <tr>
                <td class="total-label">Loyalty Points Used:</td>
                <td class="text-right">{{ $salesInvoice->loyalty_points_used }} points</td>
            </tr>
            @endif
            @if($salesInvoice->gift_card_amount > 0)
            <tr>
                <td class="total-label">Gift Card:</td>
                <td class="text-right">- ₹ {{ number_format($salesInvoice->gift_card_amount, 2) }}</td>
            </tr>
            @endif
            @if($salesInvoice->voucher_amount > 0)
            <tr>
                <td class="total-label">Voucher Discount:</td>
                <td class="text-right">- ₹ {{ number_format($salesInvoice->voucher_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td class="text-right">₹ {{ number_format($salesInvoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">Received Amount:</td>
                <td class="text-right">₹ {{ number_format($salesInvoice->received_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">Change:</td>
                <td class="text-right">₹ {{ number_format($salesInvoice->received_amount - $salesInvoice->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>

</div>
@endif


        <!-- GST Details Section -->
        @if($salesInvoice->gstDetail)
        <div class="gst-section">
            <div class="gst-details">
                <div class="gst-box">
                    <h4>GST Details</h4>
                    <div class="gst-info"><strong>GST Number:</strong> {{ $salesInvoice->gstDetail->gst_number }}</div>
                    <div class="gst-info"><strong>Business Name:</strong> {{ $salesInvoice->gstDetail->business_legal }}</div>
                    <div class="gst-info"><strong>PAN Number:</strong> {{ $salesInvoice->gstDetail->pan_no }}</div>
                    <div class="gst-info"><strong>Registration Date:</strong> {{ $salesInvoice->gstDetail->register_date ? \Carbon\Carbon::parse($salesInvoice->gstDetail->register_date)->format('d/m/Y') : 'N/A' }}</div>
                </div>

                <div class="gst-box">
                    <h4>Contact Information</h4>
                    <div class="gst-info"><strong>Contact Person:</strong> {{ $salesInvoice->gstDetail->name }}</div>
                    <div class="gst-info"><strong>Phone:</strong> {{ $salesInvoice->gstDetail->contact_no }}</div>
                    <div class="gst-info"><strong>Email:</strong> {{ $salesInvoice->gstDetail->email_id }}</div>
                    <div class="gst-info"><strong>Nature of Business:</strong> {{ $salesInvoice->gstDetail->nature_business }}</div>
                </div>
            </div>

            @if($salesInvoice->gstDetail->gstaddress)
            <div style="margin-top: 15px;">
                <h4>Billing Address</h4>
                <div class="gst-info">{{ $salesInvoice->gstDetail->gstaddress }}</div>
            </div>
            @endif
        </div>
        @endif



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
            <!--<p>For any queries, please contact us at {{ $salesInvoice->store->phone ?? 'your-phone-number' }}</p>-->
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
</html>