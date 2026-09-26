<?php

include "db.php";

if (!isset($_POST["id"])) {
    die("Capsule ID is missing.");
}

$id = (int)$_POST["id"];

$sql = "DELETE FROM capsules WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: capsules.php");
    exit();

} else {

    echo "Could not delete capsule.";

}

$stmt->close();
$conn->close();

?>