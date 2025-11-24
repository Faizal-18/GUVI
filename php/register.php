<?php
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name     = trim($data["name"] ?? "");
$email    = trim($data["email"] ?? "");
$password = trim($data["password"] ?? "");
$age      = $data["age"] ?? null;
$dob      = $data["dob"] ?? null;
$contact  = trim($data["contact"] ?? "");

if (!$name || !$email || !$password) {
    respond(["success" => false, "message" => "Missing fields"]);
}

// Check existing email
$stmt = $mysqli->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    respond(["success" => false, "message" => "Email already registered"]);
}

$stmt->close();

// Insert MySQL Registration Data
$pass_hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $mysqli->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $pass_hash);
$stmt->execute();

$user_id = $stmt->insert_id;  // get MySQL ID

// Create MongoDB profile
$profileCollection->insertOne([
    "user_id" => $user_id,
    "age" => $age,
    "dob" => $dob,
    "contact" => $contact
]);

respond(["success" => true, "message" => "Registration successful"]);
