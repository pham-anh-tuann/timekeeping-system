<?php
namespace App\Core;

class View {
    /**
     * Hàm render giao diện
     * @param string $viewPath Đường dẫn tới file giao diện (không cần đuôi .php)
     * @param array $data Dữ liệu muốn truyền ra ngoài màn hình
     */
    public static function render($viewPath, $data = []) {
        // Biến các key của mảng $data thành các biến độc lập dùng được trong HTML
        // Ví dụ: ['title' => 'Đăng nhập'] sẽ thành biến $title
        extract($data);
        
        // Đường dẫn tuyệt đối đến file giao diện
        $file = ROOT_DIR . "/src/views/$viewPath.php";
        
        if (file_exists($file)) {
            require_once $file;
        } else {
            echo "<h3 style='color: red;'>Lỗi: Không tìm thấy file giao diện: src/views/$viewPath.php</h3>";
        }
    }
}
?>