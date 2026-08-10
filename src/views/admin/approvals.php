<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Duyệt chấm công' ?></title>
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
        .page-header { margin-bottom: 20px; }
        .page-title { color: #0f172a; font-size: 24px; font-weight: 800; margin: 0 0 5px 0; }
        .page-subtitle { color: var(--text-muted); font-size: 14px; margin: 0; font-weight: 500;}

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
        
        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 25px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; }
        table th { padding: 15px 10px; text-align: left; color: var(--text-muted); font-size: 12px; text-transform: uppercase; font-weight: 700; border-bottom: 2px solid var(--border); letter-spacing: 0.5px;}
        table td { padding: 15px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
        
        .employee-name { font-weight: 800; color: #0f172a; font-size: 14px; margin-bottom: 3px; }
        .employee-id { font-size: 12px; color: #94a3b8; font-weight: 600;}
        .text-late { color: var(--danger); font-weight: 800; } .text-ontime { color: var(--success); font-weight: 800; }
        .status-badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; display: inline-block; text-transform: uppercase; }
        .badge-pending { background: #fef3c7; color: #d97706; } .badge-approved { background: #d1fae5; color: #059669; } .badge-rejected { background: #fee2e2; color: #dc2626; }
        
        .btn-action { padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; color: white; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; border: none; cursor: pointer; margin-right: 5px; margin-bottom: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        .btn-approve { background: var(--success); } .btn-approve:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3); }
        .btn-reject { background: var(--danger); } .btn-reject:hover { background: #dc2626; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(239, 68, 68, 0.3); }
        .status-done { color: #94a3b8; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 5px; background: #f1f5f9; padding: 6px 12px; border-radius: 8px; width: max-content;}
        
        .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); padding: 2px; }
    </style>
</head>
<body>

<?php 
    $userRole = strtolower(trim($_SESSION['user']['role'] ?? '')); 
    $db = \App\Config\Database::getInstance()->getConnection();
    
    // Tính toán Thống kê Duyệt
    $pendingApprovals = 0; $approved = 0; $rejected = 0;
    foreach($logs ?? [] as $l) {
        if($l['approval_status'] === 'Pending') $pendingApprovals++;
        elseif($l['approval_status'] === 'Approved') $approved++;
        else $rejected++;
    }

    $avatarImg = !empty($_SESSION['user']['avatar']) ? $_SESSION['user']['avatar'] : 'default-avatar.png';
    $avatarPath = "/timekeeping-system/public/uploads/avatars/" . htmlspecialchars($avatarImg);
?>

<div class="sidebar">
    <div class="sidebar-brand"><span>SME</span> QUẢN LÝ</div>
    <a href="/timekeeping-system/public/admin/dashboard" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="/timekeeping-system/public/admin/reports" class="nav-item"><i class="fa-solid fa-file-invoice"></i> Báo cáo</a>
    <a href="/timekeeping-system/public/admin/approvals" class="nav-item active"><i class="fa-solid fa-clipboard-check"></i> Duyệt chấm công</a>
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
            <h2 class="page-title">Kiểm duyệt chấm công hằng ngày</h2>
            <p class="page-subtitle">Xác nhận giờ công để bộ phận HR tính lương chính xác.</p>
        </div>

        <div class="summary-cards">
            <div class="stat-box"><div class="info"><span class="title">Cần Duyệt Mới</span><span class="value" id="count-pending"><?= $pendingApprovals ?></span></div><div class="icon icon-yellow"><i class="fa-solid fa-hourglass-half"></i></div></div>
            <div class="stat-box"><div class="info"><span class="title">Đã Duyệt (Tháng này)</span><span class="value" id="count-approved"><?= $approved ?></span></div><div class="icon icon-green"><i class="fa-solid fa-check-double"></i></div></div>
            <div class="stat-box"><div class="info"><span class="title">Từ Chối / Hủy</span><span class="value" id="count-rejected"><?= $rejected ?></span></div><div class="icon icon-red"><i class="fa-solid fa-ban"></i></div></div>
        </div>

        <div class="filter-bar">
            <div class="filter-group" style="flex: 2;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="aSearch" class="filter-input" placeholder="Tìm theo Tên hoặc Mã NV..." onkeyup="filterApprovals()">
            </div>
            <div class="filter-group">
                <i class="fa-regular fa-calendar" style="top:11px;"></i>
                <input type="text" id="aDate" class="filter-input" placeholder="Lọc ngày (VD: 15/04/2026)" onkeyup="filterApprovals()">
            </div>
            <div class="filter-group">
                <select id="aStatus" class="filter-select" onchange="filterApprovals()">
                    <option value="all">Tất cả trạng thái duyệt</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="rejected">Bị từ chối</option>
                </select>
            </div>
        </div>

        <div class="card">
            <table id="approvalTable">
                <thead>
                    <tr><th>Nhân viên</th><th>Ngày công</th><th>Giờ vào</th><th>Giờ ra</th><th>Hệ thống</th><th>Trạng thái</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    <?php foreach($logs ?? [] as $log): ?>
                    <tr class="approval-row" data-status="<?= strtolower($log['approval_status']) ?>">
                        <td>
                            <div class="col-name employee-name"><?= htmlspecialchars($log['full_name']) ?></div>
                            <div class="col-id employee-id"><?= htmlspecialchars($log['worker_id']) ?></div>
                        </td>
                        <td class="col-date" style="font-weight: 600; color: #64748b;"><?= date('d/m/Y', strtotime($log['work_date'])) ?></td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($log['check_in_time']) ?></td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($log['check_out_time'] ?? '--:--') ?></td>
                        <td class="<?= $log['status'] === 'Late' ? 'text-late' : 'text-ontime' ?>"><?= htmlspecialchars($log['status']) ?></td>
                        <td>
                            <?php 
                                $bClass = ''; $bText = '';
                                if ($log['approval_status'] === 'Pending') { $bClass = 'badge-pending'; $bText = 'Chờ duyệt'; }
                                elseif ($log['approval_status'] === 'Approved') { $bClass = 'badge-approved'; $bText = 'Đã duyệt'; }
                                else { $bClass = 'badge-rejected'; $bText = 'Từ chối'; }
                            ?>
                            <span class="status-badge <?= $bClass ?>"><?= $bText ?></span>
                        </td>
                        <td>
                            <?php if($log['approval_status'] === 'Pending'): ?>
                                <a href="/timekeeping-system/public/admin/approvals/action?id=<?= $log['id'] ?>&status=Approved" class="btn-action btn-approve" onclick="return confirm('Xác nhận duyệt công?');"><i class="fa-solid fa-check"></i> Duyệt ngay</a>
                                <a href="/timekeeping-system/public/admin/approvals/action?id=<?= $log['id'] ?>&status=Rejected" class="btn-action btn-reject" onclick="return confirm('Xác nhận hủy công?');"><i class="fa-solid fa-xmark"></i> Hủy bỏ</a>
                            <?php else: ?>
                                <span class="status-done"><i class="fa-solid fa-check-double"></i> Đã xử lý</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr id="noDataRow" style="display:none;"><td colspan="7" style="text-align:center; padding: 30px; color: #94a3b8;">Không tìm thấy dữ liệu chấm công!</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterApprovals() {
    let search = document.getElementById("aSearch").value.toLowerCase();
    let dateStr = document.getElementById("aDate").value;
    let status = document.getElementById("aStatus").value;
    
    let rows = document.getElementsByClassName("approval-row");
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        let rStatus = row.getAttribute("data-status");
        
        let idText = row.getElementsByClassName("col-id")[0].innerText.toLowerCase();
        let nameText = row.getElementsByClassName("col-name")[0].innerText.toLowerCase();
        let dateText = row.getElementsByClassName("col-date")[0].innerText;
        
        let matchSearch = idText.includes(search) || nameText.includes(search);
        let matchDate = dateText.includes(dateStr);
        let matchStatus = (status === 'all') || (rStatus === status);

        if (matchSearch && matchDate && matchStatus) {
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