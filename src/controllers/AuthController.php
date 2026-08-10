<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\UserModel;

class AuthController {
    
    // Hiển thị form đăng nhập
    public function showLogin() {
        if (isset($_SESSION['user'])) {
            $this->redirectBasedOnRole();
        }
        View::render('auth/login', ['title' => 'Đăng nhập hệ thống', 'error' => '']);
    }

    // Xử lý khi user bấm nút Đăng nhập
    public function processLogin() {
        // Tự động lấy dữ liệu dù form HTML đặt tên là 'worker_id' hay 'username'
        $workerId = $_POST['worker_id'] ?? $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Báo lỗi ngay nếu form gửi lên bị trống
        if (empty($workerId) || empty($password)) {
            View::render('auth/login', [
                'title' => 'Đăng nhập', 
                'error' => 'Vui lòng nhập đầy đủ tài khoản và mật khẩu!'
            ]);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->authenticate($workerId, $password);

        if ($user) {
            // Chặn đứng những tài khoản đã bị khóa (is_active = 0)
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                View::render('auth/login', [
                    'title' => 'Đăng nhập', 
                    'error' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin!'
                ]);
                return;
            }
            
            // ĐÃ FIX: Thiết lập Session cực chuẩn để hệ thống Load Avatar và Tên chính xác
            $_SESSION['user'] = [
                'id' => $user['id'],
                'worker_id' => $user['worker_id'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'avatar' => !empty($user['avatar']) ? $user['avatar'] : 'default-avatar.png',
                'shift_id' => $user['shift_id'] ?? 1
            ];
            
            // Chuyển hướng
            $this->redirectBasedOnRole();
        } else {
            // Sai mật khẩu hoặc mã nhân viên
            View::render('auth/login', [
                'title' => 'Đăng nhập', 
                'error' => 'Sai tài khoản hoặc mật khẩu!'
            ]);
        }
    }

    // Đăng xuất
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: /timekeeping-system/public/login");
        exit;
    }

    // LOGIC CHUYỂN HƯỚNG THÔNG MINH
    private function redirectBasedOnRole() {
        $role = strtolower(trim($_SESSION['user']['role'] ?? ''));
        
        if ($role === 'admin') {
            // Admin vào thẳng Dashboard quản trị
            header("Location: /timekeeping-system/public/admin/dashboard");
        } else {
            // Manager và Employee đều vào Kiosk (Profile) để quẹt thẻ trước
            header("Location: /timekeeping-system/public/profile");
        }
        exit;
    }
}