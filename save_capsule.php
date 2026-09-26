<?php

include "db.php";

$type = $_POST["type"];
$sender_name = $_POST["sender_name"];
$recipient_name = $_POST["recipient_name"];
$recipient_email = $_POST["recipient_email"];
$message = $_POST["message"];
$unlock_date = $_POST["unlock_date"];
$unlock_time = $_POST["unlock_time"];

$sql = "INSERT INTO capsules
        (type, sender_name, recipient_name, recipient_email,
         message, unlock_date, unlock_time)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssssss",
    $type,
    $sender_name,
    $recipient_name,
    $recipient_email,
    $message,
    $unlock_date,
    $unlock_time
);

if ($stmt->execute()) {

    // Get the ID of the newly created capsule
    $capsuleId = $stmt->insert_id;

    echo "success|" . $capsuleId;

} else {

    echo "Database error: " . $stmt->error;

}

$stmt->close();
$conn->close();

?>