<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Quản lý nhân viên' ?></title>
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
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-title { color: #0f172a; font-size: 24px; font-weight: 800; margin: 0; }
        .btn-add { background: #0f172a; color: var(--primary); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 10px rgba(15,23,42,0.2); }
        .btn-add:hover { background: #1e293b; transform: translateY(-2px); }

        /* THỐNG KÊ SUMMARY CARDS */
        .summary-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-box { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .stat-box .info { display: flex; flex-direction: column; }
        .stat-box .title { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px;}
        .stat-box .value { font-size: 24px; font-weight: 800; color: #0f172a; }
        .stat-box .icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .icon-blue { background: #e0f2fe; color: #3b82f6; }
        .icon-green { background: #d1fae5; color: #10b981; }
        .icon-red { background: #fee2e2; color: #ef4444; }
        .icon-purple { background: #f3e8ff; color: #a855f7; }

        /* BỘ LỌC FILTER BAR */
        .filter-bar { background: white; padding: 15px 20px; border-radius: 12px; border: 1px solid var(--border); display: flex; gap: 15px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .filter-group { flex: 1; position: relative; }
        .filter-group i { position: absolute; left: 12px; top: 12px; color: #94a3b8; }
        .filter-input { width: 100%; padding: 10px 10px 10px 35px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; background: #f8fafc; transition: 0.3s; box-sizing: border-box; }
        .filter-input:focus { border-color: var(--primary-dark); background: white; box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); }
        .filter-select { width: 100%; padding: 10px 10px 10px 15px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; background: #f8fafc; cursor: pointer; }

        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 25px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; }
        table th { padding: 15px 10px; text-align: left; color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; border-bottom: 2px solid var(--border); letter-spacing: 0.5px;}
        table td { padding: 15px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        .role-label { background: #f1f5f9; padding: 6px 12px; border-radius: 20px; font-size: 12px; color: #475569; font-weight: 600; text-transform: uppercase; }
        .status-active { color: var(--success); font-weight: 700; background: #d1fae5; padding: 4px 10px; border-radius: 8px; font-size: 12px; } 
        .status-locked { color: var(--danger); font-weight: 700; background: #fee2e2; padding: 4px 10px; border-radius: 8px; font-size: 12px; }
        
        .btn-sm { padding: 8px 12px; border-radius: 8px; font-size: 13px; text-decoration: none; display: inline-flex; transition: 0.2s; margin-right: 5px; font-weight: 600; }
        .btn-edit { background: #f1f5f9; color: #0f172a; } .btn-edit:hover { background: var(--primary); }
        .btn-lock { background: #fef3c7; color: #d97706; } .btn-lock:hover { background: #fde68a; }
        .btn-unlock { background: #d1fae5; color: #059669; } .btn-unlock:hover { background: #a7f3d0; }
        .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); padding: 2px; }
    </style>
</head>
<body>

<?php 
    $userRole = strtolower(trim($_SESSION['user']['role'] ?? '')); 
    $avatarImg = !empty($_SESSION['user']['avatar']) ? $_SESSION['user']['avatar'] : 'default-avatar.png';
    $avatarPath = "/timekeeping-system/public/uploads/avatars/" . htmlspecialchars($avatarImg);

    // Tính toán Thống kê
    $totalUsers = count($users ?? []);
    $activeUsers = 0; $lockedUsers = 0; $managers = 0;
    foreach($users ?? [] as $u) {
        if($u['is_active'] == 1) $activeUsers++; else $lockedUsers++;
        if(strtolower($u['role']) === 'manager') $managers++;
    }
?>

<div class="sidebar">
    <div class="sidebar-brand"><span>SME</span> QUẢN LÝ</div>
    <a href="/timekeeping-system/public/admin/dashboard" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="/timekeeping-system/public/admin/reports" class="nav-item"><i class="fa-solid fa-file-invoice"></i> Báo cáo</a>
    <a href="/timekeeping-system/public/admin/approvals" class="nav-item"><i class="fa-solid fa-clipboard-check"></i> Duyệt chấm công</a>
    <a href="/timekeeping-system/public/admin/leaves" class="nav-item"><i class="fa-solid fa-calendar-day"></i> Nghỉ phép</a>
    <a href="/timekeeping-system/public/admin/complaints" class="nav-item"><i class="fa-solid fa-circle-exclamation"></i> Khiếu nại</a>
    <a href="/timekeeping-system/public/admin/shift-assignment" class="nav-item"><i class="fa-solid fa-calendar-week"></i> Phân ca làm việc</a>
    <div style="padding: 20px 25px 5px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Hệ thống</div>
    <a href="/timekeeping-system/public/admin/employees" class="nav-item active"><i class="fa-solid fa-users"></i> Nhân viên</a>
    <a href="/timekeeping-system/public/admin/salary" class="nav-item"><i class="fa-solid fa-money-bill-wave"></i> Bảng lương</a>
    <a href="/timekeeping-system/public/admin/settings" class="nav-item"><i class="fa-solid fa-cog"></i> Cấu hình Ca</a>
</div>

<div class="main">
    <div class="top-bar">
        <div style="display:flex; align-items:center; gap:12px; font-weight: 600; color: #0f172a;">
            <img src="<?= $avatarPath ?>" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=0f172a&color=2effe8';" class="topbar-avatar">
            <div style="display: flex; flex-direction: column; line-height: 1.2;">
                <span style="font-size: 14px;"><?= htmlspecialchars($_SESSION['user']['full_name']) ?></span>
                <span class="role-badge" style="width: fit-content; margin-top: 2px; font-size: 9px; padding: 2px 6px;"><?= htmlspecialchars($_SESSION['user']['role']) ?></span>
            </div>
        </div>
        <a href="/timekeeping-system/public/logout" class="btn-logout"><i class="fa-solid fa-power-off"></i></a>
    </div>

    <div class="content">
        <div class="page-header">
            <h2 class="page-title">Quản lý nhân viên</h2>
            <a href="/timekeeping-system/public/admin/employees/create" class="btn-add"><i class="fa-solid fa-user-plus"></i> Thêm mới</a>
        </div>

        <div class="summary-cards">
            <div class="stat-box"><div class="info"><span class="title">Tổng Nhân Sự</span><span class="value" id="count-total"><?= $totalUsers ?></span></div><div class="icon icon-blue"><i class="fa-solid fa-users"></i></div></div>
            <div class="stat-box"><div class="info"><span class="title">Đang Hoạt Động</span><span class="value" id="count-active"><?= $activeUsers ?></span></div><div class="icon icon-green"><i class="fa-solid fa-user-check"></i></div></div>
            <div class="stat-box"><div class="info"><span class="title">Đã Khóa</span><span class="value" id="count-locked"><?= $lockedUsers ?></span></div><div class="icon icon-red"><i class="fa-solid fa-lock"></i></div></div>
            <div class="stat-box"><div class="info"><span class="title">Cấp Quản Lý</span><span class="value" id="count-manager"><?= $managers ?></span></div><div class="icon icon-purple"><i class="fa-solid fa-user-tie"></i></div></div>
        </div>

        <div class="filter-bar">
            <div class="filter-group" style="flex: 2;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchName" class="filter-input" placeholder="Tìm theo Tên hoặc Mã NV..." onkeyup="filterData()">
            </div>
            <div class="filter-group">
                <select id="filterRole" class="filter-select" onchange="filterData()">
                    <option value="all">Tất cả chức danh</option>
                    <option value="manager">Trưởng phòng (Manager)</option>
                    <option value="employee">Nhân viên (Employee)</option>
                </select>
            </div>
            <div class="filter-group">
                <select id="filterStatus" class="filter-select" onchange="filterData()">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="active">Đang hoạt động</option>
                    <option value="locked">Đã khóa</option>
                </select>
            </div>
        </div>

        <div class="card">
            <table id="employeeTable">
                <thead>
                    <tr><th>Mã NV</th><th>Họ tên</th><th>Chức danh</th><th>Lương / Ngày</th><th>Trạng thái</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    <?php foreach($users ?? [] as $u): ?>
                    <tr class="emp-row" data-role="<?= strtolower(trim($u['role'])) ?>" data-status="<?= $u['is_active'] == 1 ? 'active' : 'locked' ?>">
                        <td class="col-id" style="font-weight: 800; color: #0f172a;"><?= htmlspecialchars($u['worker_id']) ?></td>
                        <td class="col-name" style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><span class="role-label"><?= htmlspecialchars($u['role']) ?></span></td>
                        <td style="font-weight: 600; color: #64748b;"><?= number_format($u['daily_salary'] ?? 0, 0, ',', '.') ?> đ</td>
                        <td>
                            <?php if($u['is_active'] == 1): ?> <span class="status-active">Đang HĐ</span>
                            <?php else: ?> <span class="status-locked"><i class="fa-solid fa-lock"></i> Đã Khóa</span> <?php endif; ?>
                        </td>
                        <td>
                            <?php if(strtolower(trim($u['role'])) !== 'admin'): ?>
                                <a href="/timekeeping-system/public/admin/employees/edit?id=<?= $u['id'] ?>" class="btn-sm btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                                <?php if($u['is_active'] == 1): ?>
                                    <a href="/timekeeping-system/public/admin/employees/toggle?id=<?= $u['id'] ?>" class="btn-sm btn-lock" title="Khóa TK"><i class="fa-solid fa-lock"></i></a>
                                <?php else: ?>
                                    <a href="/timekeeping-system/public/admin/employees/toggle?id=<?= $u['id'] ?>" class="btn-sm btn-unlock" title="Mở khóa"><i class="fa-solid fa-unlock"></i></a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="font-size: 11px; color: #94a3b8; font-weight: 800; background: #f1f5f9; padding: 4px 8px; border-radius: 6px;"><i class="fa-solid fa-shield"></i> ROOT</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="noDataRow" style="display:none;"><td colspan="6" style="text-align:center; padding: 30px; color: #94a3b8;">Không tìm thấy nhân viên nào phù hợp!</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterData() {
    let search = document.getElementById("searchName").value.toLowerCase();
    let role = document.getElementById("filterRole").value;
    let status = document.getElementById("filterStatus").value;
    
    let rows = document.getElementsByClassName("emp-row");
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let rRole = row.getAttribute("data-role");
        let rStatus = row.getAttribute("data-status");
        
        let idText = row.getElementsByClassName("col-id")[0].innerText.toLowerCase();
        let nameText = row.getElementsByClassName("col-name")[0].innerText.toLowerCase();
        
        // Kiểm tra điều kiện
        let matchSearch = idText.includes(search) || nameText.includes(search);
        let matchRole = (role === 'all') || (rRole === role);
        let matchStatus = (status === 'all') || (rStatus === status);

        if (matchSearch && matchRole && matchStatus) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    }

    document.getElementById("noDataRow").style.display = (visibleCount === 0) ? "" : "none";
}
</script>
</body>
</html>