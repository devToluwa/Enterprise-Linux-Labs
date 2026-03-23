<?php
// db_config.php
// Load .env file
$env = parse_ini_file(__DIR__ . '/.env');
foreach ($env as $key => $value) {
    putenv("$key=$value");
}

// Global Database Settingss
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));

// Global Identity Settings
$app_server_name = gethostname();
$app_server_ip   = $_SERVER['SERVER_ADDR'] ?? "Unknown";

// Create a single connection object to be used everywhere
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection once
if ($conn->connect_error) {
    $db_status = "Connection Failed";
    $status_color = "text-red-500";
} else {
    $db_status = "Connected to DB Server";
    $status_color = "text-green-400";
}

// Identity logic (Non-hardcoded)
$current_app_server = gethostname();
$current_app_ip = $_SERVER['SERVER_ADDR'] ?? "Localhost";
?>
