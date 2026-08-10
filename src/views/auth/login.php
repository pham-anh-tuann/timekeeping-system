<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Đăng nhập hệ thống' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2effe8; /* Màu Neon Cyan bro yêu cầu */
            --dark: #0f172a;
        }
        body, html { height: 100%; margin: 0; font-family: 'Inter', sans-serif; overflow: hidden; }
        
        /* Phông nền Full màn hình cực xịn */
        .bg-image {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1920&auto=format&fit=crop') center/cover no-repeat;
            z-index: -2;
        }
        
        /* Lớp phủ làm tối ảnh nền để nổi bật form */
        .overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.4) 100%);
            z-index: -1;
        }

        .login-wrapper { display: flex; align-items: center; justify-content: center; height: 100%; padding: 20px; }
        
        /* Hiệu ứng Kính mờ (Glassmorphism) */
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px 50px;
            width: 100%; max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            color: white;
            text-align: center;
        }

        .login-card h2 { margin-top: 0; font-size: 28px; font-weight: 800; letter-spacing: 1px; margin-bottom: 5px; }
        .login-card p { color: #cbd5e1; font-size: 14px; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; text-align: left; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .form-group i { position: absolute; top: 38px; left: 15px; color: #94a3b8; }
        
        .form-control {
            width: 100%; padding: 12px 15px 12px 40px; box-sizing: border-box;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px; color: white;
            font-family: 'Inter', sans-serif; font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }
        .form-control::placeholder { color: #64748b; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(46, 255, 232, 0.1); background: rgba(0, 0, 0, 0.4); }
        .form-control:focus + i { color: var(--primary); }

        /* Nút đăng nhập phát sáng */
        .btn-submit {
            width: 100%; padding: 14px; border-radius: 10px; border: none;
            background: var(--primary); color: var(--dark);
            font-size: 16px; font-weight: 700; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(46, 255, 232, 0.3);
            display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(46, 255, 232, 0.5); background: #5cffe0; }

        .error-msg { background: rgba(239, 68, 68, 0.1); color: #fca5a5; padding: 12px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 13px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>

<div class="bg-image"></div>
<div class="overlay"></div>

<div class="login-wrapper">
    <div class="login-card">
        <h2><span style="color: var(--primary);">SME</span> QUẢN LÝ</h2>
        <p>Đăng nhập để vào hệ thống chấm công</p>
        
        <?php if (!empty($error)): ?>
            <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="/timekeeping-system/public/login" method="POST">
            <div class="form-group">
                <label>Mã Nhân Viên / Tài khoản</label>
                <i class="fa-solid fa-user"></i>
                <input type="text" name="worker_id" class="form-control" placeholder="Nhập mã nhân viên..." required>
            </div>
            
            <div class="form-group">
                <label>Mật khẩu</label>
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-submit">
                Đăng Nhập <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>
    </div>
</div>

</body>
</html>