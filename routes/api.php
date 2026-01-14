<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\FloorController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('users', UserController::class);
    Route::get('/roles', [UserController::class, 'roles']);
    Route::get('/permissions', [UserController::class, 'permissions']);

    Route::get('/categories', [MenuController::class, 'index']);
    Route::post('/categories', [MenuController::class, 'store']);
    Route::get('/categories/{category}', [MenuController::class, 'show']);
    Route::put('/categories/{category}', [MenuController::class, 'update']);
    Route::delete('/categories/{category}', [MenuController::class, 'destroy']);

    Route::get('/menu-items', [MenuController::class, 'items']);
    Route::post('/menu-items', [MenuController::class, 'storeItem']);
    Route::get('/menu-items/{item}', [MenuController::class, 'showItem']);
    Route::put('/menu-items/{item}', [MenuController::class, 'updateItem']);
    Route::delete('/menu-items/{item}', [MenuController::class, 'destroyItem']);

    Route::get('/modifiers', [MenuController::class, 'modifiers']);
    Route::post('/modifiers', [MenuController::class, 'storeModifier']);
    Route::put('/modifiers/{modifier}', [MenuController::class, 'updateModifier']);
    Route::delete('/modifiers/{modifier}', [MenuController::class, 'destroyModifier']);

    Route::apiResource('floors', FloorController::class);
    Route::apiResource('tables', TableController::class);

    Route::get('/orders/open', [OrderController::class, 'open']);
    Route::post('/orders/{order}/send', [OrderController::class, 'sendToKitchen']);
    Route::post('/orders/{order}/add-items', [OrderController::class, 'addItems']);
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay']);
    Route::post('/orders/{order}/void-item', [OrderController::class, 'voidItem']);
    Route::post('/orders/{order}/refund', [OrderController::class, 'refund']);
    Route::post('/orders/{order}/reprint', [OrderController::class, 'reprint']);
    Route::apiResource('orders', OrderController::class);

    Route::get('/kitchen/tickets', [KitchenController::class, 'index']);
    Route::post('/kitchen/{ticket}/status', [KitchenController::class, 'updateStatus']);

    Route::get('/reports/daily', [ReportController::class, 'daily']);
    Route::get('/reports/range', [ReportController::class, 'range']);
    Route::get('/reports/export', [ReportController::class, 'export']);

    Route::get('/printers', [PrinterController::class, 'index']);
    Route::post('/printers', [PrinterController::class, 'store']);
    Route::put('/printers/{printer}', [PrinterController::class, 'update']);
    Route::delete('/printers/{printer}', [PrinterController::class, 'destroy']);
});
