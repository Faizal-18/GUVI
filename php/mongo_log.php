<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !is_array($data)) {
    echo json_encode([
        "success" => false,
        "message" => "No valid JSON received"
    ]);
    exit;
}

// Convert to JSON for MongoDB
$mongoData = json_encode($data, JSON_UNESCAPED_SLASHES);

// ----------------------------------------------
// IMPORTANT: Absolute path to mongosh
// ----------------------------------------------
$mongoshPath = "C:\\Program Files\\MongoDB\\Server\\8.2\\bin\\mongosh.exe";

// Escape JSON for shell
$mongoDataEscaped = addslashes($mongoData);

// Build command using --eval (Windows safe)
$cmd = '"' . $mongoshPath . '" demo_app --eval "db.user_logs.insertOne(' . $mongoDataEscaped . ')" --quiet';

// 🔥 DEBUG MODE — SHOW FULL ERROR
exec($cmd . " 2>&1", $output, $result);

echo json_encode([
    "cmd" => $cmd,
    "output" => $output,
    "result" => $result
]);
exit;
