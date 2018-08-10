<?php

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
Route::group(['namespace'=>'Ebay'],function(){

	// Route::get('upload','EbayController@uploadCSV');
	Route::get('get-csv','EbayController@getCSV');
	
	Route::post('get-csv','EbayController@postCSV')->name('upload-csv');
	// Route::get('test-job',function(){
	// 	dispatch(new \App\Jobs\EbayUpdateListing);
	// });
	
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
