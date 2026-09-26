<?php

include "db.php";

$sql = "SELECT COUNT(*) AS total
        FROM capsules
        WHERE CONCAT(unlock_date, ' ', unlock_time) <= NOW()
        AND is_opened = 0";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    echo json_encode([
        "count" => (int)$row["total"]
    ]);

} else {

    echo json_encode([
        "count" => 0
    ]);

}

$conn->close();

?>