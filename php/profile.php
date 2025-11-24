<?php
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$action = $data["action"] ?? "";
$token = $data["token"] ?? "";

$session = $redis->get("session:$token");
if (!$session) {
    respond(["success" => false, "message" => "Invalid session"]);
}
$session = json_decode($session, true);
$user_id = $session["id"];

// ---------- FETCH PROFILE ----------
if ($action === "get") {
    $profile = $profileCollection->findOne(["user_id" => $user_id]);
    respond(["success" => true, "profile" => $profile]);
}

// ---------- UPDATE PROFILE ----------
if ($action === "update") {
    $updateFields = [
        "age" => $data["age"],
        "dob" => $data["dob"],
        "contact" => $data["contact"]
    ];

    $profileCollection->updateOne(
        ["user_id" => $user_id],
        ['$set' => $updateFields]
    );

    respond(["success" => true, "message" => "Profile updated"]);
}

respond(["success" => false, "message" => "Invalid action"]);
