<?php

use SRC\Route;
use SRC\Session;

require_once __DIR__ . '/../routes.php';
require_once __DIR__ . '/../src/Route.php';
require_once __DIR__ . '/../src/DB.php';
require_once __DIR__ . '/../src/Session.php';

define('PROJECT_ROOT', dirname(__DIR__ ) . DIRECTORY_SEPARATOR);

Session::handle();
Route::handle();