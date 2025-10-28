<?php

use Illuminate\Support\Facades\Route;
use Zaimea\SocialiteExtender\Http\Controllers\SocialiteExtenderController;

Route::middleware(['web', 'auth', 'throttle:10,1'])->group(function () {
    Route::get('/socialite-extender/connect/{provider}', [SocialiteExtenderController::class, 'redirect'])->name('socialite-extender.connect');
    Route::get('/socialite-extender/callback/{provider}', [SocialiteExtenderController::class, 'callback'])->name('socialite-extender.callback');
    Route::delete('/socialite-extender/disconnect/{provider}', [SocialiteExtenderController::class, 'disconnect'])->name('socialite-extender.disconnect');
});
