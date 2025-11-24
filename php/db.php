<?php
// ---------- MYSQL ----------
$mysqli = new mysqli("localhost", "root", "", "demo_app");
if ($mysqli->connect_errno) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
$mysqli->set_charset("utf8mb4");

// ---------- HEADERS ----------
header("Content-Type: application/json");

// ---------- COMPOSER AUTOLOAD ----------
require_once __DIR__ . "/vendor/autoload.php";

// ---------- REDIS ----------
use Predis\Client as RedisClient;

$redis = new RedisClient([
    "scheme" => "tcp",
    "host" => "127.0.0.1",
    "port" => 6379
]);

// ---------- MONGODB ----------
$mongo = (new MongoDB\Client)->demo_app;
$profileCollection = $mongo->user_profiles;

function respond($data) {
    echo json_encode($data);
    exit;
}
