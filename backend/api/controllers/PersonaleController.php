<?php
// backend/api/controllers/PersonaleController.php

class PersonaleController {
    private $db;
    private $model;

    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/../models/Personale.php';
        $this->model = new Personale($db);
    }

    public function getAll() {
        try {
            $limit = (int)($_GET['limit'] ?? 50);
            $offset = (int)($_GET['offset'] ?? 0);
            
            $data = $this->model->getAll($limit, $offset);
            $total = $this->model->count();
            
            http_response_code(200);
            return [
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'total' => $total
                ]
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $data = $this->model->getById($id);
            
            if (!$data) {
                http_response_code(404);
                return ['error' => 'Personale not found'];
            }
            
            http_response_code(200);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['personale_id']) || !isset($data['nome']) || !isset($data['cognome']) || !isset($data['sesso'])) {
                http_response_code(400);
                return ['error' => 'Missing required fields'];
            }
            
            $result = $this->model->create($data);
            
            http_response_code(201);
            return ['success' => true, 'message' => 'Personale created'];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = $this->model->update($id, $data);
            
            if ($result) {
                http_response_code(200);
                return ['success' => true, 'message' => 'Personale updated'];
            }
            
            http_response_code(500);
            return ['error' => 'Update failed'];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            $result = $this->model->delete($id);
            
            if ($result) {
                http_response_code(200);
                return ['success' => true, 'message' => 'Personale deleted'];
            }
            
            http_response_code(500);
            return ['error' => 'Delete failed'];
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => $e->getMessage()];
        }
    }
}
?>
