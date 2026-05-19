<?php
// backend/api/models/User.php

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create users table if not exists
     */
    public function createTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS " . $this->table . " (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                nome VARCHAR(100),
                cognome VARCHAR(100),
                ruolo ENUM('admin', 'capo_sm', 'armaiolo', 'ufficiale', 'operatore') DEFAULT 'operatore',
                stato ENUM('Attivo', 'Disattivato') DEFAULT 'Attivo',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_ruolo (ruolo)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        if ($this->conn->query($query) === TRUE) {
            return true;
        }
        throw new Exception("Error creating users table: " . $this->conn->error);
    }

    /**
     * Create a new user
     */
    public function create($email, $password, $nome, $cognome, $ruolo = 'operatore') {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "
            INSERT INTO " . $this->table . " 
            (email, password_hash, nome, cognome, ruolo) 
            VALUES (?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email, $password_hash, $nome, $cognome, $ruolo]);
            
            // Get last inserted ID - PDO way
            $lastId = $this->conn->lastInsertId();
            
            return [
                'user_id' => $lastId,
                'email' => $email,
                'ruolo' => $ruolo
            ];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || strpos($e->getMessage(), 'UNIQUE') !== false) {
                throw new Exception("Email already registered");
            }
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $query = "SELECT user_id, email, password_hash, nome, cognome, ruolo, stato FROM " . $this->table . " WHERE email = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result : null;
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Get all users (admin only)
     */
    public function getAll($limit = 50, $offset = 0) {
        $query = "
            SELECT user_id, email, nome, cognome, ruolo, stato, created_at 
            FROM " . $this->table . " 
            LIMIT ? OFFSET ?
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
?>
