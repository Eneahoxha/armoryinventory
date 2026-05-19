<?php
// backend/api/models/Equipaggiamento.php

class Equipaggiamento {
    private $conn;
    private $table = 'equipaggiamento';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($limit = 50, $offset = 0) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $query = "
            SELECT e.*, c.nome_categoria
            FROM " . $this->table . " e
            LEFT JOIN categorie_equipaggiamento c ON e.categoria_id = c.categoria_id
            ORDER BY e.nome_modello
            LIMIT " . $limit . " OFFSET " . $offset;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching equipaggiamento: " . $e->getMessage());
        }
    }

    public function getById($id) {
        $query = "
            SELECT e.*, c.nome_categoria
            FROM " . $this->table . " e
            LEFT JOIN categorie_equipaggiamento c ON e.categoria_id = c.categoria_id
            WHERE e.equip_id = ?
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching equipaggiamento: " . $e->getMessage());
        }
    }

    public function create($data) {
        $query = "
            INSERT INTO " . $this->table . " 
            (nome_modello, descrizione, categoria_id, stato, numero_seriale)
            VALUES (?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $data['nome_modello'],
                $data['descrizione'] ?? null,
                $data['categoria_id'],
                $data['stato'] ?? 'Disponibile',
                $data['numero_seriale'] ?? null
            ]);
            return $result;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                throw new Exception("Equipaggiamento con questo numero seriale esiste già");
            }
            throw new Exception("Error creating equipaggiamento: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'equip_id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE equip_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            throw new Exception("Error updating equipaggiamento: " . $e->getMessage());
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE equip_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting equipaggiamento: " . $e->getMessage());
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
            throw new Exception("Error counting equipaggiamento: " . $e->getMessage());
        }
    }
}
?>
