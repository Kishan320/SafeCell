<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/');
});
Route::any('/{any}', function () {
    return redirect('/');
})->where('any', '.*');
