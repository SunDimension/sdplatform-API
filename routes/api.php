<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
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






Route::apiResource('item-categories', App\Http\Controllers\ItemCategoryController::class);

Route::apiResource('create-items', App\Http\Controllers\CreateItemController::class);

Route::apiResource('dimensions', App\Http\Controllers\DimensionController::class);

Route::apiResource('roles', App\Http\Controllers\RolesController::class);

Route::apiResource('adjustment-types', App\Http\Controllers\AdjustmentTypeController::class);

Route::apiResource('weights', App\Http\Controllers\WeightController::class);

Route::apiResource('item-types', App\Http\Controllers\ItemTypeController::class);

Route::apiResource('statuses', App\Http\Controllers\StatusController::class);

Route::apiResource('branches', App\Http\Controllers\BranchController::class);

Route::apiResource('customer-types', App\Http\Controllers\CustomerTypeController::class);

Route::apiResource('warehouses', App\Http\Controllers\WarehouseController::class);

Route::apiResource('units', App\Http\Controllers\UnitController::class);

Route::apiResource('payment-terms', App\Http\Controllers\PaymentTermController::class);

Route::apiResource('discounts', App\Http\Controllers\DiscountController::class);

Route::apiResource('taxes', App\Http\Controllers\TaxController::class);

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

Route::apiResource('vendor-types', App\Http\Controllers\VendorTypeController::class);

Route::apiResource('vendors', App\Http\Controllers\VendorController::class);

Route::apiResource('transfer-orders', App\Http\Controllers\TransferOrderController::class);

Route::apiResource('deliveries', App\Http\Controllers\DeliveryController::class);

Route::apiResource('sales', App\Http\Controllers\SaleController::class);

Route::apiResource('inventory-adjustments', App\Http\Controllers\InventoryAdjustmentController::class);

Route::apiResource('invoices', App\Http\Controllers\InvoiceController::class);

Route::apiResource('credit-sales', App\Http\Controllers\CreditSaleController::class);

Route::apiResource('customers', App\Http\Controllers\CustomerController::class);

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
