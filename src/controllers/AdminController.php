<?php
namespace App\Controllers;

use App\Core\View;
use App\Middlewares\AuthMiddleware;
use App\Models\{UserModel, AttendanceLogModel, LeaveModel};
use App\Config\Database;

class AdminController {
    public function __construct() { 
        AuthMiddleware::check(); 
    }

    // ==========================================
    // HỆ THỐNG GỬI THÔNG BÁO (NOTIFICATIONS)
    // ==========================================
    private function sendNotify($userId, $title, $message) {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)";
        $db->prepare($sql)->execute([$userId, $title, $message]);
    }

    // ==========================================
    // 1. DASHBOARD & THỐNG KÊ CHUNG
    // ==========================================
    public function dashboard() {
        AuthMiddleware::checkManager(); 
        $uM = new UserModel(); 
        $db = Database::getInstance()->getConnection();
        $us = $uM->getAllUsers();
        $totalEmp = count(array_filter($us, fn($u) => strtolower(trim($u['role'])) !== 'admin' && $u['is_active'] == 1));
        
        $stToday = $db->prepare("SELECT COUNT(DISTINCT user_id) as total FROM attendance_logs WHERE work_date = CURDATE()");
        $stToday->execute();
        $todayPresent = $stToday->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;

        $stChart = $db->prepare("SELECT work_date, COUNT(DISTINCT user_id) as present_count FROM attendance_logs WHERE work_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY work_date ORDER BY work_date ASC");
        $stChart->execute();
        $chartData = $stChart->fetchAll(\PDO::FETCH_ASSOC);

        View::render('admin/dashboard', [
            'title' => 'Dashboard', 'user' => $_SESSION['user'],
            'stats' => ['total_emp' => $totalEmp, 'today_present' => $todayPresent],
            'chartData' => $chartData
        ]);
    }

    // ==========================================
    // 2. QUẢN LÝ NHÂN VIÊN (CRUD)
    // ==========================================
    public function manageEmployees() { 
        AuthMiddleware::checkAdmin(); 
        View::render('admin/employees', ['title' => 'Nhân viên', 'user' => $_SESSION['user'], 'users' => (new UserModel())->getAllUsers()]); 
    }

    public function showCreateEmployeeForm() { 
        AuthMiddleware::checkAdmin(); 
        View::render('admin/create_employee', ['title' => 'Thêm NV', 'user' => $_SESSION['user'], 'error' => '']); 
    }

    public function storeEmployee() { 
        AuthMiddleware::checkAdmin(); 
        (new UserModel())->createUser($_POST['worker_id'], $_POST['password'], $_POST['full_name'], $_POST['role'], $_POST['daily_salary']); 
        header("Location: /timekeeping-system/public/admin/employees"); exit; 
    }

    public function showEditEmployeeForm() { 
        AuthMiddleware::checkAdmin(); 
        $db = Database::getInstance()->getConnection();
        $editUser = (new UserModel())->getUserById($_GET['id']);
        $shifts = $db->query("SELECT * FROM shifts")->fetchAll(\PDO::FETCH_ASSOC);
        View::render('admin/edit_employee', ['title' => 'Sửa NV', 'user' => $_SESSION['user'], 'editUser' => $editUser, 'shifts' => $shifts]); 
    }

    public function updateEmployee() { 
        AuthMiddleware::checkAdmin(); 
        $shiftId = $_POST['shift_id'] ?? 1;
        (new UserModel())->updateUser($_POST['id'], $_POST['worker_id'], $_POST['full_name'], $_POST['role'], $_POST['daily_salary'], $shiftId); 
        header("Location: /timekeeping-system/public/admin/employees"); exit; 
    }

    public function toggleEmployeeStatus() {
        AuthMiddleware::checkAdmin();
        $id = $_GET['id'];
        $db = Database::getInstance()->getConnection();
        $st = $db->prepare("SELECT role, is_active FROM users WHERE id = ?"); $st->execute([$id]); $user = $st->fetch(\PDO::FETCH_ASSOC);
        if (strtolower(trim($user['role'])) === 'admin') die("<script>alert('Lỗi: Không được khóa Admin!'); window.history.back();</script>");
        $db->prepare("UPDATE users SET is_active = ? WHERE id = ?")->execute([$user['is_active'] == 1 ? 0 : 1, $id]);
        header("Location: /timekeeping-system/public/admin/employees"); exit;
    }

    public function deleteEmployee() {
        AuthMiddleware::checkAdmin();
        $id = $_GET['id'];
        $db = Database::getInstance()->getConnection();
        $st = $db->prepare("SELECT role FROM users WHERE id = ?"); $st->execute([$id]); $user = $st->fetch(\PDO::FETCH_ASSOC);
        if (strtolower(trim($user['role'])) === 'admin') die("<script>alert('Lỗi: Cấm xóa Admin!'); window.history.back();</script>");
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM attendance_logs WHERE user_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM leave_requests WHERE user_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM complaints WHERE user_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM notifications WHERE user_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
            $db->commit();
        } catch (\Exception $e) { $db->rollBack(); die("Lỗi: " . $e->getMessage()); }
        header("Location: /timekeeping-system/public/admin/employees"); exit;
    }

    // ==========================================
    // 3. BÁO CÁO & XUẤT EXCEL
    // ==========================================
    public function reports() {
        AuthMiddleware::checkManager();
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT a.*, u.full_name, u.worker_id FROM attendance_logs a JOIN users u ON a.user_id = u.id ORDER BY a.work_date DESC";
        View::render('admin/reports', ['title' => 'Báo cáo', 'user' => $_SESSION['user'], 'logs' => $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    public function exportReports() {
        AuthMiddleware::checkManager();
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT a.work_date, u.worker_id, u.full_name, a.check_in_time, a.check_out_time, a.status, a.approval_status 
                FROM attendance_logs a JOIN users u ON a.user_id = u.id ORDER BY a.work_date DESC";
        $logs = $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Bao_Cao_Cham_Cong_' . date('dmY') . '.csv');
        $output = fopen('php://output', 'w');
        
        // Ghi BOM để Excel hiển thị đúng Tiếng Việt
        fputs($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['Mã NV', 'Họ Tên', 'Ngày Làm', 'Giờ Vào', 'Giờ Ra', 'Hệ Thống', 'Trạng Thái Duyệt']);
        foreach ($logs as $row) { 
            fputcsv($output, [
                $row['worker_id'], 
                $row['full_name'], 
                date('d/m/Y', strtotime($row['work_date'])), 
                $row['check_in_time'], 
                $row['check_out_time'] ?? '--:--', 
                $row['status'] === 'Late' ? 'Đi Muộn' : 'Đúng Giờ', 
                $row['approval_status'] === 'Pending' ? 'Chờ Duyệt' : ($row['approval_status'] === 'Approved' ? 'Đã Duyệt' : 'Từ Chối')
            ]); 
        }
        fclose($output); exit;
    }

    // ==========================================
    // 4. QUẢN LÝ LƯƠNG
    // ==========================================
    public function salary() {
        AuthMiddleware::checkAdmin();
        $m = $_GET['month'] ?? date('m'); $y = $_GET['year'] ?? date('Y');
        $db = Database::getInstance()->getConnection();
        
        // Tính toán cả ngày nghỉ CÓ LƯƠNG
        $sql = "SELECT u.id, u.full_name, u.worker_id, u.daily_salary, 
                (SELECT COUNT(id) FROM attendance_logs WHERE user_id = u.id AND approval_status = 'Approved' AND MONTH(work_date) = ? AND YEAR(work_date) = ?) as work_days,
                (SELECT COALESCE(SUM(DATEDIFF(end_date, start_date) + 1), 0) FROM leave_requests WHERE user_id = u.id AND status = 'Approved' AND is_paid = 1 AND MONTH(start_date) = ? AND YEAR(start_date) = ?) as paid_leaves
                FROM users u WHERE u.role != 'Admin' AND u.is_active = 1";
        $st = $db->prepare($sql); $st->execute([$m, $y, $m, $y]);
        View::render('admin/salary', ['title' => 'Lương', 'user' => $_SESSION['user'], 'salaries' => $st->fetchAll(\PDO::FETCH_ASSOC), 'm' => $m, 'y' => $y]);
    }

    // ==========================================
    // 5. DUYỆT NGHỈ PHÉP
    // ==========================================
    public function leaves() { 
        AuthMiddleware::checkManager();
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT l.*, u.full_name, u.worker_id FROM leave_requests l JOIN users u ON l.user_id = u.id WHERE u.role != 'Admin' ORDER BY l.created_at DESC";
        View::render('admin/leaves', ['title' => 'Nghỉ phép', 'user' => $_SESSION['user'], 'requests' => $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC)]); 
    }

    public function approveLeaveAction() { 
        AuthMiddleware::checkManager();
        $id = $_GET['id']; $status = $_GET['status']; $isPaid = $_GET['is_paid'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $st = $db->prepare("SELECT l.user_id, l.start_date, u.role FROM leave_requests l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
        $st->execute([$id]); $request = $st->fetch(\PDO::FETCH_ASSOC);

        // Logic chặn quyền
        if ($request['user_id'] == $_SESSION['user']['id']) die("<script>alert('Lỗi: Không tự duyệt đơn của mình!'); window.history.back();</script>");
        if (strtolower(trim($request['role'])) === 'manager' && strtolower(trim($_SESSION['user']['role'])) !== 'admin') die("<script>alert('Lỗi: Chỉ Admin mới duyệt cho Trưởng phòng!'); window.history.back();</script>");

        $db->prepare("UPDATE leave_requests SET status = ?, is_paid = ? WHERE id = ?")->execute([$status, $isPaid, $id]);
        
        $title = ($status === 'Approved') ? "✅ Đơn nghỉ phép ĐƯỢC DUYỆT" : "❌ Đơn nghỉ phép BỊ TỪ CHỐI";
        $this->sendNotify($request['user_id'], $title, "Đơn nghỉ ngày " . date('d/m/Y', strtotime($request['start_date'])) . " đã có kết quả.");
        header("Location: /timekeeping-system/public/admin/leaves"); exit; 
    }

    // ==========================================
    // 6. DUYỆT CHẤM CÔNG HẰNG NGÀY
    // ==========================================
    public function approvals() {
        AuthMiddleware::checkManager(); 
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT a.*, u.full_name, u.worker_id, u.role FROM attendance_logs a JOIN users u ON a.user_id = u.id WHERE u.role != 'Admin' ORDER BY a.work_date DESC, a.check_in_time DESC";
        View::render('admin/approvals', ['title' => 'Duyệt Chấm Công', 'user' => $_SESSION['user'], 'logs' => $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    public function approveAttendanceAction() {
        AuthMiddleware::checkManager();
        $id = $_GET['id']; $status = $_GET['status']; 
        $db = Database::getInstance()->getConnection();
        $st = $db->prepare("SELECT a.user_id, u.role, a.work_date FROM attendance_logs a JOIN users u ON a.user_id = u.id WHERE a.id = ?");
        $st->execute([$id]); $log = $st->fetch(\PDO::FETCH_ASSOC);

        // Logic chặn quyền
        if ($log['user_id'] == $_SESSION['user']['id']) die("<script>alert('Lỗi: Không tự duyệt công cho mình!'); window.history.back();</script>");
        if (strtolower(trim($log['role'])) === 'manager' && strtolower(trim($_SESSION['user']['role'])) !== 'admin') die("<script>alert('Lỗi: Chỉ Admin duyệt cho Trưởng phòng!'); window.history.back();</script>");

        $db->prepare("UPDATE attendance_logs SET approval_status = ? WHERE id = ?")->execute([$status, $id]);
        $msg = ($status === 'Approved') ? "Ngày công {$log['work_date']} đã được duyệt." : "Ngày công {$log['work_date']} bị từ chối.";
        $this->sendNotify($log['user_id'], "Kết quả duyệt công", $msg);
        header("Location: /timekeeping-system/public/admin/approvals"); exit;
    }

    // ==========================================
    // 7. KHIẾU NẠI & TỰ ĐỘNG SỬA GIỜ
    // ==========================================
    public function complaints() {
        AuthMiddleware::checkManager(); 
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT c.*, u.full_name, u.worker_id, a.work_date, a.check_in_time as old_in, a.check_out_time as old_out FROM complaints c JOIN users u ON c.user_id = u.id JOIN attendance_logs a ON c.attendance_log_id = a.id ORDER BY c.created_at DESC";
        View::render('admin/complaints', ['title' => 'Khiếu nại', 'user' => $_SESSION['user'], 'complaints' => $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    public function approveComplaintAction() {
        AuthMiddleware::checkManager(); 
        $id = $_GET['id']; $status = $_GET['status']; 
        $db = Database::getInstance()->getConnection();
        $stCheck = $db->prepare("SELECT c.user_id, u.role FROM complaints c JOIN users u ON c.user_id = u.id WHERE c.id = ?"); $stCheck->execute([$id]); $cCheck = $stCheck->fetch(\PDO::FETCH_ASSOC);

        // Logic chặn quyền
        if ($cCheck['user_id'] == $_SESSION['user']['id']) die("<script>alert('Lỗi: Không tự duyệt khiếu nại!'); window.history.back();</script>");
        if (strtolower(trim($cCheck['role'])) === 'manager' && strtolower(trim($_SESSION['user']['role'])) !== 'admin') die("<script>alert('Lỗi: Chỉ Admin duyệt cho Trưởng phòng!'); window.history.back();</script>");

        if ($status === 'Approved') {
            $db->beginTransaction();
            try {
                $st = $db->prepare("SELECT user_id, attendance_log_id, suggested_time, suggested_out_time FROM complaints WHERE id = ?"); $st->execute([$id]); $c = $st->fetch(\PDO::FETCH_ASSOC);
                $db->prepare("UPDATE attendance_logs SET check_in_time = ?, check_out_time = ?, status = 'On-time' WHERE id = ?")->execute([$c['suggested_time'], $c['suggested_out_time'], $c['attendance_log_id']]);
                $db->prepare("UPDATE complaints SET status = ? WHERE id = ?")->execute([$status, $id]);
                $this->sendNotify($c['user_id'], "✅ Khiếu nại được DUYỆT", "Giờ công đã được sửa.");
                $db->commit();
            } catch (\Exception $e) { $db->rollBack(); die("Lỗi: " . $e->getMessage()); }
        } else {
            $db->prepare("UPDATE complaints SET status = ? WHERE id = ?")->execute([$status, $id]);
            $this->sendNotify($cCheck['user_id'], "❌ Khiếu nại BỊ TỪ CHỐI", "Yêu cầu không được chấp nhận.");
        }
        header("Location: /timekeeping-system/public/admin/complaints"); exit;
    }

    // ==========================================
    // 8. PHÂN CA LÀM VIỆC
    // ==========================================
    public function shiftAssignment() {
        AuthMiddleware::checkManager(); 
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT u.id, u.worker_id, u.full_name, u.role, s.shift_name 
                FROM users u 
                LEFT JOIN shifts s ON u.shift_id = s.id 
                WHERE u.role != 'Admin' 
                ORDER BY u.role DESC, u.full_name ASC";
        $users = $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $shifts = $db->query("SELECT * FROM shifts")->fetchAll(\PDO::FETCH_ASSOC);
        
        View::render('admin/shift_assignment', [
            'title' => 'Phân ca làm việc', 'user' => $_SESSION['user'], 
            'users' => $users, 'shifts' => $shifts
        ]);
    }

    public function updateShiftAssignment() {
        AuthMiddleware::checkManager();
        $db = Database::getInstance()->getConnection();
        
        $db->prepare("UPDATE users SET shift_id = ? WHERE id = ?")->execute([$_POST['shift_id'], $_POST['user_id']]);
        
        $st = $db->prepare("SELECT shift_name FROM shifts WHERE id = ?");
        $st->execute([$_POST['shift_id']]);
        $shiftName = $st->fetchColumn();
        
        $this->sendNotify($_POST['user_id'], "🔄 Thay đổi ca làm việc", "Trưởng phòng đã phân bạn vào: " . $shiftName);
        header("Location: /timekeeping-system/public/admin/shift-assignment"); exit;
    }

    // ==========================================
    // 9. CẤU HÌNH CA & SETTINGS
    // ==========================================
    public function manageShifts() {
        AuthMiddleware::checkAdmin();
        $db = Database::getInstance()->getConnection();
        $shifts = $db->query("SELECT * FROM shifts")->fetchAll(\PDO::FETCH_ASSOC);
        View::render('admin/settings', ['title' => 'Cấu hình Ca', 'user' => $_SESSION['user'], 'shifts' => $shifts]);
    }

    public function createShift() {
        AuthMiddleware::checkAdmin();
        $db = Database::getInstance()->getConnection();
        $db->prepare("INSERT INTO shifts (shift_name, start_time, end_time, late_threshold) VALUES (?, ?, ?, ?)")->execute([$_POST['shift_name'], $_POST['start_time'], $_POST['end_time'], $_POST['late_threshold']]);
        header("Location: /timekeeping-system/public/admin/settings"); exit;
    }
}