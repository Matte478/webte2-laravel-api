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

Route::group(['middleware' => 'cors'], function () {
    Route::get('calculate','CasController@calculate');
    Route::get('airplane', 'AirplaneController@index');
    Route::get('pendulum', 'PendulumController@index');
    Route::get('ballbeam', 'BallbeamController@index');
});

Route::get('logs/export/csv', 'LogsController@exportCSV');
Route::get('logs/export/pdf', 'LogsController@exportPDF');