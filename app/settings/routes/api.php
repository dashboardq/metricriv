<?php

use mavoc\core\Route;

Route::get('api/trackings/count', ['APITrackingController', 'count']);
Route::get('api/requests/count', ['APIRequestController', 'count']);
Route::get('api/votes/count', ['APIRequestController', 'countVotes']);
Route::get('api/users/count', ['APIUserController', 'count']);
