<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\FundedOpportunityController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\OpenOpportunityController;
use App\Http\Controllers\OpenProposalController;
// use App\Models\User;

// // Login route (required by auth middleware)
// Route::get('/login', function () {
//     return redirect()->route('auth.blackbaud');
// })->name('login');

// // Start Blackbaud OAuth
// Route::get('/auth/blackbaud', function () {
//     return Socialite::driver('blackbaud')->redirect();
// })->name('auth.blackbaud');

// // OAuth callback
// Route::get('/auth/blackbaud/callback', function () {
//     $blackbaudUser = Socialite::driver('blackbaud')->stateless()->user();

//     // Use token to create a dummy user
//     $user = User::firstOrCreate([
//         'email' => 'blackbaud_' . md5($blackbaudUser->token) . '@bb.fake',
//     ], [
//         'name' => 'Blackbaud User',
//         'password' => bcrypt(Str::random(16)),
//     ]);

//     Auth::login($user);

//     return redirect('/dashboard');
// });

// // Dashboard (protected)
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth']);

Route::get('/upload', [DataController::class, 'uploadForm'])->name('upload.form');
Route::post('/upload', [DataController::class, 'upload'])->name('upload.store');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/funded-opportunities', [FundedOpportunityController::class, 'index'])->name('opportunities.funded.details');
Route::get('/opportunities/{type}', [OpportunityController::class, 'show']);
Route::get('/proposal-summary/{status}', [OpportunityController::class, 'summary']);
Route::get('/open-opportunities-purpose/{purpose}', [OpenOpportunityController::class, 'details'])->name('open.opportunities.purpose.details');
Route::get('/open-proposals/{group}', [OpenProposalController::class, 'details'])->name('open.proposals.details');
Route::get('/opportunities-details/{type}', [OpportunityController::class, 'details'])->name('opportunities.details');