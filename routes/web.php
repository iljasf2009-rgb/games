<?php
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('games', [App\Http\Controllers\GameController::class, 'index'])->name('games.index');
Route::post('games/store', [App\Http\Controllers\GameController::class, 'store']);
Route::get('games/edit/{id}', [App\Http\Controllers\GameController::class, 'edit']);
Route::post('games/update/{id}', [App\Http\Controllers\GameController::class, 'update']);
Route::post('games/destroy/{id}', [App\Http\Controllers\GameController::class, 'destroy']);
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
Route::get('/games/{id}', [\App\Http\Controllers\GameController::class, 'show'])->name('games.show');
{
    return view('welcome');
};
