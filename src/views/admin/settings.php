<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Cấu hình Ca' ?></title>
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
        
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 30px; margin-bottom: 25px; border: 1px solid var(--border); }
        
        /* BỘ LỌC FILTER BAR */
        .filter-bar { background: white; padding: 15px 20px; border-radius: 12px; border: 1px solid var(--border); display: flex; gap: 15px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); align-items: center; }
        .filter-group { flex: 1; position: relative; }
        .filter-group i { position: absolute; left: 12px; top: 12px; color: #94a3b8; }
        .filter-input { width: 100%; padding: 10px 10px 10px 35px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; background: #f8fafc; transition: 0.3s; box-sizing: border-box; }
        .filter-input:focus { border-color: var(--primary-dark); background: white; box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); }

        table { width: 100%; border-collapse: collapse; }
        table th { padding: 15px; text-align: left; color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; border-bottom: 2px solid var(--border); letter-spacing: 0.5px;}
        table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; outline: none; background: #f8fafc; transition: 0.3s; box-sizing: border-box; }
        .form-control:focus { border-color: var(--primary-dark); background: white; box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); }
        
        .btn-blue { background: #0f172a; color: var(--primary); border: none; padding: 12px 25px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 10px rgba(15,23,42,0.2); height: 43px;}
        .btn-blue:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(15,23,42,0.3); }
        
        .time-badge { background: #f1f5f9; padding: 6px 12px; border-radius: 8px; color: #0f172a; font-size: 13px; font-weight: 700; display: inline-flex; align-items:center; gap: 6px;}
        .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); padding: 2px; }
    </style>
</head>
<body>

<?php 
    $userRole = strtolower(trim($_SESSION['user']['role'] ?? '')); 
    $shiftCount = count($shifts ?? []);
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
    <a href="/timekeeping-system/public/admin/shift-assignment" class="nav-item"><i class="fa-solid fa-calendar-week"></i> Phân ca làm việc</a>
    <div style="padding: 20px 25px 5px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Hệ thống</div>
    <a href="/timekeeping-system/public/admin/employees" class="nav-item"><i class="fa-solid fa-users"></i> Nhân viên</a>
    <a href="/timekeeping-system/public/admin/salary" class="nav-item"><i class="fa-solid fa-money-bill-wave"></i> Bảng lương</a>
    <a href="/timekeeping-system/public/admin/settings" class="nav-item active"><i class="fa-solid fa-cog"></i> Cấu hình Ca</a>
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
            <h2 class="page-title">Quản lý Đa Ca Làm Việc</h2>
        </div>
        
        <div class="card" style="border-top: 4px solid #0f172a;">
            <h3 style="color: #0f172a; margin-top:0; font-size: 18px; font-weight: 800;"><i class="fa-solid fa-folder-plus" style="color: var(--primary-dark);"></i> Thêm Ca Mới</h3>
            <form action="/timekeeping-system/public/admin/shifts/create" method="POST" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight:700; font-size:12px; color:var(--text-muted); margin-bottom: 8px; display:block;">Tên Ca:</label>
                    <input type="text" name="shift_name" placeholder="VD: Ca Sáng" required class="form-control">
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label style="font-weight:700; font-size:12px; color:var(--text-muted); margin-bottom: 8px; display:block;">Giờ Bắt Đầu:</label>
                    <input type="time" name="start_time" required class="form-control">
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label style="font-weight:700; font-size:12px; color:var(--text-muted); margin-bottom: 8px; display:block;">Giờ Kết Thúc:</label>
                    <input type="time" name="end_time" required class="form-control">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-weight:800; font-size:12px; color:var(--danger); margin-bottom: 8px; display:block;">Trễ nhất (Tính muộn):</label>
                    <input type="time" name="late_threshold" required class="form-control">
                </div>
                <div>
                    <button type="submit" class="btn-blue"><i class="fa-solid fa-floppy-disk"></i> Lưu Ca</button>
                </div>
            </form>
        </div>

        <div class="filter-bar">
            <div class="filter-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="shSearch" class="filter-input" placeholder="Tìm kiếm tên ca làm việc..." onkeyup="filterShifts()">
            </div>
            <div style="font-size: 13px; font-weight: 600; color: var(--text-muted);">
                Tổng cộng: <strong style="color: #0f172a; font-size: 16px;"><?= $shiftCount ?></strong> ca hoạt động
            </div>
        </div>

        <div class="card">
            <h3 style="color: #0f172a; margin-top:0; font-size: 18px; font-weight: 800;"><i class="fa-solid fa-list-check" style="color: var(--primary-dark);"></i> Danh sách thiết lập</h3>
            <table id="shiftTable">
                <thead>
                    <tr><th>ID Ca</th><th>Tên Ca Làm Việc</th><th>Giờ Bắt Đầu</th><th>Giờ Kết Thúc</th><th>Mốc Tính Đi Muộn</th></tr>
                </thead>
                <tbody>
                    <?php foreach($shifts ?? [] as $s): ?>
                    <tr class="shift-row">
                        <td style="font-weight:800; color: #94a3b8;">#<?= $s['id'] ?></td>
                        <td class="col-name"><strong style="color: #0f172a; font-size: 15px;"><?= htmlspecialchars($s['shift_name']) ?></strong></td>
                        <td><span class="time-badge"><i class="fa-regular fa-clock" style="color: #64748b;"></i> <?= substr($s['start_time'], 0, 5) ?></span></td>
                        <td><span class="time-badge"><i class="fa-solid fa-door-open" style="color: #64748b;"></i> <?= substr($s['end_time'], 0, 5) ?></span></td>
                        <td><span style="color: var(--danger); font-weight: 700; font-size: 13px; background: #fee2e2; padding: 4px 10px; border-radius: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> <?= substr($s['late_threshold'], 0, 5) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="noShiftRow" style="display:none;"><td colspan="5" style="text-align:center; padding: 30px; color: #94a3b8;">Không tìm thấy ca làm việc!</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterShifts() {
    let search = document.getElementById("shSearch").value.toLowerCase();
    let rows = document.getElementsByClassName("shift-row");
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let nameText = row.getElementsByClassName("col-name")[0].innerText.toLowerCase();
        
        if (nameText.includes(search)) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    }
    document.getElementById("noShiftRow").style.display = (visibleCount === 0) ? "" : "none";
}
</script>
</body>
</html>