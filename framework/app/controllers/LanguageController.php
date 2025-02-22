<?php

namespace App\Controllers;

use SRC\DB;
use SRC\Request;
use SRC\Response;
use SRC\Session;

class LanguageController
{
    private function writeToDB($text, $chars){
        $json = json_encode(['text'=>$text, 'error_chars' => $chars], JSON_UNESCAPED_UNICODE);
        $session_id = DB::queryOne('SELECT id FROM session WHERE payload = :payload',
            [
                ':payload' => Session::getSessionToken()
            ]);
        if ($session_id){
            $session_id = $session_id['id'];
            DB::insert('INSERT INTO history (session_id, text) VALUES (:session_id, :text)',
                [
                    ':session_id' => $session_id,
                    ':text' => $json
                ]);
        }
    }

    private function findErrorChars($text){
        $latin = preg_match_all('/[A-Za-z]/u', $text, $latinMatch);
        $cyrillic = preg_match_all('/[А-Яа-яЁё]/u', $text, $cyrillicMatch);
        $is_cyrillic = $cyrillic >= $latin;
        $arrayChar = [];

        $textMatch = $is_cyrillic ?$latinMatch : $cyrillicMatch;
        foreach ($textMatch[0] as $char) {
            $char = mb_strtolower($char, 'UTF-8');
            if (!in_array($char, $arrayChar))
                $arrayChar[] = $char;
        }
        return $arrayChar;
    }

    public function history(Request $request){
        $result = [];
        $session_id = DB::queryOne('SELECT id FROM session WHERE payload = :payload',
            [
                ':payload' => Session::getSessionToken()
            ]);
        if ($session_id){
            $session_id = $session_id['id'];
            $history = DB::query('SELECT text FROM history WHERE session_id = :session_id ORDER BY history.id DESC',
                [
                    ':session_id' => $session_id,
                ]);

            foreach ($history as &$entry)
                $result[] = json_decode($entry['text'], true);
        }
        return Response::json(['history'=> $result]);
    }
    public function check(Request $request){
        $text = $request->get('text');
        $write = $request->bool('write');
        $result = [];
        if ($text){
            $result = $this->findErrorChars($text);
            if ($write)
                $this->writeToDB($text, $result);
        }
        return Response::json(['error_chars'=> $result]);
    }
}