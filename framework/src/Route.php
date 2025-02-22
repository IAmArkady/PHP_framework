<?php
namespace SRC;

use HTTP\HttpHeadType;
use HTTP\HttpStatusCode;

require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/helper.php';

class Route{
    private static $routes = [];

    private static function getHostUrl(): string {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $scheme . '://' . $host;
    }

    private static function createMethods($uri, $methods, $controller) {
        $methods = is_array($methods) ? $methods : [$methods];
        if (isset(self::$routes[$uri])){
            $intersect = array_intersect_key(self::$routes[$uri], array_flip($methods));
            if (!empty($intersect)) {
                error_log(__FILE__ . ': Error, route already exists \'' . $uri . '\' ('. implode(', ', array_keys($intersect)) . ')');
                return;
            }
        }
        foreach ($methods as $method)
            self::$routes[$uri][$method] = [
                'controller' => ['class' => $controller[0], 'method' => $controller[1]]
            ];
    }

    private static function callController($uri): Response {
        $request = new Request(); $response = new Response();
        $body = ''; $status = -1; $head = ''; $controller = null;
        foreach (self::$routes[$uri] as $key => $route){
            if (strtoupper($key) == 'ANY')
                $controller = $route['controller'];
            if (strtoupper($key) == $request->getMethod()){
                $controller = $route['controller'];
                break;
            }
        }
        if ($controller){
             $controllerName = $controller['class'];
             $methodName = $controller['method'];
             require_once __DIR__ . '/../app/controllers/' . basename($controllerName) . '.php';
             $controllerInstance = new $controllerName();
             $response = $controllerInstance->$methodName($request);
        }
        else{
            $body = 'Method Not Allowed';
            $status = HttpStatusCode::MethodNotAllowed;
            $head = HttpHeadType::HTML;
            $response->setAll($head->value, $status->value, $body);
        }
        return $response;
    }

    public static function handle(){
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (isset(self::$routes[$uri])) {
            $response = self::callController($uri);
            $response->send();
            return;
        }
        $response = new Response();
        $response->setAll(HttpHeadType::HTML->value,
            HttpStatusCode::NotFound->value,
            '<h1> 404 - Страница не найдена </h1>');
        $response->send();
    }

    static public function get($uri, $controller): static {
        self::createMethods($uri,'GET', $controller);
        return new self();
    }

    static public function post($uri, $controller): static {
        self::createMethods($uri, 'POST', $controller);
        return new self();
    }

    static public function any($uri, $controller): static {
        self::createMethods($uri, 'ANY', $controller);
        return new self();
    }
    public function name($name): static {
        $name = trim($name);
        $uri = array_key_last(self::$routes);
        if(!empty($name)) {
            if (isset(self::$routes[$uri]['name'])){
                if (!in_array($name, self::$routes[$uri]['name']))
                    self::$routes[$uri]['name'][] = $name;
            }
            else
                self::$routes[$uri]['name'] = [$name];
        }
        else
            error_log(__FILE__ . ': Error, empty name for \'' . $uri . '\'',0);
        return $this;
    }

    public static function routeByName($name): string|null {
        $name = trim($name);
        if (empty($name))
            return null;
        foreach (self::$routes as $uri => $info)
            if (isset($info['name']) and in_array($name, $info['name']))
                return self::getHostUrl() . $uri;
        return null;
    }
}