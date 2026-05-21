<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandlordProfileController;
use App\Http\Controllers\LandlordSetupController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTenantController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/landlord/setup', [LandlordSetupController::class, 'edit'])->name('landlord.setup.edit');
    // Route::post('/landlord/setup', [LandlordSetupController::class, 'update'])->name('landlord.setup.update');

    Route::get('/landlord/setup/{step?}', [LandlordSetupController::class, 'edit'])->where('step', '[1-3]')->name('landlord.setup.edit');
    Route::post('/landlord/setup/{step}', [LandlordSetupController::class, 'update'])->where('step', '[1-3]')->name('landlord.setup.update');
    Route::get('/landlord/setup/back/{step}', [LandlordSetupController::class, 'back'])->where('step', '[1-3]')->name('landlord.setup.back');

});

Route::middleware(['auth', 'verified', 'profile-completed'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/landlord', [LandlordProfileController::class, 'update'])->name('landlord.update');

    Route::get('/properties', [PropertyController::class, 'index'])->name('property.index');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('property.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('property.store');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('property.destroy');
    Route::get('/properties/{property}', [PropertyController::class, 'edit'])->name('property.edit');
    Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('property.update');

    // ROOM
    Route::get('/properties/{property}/rooms', [RoomController::class, 'index'])->name('property.rooms');
    Route::get('/properties/{property}/rooms/create', [RoomController::class, 'create'])->name('property.rooms.create');
    // Route::get('/rooms/{rooms}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    // Route::delete('/rooms/{rooms}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    Route::get('properties/{property}/rooms/{room}/edit', [RoomController::class, 'edit'])->name('property.rooms.edit');
    Route::delete('properties/{property}/rooms/{room}', [RoomController::class, 'destroy'])->name('property.rooms.destroy');
    Route::post('/properties/{property}/rooms', [RoomController::class, 'store'])->name('property.rooms.store');
    Route::put('/properties/{property}/rooms/{room}', [RoomController::class, 'update'])->name('property.rooms.update');

    // TENANT
    Route::delete('/properties/{property}/rooms/{room}/tenants/{tenant}', [RoomTenantController::class, 'destroy'])->name('property.rooms.tenants.destroy');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transaction.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transaction.store');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');

    Route::get('/pay-bills', [TransactionController::class, 'showPayBills'])->name('pay-bills');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcement.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcement.show');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcement.update');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcement.edit');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');

    Route::get('/requests', [MaintenanceRequestController::class, 'index'])->name('request.index');
    Route::get('/requests/create', [MaintenanceRequestController::class, 'create'])->name('request.create');
    Route::post('/requests', [MaintenanceRequestController::class, 'store'])->name('request.store');
    Route::get('/requests/{request}', [MaintenanceRequestController::class, 'show'])->name('request.show');
    Route::put('/requests/{request}', [MaintenanceRequestController::class, 'update'])->name('request.update');
    Route::get('/requests/{request}/edit', [MaintenanceRequestController::class, 'edit'])->name('request.edit');
    Route::delete('/requests/{request}', [MaintenanceRequestController::class, 'destroy'])->name('request.destroy');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/start-conversation', [MessageController::class, 'startConversation'])->name('messages.start');

    Route::view('/tests', 'table');
});

require __DIR__.'/auth.php';
