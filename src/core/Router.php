<?php
namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // [QUAN TRỌNG]: Tách phần ?id=... ra khỏi đường dẫn để Router không bị nhầm lẫn
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $basePath = '/timekeeping-system/public';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        if (empty($path) || $path === '/index.php') {
            $path = '/';
        }

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            http_response_code(404);
            echo "<h1 style='color: red; text-align: center; margin-top: 50px;'>404 - Không tìm thấy trang!</h1>";
            return;
        }

        if (is_array($callback)) {
            $callback[0] = new $callback[0]();
        }

        call_user_func($callback);
    }
}
?>