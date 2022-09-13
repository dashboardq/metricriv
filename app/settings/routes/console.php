<?php

use mavoc\console\Route;

Route::command('gen cat', ['ConsoleGenController', 'cat']);

Route::command('track', ['ConsoleController', 'track']);
