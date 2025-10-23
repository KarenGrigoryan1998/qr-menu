<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController as ApiMenuController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\TableController as ApiTableController;
use App\Http\Controllers\Api\RestaurantController as ApiRestaurantController;

// Public API routes for customer ordering
Route::prefix('restaurants/{restaurant}')
    ->where(['restaurant' => '[0-9]+'])
    ->group(function () {
    // Menu
    Route::get('/menu', [ApiMenuController::class, 'index']);
    Route::get('/categories', [ApiMenuController::class, 'categories']);
    Route::get('/categories/{category}/items', [ApiMenuController::class, 'categoryItems']);

    // Tables
    Route::get('/tables/{table}', [ApiTableController::class, 'show']);
    Route::get('/tables/{tableId}/orders', [ApiOrderController::class, 'tableOrders']);

    // Orders
    Route::post('/orders', [ApiOrderController::class, 'store']);
    Route::get('/orders/{order}', [ApiOrderController::class, 'show']);
    Route::post('/orders/{order}/items', [ApiOrderController::class, 'addItems']);
    Route::post('/orders/{order}/request-waiter', [ApiOrderController::class, 'requestWaiter']);
    Route::post('/orders/{order}/payment', [ApiOrderController::class, 'processPayment']);
    Route::patch('/orders/{order}/status', [ApiOrderController::class, 'updateStatus']);
    
    // Restaurant info - MUST BE LAST to avoid catching other routes
    Route::get('/info', [ApiRestaurantController::class, 'show']);
});

// Admin API for notifications
Route::prefix('admin/api')->group(function () {
    Route::get('/waiter-requests/pending-count', function () {
        $count = \App\Models\WaiterRequest::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    });
});
