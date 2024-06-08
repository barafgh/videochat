<?php

use App\Events\CallEvent;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('/chat', 'chat')
    ->middleware(['auth'])
    ->name('chat');

Route::post('/call', function (Request $request) {
    CallEvent::dispatch($request->all());
    return response()->json('Call signal sent', 200);
});

require __DIR__.'/auth.php';
