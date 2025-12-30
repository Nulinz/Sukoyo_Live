<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers'], function () {

    // Login
    Route::get('/', 'Login@login')->name('login');
    Route::post('/login', 'Login@authenticate')->name('login.submit');
    Route::get('/logout', 'Login@logout')->name('logout');
    Route::post('/change-password', 'Login@changePassword')->name('change.password');


    // Dashboard
    Route::get('dashboard-admin', 'Dashboard@admin')->name('dashboard.admin');
    // Route::get('dashboard-manager', 'Dashboard@manager')->name('dashboard.manager');
    Route::get('dashboard-pos', 'Dashboard@pos')->name('dashboard.pos');
    // Add this route to your web.php file
    Route::get('dashboard-abc', 'Dashboard@abc')->name('dashboard.abc');
    Route::post('dashboard-abc/data', 'Dashboard@getAbcData')->name('dashboard.abc.data');
    Route::get('dashboard/top-selling-products/{month}', 'Dashboard@getTopSellingProducts');
    Route::get('/dashboard/least-selling-products/{month}', 'Dashboard@getLeastSellingProducts');
    Route::get('/dashboard/gross-avenue-data', 'Dashboard@getGrossAvenueData');
    Route::get('/dashboard/loyalty-discount-data', 'Dashboard@getLoyaltyDiscountData');

    Route::get('dashboard-bill', 'Dashboard@bill')->name('dashboard.bill');

    Route::get('/invoice-details1/{id}', 'Dashboard@invoiceDetails')->name('invoice.details');

    // Store
    // Route::get('store-list', 'Store@store_list')->name('store.list');
    // Route::get('store-profile', 'Store@store_profile')->name('store.profile');
    Route::get('store-list', 'Store@store_list')->name('store.list');
    Route::post('store-add', 'Store@store_add')->name('store.add');
    Route::post('store-update/{id}', 'Store@store_update')->name('store.update');
    // routes/web.php
    Route::get('store-profile/{id}', 'Store@storeProfile')->name('store.profile');

    Route::get('store/toggle-status/{id}', 'Store@toggle_status')->name('store.toggle_status');


    // Warehouse
    Route::get('warehouse-list', 'Store@warehouse_list')->name('warehouse.list');

    // Party
    Route::get('class-list', 'Classes@class_list')->name('class.classlist');
    Route::get('class-add', 'Classes@class_add')->name('class.classadd');
    Route::post('class-store', 'Classes@store')->name('class.store');



    Route::get('class-edit/{id}', 'Classes@edit')->name('class.classedit');
    Route::post('class-update/{id}', 'Classes@update')->name('class.update');

    Route::get('class-profile/{id}', 'Classes@class_profile')->name('class.classprofile');
    Route::get('student-profile', 'Classes@student_profile')->name('class.stdprofile');
    Route::get('bookings-list', 'Classes@bookings_list')->name('class.bookingslist');
    Route::get('bookings-add', 'Classes@bookings_add')->name('class.bookingsadd');
    Route::post('bookings-store', 'Classes@store_booking')->name('class.bookingsstore');
    Route::post('get-student-by-contact', 'Classes@getStudentByContact')->name('class.getstudentbycontact');


    // AJAX routes for dynamic dropdowns
    Route::post('get-classes-by-type', 'Classes@get_classes_by_type')->name('class.getbytype');
    Route::post('get-class-details', 'Classes@get_class_details')->name('class.getdetails');
    Route::put('bookings-update/{id}', 'Classes@bookings_update')->name('class.bookingsupdate');

    Route::get('bookings-edit/{id}', 'Classes@bookings_edit')->name('class.bookingsedit');

    // routes/web.php
    Route::get('bookings-profile/{id}', 'Classes@bookings_profile')->name('class.bookingsprofile');
    // routes/web.php
    Route::post('bookings/update-status', 'Classes@update_booking_status')->name('bookings.updateStatus');

    Route::get('tutor-list', 'Classes@tutor_list')->name('class.tutorlist');
    Route::post('tutor-toggle/{id}', 'Classes@tutor_toggle_status')->name('class.tutortoggle');
    // routes/web.php
    Route::get('tutor-add', 'Classes@tutor_add')->name('class.tutoradd');
    Route::post('tutor-store', 'Classes@tutor_store')->name('class.tutorstore');

    // routes/web.php

    Route::get('tutor-edit/{id}', 'Classes@tutor_edit')->name('class.tutoredit');
    Route::post('tutor-update/{id}', 'Classes@tutor_update')->name('class.tutorupdate');

    Route::get('tutor-profile/{id}', 'Classes@tutor_profile')->name('class.tutorprofile');

    // Party
    Route::get('vendor-list', 'Party@vendor_list')->name('party.vendorlist');
    Route::post('vendor-bulk-upload', 'Party@vendor_bulk_upload')->name('party.vendorbulkupload');
    Route::get('vendor-toggle-status/{id}', 'Party@vendor_toggle_status')->name('party.vendor_toggle_status');
    Route::get('vendor-add', 'Party@vendor_add')->name('party.vendoradd');
    Route::post('vendor-store', 'Party@vendor_store')->name('party.vendorstore');
    Route::get('vendor-edit/{id}', 'Party@vendor_edit')->name('party.vendoredit');
    Route::put('vendor-update/{id}', 'Party@vendor_update')->name('party.vendorupdate');
    Route::get('vendor-profile/{id}', 'Party@vendor_profile')->name('party.vendorprofile');


    // Route::get('customer-list', 'Party@customer_list')->name('party.customerlist');
    // Route::get('customer-add', 'Party@customer_add')->name('party.customeradd');
    // Route::get('customer-edit', 'Party@customer_edit')->name('party.customeredit');
    // No need to "use" the controller at the top

    Route::get('customer-list', 'Party@customer_list')->name('party.customerlist');
    Route::post('customer-store', 'Party@store_customer')->name('party.customer.store');
    Route::post('customer-update/{id}', 'Party@update_customer')->name('party.customer.update');
    Route::get('customer-toggle-status/{id}', 'Party@toggleCustomerStatus')->name('party.customerstatus');

    Route::post('category-bulk-upload', 'Inventory@category_bulk_upload')->name('inventory.categorybulkupload');

    // web.php
    Route::get('customer-profile/{id}', 'Party@customer_profile')->name('party.customerprofile');

    // Enquiry
    Route::get('enquiry-list', 'Party@enquiry_list')->name('enquiry.list');
    Route::post('enquiry-store', 'Party@store_enquiry')->name('enquiry.store');
    Route::post('enquiry-update/{id}', 'Party@update_enquiry')->name('enquiry.update');
    Route::post('enquiry-status/{id}', 'Party@update_status')->name('enquiry.status');


    // Inventory
    Route::get('brand-list', 'Inventory@brand_list')->name('inventory.brandlist');
    Route::post('brand-store', 'Inventory@store_brand')->name('inventory.brandstore');
    Route::post('brand-update/{id}', 'Inventory@update_brand')->name('inventory.brandupdate');
    Route::get('brand-toggle/{id}', 'Inventory@toggle_brand_status')->name('inventory.brandtoggle');
    Route::get('category-list', 'Inventory@category_list')->name('inventory.categorylist');
    Route::post('category-store', 'Inventory@store_category')->name('inventory.categorystore');
    Route::post('category-update/{id}', 'Inventory@update_category')->name('inventory.categoryupdate');
    Route::get('category-toggle/{id}', 'Inventory@toggle_category_status')->name('inventory.categorystatus');
    // routes/web.php
    Route::post('subcategory-bulk-upload', 'Inventory@subcategory_bulk_upload')->name('inventory.subcategorybulkupload');
    Route::get('check-item-code', 'Inventory@checkItemCode')->name('inventory.checkItemCode');


    Route::get('subcategory-list', 'Inventory@subcategory_list')->name('inventory.subcategorylist');
    Route::post('subcategory-store', 'Inventory@store_subcategory')->name('inventory.subcategorystore');
    Route::post('subcategory-update/{id}', 'Inventory@update_subcategory')->name('inventory.subcategoryupdate');
    Route::get('subcategory-toggle/{id}', 'Inventory@toggle_subcategory')->name('inventory.subcategorytoggle');
    // web.php
    Route::get('repacking-list', 'Inventory@repacking_list')->name('inventory.repackinglist');
    Route::post('repacking-store', 'Inventory@repacking_store')->name('inventory.repackingstore');
    Route::get('repacking-add', 'Inventory@repacking_add')->name('inventory.repackingadd');
    Route::put('repacking-update/{id?}', 'Inventory@repacking_update')->name('inventory.repackingupdate');
    Route::get('/inventory/variant-names', 'Inventory@getVariantNames')->name('inventory.variantnames');

    Route::get('repacking-edit', 'Inventory@repacking_edit')->name('inventory.repackingedit');
    Route::get('item-list', 'Inventory@item_list')->name('inventory.itemlist');
    Route::get('get-item-details1/{id}', 'Inventory@getItemDetails')->name('inventory.get_item_details');
    Route::post('generate-barcode', 'Inventory@generateBarcode')->name('inventory.generate_barcode');
    // Batch routes
    Route::post('batch', 'Inventory@storeBatch')->name('batch.store');
    Route::get('batch/edit/{id}', 'Inventory@editBatch')->name('batch.edit');
    Route::put('batch/update/{id}', 'Inventory@updateBatch')->name('batch.update');
    Route::delete('batch/delete/{id}', 'Inventory@deleteBatch')->name('batch.delete');

    Route::post('/barcode/generate', 'Inventory@generate')->name('barcode.generate');



    Route::get('uom/{itemId}', 'Inventory@index')->name('uom.index');
    Route::post('uom', 'Inventory@store')->name('uom.store');
    Route::get('uom/edit/{id}', 'Inventory@show')->name('uom.edit');
    Route::put('uom/update/{id}', 'Inventory@update')->name('uom.update');


    Route::get('/inventory/transfer-items', 'Inventory@transfer_items')->name('inventory.transferitems');
    Route::post('/inventory/store-transfer', 'Inventory@store_transfer')->name('inventory.storetransfer');
    Route::get('/inventory/transfer-items/list', 'Inventory@transfer_list')->name('inventory.transferlist');
    Route::get('/inventory/transfer-items/profile/{id}', 'Inventory@transfer_profile')->name('inventory.transferprofile');
    Route::get('/inventory/transfer-items/add-more/{id}', 'Inventory@add_more_transfer')->name('inventory.addmoretransfer');
    Route::post('/inventory/transfer-items/store-more/{id}', 'Inventory@store_more_transfer')->name('inventory.storemoretransfer');
    Route::get('/inventory/get-item-stock/{id}', 'Inventory@getItemStock')->name('inventory.getitemstock');
    Route::get('/inventory/get-all-items', 'Inventory@getAllItems')->name('inventory.getallitems');
    Route::get('/inventory/get-all-items-with-stock', 'Inventory@getAllItemsWithStock')->name('inventory.getallitemswithstock');
    Route::post('/inventory/store-transfer-batch', 'Inventory@store_transfer_batch')->name('inventory.storetransferbatch');


    Route::post('brand-bulk-upload', 'Inventory@brand_bulk_upload')->name('inventory.brandbulkupload');
    Route::post('/inventory/item-bulk-upload', 'Inventory@item_bulk_upload')->name('inventory.item_bulk_upload');
    Route::get('/get-subcategories-by-category', [App\Http\Controllers\InventoryController::class, 'getSubcategories']);

    Route::get('/get-subcategories-by-category', 'Inventory@getSubcategories');



    Route::get('item-profile/{id}', 'Inventory@item_profile')->name('inventory.itemprofile');
    Route::get('item-toggle-status/{id}', 'Inventory@item_toggle_status')->name('inventory.item_toggle_status');

    Route::get('item-add', 'Inventory@item_add')->name('inventory.itemadd');
    Route::post('item-store', 'Inventory@store_item')->name('inventory.itemstore');
    Route::get('item-edit/{id}', 'Inventory@item_edit')->name('inventory.itemedit');
    Route::post('item-update/{id}', 'Inventory@item_update')->name('inventory.itemupdate');

    // Route::get('item-profile', 'Inventory@item_profile')->name('inventory.itemprofile');

    // Stock
    Route::get('lowstock-list', 'Stock@lowstock_list')->name('stock.lowstock');
    Route::get('get-lowstock-items', 'Stock@getLowStockItems')->name('stock.getLowStockItems');
    // routes/web.php
    Route::get('overstock-profile', 'Stock@overstock_list')->name('stock.overstock');
    Route::get('get-subcategories/{category_id}', 'Stock@getSubcategories');
    Route::get('zeromovement', 'Stock@zeroMovementPage')
        ->name('stock.zeromovement.page');
    Route::get('zeromovement-list', 'Stock@zero_list')
        ->name('stock.zeromovement.list');



    // Route::get('zeromovement-list', 'Stock@zero_list')->name('stock.zeromovement');

    // Sales
    Route::get('sales-list', 'Sales@sales_list')->name('sales.list');
    Route::get('sales-profile', 'Sales@sales_profile')->name('sales.profile');
    Route::get('/sales/return-vouchers', 'Sales@return_vouchers_list')->name('sales.return_vouchers');

    // Purchase
    Route::get('purchase-inv-list', 'Purchase@inv_list')->name('purchase.inv_list');
    // routes/web.php
    Route::delete('purchase-inv-delete/{id}', 'Purchase@inv_delete')->name('purchase.inv_delete');
    Route::delete('purchase-order-delete/{id}', 'Purchase@order_delete')->name('purchase.order_delete');


    Route::get('purchase-inv-add', 'Purchase@inv_add')->name('purchase.inv_add');
    Route::post('purchase-invoice-store', 'Purchase@storeInvoice')->name('purchase.invoice.store');

    Route::get('purchase-order-daxa/{id}', 'Purchase@get_order_data')->name('purchase.get_order_data');
    Route::get('purchase-inv-edit', 'Purchase@inv_edit')->name('purchase.inv_edit');
    Route::get('purchase-inv-profile/{id}', 'Purchase@inv_profile')->name('purchase.inv_profile');
    Route::get('/vendor-details/{id}', 'Purchase@getVendorDetails');

    //purchase order
    Route::get('purchase-order-list', 'Purchase@order_list')->name('purchase.order_list');
    Route::post('purchase-order-toggle-status/{id}', 'Purchase@toggle_status')->name('purchase.order_toggle_status');
    Route::get('purchase-order-add', 'Purchase@order_add')->name('purchase.order_add');
    Route::post('purchase-order-store', 'Purchase@order_store')->name('purchase.order_store');
    Route::get('purchase-order-edit/{id}', 'Purchase@order_edit')->name('purchase.order_edit');
    Route::put('purchase-order-update/{id}', 'Purchase@order_update')->name('purchase.order_update');

    Route::get('purchase-order-profile/{id}', 'Purchase@order_profile')->name('purchase.order_profile');
    Route::get('api/store-items', 'POS@getStoreItemsAPI')->name('pos.store-items-api');
    // POS Routes
    Route::get('pos-bill', 'POS@pos_bill')->name('pos.pos-bill');
    Route::post('save-bill', 'POS@saveBill')->name('save.bill');
    Route::get('check-customer/{contact}', 'POS@checkCustomer')->name('pos.checkCustomer');
    Route::post('add-customer', 'POS@addCustomer')->name('pos.addCustomer');
    Route::get('sales-invoices', 'POS@getSalesInvoices')->name('pos.invoices');
    Route::get('invoice-details/{id}', 'POS@getInvoiceDetails')->name('pos.invoice-details');
    Route::get('/get-item-details/{id}', 'POS@getItemDetails');
    Route::get('sales-by-employee', 'POS@getSalesByEmployee')->name('pos.sales-by-employee');
    Route::get('sales-by-store', 'POS@getSalesByStore')->name('pos.sales-by-store');
    Route::post('/save-corporate-bill', 'POS@saveCorporateBill')->name('save.corporate.bill');
    Route::post('/gst/verify', 'POS@gst_verify')->name('gst.add');
    Route::get('pos/get-items', 'POS@getItemsForPos')->name('pos.getItems');
    Route::get('/corporate-invoice/print/{id}', 'POS@printCorporateInvoice')->name('corporate.invoice.print');
    Route::post('/check-return-voucher-balance', 'POS@checkReturnVoucherBalance');
    Route::post('/pos/get-bill-details', 'POS@getBillDetails')->name('pos.getBillDetails');
    Route::post('/pos/process-return', 'POS@processReturn')->name('pos.processReturn');

    Route::post('/check-gift-card-balance', 'POS@checkGiftCardBalance')->name('check.gift.card.balance');
    Route::get('/items/by-code/{code}', 'POS@getItemsByCode');

    Route::post('/ajax/fetch-item-by-code', 'POS@fetchItemByCode')
        ->name('fetch.item.by.code');


    Route::get('/ajax-items', 'POS@ajaxItemList')
        ->name('ajax.items');


    Route::post('/pos/search-barcode', 'POS@searchByBarcode')
        ->name('pos.search.barcode');

    // If you want to add more gift card related routes:
    Route::group(['prefix' => 'gift-cards'], function () {
        Route::post('/check-balance', 'POS@checkGiftCardBalance');
        Route::get('/history/{card_number}', 'POS@getGiftCardHistory');
        Route::post('/validate', 'POS@validateGiftCard');
    });
    // Optional: API routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::get('store-items', 'POS@getStoreItemsAPI')->name('pos.store-items-api');
        Route::get('sales-invoices', 'POS@getSalesInvoices');
        Route::get('sales-by-employee', 'POS@getSalesByEmployee');
        Route::get('sales-by-store', 'POS@getSalesByStore');
    });
    // Accounts
    Route::get('payment-list', 'Accounts@payment_list')->name('accounts.payment');

    Route::post('get-invoices-by-vendor', 'Accounts@getInvoicesByVendor')->name('get.invoices.by.vendor');
    Route::post('get-pending-amount', 'Accounts@getPendingAmount')->name('get.pending.amount');
    Route::post('store-payment', 'Accounts@store_payment')->name('store.payment');

    Route::get('expense-list', 'Accounts@expense')->name('accounts.expense');
    Route::post('add-expense-category', 'Accounts@storeExpenseCategory')->name('expense.category.store');
    Route::post('add-expense', 'Accounts@storeExpense')->name('expense.store');
    Route::get('expenses-by-category', 'Accounts@getExpensesByCategory')->name('expenses.by.category');
    Route::get('bankacct-list', 'Accounts@bank_account')->name('accounts.bank_account');
    Route::post('bankacct-store', 'Accounts@store_bank_account')->name('accounts.bank_account.store');
    Route::get('cash-list', 'Accounts@cash')->name('accounts.cash');
    Route::post('collect-cash', 'Accounts@storeCash')->name('cash.store');
    Route::post('bank-transfer-store', 'Accounts@storeBankTransfer')->name('bank.transfer.store');

    // Attendance
    Route::get('daily-attendance', 'Attendance@daily')->name('attd.daily');
    Route::post('get-employees-by-store', 'Attendance@getEmployeesByStore')->name('attendance.getEmployeesByStore');
    Route::post('get-attendance-by-filters', 'Attendance@getAttendanceByFilters')->name('attendance.getAttendanceByFilters');

    Route::get('/attendance', 'Attendance@index')->name('attendance.index');
    Route::get('/attendance/create', 'Attendance@create')->name('attendance.create');
    Route::post('/attendance', 'Attendance@store')->name('attendance.store');
    Route::get('/attendance/{id}/edit', 'Attendance@edit')->name('attendance.edit');
    Route::put('/attendance/{id}', 'Attendance@update')->name('attendance.update');

    // API route for loading employees by store (for admin dropdown)
    Route::get('/api/employees-by-store/{storeId}', 'Attendance@getEmployeesByStore');
    Route::get('monthly-attendance', 'Attendance@monthly')->name('attendance.monthly');
    Route::post('monthly-attendance', 'Attendance@monthly')->name('attendance.monthly');

    // AJAX Routes (if not already present)
    Route::post('get-employees-by-store', 'Attendance@getEmployeesByStore')->name('attendance.getEmployeesByStore');
    Route::post('get-monthly-attendance', 'Attendance@getMonthlyAttendance')->name('attendance.getMonthlyAttendance');

    Route::get('individual-attendance', 'Attendance@individual')->name('attendance.individual');
    Route::post('individual-attendance', 'Attendance@individual')->name('attendance.individual');
    Route::post('get-individual-attendance', 'Attendance@getIndividualAttendance')->name('attendance.getIndividualAttendance');


    //offers
    Route::get('giftcard-profile/{id}', 'Offers@gift_profile')->name('offers.giftprofile');
    Route::post('loyaltypoints-store', 'Offers@lp_store')->name('offers.lpstore');
    Route::get('loyaltypoints-list', 'Offers@lp_list')->name('offers.lplist');
    Route::get('loyaltypoints-edit', 'Offers@lp_edit')->name('offers.lpedit');
    Route::get('giftcard-list', 'Offers@gift_list')->name('offers.giftlist');
    Route::get('giftcard-add', 'Offers@gift_add')->name('offers.giftadd');
    Route::post('giftcard-store', 'Offers@gift_store')->name('offers.giftstore');
    Route::get('voucher-profile/{id}', 'Offers@voucher_profile')->name('offers.voucherprofile');
    Route::get('voucher-list', 'Offers@voucher_list')->name('offers.voucherlist');
    Route::get('voucher-add', 'Offers@voucher_add')->name('offers.voucheradd');
    Route::post('voucher-store', 'Offers@voucher_store')->name('offers.voucherstore');

    Route::get('/get-categories/{brandId}', 'Offers@getCategories');
    Route::get('/get-subcategories/{categoryId}', 'Offers@getSubcategories');
    Route::get('/get-items/{brandId}/{categoryId}/{subcatId}', 'Offers@getItems');

    // Reports
    Route::get('reports-list', 'Reports@reports_list')->name('reports.list');
    Route::get('profit-loss', 'Reports@profit_loss')->name('reports.profit_loss');
    Route::get('reports/item-sales-purchase-summary', 'Reports@item_sales_purchase_summary')->name('reports.item-sales-purchase-summary');
    Route::get('/reports/stock-summary', 'Reports@stock_summary')->name('reports.stock_summary');
    Route::get('/reports/get-subcategories-by-category', 'Reports@get_subcategories_by_category')->name('reports.get_subcategories_by_category');
    Route::get('low-stock-summary', 'Reports@low_stock_summary')->name('reports.low_stock');
    Route::get('api/low-stock-items', 'Reports@getLowStockItems')->name('api.low_stock_items');
    Route::get('/reports/item-party', 'Reports@item_report_by_party')->name('reports.item_party');
    Route::get('/api/item-party-items', 'Reports@getItemPartyItems')->name('api.item_party_items');
    Route::get('/reports/sales-summary', 'Reports@sales_summary')->name('reports.sales_summary');
    Route::get('/api/sales-summary-items', 'Reports@getSalesSummaryItems')->name('api.sales_summary_items');
    Route::get('/reports/vendor-report', 'Reports@vendor_report')->name('reports.vendor_report');
    Route::get('/api/vendor-report-items', 'Reports@getVendorReportItems')->name('api.vendor_report_items');
    Route::get('reports/vendor-outstanding', 'Reports@vendor_outstanding_report')->name('reports.vendor_outstanding');
    Route::get('reports/vendor-outstanding/items', 'Reports@getVendorOutstandingItems')->name('reports.vendor_outstanding.items');
    Route::get('reports/vendor-outstanding/vendor/{vendorId}/transactions', 'Reports@getVendorTransactionDetails')->name('reports.vendor_outstanding.transactions');
    Route::get('reports/vendor-outstanding/aging', 'Reports@vendor_aging_analysis')->name('reports.vendor_outstanding.aging');
    Route::get('reports/vendor-statement', 'Reports@vendor_statement')->name('reports.vendor_statement');
    Route::get('reports/vendor-statement/download', 'Reports@vendor_statement_download')->name('reports.vendor_statement.download');
    Route::get('/reports/purchase-summary', 'Reports@purchase_summary')->name('reports.purchase_summary');
    Route::get('/reports/purchase-summary/items', 'Reports@getPurchaseSummaryItems')->name('reports.purchase_summary.items');
    Route::get('/reports/bill-wise-profit', 'Reports@bill_wise_profit')->name('reports.bill_wise_profit');
    Route::get('/reports/bill-wise-profit-items', 'Reports@getBillWiseProfitItems')->name('reports.bill_wise_profit_items');
    Route::get('/reports/bill-item-details/{billId}', 'Reports@getBillItemDetails')->name('reports.bill_item_details');
    Route::get('reports/gstr3b-purchase', 'Reports@index')
        ->name('reports.gstr3b-purchase');

    Route::get('reports/gstr3b-purchase/export', 'Reports@exportExcel')
        ->name('reports.gstr3b-purchase.export');

    // Settings
    Route::get('company-profile', 'Settings@company_profile')->name('settings.companyprofile');
    Route::get('company-edit', 'Settings@company_edit')->name('settings.companyedit');
    Route::post('company-update', 'Settings@company_update')->name('settings.companyupdate');


    Route::get('pos-system', 'Settings@pos_list')->name('settings.pos');
    Route::post('pos-system/store', 'Settings@store_pos')->name('settings.pos.store');
    Route::post('pos-system/update/{id}', 'Settings@update_pos')->name('settings.pos.update');
    Route::get('pos-system/toggle/{id}', 'Settings@toggle_pos_status')->name('settings.pos.toggle');


    Route::get('employee-list', 'Settings@employee_list')->name('settings.emp_list');
    // Route::get('employee-add', 'Settings@employee_add')->name('settings.emp_add');
    Route::get('employee-add', 'Settings@employee_add')->name('settings.emp_add');
    Route::post('employee-store', 'Settings@employee_store')->name('settings.employee_store');

    Route::get('employee-list', 'Settings@employee_list')->name('settings.emp_list');
    // Route::get('employee-edit/{id}', 'Settings@employee_edit')->name('settings.emp_edit');
    // Show edit form
    Route::get('employee-edit/{id}', 'Settings@employee_edit')->name('settings.emp_edit');

    // Update employee (form submit)
    Route::post('employee-update/{id}', 'Settings@employee_update')->name('settings.emp_update');


    Route::get('employee-toggle-status/{id}', 'Settings@employee_toggle_status')->name('settings.employee_toggle_status');


    // Route::get('employee-edit', 'Settings@employee_edit')->name('settings.emp_edit');




    //POS DASHBOARD
    Route::get('enquiry-lists', 'POS_DashboardController@enquiry_lists')->name('enquiry.lists');
    Route::post('enquiry-store-data', 'POS_DashboardController@store_enquiry_data')->name('enquiry.store.data');
    Route::post('enquiry-update_data/{id}', 'POS_DashboardController@update_enquiry_data')->name('enquiry.update.data');

    Route::get('sales-invoice-list', 'POS_DashboardController@sales_invoice_list')->name('sales.invoice.list');
    Route::get('sales-profile-details/{id}', 'POS_DashboardController@sales_profile_details')->name('sales.profile.details');



    Route::get('bookings-list-data', 'POS_DashboardController@bookings_list_data')->name('class.bookingslist.data');
    Route::get('bookings-add-data', 'POS_DashboardController@bookings_add_data')->name('class.bookingsadd.data');
    Route::post('bookings-store-data', 'POS_DashboardController@store_booking_data')->name('class.bookingsstore.data');


    Route::put('bookings-update-data/{id}', 'POS_DashboardController@bookings_update_data')->name('class.bookingsupdate.data');

    Route::get('bookings-edit-data/{id}', 'POS_DashboardController@bookings_edit_data')->name('class.bookingsedit.data');

    // routes/web.php
    Route::get('bookings-profile-data/{id}', 'POS_DashboardController@bookings_profile_data')->name('class.bookingsprofile.data');

    // Route::get('/store-dashboard',function(){
//     return view('new');
// });

    Route::get('/dashboard/manager', 'POS_DashboardController@manager')
        ->name('dashboard.manager');
    Route::get('/pwa-demo', function () {
        return view('pwa-demo');
    });

    Route::get('/check-php', function () {
        return response()->json([
            'max_input_vars' => ini_get('max_input_vars'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'post_max_size' => ini_get('post_max_size'),
        ]);
    });

    Route::get('/export-items', 'ItemController@exportItemsCsv');

});
