<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('assets/css/profile.css')); ?>">

<style>
    @media screen and (min-width: 990px) {
        .col-xl-3 {
            width: 20%;
        }
    }
    
    /* Print-specific styles */
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: Arial, sans-serif !important;
        }
        
        .no-print {
            display: none !important;
        }
        
        .body-div {
            padding: 15px !important;
            margin: 0 !important;
        }
        
        .body-head h4 {
            font-size: 18px !important;
            font-weight: bold !important;
            margin-bottom: 15px !important;
            text-align: center !important;
            border-bottom: 2px solid #333 !important;
            padding-bottom: 10px !important;
        }
        
        .cardhead h5 {
            font-size: 16px !important;
            font-weight: bold !important;
            margin-bottom: 15px !important;
            border-bottom: 1px solid #ddd !important;
            padding-bottom: 5px !important;
        }
        
        .maincard {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            padding: 15px !important;
            margin-bottom: 20px !important;
            page-break-inside: avoid !important;
        }
        
        .maincard .col-xl-3,
        .maincard .col-md-4,
        .maincard .col-sm-12 {
            width: 25% !important;
            float: left !important;
            margin-bottom: 10px !important;
            padding: 5px !important;
            box-sizing: border-box !important;
        }
        
        .maincard h6 {
            font-size: 11px !important;
            font-weight: bold !important;
            color: #666 !important;
            margin-bottom: 3px !important;
        }
        
        .maincard h5 {
            font-size: 13px !important;
            font-weight: normal !important;
            color: #000 !important;
            margin-bottom: 0 !important;
        }
        
        .table-wrapper {
            margin-top: 20px !important;
        }
        
        .table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 11px !important;
            margin: 0 !important;
        }
        
        .table th,
        .table td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            text-align: left !important;
            vertical-align: top !important;
        }
        
        .table th {
            background-color: #f8f9fa !important;
            font-weight: bold !important;
            font-size: 11px !important;
        }
        
        .table td {
            font-size: 10px !important;
        }
        
        .filter-container {
            display: none !important;
        }
        
        .listtable {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Clear floats */
        .maincard:after {
            content: "" !important;
            display: table !important;
            clear: both !important;
        }
        
        /* Page break settings */
        .body-head:nth-of-type(2) {
            page-break-before: avoid !important;
            margin-top: 20px !important;
        }
    }
</style>

