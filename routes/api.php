

Route::apiResource('users', App\Http\Controllers\UsersController::class);

Route::apiResource('agencies', App\Http\Controllers\AgenciesController::class);

Route::apiResource('roles', App\Http\Controllers\RolesController::class);

Route::apiResource('permissions', App\Http\Controllers\PermissionsController::class);

Route::apiResource('property-types', App\Http\Controllers\PropertyTypesController::class);

Route::apiResource('property-categories', App\Http\Controllers\PropertyCategoriesController::class);

Route::apiResource('property-statuses', App\Http\Controllers\PropertyStatusesController::class);

Route::apiResource('properties', App\Http\Controllers\PropertiesController::class);

Route::apiResource('property-images', App\Http\Controllers\PropertyImagesController::class);

Route::apiResource('property-videos', App\Http\Controllers\PropertyVideosController::class);

Route::apiResource('property-documents', App\Http\Controllers\PropertyDocumentsController::class);

Route::apiResource('features', App\Http\Controllers\FeaturesController::class);

Route::apiResource('property-features', App\Http\Controllers\PropertyFeaturesController::class);

Route::apiResource('amenities', App\Http\Controllers\AmenitiesController::class);

Route::apiResource('property-amenities', App\Http\Controllers\PropertyAmenitiesController::class);

Route::apiResource('countries', App\Http\Controllers\CountriesController::class);

Route::apiResource('states', App\Http\Controllers\StatesController::class);

Route::apiResource('cities', App\Http\Controllers\CitiesController::class);

Route::apiResource('areas', App\Http\Controllers\AreasController::class);

Route::apiResource('property-enquiries', App\Http\Controllers\PropertyEnquiriesController::class);

Route::apiResource('property-views', App\Http\Controllers\PropertyViewsController::class);

Route::apiResource('saved-properties', App\Http\Controllers\SavedPropertiesController::class);

Route::apiResource('property-offers', App\Http\Controllers\PropertyOffersController::class);

Route::apiResource('inspection-bookings', App\Http\Controllers\InspectionBookingsController::class);

Route::apiResource('conversations', App\Http\Controllers\ConversationsController::class);

Route::apiResource('messages', App\Http\Controllers\MessagesController::class);

Route::apiResource('subscription-plans', App\Http\Controllers\SubscriptionPlansController::class);

Route::apiResource('subscriptions', App\Http\Controllers\SubscriptionsController::class);

Route::apiResource('payments', App\Http\Controllers\PaymentsController::class);

Route::apiResource('reviews', App\Http\Controllers\ReviewsController::class);

Route::apiResource('notifications', App\Http\Controllers\NotificationsController::class);

Route::apiResource('ad-packages', App\Http\Controllers\AdPackagesController::class);

Route::apiResource('advertisements', App\Http\Controllers\AdvertisementsController::class);

Route::apiResource('blog-posts', App\Http\Controllers\BlogPostsController::class);

Route::apiResource('pages', App\Http\Controllers\PagesController::class);

Route::apiResource('f-a-qs', App\Http\Controllers\FAQsController::class);

Route::apiResource('settings', App\Http\Controllers\SettingsController::class);

Route::apiResource('audit-logs', App\Http\Controllers\AuditLogsController::class);

Route::apiResource('reported-listings', App\Http\Controllers\ReportedListingsController::class);

Route::apiResource('mortgage-calculations', App\Http\Controllers\MortgageCalculationsController::class);
