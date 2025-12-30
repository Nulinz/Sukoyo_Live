<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode</title>
    <style>
        @page {
            size: 8.3cm auto;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 2px 10px;
            background: white;
            box-sizing: border-box;
        }

        .barcode-columns {
            display: flex;
            flex-direction: row;
            width: 100%;
        }

        .barcode-column {
            width: 50%;
            display: flex;
            flex-direction: column;
        }

        .barcode-box {
            width: 100%;
            border: 1px solid #000;
            border-radius: 2mm;
            padding: 0.2cm;
            box-sizing: border-box;
            background: white;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: start;
            gap: 5px;
            margin-bottom: 2mm;
        }

        .store-name {
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }

        .item-name {
            font-size: 7px;
            text-align: center;
        }

        .barcode-section {
            text-align: center;
            height: 0.6cm;
        }

        .barcode-svg svg {
            max-width: 100%;
            height: 100%;
        }

        .item-code {
            font-size: 7px;
            font-weight: bold;
            text-align: center;
        }

        .bottom-dotted {
            border-bottom: 1px dotted #000;
        }

        .price-section {
            display: flex;
            justify-content: space-between;
            font-size: 7px;
        }

        .price-label {
            font-weight: normal;
        }

        .price-value {
            font-weight: bold;
        }

        @media print {
            .barcode-box {
                page-break-inside: avoid;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body onload="setTimeout(function() { window.print(); }, 500);">

<div class="barcode-columns">

    <!-- Left Column (even indexes: 0, 2, 4, ...) -->
    <div class="barcode-column">
        @for($i = 0; $i < ceil($barcodeData['barcode_count'] / 2); $i++)
            @php $index = $i * 2; @endphp
            @if($index < $barcodeData['barcode_count'])
            <div class="barcode-box">
                <div class="store-name">SUKOYO</div>
                <div class="item-name">{{ $barcodeData['item_name'] }}</div>
                <div class="barcode-section">
                    <div class="barcode-svg">
                        <svg id="barcode-{{ $index }}"></svg>
                    </div>
                </div>
                <div class="item-code">{{ $barcodeData['item_code'] }}</div>
                <div class="bottom-dotted"></div>
                <div class="price-section">
                    <div><span class="price-label">MRP:</span> ₹<span class="price-value">{{ number_format($barcodeData['mrp'], 2) }}</span></div>
                    <div><span class="price-label">Net:</span> ₹<span class="price-value">{{ number_format($barcodeData['net_price'], 2) }}</span></div>
                </div>
            </div>
            @endif
        @endfor
    </div>

    <!-- Right Column (odd indexes: 1, 3, 5, ...) -->
    <div class="barcode-column">
        @for($i = 0; $i < floor($barcodeData['barcode_count'] / 2); $i++)
            @php $index = $i * 2 + 1; @endphp
            @if($index < $barcodeData['barcode_count'])
            <div class="barcode-box">
                <div class="store-name">SUKOYO</div>
                <div class="item-name">{{ $barcodeData['item_name'] }}</div>
                <div class="barcode-section">
                    <div class="barcode-svg">
                        <svg id="barcode-{{ $index }}"></svg>
                    </div>
                </div>
                <div class="item-code">{{ $barcodeData['item_code'] }}</div>
                <div class="bottom-dotted"></div>
                <div class="price-section">
                    <div><span class="price-label">MRP:</span> ₹<span class="price-value">{{ number_format($barcodeData['mrp'], 2) }}</span></div>
                    <div><span class="price-label">Net:</span> ₹<span class="price-value">{{ number_format($barcodeData['net_price'], 2) }}</span></div>
                </div>
            </div>
            @endif
        @endfor
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @for($i = 0; $i < $barcodeData['barcode_count']; $i++)
            JsBarcode("#barcode-{{ $i }}", "{{ $barcodeData['item_code'] }}", {
                format: "CODE128",
                width: 1.2,
                height: 20,
                displayValue: false,
                margin: 0,
                background: "#ffffff",
                lineColor: "#000000"
            });
        @endfor
    });
</script>

</body>
</html>
