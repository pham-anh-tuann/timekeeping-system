<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class LeaveModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Nhân viên gửi đơn
    public function createRequest($userId, $reason, $start, $end) {
        $sql = "INSERT INTO leave_requests (user_id, reason, start_date, end_date) VALUES (:user_id, :reason, :start, :end)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'reason' => $reason,
            'start' => $start,
            'end' => $end
        ]);
    }

    // Lấy danh sách đơn của 1 nhân viên
    public function getRequestsByUser($userId) {
        $sql = "SELECT * FROM leave_requests WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin lấy toàn bộ đơn của mọi người
    public function getAllRequests() {
        $sql = "SELECT lr.*, u.full_name, u.worker_id 
                FROM leave_requests lr 
                JOIN users u ON lr.user_id = u.id 
                ORDER BY lr.status DESC, lr.created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin duyệt hoặc từ chối đơn
    public function updateStatus($id, $status) {
        $sql = "UPDATE leave_requests SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }
}