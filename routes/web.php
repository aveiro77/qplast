<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PayrollsController;
use App\Http\Controllers\PresencesController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', [AuthenticatedSessionController::class, 'create']);

Route::middleware(['auth'])->group(function () {
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard chart, buatan sendiri
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/dashboard/presence', [DashboardController::class, 'presence']);

    // Resource routes for categories
    Route::resource('categories', CategoryController::class)->middleware(['role:Developer,Cashier']);
    
    Route::resource('customers', CustomerController::class)->middleware(['role:Developer,Cashier']);

    // Cash transactions (kas masuk / kas keluar)
    Route::resource('cash', CashController::class)->middleware(['role:Developer,Cashier']);

    Route::resource('sales', SaleController::class)->middleware(['role:Developer,Cashier']);
    Route::post('/sales/export', [SaleController::class, 'export'])->name('sales.export')->middleware(['role:Developer,Cashier']);
    Route::get('/sales/{id}/print', [SaleController::class, 'print'])->name('sales.print')->middleware(['role:Developer,Cashier']);

    // Resource routes for departments
    Route::resource('departments', DepartmentController::class)->middleware(['role:HR']);

    Route::resource('products', ProductController::class)->middleware(['role:Developer,Cashier']);

    //Route::resource('pos', PosController::class)->middleware(['role:Developer,Cashier']);
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/{id}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');
    

    // Resource routes for roles
    Route::resource('roles', RoleController::class)->middleware(['role:HR,Developer']);

    // Resource routes for employees
    Route::resource('employees', EmployeeController::class)->middleware(['role:HR,Developer']);

    // Resource routes for tasks
    Route::resource('tasks', TaskController::class)->middleware(['role:Developer,HR']);
    Route::get('tasks/done/{id}', [TaskController::class, 'done'])->name('tasks.done');
    Route::get('tasks/pending/{id}', [TaskController::class, 'pending'])->name('tasks.pending');

    // Resource routes for payroll
    Route::resource('payrolls', PayrollsController::class)->middleware(['role:Developer,HR']);

    // Resource routes for presences (attendance)
    Route::resource('presences', PresencesController::class)->middleware(['role:Developer,HR']);
    
    // Resource routes for leave requests
    Route::resource('leave-requests', LeaveRequestController::class)->middleware(['role:Developer,HR']);
    
    Route::get('leave-requests/confirm/{id}', [LeaveRequestController::class, 'confirm'])->name('leave-requests.confirm');
    Route::get('leave-requests/reject/{id}', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
});

// Bawaan Breeze.
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';