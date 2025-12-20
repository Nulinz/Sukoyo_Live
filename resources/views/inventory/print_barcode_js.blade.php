<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode</title>
    <style>
        @page {
            /* size: 8.4cm auto; */
            height: 100%;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0px 20px;
            padding: 0.2cm;
            height: 3cm;
            width: 8.4cm;
            background: white;
            box-sizing: border-box;
        }

        .barcode-containe1r {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Two equal columns */
            gap: 0; /* No gap */
            width: 100%;
        }
        .barcode-container{
            display: flex;
            justify-content:around;
            width: 100%;
            height:3cm;
        }

        .barcode-box {
            width: 100%;
            height: 3cm;
            /* border: 1px solid #000; */
            /* border-radius: 2mm; */
            padding: 0.2cm;
            box-sizing: border-box;
            background: white;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin-bottom: 2mm;
        }

        .store-name {
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            margin-bottom: 1mm;
        }

        .item-name {
            font-size: 7px;
            text-align: center;
            line-height: 1.1;
            margin-bottom: 1mm;
        }

        .barcode-section {
            text-align: center;
            height: 10mm;
            display: flex;
            align-items: center;
            justify-content: center;
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
            margin: 1mm 0;
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

<div class="barcode-container">
    @for($i = 0; $i < $barcodeData['barcode_count']; $i+=2)
        {{-- Left Label --}}
        <div class="barcode-box">
            <div class="store-name">SUKOYO</div>
            <div class="item-name">{{ $barcodeData['item_name'] }}</div>
            <div class="barcode-section">
                <div class="barcode-svg">
                    <svg id="barcode-{{ $i }}"></svg>
                </div>
            </div>
            <div class="item-code">{{ $barcodeData['item_code'] }}</div>
            <div class="bottom-dotted"></div>
            <div class="price-section">
                <div><span class="price-label">MRP:</span> ₹<span class="price-value">{{ number_format($barcodeData['mrp'], 2) }}</span></div>
                <div><span class="price-label">Net:</span> ₹<span class="price-value">{{ number_format($barcodeData['net_price'], 2) }}</span></div>
            </div>
        </div>

        {{-- Right Label --}}
        @if($i + 1 < $barcodeData['barcode_count'])
            <div class="barcode-box">
                <div class="store-name">SUKOYO</div>
                <div class="item-name">{{ $barcodeData['item_name'] }}</div>
                <div class="barcode-section">
                    <div class="barcode-svg">
                        <svg id="barcode-{{ $i + 1 }}"></svg>
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
