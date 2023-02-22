<?php

use mavoc\core\Route;

Route::get('/', ['MainController', 'home']);
Route::get('/pricing', ['MainController', 'pricing']);
Route::get('/terms', ['MainController', 'terms']);
Route::get('/privacy', ['MainController', 'privacy']);

Route::get('/contact', ['ContactController', 'contact']);
Route::post('/contact', ['ContactController', 'contactPost']);


Route::get('requests', ['RequestController', 'list']);
Route::get('request/add', ['RequestController', 'add']);
//Route::post('request/add', ['RequestController', 'create']);
Route::get('request/view/{id}', ['RequestController', 'view']);
Route::post('request/view/{id}', ['RequestController', 'comment']);
Route::post('request/vote/{id}', ['RequestController', 'vote']);

//Route::get('request/add', ['RequestController', 'add'], 'private');
Route::post('request/add', ['RequestController', 'create'], 'private');
Route::post('request/comment/{id}', ['RequestController', 'comment'], 'private');
Route::get('request/edit/{id}', ['RequestController', 'edit'], 'private');
Route::post('request/edit/{id}', ['RequestController', 'update'], 'private');
Route::post('request/vote/up/{id}', ['RequestController', 'up'], 'private');
Route::post('request/vote/down/{id}', ['RequestController', 'down'], 'private');

Route::get('missing', ['RequestController', 'missing']);


// Private
Route::post('logout', ['AuthController', 'logout'], 'private');

Route::get('account', ['AuthController', 'account'], 'private');
Route::post('account', ['AuthController', 'accountPost'], 'private');
Route::get('change-password', ['AuthController', 'changePassword'], 'private');
Route::post('change-password', ['AuthController', 'changePasswordPost'], 'private');

Route::get('connections', ['ConnectionController', 'list'], 'private');
Route::post('connection/delete/{id}', ['ConnectionController', 'delete'], 'private');
Route::get('connection/edit/{id}', ['ConnectionController', 'edit'], 'private');
Route::post('connection/edit/{id}', ['ConnectionController', 'update'], 'private');

Route::get('numbers', ['NumberController', 'list'], 'private');
Route::post('number/delete/{id}', ['NumberController', 'delete'], 'private');
Route::get('number/edit/{id}', ['NumberController', 'edit'], 'private');
Route::post('number/edit/{id}', ['NumberController', 'update'], 'private');
Route::get('number/add', ['NumberController', 'add'], 'private');
Route::post('number/add', ['NumberController', 'addPost'], 'private');
Route::get('number/add/{collection_id}', ['NumberController', 'addCategory'], 'private');
Route::get('number/add/{collection_id}/{category_slug}', ['NumberController', 'addNumber'], 'private');

Route::get('number/add/{collection_id}/{category_slug}/{number_slug}', ['NumberController', 'addConnection'], 'private');
Route::post('number/add/{collection_id}/{category_slug}/{number_slug}', ['NumberController', 'addConnectionPost'], 'private');

Route::get('number/add/{collection_id}/{category_slug}/{number_slug}/{connection_id}', ['NumberController', 'addTracking'], 'private');
Route::post('number/add/{collection_id}/{category_slug}/{number_slug}/{connection_id}', ['NumberController', 'addTrackingPost'], 'private');

Route::post('ajax/collection/sort/{id}', ['AjaxController', 'collectionSort'], 'private');

Route::get('collections', ['CollectionsController', 'list'], 'private');
Route::get('collection/view/{id}', ['CollectionsController', 'view'], 'private');
Route::get('collection/add', ['CollectionsController', 'add'], 'private');
Route::post('collection/add', ['CollectionsController', 'create'], 'private');
Route::get('collection/edit/{id}', ['CollectionsController', 'edit'], 'private');
Route::post('collection/edit/{id}', ['CollectionsController', 'update'], 'private');
Route::post('collection/delete/{id}', ['CollectionsController', 'delete'], 'private');

Route::get('settings', ['SettingController', 'index'], 'private');
Route::post('settings', ['SettingController', 'update'], 'private');


Route::get('usernames', ['UsernameController', 'list'], 'private');
Route::get('username/add', ['UsernameController', 'add'], 'private');
Route::post('username/create', ['UsernameController', 'create'], 'private');


Route::get('viewers', ['ViewersController', 'list'], 'private');
Route::get('viewer/add', ['ViewersController', 'add'], 'private');
Route::post('viewer/add', ['ViewersController', 'create'], 'private');
Route::get('viewer/edit/{id}', ['ViewersController', 'edit'], 'private');
Route::post('viewer/edit/{id}', ['ViewersController', 'update'], 'private');


// Handle 3rd Party OAuth
Route::post('oauth/{category_slug}/start', ['OAuthController', 'start'], 'private');
Route::get('oauth/{category_slug}/redirect', ['OAuthController', 'redirect'], 'private');

// Public
Route::get('forgot-password', ['AuthController', 'forgotPassword'], 'public');
Route::post('forgot-password', ['AuthController', 'forgotPasswordPost'], 'public');
Route::get('login', ['AuthController', 'login'], 'public');
Route::post('login', ['AuthController', 'loginPost'], 'public');
Route::post('register', ['AuthController', 'registerPost'], 'public');
Route::get('reset-password', ['AuthController', 'resetPassword'], 'public');
Route::post('reset-password', ['AuthController', 'resetPasswordPost'], 'public');

Route::get('generate-keys-file', ['DevController', 'keys']);

Route::get('test', ['TestController', 'test']);

// Private numbers are handled by the controller.
Route::get('{username}/{collection}', ['TrackingsController', 'list']);
Route::get('{username}', ['TrackingsController', 'list']);

