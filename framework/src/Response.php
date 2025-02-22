<?php

namespace SRC;

use HTTP\HttpHeadType;
use HTTP\HttpStatusCode;

class Response {
    private $status;
    private $body;
    private $head;

    public function __construct($status = 200, $body = '') {
        $this->status = $status;
        $this->body = $body;
    }

    public function setStatus($status){
        $this->status = $status;
    }
    public function setBody($body){
        $this->body = $body;
    }
    public function setHead($head){
        $this->head = $head;
    }
    public function setAll($head, $status, $body){
        $this->status = $status;
        $this->body = $body;
        $this->head = $head;
    }

    public function send() {
        http_response_code($this->status);
        header($this->head);
        echo $this->body;
    }

    public static function json($text){
        $jsonText = json_encode($text, JSON_UNESCAPED_UNICODE);
        if (!$jsonText){
            error_log(__FILE__ . ': Error, converting text to json \'' . $text . '\'',0);
            return '';
        }
        $response = new self();
        $response->setAll(HttpHeadType::JSON->value, HttpStatusCode::OK->value, $jsonText);
        return $response;
    }

    public static function html($text){
        $response = new self();
        $response->setAll(HttpHeadType::HTML->value, HttpStatusCode::OK->value, $text);
        return $response;
    }
}