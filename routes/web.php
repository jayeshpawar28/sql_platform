<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\ProblemList;
use App\Livewire\SqlEditor;
use App\Livewire\Leaderboard;
use App\Models\Problem;

Route::get('/test-lang', function () {
    return __('Password');
});

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/testapi', function () {
    return view('api_test');
})->name('testapi');

Route::get('/problems', ProblemList::class)->name('problems.index');
Route::get('/problems/{problem}', SqlEditor::class)->name('problems.show');
Route::get('/leaderboard', Leaderboard::class)->name('leaderboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
