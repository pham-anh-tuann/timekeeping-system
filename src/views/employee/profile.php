<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Cổng thông tin nhân viên' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary: #2effe8; --primary-dark: #0dd4be; 
            --sidebar-bg: #0f172a; --sidebar-hover: rgba(46, 255, 232, 0.1);
            --bg: #f8fafc; --card-bg: #ffffff; 
            --text-main: #1e293b; --text-muted: #64748b; 
            --success: #10b981; --danger: #ef4444; --warning: #f59e0b; 
            --border: #e2e8f0; 
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg); margin: 0; color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }
        
        .sidebar { width: 260px; background: var(--sidebar-bg); color: #cbd5e1; display: flex; flex-direction: column; overflow-y: auto; flex-shrink: 0; box-shadow: 4px 0 15px rgba(0,0,0,0.05); z-index: 20; }
        .sidebar-brand { padding: 25px; font-size: 22px; font-weight: 800; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); text-transform: uppercase; letter-spacing: 1px; color: white; }
        .sidebar-brand span { color: var(--primary); }
        .nav-item { padding: 15px 25px; display: flex; align-items: center; gap: 15px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-size: 14px; font-weight: 500; border-left: 3px solid transparent; }
        .nav-item:hover { background: var(--sidebar-hover); color: var(--primary); }
        .nav-item.active { background: var(--sidebar-hover); color: var(--primary); border-left: 3px solid var(--primary); font-weight: 700; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        
        .main { flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; }
        .top-bar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.03); z-index: 10; position: sticky; top: 0; border-bottom: 1px solid var(--border); }
        
        .content { padding: 30px; max-width: 1200px; margin: 0 auto; width: 100%; }
        
        .tabs { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid var(--border); padding-bottom: 10px; overflow-x: auto; }
        .tab-btn { background: transparent; border: none; padding: 10px 20px; font-weight: 600; color: var(--text-muted); cursor: pointer; border-radius: 8px; transition: 0.2s; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .tab-btn:hover { background: #f1f5f9; color: #0f172a; }
        .tab-btn.active { background: #0f172a; color: var(--primary); box-shadow: 0 4px 10px rgba(15, 23, 42, 0.2); }
        .tab-content { display: none; animation: fadeIn 0.3s; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .card { background: var(--card-bg); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 30px; border: 1px solid rgba(0,0,0,0.05); margin-bottom: 25px; transition: 0.3s; }
        .card-header { font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 25px; border-bottom: 1px solid var(--border); padding-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .card-header i { color: var(--primary-dark); }
        
        .punch-card { text-align: center; background: var(--sidebar-bg); color: white; padding: 40px 20px; position: relative; overflow: hidden; }
        .punch-card::before { content: ""; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(46, 255, 232, 0.1) 0%, transparent 60%); z-index: 0; }
        .punch-card > * { position: relative; z-index: 1; }
        .punch-card h3 { margin-top: 0; color: var(--primary); border: none; font-size: 22px; }
        .punch-time { font-size: 40px; font-weight: 800; margin: 15px 0 30px; text-shadow: 0 0 20px rgba(46, 255, 232, 0.4); }
        
        .btn { padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; justify-content: center; }
        .btn-punch { font-size: 16px; padding: 15px 40px; font-weight: 800; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .btn-primary { background-color: var(--primary); color: #0f172a; box-shadow: 0 4px 10px rgba(46, 255, 232, 0.2); } 
        .btn-primary:hover { background-color: #5cffe0; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46, 255, 232, 0.4); }
        .btn-checkin { background-color: var(--primary); color: #0f172a; } .btn-checkin:hover { background-color: #5cffe0; transform: translateY(-2px); }
        .btn-checkout { background-color: var(--warning); color: #fff; } .btn-checkout:hover { background-color: #d97706; transform: translateY(-2px); }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: var(--text-muted); }
        input, select, textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--border); border-radius: 10px; font-family: 'Inter', sans-serif; font-size: 14px; outline: none; background: #f8fafc; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary-dark); background: white; box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); }
        
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; padding: 12px 10px; border-bottom: 2px solid var(--border); color: var(--text-muted); text-transform: uppercase; font-weight: 700; font-size: 11px; letter-spacing: 0.5px;}
        td { padding: 12px 10px; border-bottom: 1px solid var(--border); font-weight: 500; color: #1e293b; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-block; text-transform: uppercase; }
        .badge-ontime { background: #d1fae5; color: #059669; } .badge-late { background: #fee2e2; color: #dc2626; }
        .Approved { background: #d1fae5; color: #059669; } .Pending { background: #fef3c7; color: #d97706; } .Rejected { background: #fee2e2; color: #dc2626; }
        .role-badge { background: var(--primary); color: #0f172a; padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: 800; text-transform: uppercase; }

        .avatar-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary); padding: 4px; margin-bottom: 15px; box-shadow: 0 10px 25px rgba(46, 255, 232, 0.2); }
        .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); padding: 2px; }
    </style>
</head>
<body>

<?php 
    $userRole = strtolower(trim($_SESSION['user']['role'] ?? '')); 
    $avatarImg = !empty($user['avatar']) ? $user['avatar'] : 'default-avatar.png';
    $avatarPath = "/timekeeping-system/public/uploads/avatars/" . htmlspecialchars($avatarImg);
?>

<div class="sidebar">
    <div class="sidebar-brand"><span>SME</span> QUẢN LÝ</div>
    <a href="/timekeeping-system/public/profile" class="nav-item active"><i class="fa-solid fa-fingerprint"></i> Kiosk Cá Nhân</a>
    
    <?php if ($userRole === 'manager' || $userRole === 'admin'): ?>
        <div style="padding: 20px 25px 5px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">Quản lý</div>
        <a href="/timekeeping-system/public/admin/dashboard" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="/timekeeping-system/public/admin/reports" class="nav-item"><i class="fa-solid fa-file-invoice"></i> Báo cáo</a>
        <a href="/timekeeping-system/public/admin/approvals" class="nav-item"><i class="fa-solid fa-clipboard-check"></i> Duyệt chấm công</a>
        <a href="/timekeeping-system/public/admin/leaves" class="nav-item"><i class="fa-solid fa-calendar-day"></i> Nghỉ phép</a>
        <a href="/timekeeping-system/public/admin/complaints" class="nav-item"><i class="fa-solid fa-circle-exclamation"></i> Khiếu nại</a>
        <a href="/timekeeping-system/public/admin/shift-assignment" class="nav-item"><i class="fa-solid fa-calendar-week"></i> Phân ca</a>
    <?php endif; ?>

    <?php if ($userRole === 'admin'): ?>
        <div style="padding: 20px 25px 5px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">Hệ thống</div>
        <a href="/timekeeping-system/public/admin/employees" class="nav-item"><i class="fa-solid fa-users"></i> Nhân viên</a>
        <a href="/timekeeping-system/public/admin/salary" class="nav-item"><i class="fa-solid fa-money-bill-wave"></i> Bảng lương</a>
        <a href="/timekeeping-system/public/admin/settings" class="nav-item"><i class="fa-solid fa-cog"></i> Cấu hình Ca</a>
    <?php endif; ?>
</div>

<div class="main">
    <div class="top-bar">
        <h2 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;">Cổng thông tin nội bộ</h2>
        <div style="display:flex; align-items:center; gap: 20px;">
            <div style="display:flex; align-items:center; gap:10px; font-weight: 600; color: #0f172a;">
                <img src="<?= $avatarPath ?>" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=0f172a&color=2effe8';" class="topbar-avatar" alt="Avatar">
                <span><?= htmlspecialchars($_SESSION['user']['full_name']) ?> <span class="role-badge"><?= htmlspecialchars($_SESSION['user']['role']) ?></span></span>
            </div>
            <a href="/timekeeping-system/public/logout" style="color: var(--danger); font-weight: 700; text-decoration: none; padding: 8px 15px; background: #fee2e2; border-radius: 8px;"><i class="fa-solid fa-power-off"></i> THOÁT</a>
        </div>
    </div>

    <div class="content">
        <div class="tabs">
            <?php if($userRole !== 'admin'): ?>
                <button class="tab-btn active" onclick="openTab(event, 'tab-kiosk')"><i class="fa-regular fa-clock"></i> Điểm danh</button>
            <?php endif; ?>
            <button class="tab-btn <?= $userRole === 'admin' ? 'active' : '' ?>" onclick="openTab(event, 'tab-profile')"><i class="fa-solid fa-user-tie"></i> Hồ sơ cá nhân</button>
            <?php if($userRole !== 'admin'): ?>
                <button class="tab-btn" onclick="openTab(event, 'tab-history')"><i class="fa-solid fa-history"></i> Lịch sử công</button>
                <button class="tab-btn" onclick="openTab(event, 'tab-forms')"><i class="fa-solid fa-file-signature"></i> Đơn từ / Yêu cầu</button>
            <?php endif; ?>
        </div>

        <?php if($userRole !== 'admin'): ?>
        <div id="tab-kiosk" class="tab-content active">
            <div class="card punch-card">
                <h3>Điểm danh nhận diện</h3>
                <p style="color: #94a3b8; margin: 5px 0;">Ngày làm việc: <strong style="color:white;"><?= date('d/m/Y') ?></strong></p>
                <?php if (!$record): ?>
                    <p class="punch-time">Sẵn sàng điểm danh</p>
                    <form action="/timekeeping-system/public/attendance/punch" method="POST">
                        <input type="hidden" name="action" value="check_in">
                        <button type="submit" class="btn btn-punch btn-checkin"><i class="fa-solid fa-fingerprint"></i> XÁC NHẬN VÀO CA</button>
                    </form>
                <?php elseif ($record && !$record['check_out_time']): ?>
                    <p style="color: #94a3b8; margin: 10px 0 0;">Thời gian đã vào ca:</p>
                    <p class="punch-time"><?= date('H:i', strtotime($record['check_in_time'])) ?></p>
                    <form action="/timekeeping-system/public/attendance/punch" method="POST">
                        <input type="hidden" name="action" value="check_out">
                        <button type="submit" class="btn btn-punch btn-checkout"><i class="fa-solid fa-door-open"></i> XÁC NHẬN RA CA</button>
                    </form>
                <?php else: ?>
                    <p style="color: #94a3b8; margin: 10px 0 0;">Thời gian làm việc hôm nay:</p>
                    <p class="punch-time"><?= date('H:i', strtotime($record['check_in_time'])) ?> - <?= date('H:i', strtotime($record['check_out_time'])) ?></p>
                    <div style="background: var(--primary); color: #0f172a; padding: 12px 25px; border-radius: 30px; font-weight: 800; font-size: 15px; display: inline-flex; align-items:center; gap: 8px;"><i class="fa-solid fa-circle-check"></i> HOÀN TẤT CA LÀM</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div id="tab-profile" class="tab-content <?= $userRole === 'admin' ? 'active' : '' ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                <div class="card">
                    <h3 class="card-header"><i class="fa-solid fa-user-pen"></i> Cập nhật hồ sơ</h3>
                    <form action="/timekeeping-system/public/user/update-info" method="POST" enctype="multipart/form-data">
                        <div style="text-align: center; margin-bottom: 25px;">
                            <img id="avatar-preview" src="<?= $avatarPath ?>" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=0f172a&color=2effe8';" class="avatar-preview">
                            <input type="file" name="avatar" accept="image/*" style="display:block; margin: 10px auto 0; font-size:13px; width: 250px; background:#f8fafc; padding:8px; border-radius:8px;" onchange="document.getElementById('avatar-preview').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <div class="form-group"><label>Số điện thoại</label><input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"></div>
                        <div class="form-group"><label>Email liên hệ</label><input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"></div>
                        <div class="form-group"><label>Địa chỉ thường trú</label><input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>"></div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-floppy-disk"></i> Lưu hồ sơ</button>
                    </form>
                </div>
                <div>
                    <div class="card">
                        <h3 class="card-header"><i class="fa-solid fa-briefcase"></i> Hợp đồng</h3>
                        <div style="display:flex; flex-direction:column; gap:15px;">
                            <div style="display:flex; justify-content:space-between; padding-bottom:10px; border-bottom:1px dashed var(--border);"><span style="color:var(--text-muted); font-weight:600;">Mã NV:</span><strong style="color:#0f172a;"><?= htmlspecialchars($user['worker_id']) ?></strong></div>
                            <div style="display:flex; justify-content:space-between; padding-bottom:10px; border-bottom:1px dashed var(--border);"><span style="color:var(--text-muted); font-weight:600;">Cấp bậc:</span><span class="role-badge"><?= htmlspecialchars($user['role']) ?></span></div>
                            <div style="display:flex; justify-content:space-between; padding-bottom:10px; border-bottom:1px dashed var(--border);"><span style="color:var(--text-muted); font-weight:600;">Ca làm:</span><strong style="color:var(--primary-dark);"><?= htmlspecialchars($user['shift_name'] ?? 'Chưa có ca') ?></strong></div>
                            <div style="display:flex; justify-content:space-between; align-items:center;"><span style="color:var(--text-muted); font-weight:600;">Lương/Ngày:</span><strong style="color:var(--success); font-size:18px;"><?= number_format($user['daily_salary']) ?> đ</strong></div>
                        </div>
                    </div>
                    <div class="card">
                        <h3 class="card-header"><i class="fa-solid fa-shield-halved"></i> Đổi mật khẩu</h3>
                        <form action="/timekeeping-system/public/user/change-password" method="POST">
                            <div class="form-group"><label>Mật khẩu mới</label><input type="password" name="new_password" required></div>
                            <button type="submit" class="btn" style="background:#0f172a; color:var(--primary); width:100%;"><i class="fa-solid fa-lock"></i> Đổi mật khẩu</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if($userRole !== 'admin'): ?>
        <div id="tab-history" class="tab-content">
            <div class="card">
                <h3 class="card-header"><i class="fa-solid fa-list-ul"></i> Lịch sử chấm công</h3>
                <table>
                    <thead><tr><th>Ngày làm việc</th><th>Giờ vào</th><th>Giờ ra</th><th>Trạng thái (POS)</th><th>Duyệt HR</th></tr></thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($log['work_date'])) ?></td>
                            <td><strong style="color: #0f172a;"><?= date('H:i:s', strtotime($log['check_in_time'])) ?></strong></td>
                            <td><strong style="color: #64748b;"><?= $log['check_out_time'] ? date('H:i:s', strtotime($log['check_out_time'])) : '--:--' ?></strong></td>
                            <td><span class="badge badge-<?= ($log['status'] === 'Late') ? 'late' : 'ontime' ?>"><?= $log['status'] ?></span></td>
                            <td><span class="badge <?= $log['approval_status'] === 'Approved' ? 'Approved' : ($log['approval_status'] === 'Rejected' ? 'Rejected' : 'Pending') ?>"><?= $log['approval_status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-forms" class="tab-content">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                
                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <div class="card" style="border-top: 4px solid var(--primary-dark); margin-bottom: 0;">
                        <h3 class="card-header"><i class="fa-solid fa-calendar-plus"></i> Xin nghỉ phép</h3>
                        <form action="/timekeeping-system/public/user/leave/send" method="POST">
                            <div class="form-group"><label>Từ ngày</label><input type="date" name="start_date" required></div>
                            <div class="form-group"><label>Đến ngày</label><input type="date" name="end_date" required></div>
                            <div class="form-group"><label>Lý do</label><textarea name="reason" required></textarea></div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-paper-plane"></i> Gửi Đơn</button>
                        </form>
                    </div>
                    <div class="card" style="margin-bottom: 0; padding: 20px 30px;">
                        <h3 class="card-header" style="font-size: 15px; margin-bottom: 15px;"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử xin nghỉ</h3>
                        <table>
                            <thead><tr><th>Từ &rarr; Đến ngày</th><th>Tình trạng</th></tr></thead>
                            <tbody>
                                <?php foreach ($leaves ?? [] as $l): ?>
                                <tr>
                                    <td style="font-weight:600;"><?= date('d/m', strtotime($l['start_date'])) ?> - <?= date('d/m', strtotime($l['end_date'])) ?></td>
                                    <td><span class="badge <?= $l['status'] ?>"><?= $l['status'] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($leaves)): ?><tr><td colspan="2" style="text-align:center; color:#94a3b8;">Chưa có dữ liệu</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 25px;">
                    <div class="card" style="border-top: 4px solid var(--danger); margin-bottom: 0;">
                        <h3 class="card-header" style="color:var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Khiếu nại công</h3>
                        <form action="/timekeeping-system/public/user/complaint/send" method="POST">
                            <div class="form-group">
                                <label>Chọn ngày lỗi</label>
                                <select name="attendance_log_id" required>
                                    <option value="" disabled selected>-- Chọn lịch sử --</option>
                                    <?php foreach (array_slice($logs, 0, 10) as $log): ?><option value="<?= $log['id'] ?>"><?= date('d/m/Y', strtotime($log['work_date'])) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div style="display: flex; gap: 15px;">
                                <div class="form-group" style="flex:1;"><label>Vào thực tế</label><input type="time" name="suggested_time" required></div>
                                <div class="form-group" style="flex:1;"><label>Ra thực tế</label><input type="time" name="suggested_out_time" required></div>
                            </div>
                            <div class="form-group"><label>Giải trình</label><textarea name="reason" required></textarea></div>
                            <button type="submit" class="btn" style="width: 100%; background: var(--danger); color: white;"><i class="fa-solid fa-paper-plane"></i> Gửi Khiếu Nại</button>
                        </form>
                    </div>
                    <div class="card" style="margin-bottom: 0; padding: 20px 30px;">
                        <h3 class="card-header" style="font-size: 15px; margin-bottom: 15px;"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử khiếu nại</h3>
                        <table>
                            <thead><tr><th>Ngày làm việc bị lỗi</th><th>Tình trạng</th></tr></thead>
                            <tbody>
                                <?php foreach ($complaints ?? [] as $c): ?>
                                <tr>
                                    <td style="font-weight:600;"><?= date('d/m/Y', strtotime($c['work_date'])) ?></td>
                                    <td><span class="badge <?= $c['status'] ?>"><?= $c['status'] ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($complaints)): ?><tr><td colspan="2" style="text-align:center; color:#94a3b8;">Chưa có dữ liệu</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
    function openTab(evt, tabId) {
        let i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) tabcontent[i].style.display = "none";
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) tablinks[i].className = tablinks[i].className.replace(" active", "");
        document.getElementById(tabId).style.display = "block";
        evt.currentTarget.className += " active";
    }
</script>
</body>
</html>