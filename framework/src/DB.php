<?php

namespace SRC;

class DB
{
    protected static $connection;

    public static function connection()
    {
        if (is_null(self::$connection)) {
            require __DIR__ . '/SQLiteDatabase.php';
            $config = require __DIR__ . '/../config/database.php';
            self::$connection = new SQLiteDatabase($config['database']);
        }
        return self::$connection;
    }

    public static function insert($sql, $params = [])
    {
        return self::connection()->insert($sql, $params);
    }

    public static function query($sql, $params = [])
    {
        return self::connection()->query($sql, $params);
    }

    public static function delete($sql, $params = []){
        return self::connection()->delete($sql, $params);
    }

    public static function queryOne($sql, $params = []){
        return self::connection()->queryOne($sql, $params);
    }
}