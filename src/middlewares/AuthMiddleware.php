<?php
namespace App\Middlewares;

class AuthMiddleware {
    
    // Kiểm tra xem đã đăng nhập chưa
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header("Location: /timekeeping-system/public/login");
            exit;
        }
    }

    // Kiểm tra quyền Admin
    public static function checkAdmin() {
        self::check();
        $role = strtolower(trim($_SESSION['user']['role'] ?? ''));
        
        if ($role !== 'admin') {
            header("Location: /timekeeping-system/public/profile");
            exit;
        }
    }

    // Kiểm tra quyền Trưởng phòng (Admin cũng được phép qua cửa này)
    public static function checkManager() {
        self::check();
        $role = strtolower(trim($_SESSION['user']['role'] ?? ''));
        
        // CHÚ Ý: Chấp nhận cả admin VÀ manager
        if ($role !== 'admin' && $role !== 'manager') {
            header("Location: /timekeeping-system/public/profile");
            exit;
        }
    }
}