<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['namespace'=>'Web'],function() {
    /* Web routes */
    Route::get('/', [Web\BillController::class, 'index'])->name('home');
    Route::get('/create', [Web\BillController::class, 'create'])->name('bills.create');
    Route::post('/create', [Web\BillController::class, 'store'])->name('bills.store');
    Route::post('/products/loadOptionValues',[Web\BillController::class, 'loadOptionValues'])->name('bills.loadoptionvalues');
    Route::post('/products/loadOptionsAdd',[Web\BillController::class, 'loadOptionsAdd'])->name('bills.loadoptionsadd');
    Route::get('/edit/{id}', [Web\BillController::class, 'edit'])->name('bills.edit');
    Route::post('/edit/{id}', [Web\BillController::class, 'update'])->name('bills.update');

    Route::get('/delete/{id}', [Web\BillController::class, 'destroy'])->name('bills.delete');
    Route::post('/change-status', [Web\BillController::class, 'changeStatus'])->name('bills.status');
    Route::post('/update-order', [Web\BillController::class, 'updateOrder'])->name('bills.order');
});
