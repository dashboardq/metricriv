<?php

use mavoc\console\Route;

Route::command('gen cat', ['ConsoleGenController', 'cat']);
Route::command('gen num', ['ConsoleGenController', 'num']);

Route::command('track', ['ConsoleController', 'track']);
