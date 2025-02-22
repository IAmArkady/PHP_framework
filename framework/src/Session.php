<?php

namespace SRC;

class Session
{
    private static $token = null;
    public static function handle()
    {
        session_name('session_token');
        session_start();
        self::$token = session_id();

        $query = DB::queryOne('SELECT payload FROM session WHERE payload = :payload',
            [
                ':payload' => self::$token
            ]);

        if ($query) {
            DB::query('UPDATE session SET ip_address = :ip, user_agent = :user_agent WHERE payload = :payload',
                [
                    ':ip' => (new Request())->getIP(),
                    ':user_agent' => (new Request())->getUserAgent(),
                    ':payload' => self::$token,
                ]);
        }
        else {
            $request = new Request();
            $session_id = bin2hex(random_bytes(32));
            DB::insert('INSERT INTO session (id, ip_address, user_agent, payload) VALUES (:id, :ip, :user_agent, :payload)',
                [
                    ':id' => $session_id,
                    ':ip' => $request->getIP(),
                    ':user_agent' => $request->getUserAgent(),
                    ':payload' =>  self::$token,
                ]);
        }
    }

    public static function getSessionToken(){
        return self::$token;
    }

}