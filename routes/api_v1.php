<?php

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

Route::group([
    'prefix'    => 'meetings',
    'namespace' => 'Meetings\Controllers',
], function () {
    Route::get('/available', 'MeetingsController@getAvailabilities')
         ->name('meetings.get-availabilities');
});

Route::group([
    'prefix'     => 'files',
    'namespace'  => 'Uploaders\Controllers',
], function () {
    Route::post('/', 'FilesController@uploadNewFile')->name('files.upload_file');
});