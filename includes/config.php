<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'smartuae');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_URL', '/smatweb');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');

// Database Connection
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Helper functions
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function getSetting($key) {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['setting_value'] : '';
}

function uploadImage($file, $folder) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;

    $filename = uniqid() . '.' . $ext;
    $path = UPLOAD_PATH . $folder . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $filename;
    }
    return false;
}

function getImageUrl($filename, $folder) {
    if (empty($filename)) return SITE_URL . '/assets/images/placeholder.jpg';
    if (str_starts_with($filename, 'http')) return $filename;
    return UPLOAD_URL . $folder . '/' . $filename;
}

session_start();
