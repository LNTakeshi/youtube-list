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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'Api'],function () {
    Route::post('youtubelist/send', 'YoutubeListController@send');
    Route::get('youtubelist/getList', 'YoutubeListController@getList');
    Route::post('youtubelist/setCurrentIndex', 'YoutubeListController@setCurrentIndex');
    Route::post('youtubelist/remove', 'YoutubeListController@remove');
    Route::post('youtubelist/sendError', 'YoutubeListController@sendError');

});
