<?php

Route::prefix('laravel-nordigen')->namespace('Hypnodev\\LaravelNordigen\\Http\\Controllers')->group(function () {
    Route::get('nordigen-redirect', 'AuthorizationController@store');
});
