<?php
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? "");
$password = trim($data['password'] ?? "");

$stmt = $mysqli->prepare("SELECT id, name, password_hash FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    respond(["success" => false, "message" => "Invalid email or password"]);
}

$user = $res->fetch_assoc();

if (!password_verify($password, $user['password_hash'])) {
    respond(["success" => false, "message" => "Invalid email or password"]);
}

// CREATE SESSION TOKEN
$token = bin2hex(random_bytes(32));

// SAVE SESSION IN REDIS (VALID FOR 1 HOUR)
$redis->setex("session:$token", 3600, json_encode([
    "id" => $user['id'],
    "name" => $user['name'],
    "email" => $email
]));

// RETURN TOKEN
respond(["success" => true, "token" => $token]);
