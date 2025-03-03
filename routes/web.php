<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;
use App\Exports\RequestsExport;
use Maatwebsite\Excel\Facades\Excel;

// Redirect root URL to /login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile Routes
Route::get('/setup-profile', [UserController::class, 'setupProfile'])->middleware('authCheck');
Route::post('/setup-profile', [UserController::class, 'saveProfile'])->middleware('authCheck');
Route::post('/profile/update', [UserController::class, 'updateProfile'])->middleware('authCheck')->name('profile.update');

// Dashboard Routes
Route::middleware(['authCheck'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/head-office-dashboard', [UserController::class, 'headOfficeDashboard'])->name('head.office.dashboard');
    Route::get('/coordinator-dashboard', [UserController::class, 'coordinatorDashboard'])->name('coordinator.dashboard');
    Route::get('/admin-dashboard', [UserController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/superAdmin', [UserController::class, 'superAdminDashboard'])->name('superAdmin');
    
    // Admin specific routes
    Route::get('/export-approved-requests', [RequestController::class, 'exportApprovedRequests'])->name('export.approved.requests');
    Route::post('/request/{id}/assign-producer', [RequestController::class, 'assignProducer'])->name('assign.producer');
    Route::post('/request/{id}/complete', [RequestController::class, 'markRequestComplete']);
});

// Admin Routes
Route::post('/assign-producer/{id}', [UserController::class, 'assignProducer'])
    ->name('assign.producer')
    ->middleware('authCheck');

// Request Form Routes
Route::get('/request-form', [RequestController::class, 'showRequestForm'])->middleware('authCheck')->name('request.form');
Route::post('/submit-request', [RequestController::class, 'submitTestQuestionnaireRequest'])->middleware('authCheck')->name('submit.request');
Route::get('/request-formDoc', [RequestController::class, 'showRequestFormDoc'])->middleware('authCheck')->name('request.form.doc');
Route::post('/submit-requestDoc', [RequestController::class, 'submitDocumentRequest'])->middleware('authCheck')->name('submit.request.doc');
Route::get('/request-history', [RequestController::class, 'requestHistory'])->middleware('authCheck')->name('request.history');

// Edit and resubmit rejected requests
Route::get('/request/{id}/edit', [RequestController::class, 'editRequest'])->middleware('authCheck')->name('request.edit');
Route::post('/request/{id}/resubmit', [RequestController::class, 'resubmitRequest'])->middleware('authCheck')->name('request.resubmit');

Route::middleware('authCheck')->group(function () {
    Route::get('/manage-requests', [RequestController::class, 'manageRequests'])->name('manage.requests');
    Route::post('/approve-request-coordinator/{id}', [RequestController::class, 'approveByCoordinator'])->name('approve.coordinator');
    Route::post('/approve-request-dean/{id}', [RequestController::class, 'approveByDean'])->name('approve.dean');
    Route::post('/reject-request/{id}', [RequestController::class, 'rejectRequest'])->name('reject.request');
    Route::post('/add-comment/{id}', [RequestController::class, 'addComment'])->name('add.comment');
    Route::get('/view-approval-sheet/{id}', [RequestController::class, 'viewApprovalSheet'])->name('request.viewApprovalSheet');
    Route::patch('/requests/{request}/claimants', [RequestController::class, 'updateClaimants'])->name('update.claimants');
});

Route::middleware(['authCheck'])->group(function () {
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/export-approved-requests', [RequestController::class, 'exportApprovedRequests'])->name('export.approved.requests');
});

Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::put('/user/edit/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');

Route::get('/export-requests', [RequestController::class, 'exportRequests']);

// Temporary route to check user data
Route::get('/check-user', function() {
    $user = Session::get('user');
    dd([
        'profile_picture' => $user->profile_picture,
        'storage_path' => storage_path('app/public'),
        'public_path' => public_path('storage')
    ]);
});