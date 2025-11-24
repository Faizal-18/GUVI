<?php
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$action = $data['action'] ?? '';
$token = $data['token'] ?? '';

// Check Redis session
$session = $redis->get("session:$token");
if (!$session) {
    respond(["success" => false, "message" => "Invalid session"]);
}

$session = json_decode($session, true);
$user_id = $session['id'];
$email   = $session['email'];

// REST Mongo Logging function
function logToMongo($event, $email) {
    $logData = [
        "event" => $event,
        "email" => $email,
        "time"  => date("c")
    ];

    $ch = curl_init("http://localhost/GUVI/php/mongo_log.php");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($logData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// -------------------------------------------------------
// GET PROFILE
// -------------------------------------------------------
if ($action === "get") {

    // Optional logging
    logToMongo("PROFILE_FETCH", $email);

    $stmt = $mysqli->prepare("SELECT name, email, age, dob, contact FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    respond(["success" => true, "user" => $result->fetch_assoc()]);
}

// -------------------------------------------------------
// UPDATE PROFILE
// -------------------------------------------------------
if ($action === "update") {

    $name    = $data['name'];
    $age     = $data['age'];
    $dob     = $data['dob'];
    $contact = $data['contact'];

    $stmt = $mysqli->prepare(
        "UPDATE users SET name=?, age=?, dob=?, contact=? WHERE id=?"
    );
    $stmt->bind_param("sissi", $name, $age, $dob, $contact, $user_id);
    $stmt->execute();

    // Log update
    logToMongo("PROFILE_UPDATE", $email);

    respond(["success" => true, "message" => "Profile updated"]);
}

// -------------------------------------------------------
// LOGOUT
// -------------------------------------------------------
if ($action === "logout") {

    $redis->del("session:$token");

    // Log logout
    logToMongo("LOGOUT", $email);

    respond(["success" => true, "message" => "Logged out"]);
}

respond(["success" => false, "message" => "Invalid action"]);
