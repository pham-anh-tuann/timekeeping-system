<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; background: #f4f7f6; }
        .nav { background: #4e73df; color: white; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; }
        .container { padding: 30px 50px; display: grid; grid-template-columns: 400px 1fr; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h3 { color: #333; margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #f8f9fc; }
        label { display: block; font-weight: 600; margin-top: 10px; font-size: 14px; }
        input, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; }
        .btn-submit { background: #1cc88a; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 700; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f8f9fc; text-align: left; padding: 12px; color: #4e73df; font-size: 13px; }
        table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .Pending { background: #fff3cd; color: #856404; }
        .Approved { background: #d1fae5; color: #065f46; }
        .Rejected { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

<div class="nav">
    <div style="font-size: 20px; font-weight: 800;">SME PRO</div>
    <div>
        <a href="/timekeeping-system/public/profile" style="color:white; margin-right:20px; text-decoration:none;">Nhật ký công</a>
        <a href="/timekeeping-system/public/logout" style="color:white;"><i class="fa-solid fa-power-off"></i></a>
    </div>
</div>

<div class="container">
    <div class="card">
        <h3><i class="fa-solid fa-paper-plane"></i> Gửi đơn xin nghỉ</h3>
        <form action="/timekeeping-system/public/user/leave/send" method="POST">
            <label>Từ ngày</label>
            <input type="date" name="start_date" required>
            <label>Đến ngày</label>
            <input type="date" name="end_date" required>
            <label>Lý do nghỉ</label>
            <textarea name="reason" rows="4" placeholder="Nhập lý do cụ thể..." required></textarea>
            <button type="submit" class="btn-submit">Gửi đơn xin phép</button>
        </form>
    </div>

    <div class="card">
        <h3><i class="fa-solid fa-list-check"></i> Trạng thái đơn đã gửi</h3>
        <table>
            <thead>
                <tr><th>Ngày gửi</th><th>Thời gian nghỉ</th><th>Lý do</th><th>Trạng thái</th></tr>
            </thead>
            <tbody>
                <?php foreach($leaves as $l): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($l['created_at'])) ?></td>
                    <td><small><?= $l['start_date'] ?> → <?= $l['end_date'] ?></small></td>
                    <td><?= htmlspecialchars($l['reason']) ?></td>
                    <td><span class="badge <?= $l['status'] ?>"><?= $l['status'] ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>