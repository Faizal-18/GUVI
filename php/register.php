<?php
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    respond(["success" => false, "message" => "Invalid JSON received"]);
}

$name = trim($data['name'] ?? "");
$email = trim($data['email'] ?? "");
$password = trim($data['password'] ?? "");
$age = $data['age'] ?? null;
$dob = $data['dob'] ?? null;
$contact = trim($data['contact'] ?? "");

if (!$name || !$email || !$password) {
    respond(["success" => false, "message" => "Missing required fields"]);
}

// check duplicate email
$stmt = $mysqli->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    respond(["success" => false, "message" => "Email already registered"]);
}

$stmt->close();

$hash = password_hash($password, PASSWORD_BCRYPT);

// insert user
$stmt = $mysqli->prepare("
    INSERT INTO users (name, email, password_hash, age, dob, contact)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("sssiss", $name, $email, $hash, $age, $dob, $contact);

if ($stmt->execute()) {
    respond(["success" => true, "message" => "Registration successful"]);
}

respond(["success" => false, "message" => "Registration failed"]);
