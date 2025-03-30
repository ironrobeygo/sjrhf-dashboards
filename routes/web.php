<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

// Login route (required by auth middleware)
Route::get('/login', function () {
    return redirect()->route('auth.blackbaud');
})->name('login');

// Start Blackbaud OAuth
Route::get('/auth/blackbaud', function () {
    return Socialite::driver('blackbaud')->redirect();
})->name('auth.blackbaud');

// OAuth callback
Route::get('/auth/blackbaud/callback', function () {
    $blackbaudUser = Socialite::driver('blackbaud')->stateless()->user();

    // Use token to create a dummy user
    $user = User::firstOrCreate([
        'email' => 'blackbaud_' . md5($blackbaudUser->token) . '@bb.fake',
    ], [
        'name' => 'Blackbaud User',
        'password' => bcrypt(Str::random(16)),
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});

// Dashboard (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth']);