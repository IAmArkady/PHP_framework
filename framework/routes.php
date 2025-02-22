<?php
require_once __DIR__ . '/src/Route.php';

use App\Controllers\IndexController;
use App\Controllers\LanguageController;
use SRC\Route;

Route::get('/', [IndexController::class, 'index'])->name('indexPage');
Route::post('/check', [LanguageController::class, 'check'])->name('checkLanguage');
Route::get('/check', [LanguageController::class, 'history'])->name('historyLanguage');