<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Phân ca làm việc' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #2effe8; --primary-dark: #0dd4be; --sidebar-bg: #0f172a; --sidebar-hover: rgba(46, 255, 232, 0.1); --bg: #f8fafc; --card-bg: #ffffff; --text-main: #1e293b; --text-muted: #64748b; --success: #10b981; --danger: #ef4444; --border: #e2e8f0; }
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
        .page-title { color: #0f172a; font-size: 24px; font-weight: 800; margin: 0 0 25px 0; }
        
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 30px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; }
        table th { padding: 15px; text-align: left; color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; border-bottom: 2px solid var(--border); letter-spacing: 0.5px;}
        table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .form-control { padding: 10px 15px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; outline: none; font-size: 13px; background: #f8fafc; transition: 0.3s; }
        .form-control:focus { border-color: var(--primary-dark); background: white; box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); }
        
        .btn-save { background: var(--primary); color: #0f172a; border: none; padding: 10px 15px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 5px; font-size: 13px; box-shadow: 0 4px 10px rgba(46,255,232,0.2); }
        .btn-save:hover { background: #5cffe0; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46,255,232,0.4); }
        
        .btn-edit-shift { background: #0f172a; color: var(--primary); border: none; padding: 10px 15px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; box-shadow: 0 4px 10px rgba(15,23,42,0.2); }
        .btn-edit-shift:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(15,23,42,0.3); }
        
        .btn-cancel { background: #f1f5f9; color: #475569; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; transition: 0.2s; font-size: 13px; font-weight: 600; }
        .btn-cancel:hover { background: #e2e8f0; color: #0f172a; }

        .badge-shift { background: #0f172a; color: var(--primary); padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items:center; gap: 6px; box-shadow: 0 4px 10px rgba(15,23,42,0.1); }
        .badge-none { background: #fee2e2; color: var(--danger); padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items:center; gap: 6px; }
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
        <a href="/timekeeping-system/public/admin/shift-assignment" class="nav-item active"><i class="fa-solid fa-calendar-week"></i> Phân ca làm việc</a>
    <?php endif; ?>

    <?php if($userRole === 'admin'): ?>
        <div style="padding: 20px 25px 5px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Hệ thống</div>
        <a href="/timekeeping-system/public/admin/employees" class="nav-item"><i class="fa-solid fa-users"></i> Nhân viên</a>
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
        <h2 class="page-title">Sắp xếp ca làm việc</h2>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Chức danh</th>
                        <th>Ca hiện tại</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users ?? [] as $u): ?>
                    <tr>
                        <td>
                            <strong style="color: #0f172a; font-size: 15px;"><?= htmlspecialchars($u['full_name']) ?></strong><br>
                            <span style="font-size:12px; color:var(--text-muted); font-weight: 600;"><?= htmlspecialchars($u['worker_id']) ?></span>
                        </td>
                        <td><span style="background: #f1f5f9; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase;"><?= htmlspecialchars($u['role']) ?></span></td>
                        
                        <td>
                            <?php if($u['shift_name']): ?>
                                <span class="badge-shift"><i class="fa-regular fa-clock" style="color: var(--primary);"></i> <?= htmlspecialchars($u['shift_name']) ?></span>
                            <?php else: ?>
                                <span class="badge-none"><i class="fa-solid fa-triangle-exclamation"></i> Chưa phân ca</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div id="view-<?= $u['id'] ?>">
                                <button type="button" class="btn-edit-shift" onclick="toggleEdit(<?= $u['id'] ?>)">
                                    <i class="fa-solid fa-pen-clip"></i> Sửa Ca
                                </button>
                            </div>

                            <form id="form-<?= $u['id'] ?>" action="/timekeeping-system/public/admin/shift-assignment/action" method="POST" style="display:none; gap:8px; margin:0; align-items:center;">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                
                                <select name="shift_id" class="form-control" required style="width: auto;">
                                    <option value="" disabled <?= empty($u['shift_name']) ? 'selected' : '' ?>>-- Lựa chọn ca --</option>
                                    <?php foreach($shifts as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= ($u['shift_name'] === $s['shift_name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['shift_name']) ?> (<?= substr($s['start_time'],0,5) ?> - <?= substr($s['end_time'],0,5) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <button type="submit" class="btn-save"><i class="fa-solid fa-check"></i> Lưu</button>
                                <button type="button" class="btn-cancel" onclick="toggleEdit(<?= $u['id'] ?>)"><i class="fa-solid fa-xmark"></i> Hủy</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleEdit(id) {
    const viewDiv = document.getElementById('view-' + id);
    const formDiv = document.getElementById('form-' + id);
    if (formDiv.style.display === 'none') {
        formDiv.style.display = 'flex';
        viewDiv.style.display = 'none';
    } else {
        formDiv.style.display = 'none';
        viewDiv.style.display = 'block';
    }
}
</script>
</body>
</html>