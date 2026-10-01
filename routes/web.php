<?php

use App\Http\Controllers\Admin\AccessManagementController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Klanten en admins mogen het overzicht en de details bekijken.
Route::middleware(['auth', 'role:admin|klant'])->group(function () {
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/show/{id}', [GameController::class, 'show'])->name('games.show');
});

// Alleen admins mogen games aanmaken, bewerken en verwijderen.
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/permissions', [AccessManagementController::class, 'permissions'])->name('permissions.index');
        Route::post('/permissions', [AccessManagementController::class, 'storePermission'])->name('permissions.store');
        Route::put('/permissions/{permission}', [AccessManagementController::class, 'updatePermission'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [AccessManagementController::class, 'destroyPermission'])->name('permissions.destroy');

        Route::get('/roles', [AccessManagementController::class, 'roles'])->name('roles.index');
        Route::post('/roles', [AccessManagementController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}', [AccessManagementController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [AccessManagementController::class, 'destroyRole'])->name('roles.destroy');

        Route::get('/role-permissions', [AccessManagementController::class, 'rolePermissions'])->name('role-permissions.index');
        Route::post('/role-permissions', [AccessManagementController::class, 'storeRolePermission'])->name('role-permissions.store');
        Route::put('/role-permissions/{role}/{permission}', [AccessManagementController::class, 'updateRolePermission'])->name('role-permissions.update');
        Route::delete('/role-permissions/{role}/{permission}', [AccessManagementController::class, 'destroyRolePermission'])->name('role-permissions.destroy');

        Route::get('/user-roles', [AccessManagementController::class, 'userRoles'])->name('user-roles.index');
        Route::post('/user-roles', [AccessManagementController::class, 'storeUserRole'])->name('user-roles.store');
        Route::put('/user-roles/{user}/{role}', [AccessManagementController::class, 'updateUserRole'])->name('user-roles.update');
        Route::delete('/user-roles/{user}/{role}', [AccessManagementController::class, 'destroyUserRole'])->name('user-roles.destroy');
    });

    Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
    Route::post('/games/store', [GameController::class, 'store'])->name('games.store');
    Route::get('/games/edit/{id}', [GameController::class, 'edit'])->name('games.edit');
    Route::post('/games/update/{id}', [GameController::class, 'update'])->name('games.update');
    Route::delete('/games/delete/{id}', [GameController::class, 'destroy'])->name('games.destroy');

    Route::get('/geheim', function () {
        return view('geheim');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
