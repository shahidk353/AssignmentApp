<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FriendRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware('auth')->group(function () {
   
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

   
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('profile.show'); 

   
    Route::post('/friend-requests/{recipient}', [FriendRequestController::class, 'send'])->name('friend-requests.send');
    Route::get('/friend-requests/pending', [FriendRequestController::class, 'pending'])->name('friend-requests.pending');
    Route::post('/friend-requests/{friendRequest}/accept', [FriendRequestController::class, 'accept'])->name('friend-requests.accept');
    Route::delete('/friend-requests/{friendRequest}/decline', [FriendRequestController::class, 'decline'])->name('friend-requests.decline');
});


require __DIR__.'/auth.php';