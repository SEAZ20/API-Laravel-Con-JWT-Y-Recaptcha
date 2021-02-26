<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group([
    'middleware'=>'api',
    'prefix'=>'auth'
],function($router){
    Route::post('/login', 'UsuarioController@login')->middleware('cors');
    Route::post('/register', 'UsuarioController@register')->middleware('cors');
    Route::post('/logout', 'UsuarioController@logout')->middleware('cors');
    Route::get('/perfil', 'UsuarioController@userProfile')->middleware('cors');
    Route::post('/verificar', 'UsuarioController@verificar')->middleware('cors');
});



