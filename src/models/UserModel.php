<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class UserModel {
    private $db;

    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    public function authenticate($workerId, $password) {
        $st = $this->db->prepare("SELECT * FROM users WHERE worker_id = ? LIMIT 1");
        $st->execute([$workerId]);
        $user = $st->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Hỗ trợ cả mật khẩu mã hóa (hash) lẫn mật khẩu nhập tay (text thường) để dễ test
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                return $user;
            }
        }
        return false;
    }

    public function getUserByWorkerId($id) {
        $st = $this->db->prepare("SELECT * FROM users WHERE worker_id = ? LIMIT 1");
        $st->execute([$id]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $st = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $st->execute([$id]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        // Nối với bảng shifts để lấy tên ca làm việc ra cho đầy đủ
        $sql = "SELECT u.*, s.shift_name FROM users u LEFT JOIN shifts s ON u.shift_id = s.id ORDER BY u.created_at DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ĐÃ FIX: Thêm shift_id và avatar mặc định
    public function createUser($workerId, $password, $fullName, $role, $dailySalary, $shiftId = 1) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (worker_id, password, full_name, role, daily_salary, shift_id, is_active, avatar) VALUES (?, ?, ?, ?, ?, ?, 1, 'default-avatar.png')";
        return $this->db->prepare($sql)->execute([$workerId, $hash, $fullName, $role, $dailySalary, $shiftId]);
    }

    // ĐÃ FIX: Hỗ trợ lưu thay đổi shift_id
    public function updateUser($id, $workerId, $fullName, $role, $dailySalary, $shiftId) {
        $sql = "UPDATE users SET worker_id = ?, full_name = ?, role = ?, daily_salary = ?, shift_id = ? WHERE id = ?";
        return $this->db->prepare($sql)->execute([$workerId, $fullName, $role, $dailySalary, $shiftId, $id]);
    }

    public function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $id]);
    }

    public function toggleUserStatus($id, $status) {
        return $this->db->prepare("UPDATE users SET is_active = ? WHERE id = ?")->execute([$status ? 0 : 1, $id]);
    }
}