<div class="body-div p-3" id="printableArea">
    <div class="body-head mb-3">
        <h4>Sales Profile</h4>
        <div class="no-print">
            <button class="exportbtn" onclick="downloadPDF()">
                <i class="fas fa-download pe-2"></i>Download PDF
            </button>
            <button class="exportbtn" onclick="printPage()">
                <i class="fas fa-print pe-2"></i>Print
            </button>
        </div>
    </div>

    <div class="mainbdy d-block">

        <!-- Right Content -->
        <div class="contentright">
            <div class="tab-content">
               <div class="cards mb-2">
    <div class="maincard row py-0 mb-3">
        <div class="cardhead my-3">
            <h5>Details</h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Store</h6>
            <h5 class="mb-0"><?php echo e($sale->store_name); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Bill Type</h6>
            <h5 class="mb-0"><?php echo e(ucfirst($sale->status)); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">POS System</h6>
            <h5 class="mb-0"><?php echo e($sale->employee_name); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Invoice No</h6>
            <h5 class="mb-0"><?php echo e('INV' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Customer Name</h6>
            <h5 class="mb-0"><?php echo e($sale->customer->name ?? 'N/A'); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Date</h6>
            <h5 class="mb-0"><?php echo e($sale->invoice_date->format('d-m-Y')); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Payment Type</h6>
            <h5 class="mb-0"><?php echo e($sale->mode_of_payment); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Discount</h6>
            <h5 class="mb-0">₹ <?php echo e(number_format($sale->total_discount, 2)); ?></h5>
        </div>
        <div class="col-sm-12 col-md-4 col-xl-3 mb-3">
            <h6 class="mb-1">Total Amount</h6>
            <h5 class="mb-0">₹ <?php echo e(number_format($sale->grand_total, 2)); ?></h5>
        </div>
    </div>
</div>

<div class="body-head mt-3">
    <h4>Item List</h4>
</div>

<div class="container-fluid listtable">
    <div class="filter-container no-print">
        <div class="filter-container-start">
            <select class="form-select filter-option" id="headerDropdown1">
                <option value="All" selected>All</option>
            </select>
            <input type="text" class="form-control" id="filterInput1" placeholder=" Search">
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table table-bordered" id="table1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Items</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Amount</th>
                </tr>
            </thead>
<tbody>
                    <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($item->item->item_name ?? 'N/A'); ?></td>
                            <td><?php echo e($item->unit); ?></td>
                            <td><?php echo e($item->qty); ?></td>
                            <td><?php echo e($item->discount); ?>%</td>
                            <td><?php echo e($item->tax); ?>%</td>
                            <td>₹ <?php echo e(number_format($item->amount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
        </table>
    </div>
</div>
            </div>
        </div>

    </div>
</div>

<!-- Include jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    $(document).ready(function() {
        function initTable(tableId, dropdownId, filterInputId) {
            var table = $(tableId).DataTable({
                "paging": false,
                "searching": true,
                "ordering": true,
                "order": [0, "asc"],
                "bDestroy": true,
                "info": false,
                "responsive": true,
                "pageLength": 30,
                "dom": '<"top"f>rt<"bottom"ilp><"clear">',
            });
            $(tableId + ' thead th').each(function(index) {
                var headerText = $(this).text();
                if (headerText != "" && headerText.toLowerCase() != "action") {
                    $(dropdownId).append('<option value="' + index + '">' + headerText + '</option>');
                }
            });
            $(filterInputId).on('keyup', function() {
                var selectedColumn = $(dropdownId).val();
                if (selectedColumn !== 'All') {
                    table.column(selectedColumn).search($(this).val()).draw();
                } else {
                    table.search($(this).val()).draw();
                }
            });
            $(dropdownId).on('change', function() {
                $(filterInputId).val('');
                table.search('').columns().search('').draw();
            });
            $(filterInputId).on('keyup', function() {
                table.search($(this).val()).draw();
            });
        }
        // Initialize each table
        initTable('#table1', '#headerDropdown1', '#filterInput1');
    });

    // Print function with improved formatting
    function printPage() {
        // Create a new window for printing with clean styles
        const printWindow = window.open('', '_blank');
        
        // Get the content to print
        const printContent = document.getElementById('printableArea').cloneNode(true);
        
        // Remove no-print elements
        const noPrintElements = printContent.querySelectorAll('.no-print');
        noPrintElements.forEach(element => element.remove());
        
        // Create the print document
        const printDocument = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Sales Profile - <?php echo e('INV' . str_pad($sale->id, 4, '0', STR_PAD_LEFT)); ?></title>
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
                        padding: 20px;
                    }
                    
                    .body-head h4 {
                        font-size: 20px;
                        font-weight: bold;
                        text-align: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #333;
                        padding-bottom: 10px;
                    }
                    
                    .cardhead h5 {
                        font-size: 16px;
                        font-weight: bold;
                        margin-bottom: 15px;
                        border-bottom: 1px solid #ddd;
                        padding-bottom: 5px;
                    }
                    
                    .maincard {
                        border: 1px solid #ddd;
                        padding: 15px;
                        margin-bottom: 25px;
                    }
                    
                    .row {
                        display: flex;
                        flex-wrap: wrap;
                        margin: 0;
                    }
                    
                    .col-xl-3, .col-md-4, .col-sm-12 {
                        flex: 0 0 25%;
                        max-width: 25%;
                        padding: 8px;
                    }
                    
                    .maincard h6 {
                        font-size: 11px;
                        font-weight: bold;
                        color: #666;
                        margin-bottom: 3px;
                    }
                    
                    .maincard h5 {
                        font-size: 13px;
                        font-weight: normal;
                        color: #000;
                        margin: 0;
                    }
                    
                    .table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                    }
                    
                    .table th,
                    .table td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                        font-size: 11px;
                    }
                    
                    .table th {
                        background-color: #f8f9fa;
                        font-weight: bold;
                    }
                    
                    .mt-3 {
                        margin-top: 25px;
                    }
                    
                    .mb-3 {
                        margin-bottom: 15px;
                    }
                </style>
            </head>
            <body>
                ${printContent.outerHTML}
            </body>
            </html>
        `;
        
        // Write the document and print
        printWindow.document.write(printDocument);
        printWindow.document.close();
        
        // Wait for content to load then print
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    }

    // Download PDF function
    function downloadPDF() {
        // Show loading indicator
        const downloadBtn = document.querySelector('button[onclick="downloadPDF()"]');
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin pe-2"></i>Generating PDF...';
        downloadBtn.disabled = true;

        // Hide no-print elements temporarily
        const noPrintElements = document.querySelectorAll('.no-print');
        noPrintElements.forEach(element => {
            element.style.display = 'none';
        });

        // Get the printable area
        const element = document.getElementById('printableArea');
        
        // Configure html2canvas options
        const options = {
            scale: 2,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            width: element.offsetWidth,
            height: element.offsetHeight
        };

        html2canvas(element, options).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            
            // Create PDF
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            // Calculate dimensions to fit A4
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();
            const imgWidth = canvas.width;
            const imgHeight = canvas.height;
            const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
            const imgX = (pdfWidth - imgWidth * ratio) / 2;
            const imgY = 10;

            pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio);
            
            // Generate filename with invoice number and date
            const invoiceNo = '<?php echo e("INV" . str_pad($sale->id, 4, "0", STR_PAD_LEFT)); ?>';
            const date = new Date().toISOString().split('T')[0];
            const filename = `Sales_Profile_${invoiceNo}_${date}.pdf`;
            
            // Download the PDF
            pdf.save(filename);

            // Show no-print elements back
            noPrintElements.forEach(element => {
                element.style.display = 'block';
            });

            // Reset button
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;
        }).catch(error => {
            console.error('Error generating PDF:', error);
            alert('Error generating PDF. Please try again.');
            
            // Show no-print elements back
            noPrintElements.forEach(element => {
                element.style.display = 'block';
            });

            // Reset button
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;
        });
    }
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/sukoyo/resources/views/sales/profile.blade.php ENDPATH**/ ?>