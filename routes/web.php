<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::controller(UserController::class)->group(function () {
    Route::get('/logout', 'logout');
    Route::post('/login', 'login');
    Route::post('/registration', 'registration');
    Route::get('/getUserdataById/{id}', 'getUserdataById');
    Route::post('/changeEmail', 'changeEmail');
    Route::post('/changePhone', 'changePhone');
    Route::post('/changePassword', 'changePassword');
    Route::get('/userdata', 'userdata');
    Route::get('/logout', 'logout');
});

Route::controller(PostController::class)->group(function () {
    Route::post('/postCreate', 'postCreate');
    Route::post('/postEdit', 'postEdit');
    Route::get('/postDelete/{id}', 'postDelete');
    Route::get('/getPosts', 'getPosts');
});

Route::get('/{any}', function (){
    return view('app');
})->where('any', '.*');
