<?php
namespace SRC;

use HTTP\HttpDataType;
use HTTP\HttpHeadType;

class Request
{
    private $data = '';
    private function array_key_exists_recursive($key, $array): bool {
        if (array_key_exists($key, $array)) {
            return true;
        }
        foreach ($array as $value) {
            if (is_array($value) && $this->array_key_exists_recursive($key, $value)) {
                return true;
            }
        }
        return false;
    }
    private function getRecursiveValue($array, $keys, $default) {
        foreach ($keys as $key) {
            if (!is_array($array) || !array_key_exists($key, $array))
                return $default;
            $array = $array[$key];
        }
        return $array;
    }


    public function __construct(){
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (!strcmp($contentType, HttpDataType::URLENCODED->value))
            $this->data = $_REQUEST;
        elseif (!strcmp($contentType, HttpDataType::JSON->value)){
            $json = file_get_contents('php://input');
            $this->data = json_decode($json, true);
        }
    }

    public function getUserAgent(){
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function getIP(){
        return $_SERVER['REMOTE_ADDR'];
    }
    public function getMethod(): string{
        return $_SERVER['REQUEST_METHOD'];
    }
    public function all(){
        return $this->data;
    }

    public function get($key, $default = null){
        return $this->getRecursiveValue($this->data, explode('.', $key), $default);
    }

    public function bool($key){
        return filter_var($this->get($key), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    public function exists($key){
        return $this->array_key_exists_recursive($key, $this->data);
    }
}