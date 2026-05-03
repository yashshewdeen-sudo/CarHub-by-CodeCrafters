<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Web\ListingController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\MessageController;

Route::get('/', fn () => redirect()->route('listings.index'));

// Auth routes
Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');#

// Public routes
Route::get('/listings/search/ajax',   [ListingController::class, 'ajaxSearch'])->name('listings.search');
Route::get('/listings',               [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}',     [ListingController::class, 'show'])->name('listings.show');

// Auth only (all logged in users)
Route::middleware('auth')->group(function () {
    Route::get('/profile',           [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile/upgrade', [ProfileController::class, 'upgradeSeller'])->name('profile.upgrade');

    // Messages
    Route::get('/inbox',                              [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/{listing}/thread',          [MessageController::class, 'thread'])->name('messages.thread');
    Route::post('/messages/{listing}/send',           [MessageController::class, 'send'])->name('messages.send');
    Route::post('/messages/{listing}/reply',          [MessageController::class, 'reply'])->name('messages.reply');

    // JSON export
    Route::get('/listings/{listing}/export-json', [ListingController::class, 'exportJson'])->name('listings.json.export');
    Route::get('/listings/{listing}/view-json',   [ListingController::class, 'viewJson'])->name('listings.json.view');
});

// Seller only routes
Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/listings-create',           [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings',                 [ListingController::class, 'store'])->name('listings.store');
    Route::delete('/listings/{listing}',     [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::patch('/listings/{listing}/sold', [ListingController::class, 'markSold'])->name('listings.sold');
});

// Admin only routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/listings',                     [AdminController::class, 'listings'])->name('admin.listings');
    Route::patch('/listings/{listing}/approve', [AdminController::class, 'approve'])->name('admin.approve');
    Route::patch('/listings/{listing}/reject',  [AdminController::class, 'reject'])->name('admin.reject');
});