<?php

use Illuminate\Support\Facades\Route;
use Zaimea\SocialiteExtender\Http\Controllers\SocialiteExtenderController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/socialite/connect/{provider}', [SocialiteExtenderController::class, 'redirect'])->name('socialite-extender.connect');
    Route::get('/socialite/callback/{provider}', [SocialiteExtenderController::class, 'callback'])->name('socialite-extender.callback');
    Route::delete('/socialite/disconnect/{provider}', [SocialiteExtenderController::class, 'disconnect'])->name('socialite-extender.disconnect');
});
