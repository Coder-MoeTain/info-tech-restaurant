<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PrinterController;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\RTable;
use App\Models\Modifier;

// Public APIs (add auth middleware once Sanctum is configured)
Route::get('/categories', fn() => Category::all());
Route::get('/menu-items', fn() => MenuItem::all());
Route::get('/tables', fn() => RTable::with('floor')->get());
Route::get('/modifiers', fn() => Modifier::all());

Route::get('/orders/open', [OrderController::class, 'open']);
Route::post('/orders/{order}/send', [OrderController::class, 'sendToKitchen']);
Route::post('/orders/{order}/add-items', [OrderController::class, 'addItems']);
Route::post('/orders/{order}/pay', [OrderController::class, 'pay']);
Route::post('/orders/{order}/void-item', [OrderController::class, 'voidItem']);
Route::post('/orders/{order}/refund', [OrderController::class, 'refund']);
Route::post('/orders/{order}/reprint', [OrderController::class, 'reprint']);
Route::post('/orders/{order}/transfer', [OrderController::class, 'transfer']);
Route::apiResource('orders', OrderController::class);

Route::get('/kitchen/tickets', [KitchenController::class, 'index']);
Route::post('/kitchen/{ticket}/status', [KitchenController::class, 'updateStatus']);

Route::get('/reports/daily', [ReportController::class, 'daily']);
Route::get('/reports/range', [ReportController::class, 'range']);
Route::get('/reports/export', [ReportController::class, 'export']);
Route::get('/reports/dashboard', [ReportController::class, 'dashboard']);

Route::get('/printers', [PrinterController::class, 'index']);
Route::post('/printers', [PrinterController::class, 'store']);
Route::put('/printers/{printer}', [PrinterController::class, 'update']);
Route::delete('/printers/{printer}', [PrinterController::class, 'destroy']);
