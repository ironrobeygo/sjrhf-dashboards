<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\DataController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ProposalController;
use App\Models\User;
use App\Http\Controllers\ActionController;

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

// Route::middleware(['auth'])->group(function () {
    Route::get('/upload', [DataController::class, 'uploadForm'])->name('upload.form');
    Route::post('/upload', [DataController::class, 'upload'])->name('upload.store');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/opportunities/{type}', [OpportunityController::class, 'type']);
    Route::get('/opportunities-details/{type}', [OpportunityController::class, 'typeDetails'])->name('opportunities.details');
    Route::get('/funded-opportunities', [OpportunityController::class, 'fundedOpportunity'])->name('opportunities.funded.details');
    Route::get('/open-opportunities-purpose/{purpose}', [OpportunityController::class, 'purposeDetails'])->name('open.opportunities.purpose.details');
    
    Route::get('/proposal-summary/{status}', [ProposalController::class, 'summary']);
    Route::get('/open-proposals/{group}', [ProposalController::class, 'details'])->name('open.proposals.details');

    Route::get('/actions/{fundraiser}/{category}', [ActionController::class, 'showFundraiserActionDetails']);
    Route::get('/action-type/{fundraiser}/{type}', [ActionController::class, 'showFundraiserActionType']);

// });