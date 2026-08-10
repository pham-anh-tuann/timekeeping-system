<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class ShiftModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllShifts() {
        $stmt = $this->db->query("SELECT * FROM shifts");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getShiftById($id) {
        $stmt = $this->db->prepare("SELECT * FROM shifts WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}