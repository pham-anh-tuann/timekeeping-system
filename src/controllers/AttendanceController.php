<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\{AttendanceLogModel, UserModel, LeaveModel};
use App\Config\Database;
use App\Middlewares\AuthMiddleware;

class AttendanceController {
    public function __construct() { 
        AuthMiddleware::check(); 
        // 1. ÉP MÚI GIỜ VIỆT NAM ĐỂ HÀM DATE() CHẠY CHUẨN XÁC
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    public function showProfile() {
        $userId = $_SESSION['user']['id'];
        $userRole = strtolower(trim($_SESSION['user']['role'] ?? ''));
        $db = Database::getInstance()->getConnection();
        
        $st4 = $db->prepare("SELECT u.*, s.shift_name, s.start_time, s.end_time 
                             FROM users u 
                             LEFT JOIN shifts s ON u.shift_id = s.id 
                             WHERE u.id = ?");
        $st4->execute([$userId]);
        $currentUser = $st4->fetch(\PDO::FETCH_ASSOC);

        $record = null; $logs = []; $leaves = []; $complaints = [];

        if ($userRole !== 'admin') {
            // FIX CA ĐÊM: Thay vì tìm theo ngày hiện tại, tìm Ca Gần Nhất chưa Check-out HOẶC Ca của ngày hôm nay
            $st1 = $db->prepare("SELECT * FROM attendance_logs 
                                 WHERE user_id = ? AND (check_out_time IS NULL OR work_date = CURDATE()) 
                                 ORDER BY id DESC LIMIT 1");
            $st1->execute([$userId]);
            $record = $st1->fetch(\PDO::FETCH_ASSOC);

            $st2 = $db->prepare("SELECT * FROM attendance_logs WHERE user_id = ? ORDER BY work_date DESC, id DESC");
            $st2->execute([$userId]);
            $logs = $st2->fetchAll(\PDO::FETCH_ASSOC);

            $st3 = $db->prepare("SELECT * FROM leave_requests WHERE user_id = ? ORDER BY created_at DESC");
            $st3->execute([$userId]);
            $leaves = $st3->fetchAll(\PDO::FETCH_ASSOC);

            $st5 = $db->prepare("SELECT c.*, a.work_date FROM complaints c JOIN attendance_logs a ON c.attendance_log_id = a.id WHERE c.user_id = ? ORDER BY c.created_at DESC");
            $st5->execute([$userId]);
            $complaints = $st5->fetchAll(\PDO::FETCH_ASSOC);
        }

        View::render('employee/profile', [
            'title' => 'Hồ sơ & Kiosk',
            'user' => $currentUser,
            'record' => $record,
            'logs' => $logs,
            'leaves' => $leaves,
            'complaints' => $complaints
        ]);
    }

    public function processPunch() {
        $userId = $_SESSION['user']['id'];
        $db = Database::getInstance()->getConnection();
        $action = $_POST['action'] ?? '';
        
        // ==========================================
        // TÍNH NĂNG 1: KHÓA IP CHỐNG GIAN LẬN
        // ==========================================
        $userIp = $_SERVER['REMOTE_ADDR'];
        // Khai báo các IP mạng Wi-Fi công ty được phép chấm công. 
        // (127.0.0.1 và ::1 là IP local khi bro test trên XAMPP)
        $allowedIps = ['127.0.0.1', '::1', '192.168.1.100', '192.168.1.101']; 
        
        if (!in_array($userIp, $allowedIps)) {
            die("<script>alert('Lỗi bảo mật: Bạn phải kết nối mạng Wi-Fi của công ty để chấm công!\\nIP thiết bị của bạn hiện tại là: $userIp'); window.location.href='/timekeeping-system/public/profile';</script>");
        }

        // Lấy giờ hiện tại
        $currentTime = date('H:i:s');

        // Lấy thông tin ca làm việc
        $stShift = $db->prepare("SELECT s.start_time, s.end_time, s.late_threshold FROM users u JOIN shifts s ON u.shift_id = s.id WHERE u.id = ?");
        $stShift->execute([$userId]);
        $shift = $stShift->fetch(\PDO::FETCH_ASSOC);

        if (!$shift) {
            die("<script>alert('Lỗi: Bạn chưa được phân ca làm việc!'); window.location.href='/timekeeping-system/public/profile';</script>");
        }

        // ==========================================
        // TÍNH NĂNG 2: XỬ LÝ CA ĐÊM & CHẶN SỚM 15P
        // ==========================================
        
        // Tìm xem có ca nào ĐANG MỞ (Chưa check-out) hay không (Dùng cho Ca đêm)
        $stOpen = $db->prepare("SELECT * FROM attendance_logs WHERE user_id = ? AND check_out_time IS NULL ORDER BY id DESC LIMIT 1");
        $stOpen->execute([$userId]);
        $openLog = $stOpen->fetch(\PDO::FETCH_ASSOC);

        if ($action === 'check_in') {
            if ($openLog) {
                die("<script>alert('Lỗi: Bạn chưa Check-out ca làm việc trước đó!'); window.location.href='/timekeeping-system/public/profile';</script>");
            }

            $startTime = $shift['start_time'];
            $lateThreshold = $shift['late_threshold'];
            
            // Tính thời gian cho phép vào ca (Sớm 15 phút)
            $allowedInTime = date('H:i:s', strtotime($startTime) - (15 * 60));

            if ($currentTime < $allowedInTime) {
                die("<script>alert('Chưa đến giờ vào ca!\\nCửa hệ thống mở lúc: {$allowedInTime}\\nGiờ hiện tại: {$currentTime}'); window.location.href='/timekeeping-system/public/profile';</script>");
            }

            $status = ($currentTime <= $lateThreshold) ? 'On-time' : 'Late';
            $sql = "INSERT INTO attendance_logs (user_id, work_date, check_in_time, status, approval_status) VALUES (?, CURDATE(), NOW(), ?, 'Pending')";
            $db->prepare($sql)->execute([$userId, $status]);
            
        } elseif ($action === 'check_out') {
            if (!$openLog) {
                die("<script>alert('Lỗi: Không tìm thấy ca làm việc đang mở!'); window.location.href='/timekeeping-system/public/profile';</script>");
            }

            $endTime = $shift['end_time'];
            $allowedOutTime = date('H:i:s', strtotime($endTime) - (15 * 60));

            // Logic chặn ra sớm: Dùng hàm tính khoảng cách giờ để xử lý được cả ca đêm
            // Nếu current_time < allowedOutTime VÀ (khoảng cách giờ > 1 tiếng - tránh lỗi ca đêm)
            if ($currentTime < $allowedOutTime && strtotime($allowedOutTime) - strtotime($currentTime) > 0 && strtotime($allowedOutTime) - strtotime($currentTime) < 43200) {
                die("<script>alert('Chưa đến giờ tan ca!\\nĐược phép về sớm nhất lúc: {$allowedOutTime}\\nGiờ hiện tại: {$currentTime}'); window.location.href='/timekeeping-system/public/profile';</script>");
            }

            // Lưu Check-out dựa theo ID log đang mở (Hoàn hảo cho ca đêm xuyên ngày)
            $sql = "UPDATE attendance_logs SET check_out_time = NOW(), approval_status = 'Pending' WHERE id = ?";
            $db->prepare($sql)->execute([$openLog['id']]);
        }
        
        header("Location: /timekeeping-system/public/profile");
        exit;
    }

    public function submitLeave() {
        $userId = $_SESSION['user']['id'];
        $db = Database::getInstance()->getConnection();
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];

        // ==========================================
        // TÍNH NĂNG 3: CHỐNG TRÙNG LẶP ĐƠN NGHỈ PHÉP
        // ==========================================
        // Công thức check Overlap: (Ngày bắt đầu mới <= Ngày kết thúc cũ) VÀ (Ngày kết thúc mới >= Ngày bắt đầu cũ)
        $checkSql = "SELECT COUNT(*) FROM leave_requests 
                     WHERE user_id = ? AND status != 'Rejected'
                     AND (start_date <= ? AND end_date >= ?)";
        $stCheck = $db->prepare($checkSql);
        $stCheck->execute([$userId, $endDate, $startDate]);
        $overlap = $stCheck->fetchColumn();

        if ($overlap > 0) {
            die("<script>alert('Lỗi: Khoảng thời gian này bị TRÙNG LẶP với một đơn xin nghỉ phép khác của bạn đã gửi trước đó!'); window.location.href='/timekeeping-system/public/profile';</script>");
        }

        $sql = "INSERT INTO leave_requests (user_id, start_date, end_date, reason, status, is_paid) VALUES (?, ?, ?, ?, 'Pending', 0)";
        $db->prepare($sql)->execute([$userId, $startDate, $endDate, $_POST['reason']]);
        header("Location: /timekeeping-system/public/profile");
        exit;
    }

    public function submitComplaint() {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO complaints (user_id, attendance_log_id, reason, suggested_time, suggested_out_time, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
        $db->prepare($sql)->execute([$_SESSION['user']['id'], $_POST['attendance_log_id'], $_POST['reason'], $_POST['suggested_time'], $_POST['suggested_out_time']]);
        header("Location: /timekeeping-system/public/profile");
        exit;
    }

    public function updateInfo() {
        $userId = $_SESSION['user']['id'];
        $db = Database::getInstance()->getConnection();
        
        $st = $db->prepare("SELECT avatar FROM users WHERE id = ?");
        $st->execute([$userId]);
        $avatarName = $st->fetchColumn() ?: 'default-avatar.png';

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_DIR . '/public/uploads/avatars/';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
            
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($ext, $allowed)) {
                $avatarName = 'user_' . $userId . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $avatarName);
                $_SESSION['user']['avatar'] = $avatarName;
            }
        }

        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';

        $db->prepare("UPDATE users SET email = ?, phone = ?, address = ?, avatar = ? WHERE id = ?")
           ->execute([$email, $phone, $address, $avatarName, $userId]);
           
        header("Location: /timekeeping-system/public/profile");
        exit;
    }

    public function changePassword() {
        (new UserModel())->updatePassword($_SESSION['user']['id'], $_POST['new_password']);
        die("<script>alert('Đổi mật khẩu thành công!'); window.location.href='/timekeeping-system/public/profile';</script>");
    }
}