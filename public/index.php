<?php
// Thiết lập thời gian sống của Session (24 giờ)
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Hiển thị lỗi để dễ dàng kiểm tra trong quá trình phát triển
ini_set('display_errors', 1); 
error_reporting(E_ALL);

// Định nghĩa thư mục gốc và nạp Autoload
define('ROOT_DIR', dirname(__DIR__));
require_once ROOT_DIR . '/vendor/autoload.php';

use App\Core\Router;
use App\Controllers\{AuthController, AdminController, AttendanceController};

$router = new Router();

// --- TRANG CHỦ (Mặc định đẩy về Login) ---
$router->get('/', function() { 
    header("Location: /timekeeping-system/public/login"); 
    exit; 
});

// --- HỆ THỐNG XÁC THỰC (AUTH) ---
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/logout', [AuthController::class, 'logout']);

// ==========================================
// CÁC ĐƯỜNG DẪN DÀNH CHO ADMIN VÀ TRƯỞNG PHÒNG
// (Đã map đúng 100% tên hàm với AdminController)
// ==========================================

$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);

// 1. Quản lý Báo cáo
$router->get('/admin/reports', [AdminController::class, 'reports']);
$router->get('/admin/reports/export', [AdminController::class, 'exportReports']); 

// 2. Duyệt chấm công hằng ngày
$router->get('/admin/approvals', [AdminController::class, 'approvals']);
$router->get('/admin/approvals/action', [AdminController::class, 'approveAttendanceAction']);

// 3. Duyệt nghỉ phép
$router->get('/admin/leaves', [AdminController::class, 'leaves']);
$router->get('/admin/leaves/approve', [AdminController::class, 'approveLeaveAction']);

// 4. Duyệt khiếu nại (Tự động sửa giờ)
$router->get('/admin/complaints', [AdminController::class, 'complaints']);
$router->get('/admin/complaints/approve', [AdminController::class, 'approveComplaintAction']);

// 5. Phân ca làm việc
$router->get('/admin/shift-assignment', [AdminController::class, 'shiftAssignment']);
$router->post('/admin/shift-assignment/action', [AdminController::class, 'updateShiftAssignment']);

// ==========================================
// CÁC ĐƯỜNG DẪN CHỈ DÀNH RIÊNG CHO QUẢN TRỊ VIÊN (ADMIN)
// ==========================================

// 6. Quản lý Nhân viên
$router->get('/admin/employees', [AdminController::class, 'manageEmployees']);
$router->get('/admin/employees/create', [AdminController::class, 'showCreateEmployeeForm']);
$router->post('/admin/employees/create', [AdminController::class, 'storeEmployee']);
$router->get('/admin/employees/edit', [AdminController::class, 'showEditEmployeeForm']);
$router->post('/admin/employees/edit', [AdminController::class, 'updateEmployee']);
$router->get('/admin/employees/toggle', [AdminController::class, 'toggleEmployeeStatus']);
$router->get('/admin/employees/delete', [AdminController::class, 'deleteEmployee']);

// 7. Bảng lương và Cấu hình Ca
$router->get('/admin/salary', [AdminController::class, 'salary']);
$router->get('/admin/settings', [AdminController::class, 'manageShifts']);
$router->post('/admin/shifts/create', [AdminController::class, 'createShift']); 

// ==========================================
// CỔNG THÔNG TIN NHÂN VIÊN (KIOSK/PROFILE)
// ==========================================
$router->get('/profile', [AttendanceController::class, 'showProfile']); 
$router->post('/attendance/punch', [AttendanceController::class, 'processPunch']);
$router->post('/user/change-password', [AttendanceController::class, 'changePassword']);
$router->post('/user/complaint/send', [AttendanceController::class, 'submitComplaint']);
$router->post('/user/leave/send', [AttendanceController::class, 'submitLeave']);
$router->post('/user/update-info', [AttendanceController::class, 'updateInfo']); 

// Alias cho ai gõ tắt
$router->get('/attendance', function() { header("Location: /timekeeping-system/public/profile"); exit; });
$router->get('/leave', function() { header("Location: /timekeeping-system/public/profile"); exit; });

$router->resolve();