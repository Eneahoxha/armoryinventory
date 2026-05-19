<?php
// backend/api/models/Personale.php

class Personale {
    private $conn;
    private $table = 'personale';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all personale with pagination
     */
    public function getAll($limit = 50, $offset = 0) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $query = "
            SELECT p.*, s.nome_squadra 
            FROM " . $this->table . " p
            LEFT JOIN squadre s ON p.squadra_id = s.squadra_id
            ORDER BY p.cognome, p.nome
            LIMIT " . $limit . " OFFSET " . $offset;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching personale: " . $e->getMessage());
        }
    }

    /**
     * Get single personale by ID
     */
    public function getById($id) {
        $query = "
            SELECT p.*, s.nome_squadra 
            FROM " . $this->table . " p
            LEFT JOIN squadre s ON p.squadra_id = s.squadra_id
            WHERE p.personale_id = ?
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching personale: " . $e->getMessage());
        }
    }

    /**
     * Create new personale
     */
    public function create($data) {
        // Generate personale_id if not provided
        if (empty($data['personale_id'])) {
            $count = $this->count();
            $data['personale_id'] = 'SEAL_' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        }
        
        $query = "
            INSERT INTO " . $this->table . " 
            (personale_id, nome, cognome, soprannome, nome_in_codice, occupazione, grado, 
             data_nascita, luogo_origine, sesso, altezza, stato_servizio, squadra_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $data['personale_id'],
                $data['nome'],
                $data['cognome'],
                $data['soprannome'] ?? null,
                $data['nome_in_codice'] ?? null,
                $data['occupazione'] ?? null,
                $data['grado'] ?? null,
                $data['data_nascita'] ?? null,
                $data['luogo_origine'] ?? null,
                $data['sesso'],
                $data['altezza'] ?? null,
                $data['stato_servizio'] ?? 'Attivo',
                $data['squadra_id'] ?? null
            ]);
            
            return $result;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                throw new Exception("Personale con questo ID esiste già");
            }
            throw new Exception("Error creating personale: " . $e->getMessage());
        }
    }

    /**
     * Update personale
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'personale_id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE personale_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            throw new Exception("Error updating personale: " . $e->getMessage());
        }
    }

    /**
     * Delete personale
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE personale_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting personale: " . $e->getMessage());
        }
    }

    /**
     * Get total count
     */
    public function count() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Error counting personale: " . $e->getMessage());
        }
    }
}
?>
