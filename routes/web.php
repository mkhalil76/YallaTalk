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
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', function () {
        Breadcrumbs::addCrumb('Home', 'admin/');
        return view('welcome');
    });
    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');

    Route::group(['prefix' => 'users', 'middleware' => 'auth'], function () {
        Route::get('/profile/{user_id}', 'UsersController@show');
        Route::post('/update', 'UsersController@update');
        Route::post('/admins-for-datatable', 'UsersController@adminsForDatatable');
    });

    Route::group(['prefix' => 'topics', 'middleware' => 'auth'], function () {
        Route::get('/', 'TopicsController@index');
        Route::get('delete/{id}', 'TopicsController@delete');
        Route::get('update/{id}', 'TopicsController@update');
        Route::post('update/{id}', 'TopicsController@postUpdate');
        Route::get('create', 'TopicsController@create');
        Route::post('create', 'TopicsController@postCreate');
        Route::post('topics-for-datatable', 'TopicsController@getTopicsForDataTable');
    });

    Route::group(['prefix' => 'languages', 'middleware' => 'auth'], function () {
        Route::get('/', 'LanguagesController@index');
        Route::get('delete/{id}', 'LanguagesController@delete');
        Route::get('update/{id}', 'LanguagesController@update');
        Route::post('update/{id}', 'LanguagesController@postUpdate');
    });

    Route::group(['prefix' => 'service-providers'], function () {
        Route::get('/', 'UsersController@serviceProviders');
        Route::post('/providers-for-datatable', 'UsersController@providersForDatatable');
        Route::get('/show-inactive-accounts', 'UsersController@inactiveAccounts');
        Route::get('/defreez-account/{provider_id}', 'UsersController@defreezAccount');
    });

    Route::group(['prefix' => 'clients', 'middleware' => 'auth'], function () {
        Route::get('/', 'UsersController@client');
        Route::post('/clients-for-datatable', 'UsersController@clientsForDatatable');
    });

    Route::group(['prefix' => 'admins', 'middleware' => 'auth'], function () {
        Route::get('/', 'UsersController@admin');
        Route::get('/appointments', 'UsersController@appointment');
        Route::post('/update-calender', 'UsersController@updateAppointment');
    });

    Route::group(['prefix' => 'languages', 'middleware' => 'auth'], function () {
        Route::get('/', 'LanguagesController@index');
        Route::get('create', 'LanguagesController@create');
        Route::post('create', 'LanguagesController@postCreate');
        Route::get('update', 'LanguagesController@update');
        Route::post('update', 'LanguagesController@postUpdate');
        Route::get('delete', 'LanguagesController@delete');
        Route::post('languages-for-datatable', 'LanguagesController@languagesForDataTable');
    });

    Route::group(['prefix' => 'calls', 'middleware' => 'auth'], function () {
        Route::get('active-calls', 'CallsController@getCurrentCalls');
    });

    //---------------[ payments routest ]------------
    Route::group(['prefix' => 'payments', 'middleware' => 'auth'], function () {
        Route::get('track-money', 'HomeController@trackMoney');
        Route::get('fill-money-page/{provider_id}/{call_id}/{call_price}', 'HomeController@fillMoneyPage');
        Route::post('post-fill-money', 'HomeController@postFillMoneyPage');
        Route::get('reject-transaction/{call_id}', 'HomeController@rejectTransaction');
        Route::get('refund', 'HomeController@refund');
        Route::get('accept-refund/{refund_id}/{type?}', 'HomeController@acceptRefund');
        Route::get('reject-refund/{refund_id}/{type?}', 'HomeController@rejectRefund');
        Route::get('transfer-money', 'HomeController@transferMoney');
        Route::get('accept-transfer/{refund_id}/{type?}', 'HomeController@acceptTransfer');
    });

    Route::group(['prefix' => 'packeges', 'middleware' => 'auth'], function () {
        Route::get('index', 'HomeController@packageIndex');
        Route::get('create', 'HomeController@createPackage');
        Route::post('post-create-packege', 'HomeController@postCreatePackege');
        Route::get('update/{packege_id}', 'HomeController@updatePackege');
        Route::post('post-update-packege', 'HomeController@postUpdatePackege');
        Route::get('delete/{packege_id}', 'HomeController@deletePackege');
    });

    Route::group(['prefix' => 'settings', 'middleware' => 'auth'], function () {
        Route::get('/', 'SettingController@index');
        Route::post('update-commision', 'SettingController@updateCommision');
    });
});
Route::get('share-link/{code}', 'UsersController@getShareLink');
