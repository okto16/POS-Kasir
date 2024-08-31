<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseDetailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleDetailController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Sale;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Home route
Route::get('/', function () {
    return view('welcome');
});

// // Auth routes
// Auth::routes();

// // Testing Spatie
// Route::get('test_spatie', [AdminController::class, 'test_spatie']);
// Route::get('dashboard', [AdminController::class, 'dashboard']);

Route::resource('/categories', CategorieController::class);
Route::resource('/users', UserController::class);
Route::resource('/purchases', PurchaseController::class);
Route::resource('/sales', SaleController::class);
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
// Route to show the edit form (GET request)
Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');

// Route to handle the form submission (PUT request)
Route::put('/purchases/{id}', [PurchaseController::class, 'update'])->name('purchases.update');

Route::resource('/purchases-details', PurchaseDetailController::class);
Route::resource('/sales-details', SaleDetailController::class);
Route::resource('/suppliers', SupplierController::class);
Route::resource('/products', ProductController::class);
Route::resource('/members', MemberController::class);
Route::resource('/reports', ReportController::class);
Route::get('cetak/barcode', [ProductController::class, 'cetakBarcode']);
Route::get('cetak/kartu', [MemberController::class, 'cetakMember']);

// Home route
Route::get('/dashboard', [DashboardController::class, 'index']);

// API routes
Route::get('/api/products',[ProductController::class, 'api']);
Route::get('/api/reports',[ReportController::class, 'api']);
Route::get('/api/users',[UserController::class, 'api']);
Route::get('/api/categories',[CategorieController::class, 'api']);
Route::get('/api/members',[MemberController::class, 'api']);
Route::get('/api/suppliers',[SupplierController::class, 'api']);
Route::get('/api/purchases',[PurchaseController::class, 'api']);
Route::get('/api/sales',[SaleController::class, 'api']);
Route::get('/api/sales-details',[SaleDetailController::class, 'api']);
Route::get('/api/purchases-details',[PurchaseDetailController::class, 'api']);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
