<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function() {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
   dd([
       \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->toArray() : "No auth",
       \App\Models\User::all()->toArray(),
       ]);
});

