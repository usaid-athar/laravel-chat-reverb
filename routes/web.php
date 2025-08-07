<?php

use App\Http\Controllers\User\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';



Route::middleware(['auth','verified'])->group(function () {


    Route::get('dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::get('chat/{id?}',[DashboardController::class,'chat'])->name('chat');
    Route::post('/chat/send', [DashboardController::class, 'send'])->name('chat.send');
    

});