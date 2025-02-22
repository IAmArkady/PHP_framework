<?php

namespace SRC;

use PDO;
use PDOException;

class SQLiteDatabase {
    private $pdo;

    public function __construct($databasePath)
    {
        $databasePath = PROJECT_ROOT . $databasePath;
        if (!file_exists($databasePath)){
            die('Ошибка, база данных не найдена ' . $databasePath);
        }
        try {
            $this->pdo = new PDO('sqlite:' . $databasePath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }

    public function queryOne($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }

    public function insert($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            die("Ошибка вставки данных: " . $e->getMessage());
        }
    }

    public function delete($sql, $params = [])
    {
        return $this->execute($sql, $params);
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}

