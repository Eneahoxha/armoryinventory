<?php
// backend/api/config/Database.php

class Database {
    private $host = 'mysql';
    private $db_name = 'USNAVY';
    private $user = 'armory_user';
    private $password = 'armory_password';
    private $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }
        return $this->conn;
    }
}
?>
