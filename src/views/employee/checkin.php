<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; margin: 0; display: flex; flex-direction: column; height: 100vh; }
        .navbar { background-color: #fff; padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .kiosk-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .kiosk-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; max-width: 450px; width: 100%; }
        .digital-clock { font-size: 48px; font-weight: 700; color: #2c3e50; margin: 20px 0 5px; }
        .current-date { font-size: 16px; color: #858796; margin-bottom: 30px; font-weight: 600; text-transform: uppercase; }
        .btn-punch { border: none; border-radius: 50%; width: 180px; height: 180px; font-size: 24px; font-weight: 700; color: white; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.2); display: flex; flex-direction: column; justify-content: center; align-items: center; margin: 0 auto 20px; }
        .btn-in { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
        .btn-out { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
        .btn-done { background: #eaecf4; color: #858796; cursor: not-allowed; }
        .nav-actions { display: flex; align-items: center; gap: 20px; }
        .btn-icon { color: #858796; text-decoration: none; font-size: 18px; transition: 0.2s; }
        .btn-icon:hover { color: #4e73df; }
        .btn-logout { color: #e74a3b; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
<div class="navbar">
    <div style="font-size: 20px; font-weight: bold; color: #4e73df;"><i class="fa-solid fa-fingerprint"></i> Kiosk</div>
    <div class="nav-actions">
        <span style="font-weight: 600; color: #2c3e50;"><?= htmlspecialchars($user['full_name']) ?></span>
        <a href="/timekeeping-system/public/leave" class="btn-icon" title="Xin nghỉ phép"><i class="fa-solid fa-calendar-plus"></i></a>
        <a href="/timekeeping-system/public/profile" class="btn-icon" title="Đổi mật khẩu"><i class="fa-solid fa-gear"></i></a>
        <a href="/timekeeping-system/public/logout" class="btn-logout">Thoát</a>
    </div>
</div>
<div class="kiosk-container">
    <div class="kiosk-card">
        <div class="digital-clock" id="clock">00:00:00</div>
        <div class="current-date" id="date">Đang tải...</div>
        <form action="/timekeeping-system/public/attendance/punch" method="POST">
            <?php if (!$record): ?>
                <input type="hidden" name="action" value="check_in">
                <button type="submit" class="btn-punch btn-in"><i class="fa-solid fa-sign-in"></i> CHECK IN</button>
            <?php elseif (empty($record['check_out_time'])): ?>
                <input type="hidden" name="action" value="check_out">
                <button type="submit" class="btn-punch btn-out"><i class="fa-solid fa-sign-out"></i> CHECK OUT</button>
            <?php else: ?>
                <button type="button" class="btn-punch btn-done" disabled><i class="fa-solid fa-check"></i> ĐÃ XONG</button>
            <?php endif; ?>
        </form>
    </div>
</div>
<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').innerText = now.toLocaleTimeString('vi-VN', {hour12: false});
        document.getElementById('date').innerText = now.toLocaleDateString('vi-VN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
    setInterval(updateClock, 1000); updateClock();
</script>
</body>
</html>