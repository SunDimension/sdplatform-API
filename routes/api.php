<?php


use App\Http\Controllers\CreateItemController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SalesOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {

    // Route::middleware('auth:sanctum')->get('/users', [UsersController::class, 'index']);

    Route::prefix('users')->group(function () {
        // Get all users
        Route::get('/', [UsersController::class, 'index'])->name('users.index');

        // Create a new user
        Route::post('/', [UsersController::class, 'store'])->name('users.store');

        // Get a specific user
        Route::get('/{user}', [UsersController::class, 'show'])->name('users.show');

        // Update a specific user
        Route::put('/{user}', [UsersController::class, 'update'])->name('users.update');

        // Delete a specific user
        Route::delete('/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

        // Assign roles to a user
        Route::post('/{user}/roles/assign', [UsersController::class, 'assignRole'])->name('users.assignRole');

        // Remove roles from a user
        Route::post('/{user}/roles/remove', [UsersController::class, 'removeRole'])->name('users.removeRole');

        // Sync roles for a user (replace existing roles with new ones)
        Route::post('/{user}/roles/sync', [UsersController::class, 'syncRoles'])->name('users.syncRoles');
    });


    Route::prefix('roles')->group(function () {
        // Get all roles
        Route::get('/', [RolesController::class, 'index'])->name('roles.index');

        // Create a new role
        Route::post('/', [RolesController::class, 'store'])->name('roles.store');

        // Get a specific role
        Route::get('/{role}', [RolesController::class, 'show'])->name('roles.show');

        // Update a specific role
        Route::put('/{role}', [RolesController::class, 'update'])->name('roles.update');

        // Delete a specific role
        Route::delete('/{role}', [RolesController::class, 'destroy'])->name('roles.destroy');

        // Attach a permission to a role
        Route::post('/{role}/permissions/attach', [RolesController::class, 'attachPermission'])
            ->name('roles.attachPermission');

        // Detach a permission from a role
        Route::post('/{role}/permissions/detach', [RolesController::class, 'detachPermission'])
            ->name('roles.detachPermission');
    });

    Route::apiResource('item-categories', App\Http\Controllers\ItemCategoryController::class);

    Route::apiResource('years', App\Http\Controllers\YearController::class);

    Route::apiResource('service-types', App\Http\Controllers\ServiceTypeController::class);

    Route::apiResource('vendor-targets', App\Http\Controllers\VendorTargetController::class);

    Route::patch('create-items/{id}/reduce-stock', [App\Http\Controllers\CreateItemController::class, 'reduceStock']);


    Route::apiResource('create-items', App\Http\Controllers\CreateItemController::class);
    Route::apiResource('cash-discrepancies', App\Http\Controllers\CashDiscrepancyController::class);
    Route::apiResource('expense-lines', App\Http\Controllers\ExpenseLineController::class);
    
     Route::apiResource('regions', App\Http\Controllers\RegionController::class);

    Route::apiResource('cashier-expenses', App\Http\Controllers\CashierExpenseController::class);
    Route::apiResource('bank-remittances', App\Http\Controllers\BankRemittanceController::class);
    
    
    Route::apiResource('cashier-remittances', App\Http\Controllers\CashierRemittanceController::class);
    Route::get('get-cashier-remittance/{id}', [App\Http\Controllers\CashierRemittanceController::class,'get']);
    Route::get('get-bank-remittance/{id}', [App\Http\Controllers\BankRemittanceController::class,'get']);
    
    // Route::get('cashier-remit', App\Http\Controllers\CashierRemittanceController::class,'newGet');
    
    // Route::get('cashier-remittances-get', [App\Http\Controllers\CashierRemittanceController::class,'index']);
    Route::get('cashier-expense-pending', [App\Http\Controllers\CashierExpenseController::class,'pending']);
    Route::get('bank-remittance-pending', [App\Http\Controllers\BankRemittanceController::class,'pending']);
    Route::get('cashier-remittance-pending', [App\Http\Controllers\CashierRemittanceController::class,'pending']);

    Route::post('cashier-expense-approve', [App\Http\Controllers\CashierExpenseController::class,'approve']);
    Route::post('bank-remittance-approve', [App\Http\Controllers\BankRemittanceController::class,'approve']);
    Route::post('cashier-remittance-approve', [App\Http\Controllers\CashierRemittanceController::class,'approve']);

    Route::post('search-cashier-remittance', [App\Http\Controllers\CashierRemittanceController::class,'index']);
     Route::post('search-bank-remittance', [App\Http\Controllers\BankRemittanceController::class,'index']);
    Route::post('search-cashier-expense', [App\Http\Controllers\CashierExpenseController::class,'index']);
    ///////////// Sales Routes /////////////////
    // Route::post('sales-orders', [SalesOrderController::class,'store']);
    // Route::get('/sales-orders/{id}/edit', [SalesOrderController::class, 'edit']);
    // Route::put('/sales-orders/{id}', [SalesOrderController::class, 'update']);
    ///////////////

    Route::apiResource('sales-orders', App\Http\Controllers\SalesOrderController::class);

    Route::post('search-post-outflows', [App\Http\Controllers\PostOutflowController::class,'index']);

    Route::post('search-post-inflows', [App\Http\Controllers\PostInflowController::class,'index']);

    Route::post('search-sales-orders', [App\Http\Controllers\SalesOrderController::class,'index']);


    Route::post('search-sales-receipts', [App\Http\Controllers\SalesReceiptController::class,'index']);

    Route::post('search-sales-release', [App\Http\Controllers\ReleaseController::class,'index']);

    Route::post('search-sales-receipts', [App\Http\Controllers\SalesReceiptController::class, 'index']);
    Route::post('my-sales-receipts', [App\Http\Controllers\SalesReceiptController::class, 'myReceipts']);

    Route::post('search-sales-release', [App\Http\Controllers\ReleaseController::class, 'index']);

    Route::get('/credit-awaiting-payment', [App\Http\Controllers\CreditTransactionController::class, 'pendingPayment']);
    Route::get('/customer-balances', [App\Http\Controllers\CustomerController::class, 'balances']);
    Route::get('/customer-balance-history/{id}', [App\Http\Controllers\CustomerController::class, 'customerBalanceHistory']);
    Route::get('/sales-orders-pending-credit', [App\Http\Controllers\SalesOrderController::class, 'pendingCredit']);
    Route::get('/sales-orders-pending-receipt', [App\Http\Controllers\SalesOrderController::class, 'pendingReceipts']);
    Route::get('/sales-order-info/{orderno}', [App\Http\Controllers\SalesOrderController::class, 'getbynumber']);
    Route::get('/sales-receipt-info/{orderno}', [App\Http\Controllers\SalesReceiptController::class, 'getbynumber']);
    Route::apiResource('sales-order', App\Http\Controllers\SalesOrderController::class);
    // Route::post('sales-order', App\Http\Controllers\SalesOrderController::class);

    Route::post('/sales-orders/{id}/cancel', [SalesOrderController::class, 'cancel']);


    // Route::post('/customers/{id}/assign-credit', [App\Http\Controllers\CustomerController::class, 'assignCredit']);

    Route::post('/customers/{id}/assign-credit', [App\Http\Controllers\CustomerController::class, 'assignCredit']);

    Route::post('/store-items/{id}/set-limit', [App\Http\Controllers\StoreItemController::class, 'setLimit']);
    /////////// StoreItem////////////////////////////////////////

    Route::apiResource('store-items', App\Http\Controllers\StoreItemController::class);
    
    Route::get('my-store-items', [App\Http\Controllers\StoreItemController::class, "myStoreItems"]);

    Route::get('my-stores-inventory', [App\Http\Controllers\StoreItemController::class, "myStoreItemsSetLimit"]);




    Route::get('my-store-items-invt', [App\Http\Controllers\StoreItemController::class, 'myStoreItems2']);

    Route::get('get-inventory-by-store/{itemId}', [App\Http\Controllers\StoreItemController::class, "GetInventoryByStore"]);
    Route::get('get-inventory-by-branch-store/{itemId}/{branchId}', [App\Http\Controllers\StoreItemController::class, "GetInventoryByStoreBranch"]);

    Route::apiResource('item_price', App\Http\Controllers\ItemPriceController::class);
    Route::apiResource('sales-receipt', App\Http\Controllers\SalesReceiptController::class);

    Route::apiResource('item-sold', App\Http\Controllers\ItemSoldController::class);

    Route::apiResource('release', App\Http\Controllers\ReleaseController::class);

    Route::apiResource('return-items', App\Http\Controllers\ReturnItemController::class);

    Route::apiResource('return-details', App\Http\Controllers\ReturnDetailsController::class);

    Route::apiResource('release-details', App\Http\Controllers\ReleaseDetailsController::class);

    Route::apiResource('release', App\Http\Controllers\ReleaseController::class);

    Route::apiResource('return-items', App\Http\Controllers\ReturnItemController::class);

    Route::apiResource('return-details', App\Http\Controllers\ReturnDetailsController::class);

    Route::apiResource('release-details', App\Http\Controllers\ReleaseDetailsController::class);
    Route::apiResource('dimensions', App\Http\Controllers\DimensionController::class);

    Route::apiResource('roles', App\Http\Controllers\RolesController::class);

    Route::apiResource('adjustment-types', App\Http\Controllers\AdjustmentTypeController::class);

    Route::apiResource('weights', App\Http\Controllers\WeightController::class);

    Route::apiResource('item-types', App\Http\Controllers\ItemTypeController::class);

    Route::apiResource('statuses', App\Http\Controllers\StatusController::class);

    Route::apiResource('inflow-statuses', App\Http\Controllers\InflowStatusController::class);

    Route::apiResource('outflow-modes', App\Http\Controllers\OutflowModeController::class);

    Route::apiResource('post-outflows', App\Http\Controllers\PostOutflowController::class);

    Route::apiResource('settle-credits', App\Http\Controllers\SettleCreditController::class);

    Route::apiResource('post-inflows', App\Http\Controllers\PostInflowController::class);

    Route::apiResource('branches', App\Http\Controllers\BranchController::class);

    Route::apiResource('customer-types', App\Http\Controllers\CustomerTypeController::class);

    Route::apiResource('warehouses', App\Http\Controllers\WarehouseController::class);

    Route::apiResource('units', App\Http\Controllers\UnitController::class);

    Route::apiResource('payment-terms', App\Http\Controllers\PaymentTermController::class);

    Route::apiResource('discounts', App\Http\Controllers\DiscountController::class);

    Route::apiResource('taxes', App\Http\Controllers\TaxController::class);

    Route::apiResource('titles', App\Http\Controllers\TitleController::class);

    Route::apiResource('carriers', App\Http\Controllers\CarrierController::class);

    Route::apiResource('payment-modes', App\Http\Controllers\PaymentModeController::class);

    Route::apiResource('payment-types', App\Http\Controllers\PaymentTypeController::class);

    Route::apiResource('designations', App\Http\Controllers\DesignationController::class);

    Route::apiResource('manufacturers', App\Http\Controllers\ManufacturerController::class);

    Route::apiResource('brands', App\Http\Controllers\BrandController::class);

    Route::apiResource('attributes', App\Http\Controllers\AttributeController::class);

    Route::apiResource('countries', App\Http\Controllers\CountryController::class);

    Route::apiResource('states', App\Http\Controllers\StateController::class);

    Route::apiResource('banks', App\Http\Controllers\BankController::class);

    Route::apiResource('permissions', App\Http\Controllers\PermissionController::class);

    // Route::apiResource('search', App\Http\Controllers\CreateItemController::class, 'search');
    Route::get('/create-items/search', [CreateItemController::class, 'search'])->name('createItems.search');

    Route::apiResource('vendor-types', App\Http\Controllers\VendorTypeController::class);

    Route::apiResource('vendors', App\Http\Controllers\VendorController::class);

    Route::apiResource('reasons', App\Http\Controllers\ReasonController::class);

    Route::apiResource('transfer-orders', App\Http\Controllers\TransferOrderController::class);

    Route::apiResource('deliveries', App\Http\Controllers\DeliveryController::class);

    // Route::apiResource('sales', App\Http\Controllers\SaleController::class);

    Route::apiResource('inventory-adjustments', App\Http\Controllers\InventoryAdjustmentController::class);

    // Route::apiResource('invoices', App\Http\Controllers\InvoiceController::class);

    Route::apiResource('credit-sales', App\Http\Controllers\CreditSaleController::class);

    Route::apiResource('customers', App\Http\Controllers\CustomerController::class);

    Route::get('pending-release/{storeId}', [App\Http\Controllers\SalesReceiptController::class, 'pendingReleaseStore']);
    Route::get('pending-release', [App\Http\Controllers\SalesReceiptController::class, 'pendingRelease']);
    Route::get('new-release-order-info/{orderno}/{storeId}', [App\Http\Controllers\SalesReceiptController::class, 'pendingReleaseOrder2']);
    Route::get('new-release-order-info/{orderno}', [App\Http\Controllers\SalesReceiptController::class, 'pendingReleaseOrder']);

    Route::apiResource('sales-receipts', App\Http\Controllers\SalesReceiptController::class);

    Route::apiResource('credit-limits', App\Http\Controllers\CreditLimitController::class);

    Route::apiResource('payment-receiveds', App\Http\Controllers\PaymentReceivedController::class);

    Route::apiResource('payment-vouchers', App\Http\Controllers\PaymentVoucherController::class);

    Route::apiResource('vendor-credits', App\Http\Controllers\VendorCreditController::class);

    Route::apiResource('purchase-received-details', App\Http\Controllers\PurchaseReceivedDetailController::class);

    Route::apiResource('new-purchase-orders', App\Http\Controllers\NewPurchaseOrderController::class);

    Route::apiResource('users', App\Http\Controllers\UsersController::class);

    Route::apiResource('new-purchase-receiveds', App\Http\Controllers\NewPurchaseReceivedController::class);

    Route::apiResource('payment-voucher-details', App\Http\Controllers\PaymentVoucherDetailController::class);

    Route::apiResource('new-payments', App\Http\Controllers\NewPaymentController::class);

    Route::apiResource('purchase-order-details', App\Http\Controllers\PurchaseOrderDetailController::class);


    Route::apiResource('refund-types', App\Http\Controllers\RefundTypeController::class);

    Route::apiResource('sales-type', App\Http\Controllers\SalesTypeController::class);
    Route::apiResource('store-types', App\Http\Controllers\StoreTypeController::class);

    Route::apiResource('stores', App\Http\Controllers\StoreController::class);
    Route::get('my-stores/{branchid}', [App\Http\Controllers\StoreController::class, 'mystore2']);
    Route::get('my-stores', [App\Http\Controllers\StoreController::class, 'mystore']);
    Route::get('my-stores-with-items', [App\Http\Controllers\StoreController::class, 'mystorewithItems']);

    Route::apiResource('accounts', App\Http\Controllers\AccountController::class);
    Route::apiResource('account-groups', App\Http\Controllers\AccountGroupController::class);

    Route::apiResource('account-subtypes', App\Http\Controllers\AccountSubtypeController::class);
    Route::apiResource('account-types', App\Http\Controllers\AccountTypeController::class);
    Route::apiResource('charts', App\Http\Controllers\ChartController::class);
    Route::apiResource('chart-cards', App\Http\Controllers\ChartCardController::class);
    Route::apiResource('chart-categories', App\Http\Controllers\ChartCategoryController::class);
    Route::apiResource('chart-providers', App\Http\Controllers\ChartProviderController::class);
    Route::apiResource('chart-types', App\Http\Controllers\ChartTypeController::class);
    Route::apiResource('dashboard-settings', App\Http\Controllers\DashboardSettingController::class);
    Route::apiResource('journal-types', App\Http\Controllers\JournalTypeController::class);
    Route::apiResource('journal-entries', App\Http\Controllers\JournalEntryController::class);
    Route::get('journal-entry/pending', [App\Http\Controllers\JournalEntryController::class, 'pending']);

    Route::apiResource('journal-entry-details', App\Http\Controllers\JournalEntryDetailController::class);

    Route::apiResource('transactions', App\Http\Controllers\TransactionController::class);

    Route::apiResource('account-opening-balances', App\Http\Controllers\AccountOpeningBalanceController::class);

    Route::apiResource('period-accounts', App\Http\Controllers\PeriodAccountController::class);

    Route::apiResource('period-account-years', App\Http\Controllers\PeriodAccountYearController::class);

    Route::apiResource('period-account-dailies', App\Http\Controllers\PeriodAccountDailyController::class);

    Route::apiResource('financial-years', App\Http\Controllers\FinancialYearController::class);

    Route::apiResource('financial-quarters', App\Http\Controllers\FinancialQuarterController::class);

    Route::apiResource('financial-periods', App\Http\Controllers\FinancialPeriodController::class);

    Route::apiResource('approval-instances', App\Http\Controllers\ApprovalInstanceController::class);

    Route::apiResource('approval-limits', App\Http\Controllers\ApprovalLimitController::class);

    Route::apiResource('approval-process-flows', App\Http\Controllers\ApprovalProcessFlowController::class);

    Route::apiResource('approval-process-modules', App\Http\Controllers\ApprovalProcessModuleController::class);

    Route::apiResource('approval-process-types', App\Http\Controllers\ApprovalProcessTypeController::class);

    Route::apiResource('approval-stages', App\Http\Controllers\ApprovalStageController::class);

    Route::apiResource('approval-types', App\Http\Controllers\ApprovalTypeController::class);



    Route::apiResource('receive-orders', App\Http\Controllers\ReceiveOrderController::class);
    Route::get('pending-receive-orders', [App\Http\Controllers\ReceiveOrderController::class,'pending']);
    Route::post('approve-receive-order', [App\Http\Controllers\ReceiveOrderController::class,'approve']);
    
    Route::apiResource('receive-items', App\Http\Controllers\ReceiveItemController::class);

    Route::apiResource('store-transfer-orders', App\Http\Controllers\StoreTransferOrderController::class);

    Route::apiResource('store-transfer-items', App\Http\Controllers\StoreTransferItemController::class);
    Route::apiResource('credit-transactions', App\Http\Controllers\CreditTransactionController::class); 
    Route::apiResource('price-changes', App\Http\Controllers\PriceChangeController::class);
    Route::apiResource('change-reasons', App\Http\Controllers\ChangeReasonController::class);
    Route::get('pending-price-change', [App\Http\Controllers\PriceChangeController::class,'pending']);
    Route::post('approve-price-change', [App\Http\Controllers\PriceChangeController::class,'approve']);
    
});