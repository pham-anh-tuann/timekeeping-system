<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Thêm Nhân viên' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2effe8; --primary-dark: #0dd4be; --sidebar-bg: #0f172a; --sidebar-hover: rgba(46, 255, 232, 0.1); --bg: #f8fafc; --card-bg: #ffffff; --text-main: #1e293b; --text-muted: #64748b; --success: #10b981; --danger: #ef4444; --warning: #f59e0b; --border: #e2e8f0; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); margin: 0; display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        .sidebar { width: 260px; background: var(--sidebar-bg); color: #cbd5e1; display: flex; flex-direction: column; overflow-y: auto; flex-shrink: 0; box-shadow: 4px 0 15px rgba(0,0,0,0.05); z-index: 20; }
        .sidebar-brand { padding: 25px; font-size: 22px; font-weight: 800; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); text-transform: uppercase; letter-spacing: 1px; color: white; }
        .sidebar-brand span { color: var(--primary); }
        .nav-item { padding: 15px 25px; display: flex; align-items: center; gap: 15px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-size: 14px; font-weight: 500; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { background: var(--sidebar-hover); color: var(--primary); border-left-color: var(--primary); }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .main { flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .top-bar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 15px 30px; display: flex; justify-content: flex-end; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.03); z-index: 10; position: sticky; top: 0; border-bottom: 1px solid var(--border); }
        .role-badge { background: var(--primary); padding: 4px 10px; border-radius: 15px; font-size: 11px; color: #0f172a; font-weight: 800; text-transform: uppercase; }
        .btn-logout { color: var(--danger); margin-left: 20px; font-weight: 700; text-decoration: none; padding: 8px 15px; background: #fee2e2; border-radius: 8px; transition: 0.2s; }
        .btn-logout:hover { background: #fca5a5; }
        
        .content { padding: 30px; max-width: 1200px; margin: 0 auto; width: 100%; }
        .page-title { color: #0f172a; font-size: 24px; font-weight: 800; margin: 0 0 20px 0; }
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 35px; border: 1px solid var(--border); max-width: 600px; }
        
        .form-group { margin-bottom: 22px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; font-size: 13px; color: var(--text-muted); }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid var(--border); border-radius: 10px; font-family: 'Inter', sans-serif; outline: none; background: #f8fafc; transition: 0.3s; box-sizing: border-box; font-size: 14px; }
        .form-control:focus { border-color: var(--primary-dark); background: white; box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); }
        
        .btn-submit { background: #0f172a; color: var(--primary); border: none; padding: 14px 25px; border-radius: 10px; font-weight: 800; cursor: pointer; transition: 0.3s; width: 100%; font-size: 15px; box-shadow: 0 4px 15px rgba(15,23,42,0.2); margin-top: 10px;}
        .btn-submit:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,23,42,0.3); }
        
        .btn-back { background: #f1f5f9; color: #475569; text-decoration: none; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 25px; font-weight: 700; font-size: 13px; transition: 0.2s;}
        .btn-back:hover { background: #e2e8f0; color: #0f172a; }
        .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); padding: 2px; }
    </style>
</head>
<body>

<?php 
    $userRole = strtolower(trim($_SESSION['user']['role'] ?? '')); 
    $db = \App\Config\Database::getInstance()->getConnection();
    $pendingLeaves = $db->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending'")->fetchColumn();
    $pendingComplaints = $db->query("SELECT COUNT(*) FROM complaints WHERE status = 'Pending'")->fetchColumn();
    $totalNotif = $pendingLeaves + $pendingComplaints;
    $avatarImg = !empty($_SESSION['user']['avatar']) ? $_SESSION['user']['avatar'] : 'default-avatar.png';
    $avatarPath = "/timekeeping-system/public/uploads/avatars/" . htmlspecialchars($avatarImg);
?>

<div class="sidebar">
    <div class="sidebar-brand"><span>SME</span> QUẢN LÝ</div>
    <a href="/timekeeping-system/public/admin/dashboard" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="/timekeeping-system/public/admin/reports" class="nav-item"><i class="fa-solid fa-file-invoice"></i> Báo cáo</a>
    <a href="/timekeeping-system/public/admin/approvals" class="nav-item"><i class="fa-solid fa-clipboard-check"></i> Duyệt chấm công</a>
    <a href="/timekeeping-system/public/admin/leaves" class="nav-item"><i class="fa-solid fa-calendar-day"></i> Nghỉ phép</a>
    <a href="/timekeeping-system/public/admin/complaints" class="nav-item"><i class="fa-solid fa-circle-exclamation"></i> Khiếu nại</a>
    
    <?php if($userRole === 'manager' || $userRole === 'admin'): ?>
        <a href="/timekeeping-system/public/admin/shift-assignment" class="nav-item"><i class="fa-solid fa-calendar-week"></i> Phân ca làm việc</a>
    <?php endif; ?>

    <?php if($userRole === 'admin'): ?>
        <div style="padding: 20px 25px 5px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Hệ thống</div>
        <a href="/timekeeping-system/public/admin/employees" class="nav-item active"><i class="fa-solid fa-users"></i> Nhân viên</a>
        <a href="/timekeeping-system/public/admin/salary" class="nav-item"><i class="fa-solid fa-money-bill-wave"></i> Bảng lương</a>
        <a href="/timekeeping-system/public/admin/settings" class="nav-item"><i class="fa-solid fa-cog"></i> Cấu hình Ca</a>
    <?php endif; ?>
</div>

<div class="main">
    <div class="top-bar">
        <div style="position: relative; margin-right: 25px; cursor: pointer;" onclick="document.getElementById('notif-box').style.display = (document.getElementById('notif-box').style.display === 'none') ? 'block' : 'none'">
            <i class="fa-solid fa-bell" style="font-size: 22px; color: #0f172a;"></i>
            <?php if($totalNotif > 0): ?><span style="position: absolute; top: -5px; right: -8px; background: var(--danger); color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight:bold;"><?= $totalNotif ?></span><?php endif; ?>
            <div id="notif-box" style="display:none; position: absolute; right: 0; top: 35px; width: 300px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; z-index: 9999; overflow: hidden; border: 1px solid var(--border);">
                <div style="padding: 15px; background: #0f172a; color: white; font-weight: 700; font-size: 14px;">THÔNG BÁO CHỜ XỬ LÝ</div>
                <?php if($totalNotif == 0): ?>
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">Không có yêu cầu nào tồn đọng.</div>
                <?php else: ?>
                    <?php if($pendingLeaves > 0): ?><a href="/timekeeping-system/public/admin/leaves" style="display:flex; align-items: center; gap: 15px; padding: 15px; text-decoration:none; color: var(--text-main); border-bottom: 1px solid var(--border);"><div style="background: #fef3c7; color: #d97706; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;"><i class="fa-solid fa-calendar-day"></i></div><div style="font-size: 13px; font-weight: 600;">Có <?= $pendingLeaves ?> đơn nghỉ chờ duyệt</div></a><?php endif; ?>
                    <?php if($pendingComplaints > 0): ?><a href="/timekeeping-system/public/admin/complaints" style="display:flex; align-items: center; gap: 15px; padding: 15px; text-decoration:none; color: var(--text-main);"><div style="background: #fee2e2; color: #dc2626; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;"><i class="fa-solid fa-circle-exclamation"></i></div><div style="font-size: 13px; font-weight: 600;">Có <?= $pendingComplaints ?> khiếu nại chờ xử lý</div></a><?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:12px; font-weight: 600; color: #0f172a;">
            <img src="<?= $avatarPath ?>" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=0f172a&color=2effe8';" class="topbar-avatar" alt="Avatar">
            <div style="display: flex; flex-direction: column; line-height: 1.2;">
                <span style="font-size: 14px;"><?= htmlspecialchars($_SESSION['user']['full_name']) ?></span>
                <span class="role-badge" style="width: fit-content; margin-top: 2px; font-size: 9px; padding: 2px 6px;"><?= htmlspecialchars($_SESSION['user']['role']) ?></span>
            </div>
        </div>
        <a href="/timekeeping-system/public/logout" class="btn-logout"><i class="fa-solid fa-power-off"></i></a>
    </div>

    <div class="content">
        <a href="/timekeeping-system/public/admin/employees" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
        <div class="card">
            <h2 class="page-title" style="margin-bottom: 25px;"><i class="fa-solid fa-user-plus" style="color: var(--primary-dark);"></i> Thêm Nhân Viên Mới</h2>
            <form action="/timekeeping-system/public/admin/employees/create" method="POST">
                <div class="form-group">
                    <label>Mã Nhân Viên (Worker ID)</label>
                    <input type="text" name="worker_id" class="form-control" required placeholder="Ví dụ: NV005">
                </div>
                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Nhập họ và tên đầy đủ">
                </div>
                <div class="form-group">
                    <label>Mật khẩu đăng nhập</label>
                    <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu mặc định (Sẽ đổi sau)">
                </div>
                <div class="form-group">
                    <label>Vai trò / Chức vụ</label>
                    <select name="role" class="form-control" required>
                        <option value="Employee">Nhân viên (Employee)</option>
                        <option value="Manager">Trưởng phòng (Manager)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mức lương / Ngày (VNĐ)</label>
                    <input type="number" name="daily_salary" class="form-control" required value="0" placeholder="VD: 300000">
                </div>
                <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Tạo Tài Khoản</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>