<?php

ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}


ini_set('display_errors', 1); 
error_reporting(E_ALL);


define('ROOT_DIR', dirname(__DIR__));
require_once ROOT_DIR . '/vendor/autoload.php';

use App\Core\Router;
use App\Controllers\{AuthController, AdminController, AttendanceController};

$router = new Router();


$router->get('/', function() { 
    header("Location: /timekeeping-system/public/login"); 
    exit; 
});


$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/logout', [AuthController::class, 'logout']);






$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);


$router->get('/admin/reports', [AdminController::class, 'reports']);
$router->get('/admin/reports/export', [AdminController::class, 'exportReports']); 


$router->get('/admin/approvals', [AdminController::class, 'approvals']);
$router->get('/admin/approvals/action', [AdminController::class, 'approveAttendanceAction']);


$router->get('/admin/leaves', [AdminController::class, 'leaves']);
$router->get('/admin/leaves/approve', [AdminController::class, 'approveLeaveAction']);


$router->get('/admin/complaints', [AdminController::class, 'complaints']);
$router->get('/admin/complaints/approve', [AdminController::class, 'approveComplaintAction']);


$router->get('/admin/shift-assignment', [AdminController::class, 'shiftAssignment']);
$router->post('/admin/shift-assignment/action', [AdminController::class, 'updateShiftAssignment']);






$router->get('/admin/employees', [AdminController::class, 'manageEmployees']);
$router->get('/admin/employees/create', [AdminController::class, 'showCreateEmployeeForm']);
$router->post('/admin/employees/create', [AdminController::class, 'storeEmployee']);
$router->get('/admin/employees/edit', [AdminController::class, 'showEditEmployeeForm']);
$router->post('/admin/employees/edit', [AdminController::class, 'updateEmployee']);
$router->get('/admin/employees/toggle', [AdminController::class, 'toggleEmployeeStatus']);
$router->get('/admin/employees/delete', [AdminController::class, 'deleteEmployee']);


$router->get('/admin/salary', [AdminController::class, 'salary']);
$router->get('/admin/settings', [AdminController::class, 'manageShifts']);
$router->post('/admin/shifts/create', [AdminController::class, 'createShift']); 

// 👉 ĐÂY LÀ 3 DÒNG TÔI BỔ SUNG ĐỂ SỬA LỖI 404 (SỬA / CẬP NHẬT / XÓA CA LÀM VIỆC)
$router->get('/admin/shifts/edit', [AdminController::class, 'showEditShiftForm']);
$router->post('/admin/shifts/update', [AdminController::class, 'updateShift']);
$router->get('/admin/shifts/delete', [AdminController::class, 'deleteShift']);


$router->get('/profile', [AttendanceController::class, 'showProfile']); 
$router->post('/attendance/punch', [AttendanceController::class, 'processPunch']);
$router->post('/user/change-password', [AttendanceController::class, 'changePassword']);
$router->post('/user/complaint/send', [AttendanceController::class, 'submitComplaint']);
$router->post('/user/leave/send', [AttendanceController::class, 'submitLeave']);
$router->post('/user/update-info', [AttendanceController::class, 'updateInfo']); 


$router->get('/attendance', function() { header("Location: /timekeeping-system/public/profile"); exit; });
$router->get('/leave', function() { header("Location: /timekeeping-system/public/profile"); exit; });

$router->resolve();