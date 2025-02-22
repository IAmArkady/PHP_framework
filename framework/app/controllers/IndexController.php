<?php


namespace App\Controllers;
use src\Request;
use SRC\Response;
use src\TemplateEngine;

require_once __DIR__ . '/../../src/TemplateEngine.php';

class IndexController
{
    public function index(Request $request)
    {
        return Response::html(TemplateEngine::render('index', ['title' => 'Проверка']));
    }
}