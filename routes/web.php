<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome'); // or any valid Blade view
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/auth/blackbaud', function () {
    return Socialite::driver('blackbaud')->redirect();
})->name('auth.blackbaud');

Route::get('/auth/blackbaud/callback', function () {
    $blackbaudUser = Socialite::driver('blackbaud')->stateless()->user();

    $user = User::firstOrCreate([
        'email' => $blackbaudUser->getEmail(),
    ], [
        'name' => $blackbaudUser->getName() ?? 'Blackbaud User',
        'password' => bcrypt(Str::random(16)), // dummy password
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});
