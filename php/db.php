<?php
// MySQL Connection
$mysqli = new mysqli("localhost", "root", "", "demo_app");

if ($mysqli->connect_errno) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
$mysqli->set_charset("utf8mb4");

header("Content-Type: application/json");

// Composer Autoload (for Predis + MongoDB)
require_once __DIR__ . "/vendor/autoload.php";

// Redis (Predis)
use Predis\Client as RedisClient;
$redis = new RedisClient([
    'scheme' => 'tcp',
    'host' => '127.0.0.1',
    'port' => 6379
]);

// MongoDB
//$mongo = (new MongoDB\Client)->demo_app;
//$logs = $mongo->user_logs;

function respond($data) {
    echo json_encode($data);
    exit;
}
?>