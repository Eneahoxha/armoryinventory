<?php
// backend/api/models/Dotazioni.php

class Dotazioni {
    private $conn;
    private $table = 'dotazioni_personale';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($limit = 50, $offset = 0) {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        
        $query = "
            SELECT d.*, p.nome, p.cognome, e.nome_modello, e.numero_seriale
            FROM " . $this->table . " d
            LEFT JOIN personale p ON d.personale_id = p.personale_id
            LEFT JOIN equipaggiamento e ON d.equip_id = e.equip_id
            ORDER BY p.cognome, p.nome
            LIMIT " . $limit . " OFFSET " . $offset;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching dotazioni: " . $e->getMessage());
        }
    }

    public function getById($id) {
        $query = "
            SELECT d.*, p.nome, p.cognome, e.nome_modello, e.numero_seriale
            FROM " . $this->table . " d
            LEFT JOIN personale p ON d.personale_id = p.personale_id
            LEFT JOIN equipaggiamento e ON d.equip_id = e.equip_id
            WHERE d.dotazione_id = ?
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching dotazione: " . $e->getMessage());
        }
    }

    public function getByPersonale($personale_id) {
        $query = "
            SELECT d.*, e.nome_modello, e.numero_seriale, c.nome_categoria
            FROM " . $this->table . " d
            LEFT JOIN equipaggiamento e ON d.equip_id = e.equip_id
            LEFT JOIN categorie_equipaggiamento c ON e.categoria_id = c.categoria_id
            WHERE d.personale_id = ? AND d.data_ritiro IS NULL
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$personale_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching dotazioni for personale: " . $e->getMessage());
        }
    }

    public function create($data) {
        $query = "
            INSERT INTO " . $this->table . " 
            (personale_id, equip_id, tipo_uso, note_configurazione, data_assegnazione)
            VALUES (?, ?, ?, ?, ?)
        ";
        
        try {
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([
                $data['personale_id'],
                $data['equip_id'],
                $data['tipo_uso'],
                $data['note_configurazione'] ?? null,
                $data['data_assegnazione'] ?? date('Y-m-d')
            ]);
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error creating dotazione: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'dotazione_id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE dotazione_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($values);
        } catch (PDOException $e) {
            throw new Exception("Error updating dotazione: " . $e->getMessage());
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE dotazione_id = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Error deleting dotazione: " . $e->getMessage());
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
            throw new Exception("Error counting dotazioni: " . $e->getMessage());
        }
    }
}
?>
