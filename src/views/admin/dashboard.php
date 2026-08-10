<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { 
            --primary: #2effe8; --primary-dark: #0dd4be; 
            --sidebar-bg: #0f172a; --sidebar-hover: rgba(46, 255, 232, 0.1);
            --bg: #f8fafc; --card-bg: #ffffff; 
            --text-main: #1e293b; --text-muted: #64748b; 
            --success: #10b981; --danger: #ef4444; --warning: #f59e0b; 
            --border: #e2e8f0; 
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); margin: 0; display: flex; height: 100vh; overflow: hidden; color: var(--text-main); }
        
        .sidebar { width: 260px; background: var(--sidebar-bg); color: #cbd5e1; display: flex; flex-direction: column; overflow-y: auto; flex-shrink: 0; box-shadow: 4px 0 15px rgba(0,0,0,0.05); z-index: 20; }
        .sidebar-brand { padding: 25px; font-size: 22px; font-weight: 800; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); text-transform: uppercase; letter-spacing: 1px; color: white; }
        .sidebar-brand span { color: var(--primary); }
        .nav-item { padding: 15px 25px; display: flex; align-items: center; gap: 15px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-size: 14px; font-weight: 500; border-left: 3px solid transparent; }
        .nav-item:hover { background: var(--sidebar-hover); color: var(--primary); }
        .nav-item.active { background: var(--sidebar-hover); color: var(--primary); border-left: 3px solid var(--primary); font-weight: 700; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        
        .main { flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .top-bar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 15px 30px; display: flex; justify-content: flex-end; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.03); z-index: 10; position: sticky; top: 0; border-bottom: 1px solid var(--border); }
        
        .role-badge { background: var(--primary); padding: 4px 10px; border-radius: 15px; font-size: 11px; color: #0f172a; font-weight: 800; text-transform: uppercase; }
        .btn-logout { color: var(--danger); margin-left: 20px; font-weight: 700; text-decoration: none; padding: 8px 15px; background: #fee2e2; border-radius: 8px; transition: 0.2s; }
        .btn-logout:hover { background: #fca5a5; color: #991b1b; }
        
        .content { padding: 30px; max-width: 1200px; margin: 0 auto; width: 100%; }
        .page-title { color: #0f172a; font-size: 24px; font-weight: 800; margin: 0 0 25px 0; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 30px; }
        .stat-card { background: var(--card-bg); border-radius: 16px; padding: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid var(--border); transition: 0.3s; position: relative; overflow: hidden;}
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        .stat-card::after { content: ''; position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: var(--primary); }
        .stat-card.success::after { background: var(--success); }
        
        .stat-title { font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;}
        .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; }
        .stat-icon { font-size: 35px; color: #e2e8f0; }
        
        .chart-card { background: var(--card-bg); border-radius: 16px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid var(--border); }
        .chart-header { margin-top: 0; margin-bottom: 25px; color: #0f172a; font-size: 18px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .chart-header i { color: var(--primary-dark); }
        
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
    <a href="/timekeeping-system/public/admin/dashboard" class="nav-item active"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="/timekeeping-system/public/admin/reports" class="nav-item"><i class="fa-solid fa-file-invoice"></i> Báo cáo</a>
    <a href="/timekeeping-system/public/admin/approvals" class="nav-item"><i class="fa-solid fa-clipboard-check"></i> Duyệt chấm công</a>
    <a href="/timekeeping-system/public/admin/leaves" class="nav-item"><i class="fa-solid fa-calendar-day"></i> Nghỉ phép</a>
    <a href="/timekeeping-system/public/admin/complaints" class="nav-item"><i class="fa-solid fa-circle-exclamation"></i> Khiếu nại</a>
    
    <?php if($userRole === 'manager' || $userRole === 'admin'): ?>
        <a href="/timekeeping-system/public/admin/shift-assignment" class="nav-item"><i class="fa-solid fa-calendar-week"></i> Phân ca làm việc</a>
    <?php endif; ?>

    <?php if($userRole === 'admin'): ?>
        <div style="padding: 20px 25px 5px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Hệ thống</div>
        <a href="/timekeeping-system/public/admin/employees" class="nav-item"><i class="fa-solid fa-users"></i> Nhân viên</a>
        <a href="/timekeeping-system/public/admin/salary" class="nav-item"><i class="fa-solid fa-money-bill-wave"></i> Bảng lương</a>
        <a href="/timekeeping-system/public/admin/settings" class="nav-item"><i class="fa-solid fa-cog"></i> Cấu hình Ca</a>
    <?php endif; ?>
    <?php if($userRole === 'manager'): ?>
        <a href="/timekeeping-system/public/profile" class="nav-item" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); color: var(--primary); font-weight: bold;"><i class="fa-solid fa-fingerprint"></i> Kiosk (Chấm công)</a>
    <?php endif; ?>
</div>

<div class="main">
    <div class="top-bar">
        <div style="position: relative; margin-right: 25px; cursor: pointer;" onclick="document.getElementById('notif-box').style.display = (document.getElementById('notif-box').style.display === 'none') ? 'block' : 'none'">
            <i class="fa-solid fa-bell" style="font-size: 22px; color: #0f172a;"></i>
            <?php if($totalNotif > 0): ?><span style="position: absolute; top: -5px; right: -8px; background: var(--danger); color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight:bold; box-shadow: 0 2px 5px rgba(239,68,68,0.4);"><?= $totalNotif ?></span><?php endif; ?>
            <div id="notif-box" style="display:none; position: absolute; right: 0; top: 35px; width: 300px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; z-index: 9999; overflow: hidden; border: 1px solid var(--border);">
                <div style="padding: 15px; background: #0f172a; color: white; font-weight: 700; font-size: 14px; letter-spacing: 0.5px;">THÔNG BÁO CHỜ XỬ LÝ</div>
                <?php if($totalNotif == 0): ?>
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">Tuyệt vời! Không có yêu cầu nào tồn đọng.</div>
                <?php else: ?>
                    <?php if($pendingLeaves > 0): ?><a href="/timekeeping-system/public/admin/leaves" style="display:flex; align-items: center; gap: 15px; padding: 15px; text-decoration:none; color: var(--text-main); border-bottom: 1px solid var(--border); transition: 0.2s;"><div style="background: #fef3c7; color: #d97706; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;"><i class="fa-solid fa-calendar-day"></i></div><div style="font-size: 13px; font-weight: 600;">Có <?= $pendingLeaves ?> đơn nghỉ phép chờ duyệt</div></a><?php endif; ?>
                    <?php if($pendingComplaints > 0): ?><a href="/timekeeping-system/public/admin/complaints" style="display:flex; align-items: center; gap: 15px; padding: 15px; text-decoration:none; color: var(--text-main); transition: 0.2s;"><div style="background: #fee2e2; color: #dc2626; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px;"><i class="fa-solid fa-circle-exclamation"></i></div><div style="font-size: 13px; font-weight: 600;">Có <?= $pendingComplaints ?> khiếu nại chờ xử lý</div></a><?php endif; ?>
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
        <h2 class="page-title">Tổng quan hệ thống</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div><div class="stat-title">Nhân sự Active</div><div class="stat-value"><?= $stats['total_emp'] ?></div></div>
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="stat-card success">
                <div><div class="stat-title">Đi làm hôm nay</div><div class="stat-value"><?= $stats['today_present'] ?> <span style="font-size: 16px; color: var(--text-muted);">/ <?= $stats['total_emp'] ?></span></div></div>
                <div class="stat-icon"><i class="fa-solid fa-user-check"></i></div>
            </div>
        </div>
        
        <div class="chart-card">
            <h3 class="chart-header"><i class="fa-solid fa-chart-column"></i> Thống kê nhân sự đi làm (7 ngày qua)</h3>
            <div style="position: relative; height: 320px; width: 100%;"><canvas id="attendanceChart"></canvas></div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const chartData = <?= json_encode($chartData ?? []) ?>;
    if (chartData.length === 0) { chartData.push({ work_date: 'Hôm nay', present_count: <?= $stats['today_present'] ?? 0 ?> }); }
    
    // Đổi màu Chart sang Neon Cyan cho hợp tông
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(46, 255, 232, 0.8)');
    gradient.addColorStop(1, 'rgba(46, 255, 232, 0.1)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.work_date),
            datasets: [{
                label: 'Số lượng đi làm', 
                data: chartData.map(d => parseInt(d.present_count, 10)),
                backgroundColor: gradient, 
                borderColor: '#0dd4be', 
                borderWidth: 2, 
                borderRadius: 6, 
                barPercentage: 0.4
            }]
        },
        options: { 
            responsive: true, maintainAspectRatio: false, 
            scales: { 
                y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1, color: '#64748b' }, grid: { color: '#f1f5f9' } },
                x: { ticks: { color: '#64748b' }, grid: { display: false } }
            }, 
            plugins: { legend: { display: false } } 
        }
    });
</script>
</body>
</html>