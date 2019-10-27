<?php

use Illuminate\Http\Request;

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
// -----------------[ login / signup Routes ]------------
Route::post('login', 'Controllers\AuthController@login');
Route::post('register', 'Controllers\AuthController@register');
Route::post('oauth/callback', 'Controllers\AuthController@callBack');
// -----------------------------[User Routes]-----------------------------
Route::group(['middleware' => ['auth:api', 'availability']], function ($router) {
    Route::get('user/{user_id}', 'Controllers\UserController@user');
    Route::post('users/profile', 'Controllers\UserController@profile');
    Route::post('update-user-token', 'Controllers\UserController@updateDeviceToken');
    Route::post('update-user-name', 'Controllers\UserController@updateUserName');
    Route::post('post-update-user-info', 'Controllers\UserController@postUpdateUserInfo');
    Route::get('next-calls-details', 'Controllers\UserController@nextCallsWithDetails');
    Route::post('change-password', 'Controllers\UserController@changePassword');
    Route::post('set-user-offline', 'Controllers\UserController@setUserOffline');
});
//--------------------------------[ Clients info Routes]--------------------
Route::group(['middleware' => ['auth:api'], 'prefix' => 'client'], function () {
    Route::post('update-practice-language', 'Controllers\UserController@updatePractingLanguage');
    Route::post('add-practing-language', 'Controllers\UserController@addPractingLanguage');
    Route::get('practing-languages', 'Controllers\UserController@getPractingLanguage');
    Route::post('delete-practing-language', 'Controllers\UserController@deletePractingLanguage');
    Route::put('update-client-status', 'Controllers\UserController@updateClientStatus');
});
//--------------------------------[caller info Routes]--------------------
Route::get('caller-info/{user_id}', 'Controllers\UserController@getCallerInfo');
// --------------------------[User Password Reset Routes]-----------------------------
Route::post('user/reset-password', 'Controllers\Auth\ForgotPasswordController@resetPassword');
Route::post('user/reset-password/{token}', 'Controllers\Auth\ResetPasswordController@postResetPassword');
// -------------------------[Topics Routes]-----------------------------
Route::get('topics', function () {
    Cache::put('topics', YallaTalk\Models\Topic::all(), 10);
    $topics = Cache::get('topics');
    return response()->json(['topics'=> $topics]);
});
Route::group(['prefix' => 'topics'], function () {
    Route::get('search', 'Controllers\TopicController@topicsSearch');
});
//--------------------------[languages Routes]-----------------
Route::get('languages', function () {
    Cache::put('languages', YallaTalk\Models\Language::all(), 10);
    $languages = Cache::get('languages');
    return response()->json(['languages'=> $languages]);
});
//-------------------------[service provider Routes]--------
Route::group(['middleware' => ['auth:api', 'availability']], function ($router) {
    Route::get('providers/{topic_id}/{practing_language?}', 'Controllers\TopicController@serviceProvider');
    Route::get('provider/search', 'Controllers\ServiceProviderController@search');
    Route::get('users/{USER_ID}/calendar', 'Controllers\ServiceProviderController@showCalender');
    Route::post(
        'provider/{PROVIDER_ID}/calendar',
        'Controllers\ServiceProviderController@postProviderCalender'
    );
    Route::post('provider/add-topic', 'Controllers\TopicController@addTopic');
    Route::post('provider/delete-topic', 'Controllers\TopicController@deleteTopic');
    Route::put('provider/update-native-language', 'Controllers\ServiceProviderController@updateNativeLanguage');
    Route::post('update-topics', 'Controllers\TopicController@topicsUpdate');
    Route::put('provider/avaliabilty/{status}', 'Controllers\ServiceProviderController@updateStatus');
    Route::put(
        'provider/{PROVIDER_ID}/calendar/{REQUEST_ID}',
        'Controllers\ServiceProviderController@updateCalender'
    );
    Route::post('add-native-language', 'Controllers\ServiceProviderController@addNativeLanguage');
    Route::get('native-languages', 'Controllers\ServiceProviderController@getNativeLanguage');
    Route::post('delete-native-language', 'Controllers\ServiceProviderController@deleteNativeLanguage');

    Route::get('provider-topics', 'Controllers\TopicController@getProviderTopics');
    Route::get('alternative/{PROVIDER_ID}', 'Controllers\ServiceProviderController@getAlternative');
    Route::put('provider/update-houre-rate', 'Controllers\ServiceProviderController@UpdateHoureRate');
    Route::get('check-invitation-code', 'Controllers\AuthController@invitationCodeValidation');
    Route::get('check-hour-price', 'Controllers\ServiceProviderController@checkHourRate');
    Route::get('provider/call-summary/{provider_id}', 'Controllers\ServiceProviderController@providerCallSummary');
    Route::get('provider/call-log/{provider_id}', 'Controllers\ServiceProviderController@getCallLogs');
    Route::get('user/{user_id}/my-calendar', 'Controllers\UserController@getMyCalendar');
    Route::get('user/validate-mobile-number/{mobile_number}', 'Controllers\UserController@validateMobileNumber');
    Route::get('provider/{PROVIDER_ID}', 'Controllers\ServiceProviderController@getInfo');
    Route::get('provider/{PROVIDER_ID}/calendar', 'Controllers\ServiceProviderController@getProviderCalender');
    Route::get('next-calls/{user_id}', 'Controllers\UserController@getNextCalls');
    Route::post('service-provider-filter', 'Controllers\ServiceProviderController@serviceProviderFilter');
    Route::post('service-provider-account-information', 'Controllers\ServiceProviderController@saveProviderAccountInformation');
    Route::get('provider-bank-info', 'Controllers\ServiceProviderController@providerBankInfo');
});
//-------------------------------[notification Routes]---------
Route::group(['middleware' => ['auth:api', 'availability']], function ($router) {
    Route::get('users/notifications', 'Controllers\UserController@getNotification');
    Route::post('notification/mark-as-reed', 'Controllers\UserController@markAsRead');
    Route::post('user/profile/avatar', 'Controllers\UserController@profileAvatar');
    Route::get('count-unread-notifications', 'Controllers\UserController@countUnreadNotifications');
    Route::put('update-notification', 'Controllers\UserController@updateNotification');
    Route::post('mark-notification-as-seen', 'Controllers\UserController@setNotificationAsSeen');
});
//------------------------[user profile routes]---------
Route::group(['middleware' => ['auth:api', 'availability']], function ($router) {
    Route::post('user/{USER_ID}/language/native', 'Controllers\UserController@nativeLanguage');
    Route::post('user/{USER_ID}/language', 'Controllers\UserController@practicingLanguage');
    Route::post('user/{USER_ID}/status', 'Controllers\UserController@updateStatus');
    Route::post('user/{USER_ID}/rating', 'Controllers\UserController@showRating');
});
//----------------------------[Twilio api]------------
Route::group(['middleware' => ['auth:api', 'availability']], function ($router) {
    Route::post('ceate-new-room', 'Controllers\TwilioController@createRoom');
    Route::post('voic-call', 'Controllers\TwilioController@voicCall');
    Route::post('join-video-room', 'Controllers\TwilioController@joinVideoRoom');
    Route::post('send-message', 'Controllers\TwilioController@sendMessage');
    Route::post('create-twilm', 'Controllers\TwilioController@createTwilm');
    Route::get('index', 'Controllers\TwilioController@index');
    Route::get('room-name', 'Controllers\TwilioController@getRoomName');
    Route::post('end-call', 'Controllers\TwilioController@endCall');
    Route::post('reject-call', 'Controllers\TwilioController@rejectCall');
    Route::post('update-call-start-time', 'Controllers\TwilioController@updateCallStartTime');
});
//---------------[ Twilio status callback api ]---------
Route::post('room-created', 'Controllers\TwilioController@roomCreated');
Route::get('get-all-countries', 'Controllers\UserController@getAllCountries');
//---------------[ statistic/ call summary routes]-----------
Route::group(['middleware' => ['auth:api', 'availability']], function () {
    Route::get('get-provider-statistics', 'Controllers\ServiceProviderController@statistics');
    Route::get('call-summary', 'Controllers\TwilioController@getCallSummary');
    Route::post('rate-call', 'Controllers\TwilioController@rateCall');
    Route::get('call-history', 'Controllers\UserController@callHistory');
});

//----------------[Payments Route]---------------
Route::group(['prefix' => 'payments', 'middleware' => ['auth:api', 'availability']], function () {
    Route::post('charge', 'Controllers\PaymentController@charge');
    Route::post('save-customer', 'Controllers\PaymentController@saveCustomer');
    Route::get('test', 'Controllers\PaymentController@test');
    Route::get('save-customer', 'Controllers\PaymentController@SaveCustomer');
    Route::post('request-refund', 'Controllers\PaymentController@requestRefund');
    Route::post('transfer-money', 'Controllers\PaymentController@transferMoney');
    Route::get('transfer-money-history', 'Controllers\PaymentController@transferMoneyHistory');
});

Route::get('test', 'Controllers\UserController@test');