<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Duyệt nghỉ phép' ?></title>
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
        .page-title { color: #0f172a; font-size: 24px; font-weight: 800; margin: 0 0 25px 0; }
        
        /* THỐNG KÊ SUMMARY CARDS */
        .summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-box { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .stat-box .info { display: flex; flex-direction: column; }
        .stat-box .title { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px;}
        .stat-box .value { font-size: 24px; font-weight: 800; color: #0f172a; }
        .stat-box .icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .icon-yellow { background: #fef3c7; color: #d97706; }
        .icon-green { background: #d1fae5; color: #10b981; }
        .icon-red { background: #fee2e2; color: #ef4444; }

        /* BỘ LỌC FILTER BAR */
        .filter-bar { background: white; padding: 15px 20px; border-radius: 12px; border: 1px solid var(--border); display: flex; gap: 15px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .filter-group { flex: 1; position: relative; }
        .filter-group i { position: absolute; left: 12px; top: 12px; color: #94a3b8; }
        .filter-input { width: 100%; padding: 10px 10px 10px 35px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; background: #f8fafc; transition: 0.3s; box-sizing: border-box; }
        .filter-input:focus { border-color: var(--primary-dark); background: white; box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); }
        .filter-select { width: 100%; padding: 10px 10px 10px 15px; border: 1px solid var(--border); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13px; outline: none; background: #f8fafc; cursor: pointer; }

        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 30px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; }
        table th { padding: 15px 10px; text-align: left; color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; border-bottom: 2px solid var(--border); letter-spacing: 0.5px;}
        table td { padding: 15px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; display: inline-block; text-transform: uppercase;}
        .Approved { background: #d1fae5; color: #059669; } .Pending { background: #fef3c7; color: #d97706; } .Rejected { background: #fee2e2; color: #dc2626; }
        
        .btn-action { padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; border: none; cursor: pointer; margin-right: 5px; margin-bottom: 5px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn-paid { background: #10b981; color: white; } .btn-paid:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(16,185,129,0.3); }
        .btn-unpaid { background: #f59e0b; color: #fff; } .btn-unpaid:hover { background: #d97706; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(245,158,11,0.3); }
        .btn-reject { background: #ef4444; color: white; } .btn-reject:hover { background: #dc2626; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(239,68,68,0.3); }
        
        .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); padding: 2px; }
    </style>
</head>
<body>

<?php 
    $userRole = strtolower(trim($_SESSION['user']['role'] ?? '')); 
    
    // Tính toán Thống kê Đơn Nghỉ Phép
    $pendingLeavesCount = 0; $approvedLeaves = 0; $rejectedLeaves = 0;
    foreach($requests ?? [] as $r) {
        if($r['status'] === 'Pending') $pendingLeavesCount++;
        elseif($r['status'] === 'Approved') $approvedLeaves++;
        else $rejectedLeaves++;
    }

    $avatarImg = !empty($_SESSION['user']['avatar']) ? $_SESSION['user']['avatar'] : 'default-avatar.png';
    $avatarPath = "/timekeeping-system/public/uploads/avatars/" . htmlspecialchars($avatarImg);
?>

<div class="sidebar">
    <div class="sidebar-brand"><span>SME</span> QUẢN LÝ</div>
    <a href="/timekeeping-system/public/admin/dashboard" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="/timekeeping-system/public/admin/reports" class="nav-item"><i class="fa-solid fa-file-invoice"></i> Báo cáo</a>
    <a href="/timekeeping-system/public/admin/approvals" class="nav-item"><i class="fa-solid fa-clipboard-check"></i> Duyệt chấm công</a>
    <a href="/timekeeping-system/public/admin/leaves" class="nav-item active"><i class="fa-solid fa-calendar-day"></i> Nghỉ phép</a>
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
        <h2 class="page-title">Quản lý & Duyệt đơn xin nghỉ phép</h2>

        <div class="summary-cards">
            <div class="stat-box"><div class="info"><span class="title">Đơn Chờ Xét Duyệt</span><span class="value" id="count-pending"><?= $pendingLeavesCount ?></span></div><div class="icon icon-yellow"><i class="fa-solid fa-envelope-open-text"></i></div></div>
            <div class="stat-box"><div class="info"><span class="title">Đã Duyệt (Tháng này)</span><span class="value" id="count-approved"><?= $approvedLeaves ?></span></div><div class="icon icon-green"><i class="fa-solid fa-calendar-check"></i></div></div>
            <div class="stat-box"><div class="info"><span class="title">Từ Chối / Hủy</span><span class="value" id="count-rejected"><?= $rejectedLeaves ?></span></div><div class="icon icon-red"><i class="fa-solid fa-calendar-xmark"></i></div></div>
        </div>

        <div class="filter-bar">
            <div class="filter-group" style="flex: 2;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="lSearch" class="filter-input" placeholder="Tìm theo Tên hoặc Mã NV..." onkeyup="filterLeaves()">
            </div>
            <div class="filter-group">
                <select id="lStatus" class="filter-select" onchange="filterLeaves()">
                    <option value="all">Tất cả tình trạng đơn</option>
                    <option value="pending">Đơn đang chờ duyệt</option>
                    <option value="approved">Đơn đã được duyệt</option>
                    <option value="rejected">Đơn bị từ chối</option>
                </select>
            </div>
        </div>
        
        <div class="card">
            <table id="leaveTable">
                <thead>
                    <tr><th>Nhân viên</th><th>Lý do</th><th>Thời gian nghỉ</th><th>Loại nghỉ</th><th>Trạng thái</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    <?php foreach($requests ?? [] as $r): ?>
                    <tr class="leave-row" data-status="<?= strtolower($r['status']) ?>">
                        <td>
                            <strong class="col-name" style="color: #0f172a; font-size: 14px;"><?= htmlspecialchars($r['full_name']) ?></strong><br>
                            <span class="col-id" style="font-size:12px; color:var(--text-muted); font-weight:600;"><?= htmlspecialchars($r['worker_id']) ?></span>
                        </td>
                        <td style="font-style: italic; color: #475569; max-width: 250px;">"<?= htmlspecialchars($r['reason']) ?>"</td>
                        <td style="font-weight: 700; color: #334155;"><?= date('d/m/Y', strtotime($r['start_date'])) ?> &rarr; <?= date('d/m/Y', strtotime($r['end_date'])) ?></td>
                        <td>
                            <?php if($r['status'] === 'Approved'): ?>
                                <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: <?= $r['is_paid'] ? '#10b981' : '#f59e0b' ?>; background: <?= $r['is_paid'] ? '#d1fae5' : '#fef3c7' ?>; padding: 4px 10px; border-radius: 8px;">
                                    <?= $r['is_paid'] ? '<i class="fa-solid fa-sack-dollar"></i> Có lương' : '<i class="fa-solid fa-user-minus"></i> Không lương' ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #cbd5e1; font-size: 12px; font-weight: 600;">-- Chờ xét duyệt --</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge <?= $r['status'] ?>"><?= $r['status'] ?></span></td>
                        <td style="min-width: 280px;">
                            <?php if($r['status'] === 'Pending'): ?>
                                <a href="/timekeeping-system/public/admin/leaves/approve?id=<?= $r['id'] ?>&status=Approved&is_paid=1" class="btn-action btn-paid" onclick="return confirm('Duyệt nghỉ CÓ LƯƠNG?');"><i class="fa-solid fa-check"></i> Có Lương</a>
                                <a href="/timekeeping-system/public/admin/leaves/approve?id=<?= $r['id'] ?>&status=Approved&is_paid=0" class="btn-action btn-unpaid" onclick="return confirm('Duyệt nghỉ KHÔNG LƯƠNG?');"><i class="fa-solid fa-check"></i> Không Lương</a>
                                <a href="/timekeeping-system/public/admin/leaves/approve?id=<?= $r['id'] ?>&status=Rejected" class="btn-action btn-reject" onclick="return confirm('Từ chối đơn này?');"><i class="fa-solid fa-xmark"></i> Từ chối</a>
                            <?php else: ?>
                                <span style="color:#94a3b8; font-size:13px; font-weight: 700; background: #f1f5f9; padding: 6px 12px; border-radius: 8px;"><i class="fa-solid fa-lock"></i> Đã xử lý</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="noDataRow" style="display:none;"><td colspan="6" style="text-align:center; padding: 30px; color: #94a3b8;">Không tìm thấy đơn từ nào!</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterLeaves() {
    let search = document.getElementById("lSearch").value.toLowerCase();
    let status = document.getElementById("lStatus").value;
    
    let rows = document.getElementsByClassName("leave-row");
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let rStatus = row.getAttribute("data-status");
        
        let idText = row.getElementsByClassName("col-id")[0].innerText.toLowerCase();
        let nameText = row.getElementsByClassName("col-name")[0].innerText.toLowerCase();
        
        let matchSearch = idText.includes(search) || nameText.includes(search);
        let matchStatus = (status === 'all') || (rStatus === status);

        if (matchSearch && matchStatus) {
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