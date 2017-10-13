<?php

/*
|--------------------------------------------------------------------------
| Laravel user verification routes
|--------------------------------------------------------------------------
*/
Route::get('user/verification/{token}', 'App\Http\Controllers\Auth\RegisterController@verifyUser')
    ->name('user.verify')->middleware('web');