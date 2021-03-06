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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
	Route::get('table-list', function () {
		return view('pages.table_list');
	})->name('table');

	Route::get('typography', function () {
		return view('pages.typography');
	})->name('typography');

	Route::get('icons', function () {
		return view('pages.icons');
	})->name('icons');

	Route::get('map', function () {
		return view('pages.map');
	})->name('map');


    Route::group(['prefix' => 'oai'], function () {
        Route::get('/check_status', 'DashboardController@check_status');
        Route::post('/start', 'DashboardController@oai_start')->name('oai_start');
        Route::post('/read', 'DashboardController@read_file');
        Route::post('/kill', 'DashboardController@kill');
        Route::post('/analysis', 'DashboardController@run_analysis');
        Route::get('/', 'DashboardController@oai')->name('oai');
    });

    Route::group(['prefix' => 'custom'], function () {
        Route::post('/analysis', 'DashboardController@custom_analysis');
        Route::post('/co-analysis', 'DashboardController@co_analysis');
        Route::get('/', 'DashboardController@custom')->name('custom');
    });

	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');

	Route::get('rtl-support', function () {
		return view('pages.language');
	})->name('language');

	Route::get('upgrade', function () {
		return view('pages.upgrade');
	})->name('upgrade');
});

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
});

