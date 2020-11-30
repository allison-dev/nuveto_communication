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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/callback/conversations/{cid}/create', 'fivenineCallbackController@messageCreateCallback');
Route::post('/callback/conversations/{cid}/message', 'fivenineCallbackController@messageCallback');
Route::post('/callback/conversations/{cid}/terminate', 'fivenineCallbackController@terminateCallback');
