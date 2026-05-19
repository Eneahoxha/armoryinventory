<?php
// backend/api/models/Squadre.php

class Squadre {
    private $conn;
    private $table = 'squadre';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($limit = 50, $offset = 0) {
        // LIMIT with prepare statements requires special handling in PDO
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $query = "
            SELECT s.*, COUNT(p.personale_id) as num_personale
            FROM " . $this->table . " s
            LEFT JOIN personale p ON s.squadra_id = p.squadra_id
            GROUP BY s.squadra_id
            ORDER BY s.nome_squadra
            LIMIT " . $limit . " OFFSET " . $offset;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching squadre: " . $e->getMessage());
        }
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE squadra_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching squadra: " . $e->getMessage());
        }
    }

    public function create($data) {
        $query = "
            INSERT INTO " . $this->table . " (nome_squadra, stato)
            VALUES (?, ?)
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $data['nome_squadra'],
                $data['stato'] ?? 'Disponibile'
            ]);
            return $result;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                throw new Exception("Squadra con questo nome esiste già");
            }
            throw new Exception("Error creating squadra: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'squadra_id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE squadra_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            throw new Exception("Error updating squadra: " . $e->getMessage());
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE squadra_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting squadra: " . $e->getMessage());
        }
    }

    public function count() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Error counting squadre: " . $e->getMessage());
        }
    }
}
?>
