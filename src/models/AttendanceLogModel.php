<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class AttendanceLogModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTodayRecord($userId) {
        $today = date('Y-m-d');
        $sql = "SELECT * FROM attendance_logs WHERE user_id = :user_id AND work_date = :today LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'today' => $today]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkIn($userId) {
        $today = date('Y-m-d');
        $now = date('H:i:s');
        $fullNow = date('Y-m-d H:i:s');

        $sqlShift = "SELECT s.start_time FROM shifts s JOIN users u ON u.shift_id = s.id WHERE u.id = :user_id";
        $stmtShift = $this->db->prepare($sqlShift);
        $stmtShift->execute(['user_id' => $userId]);
        $shift = $stmtShift->fetch(PDO::FETCH_ASSOC);

        $status = 'Đúng giờ';
        if ($shift && strtotime($now) > strtotime($shift['start_time'])) {
            $status = 'Đi muộn';
        }

        $sql = "INSERT INTO attendance_logs (user_id, work_date, check_in_time, status) 
                VALUES (:user_id, :today, :now, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'today' => $today,
            'now' => $fullNow,
            'status' => $status
        ]);
    }

    public function checkOut($recordId) {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE attendance_logs SET check_out_time = :now WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['now' => $now, 'id' => $recordId]);
    }

    public function getFullAttendanceLogs() {
        $sql = "SELECT logs.*, users.full_name, users.worker_id, shifts.shift_name 
                FROM attendance_logs AS logs
                JOIN users ON logs.user_id = users.id
                JOIN shifts ON users.shift_id = shifts.id
                ORDER BY logs.work_date DESC, logs.check_in_time DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTodayPresent() {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT user_id) as total FROM attendance_logs WHERE work_date = :today");
        $stmt->execute(['today' => $today]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // [MỚI] Thống kê số người đi làm trong 7 ngày gần nhất
    public function getWeeklyStats() {
        $sql = "SELECT work_date, COUNT(id) as total 
                FROM attendance_logs 
                WHERE work_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY work_date 
                ORDER BY work_date ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}