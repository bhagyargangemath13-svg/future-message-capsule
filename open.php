
<?php

include "db.php";

date_default_timezone_set("Asia/Kolkata");


// ============================================
// GET CAPSULE ID
// ============================================

if (!isset($_GET["id"])) {
    die("Invalid capsule link.");
}

$id = (int)$_GET["id"];


// ============================================
// GET CAPSULE
// ============================================

$sql = "SELECT * FROM capsules WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {
    die("Capsule not found.");
}

$capsule = $result->fetch_assoc();


// ============================================
// CHECK UNLOCK TIME
// ============================================

$unlockDateTime = new DateTime(
    $capsule["unlock_date"] . " " . $capsule["unlock_time"],
    new DateTimeZone("Asia/Kolkata")
);

$currentDateTime = new DateTime(
    "now",
    new DateTimeZone("Asia/Kolkata")
);

$isUnlocked = $currentDateTime >= $unlockDateTime;


// ============================================
// MARK AS OPENED
// ============================================

if ($isUnlocked && $capsule["is_opened"] == 0) {

    $updateSql = "
        UPDATE capsules
        SET is_opened = 1
        WHERE id = ?
    ";

    $updateStmt = $conn->prepare($updateSql);

    $updateStmt->bind_param("i", $id);

    $updateStmt->execute();

    $updateStmt->close();

    $capsule["is_opened"] = 1;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        A Message For You 
    </title>

    <link rel="stylesheet" href="style.css">

</head>


<body>


<header class="header">

    <?php

    if ($capsule["type"] === "friend") {

        echo "<h1>A Message For You</h1>";

    } else {

        echo "<h1>🔐 Future Message Capsule</h1>";

    }

    ?>

</header>


<main class="open-main">


<?php

// ==================================================
// UNLOCKED CAPSULE
// ==================================================

if ($isUnlocked) {

?>


    <div class="popup-card">

        <div class="popup-icon">
            💌
        </div>


        <h2>
            Your Capsule Is Open! ✨
        </h2>


        <?php

        if ($capsule["type"] === "friend") {

        ?>

            <p>

                <strong>
                    A message from
                    <?php
                    echo htmlspecialchars(
                        $capsule["sender_name"]
                    );
                    ?>
                </strong>

            </p>


            <p>

                Dear
                <?php
                echo htmlspecialchars(
                    $capsule["recipient_name"]
                );
                ?>,

            </p>

        <?php

        } else {

        ?>

            <p>

                Dear
                <?php
                echo htmlspecialchars(
                    $capsule["sender_name"]
                );
                ?>,

            </p>

        <?php

        }

        ?>


        <p>
            Here's your message:
        </p>


        <p class="message-text">

            "<?php

            echo nl2br(
                htmlspecialchars(
                    $capsule["message"]
                )
            );

            ?>"

        </p>


        <p>
            ❤️❤️❤️
        </p>


    </div>


<?php

} else {


// ==================================================
// STILL LOCKED
// ==================================================

?>

    <div class="popup-card">

        <div class="popup-icon">
            🔒
        </div>


        <h2>
            Ohhh It's Still Locked!!
        </h2>


        <?php

        if ($capsule["type"] === "friend") {

        ?>

            <p>
                Hi
                <?php
                echo htmlspecialchars(
                    $capsule["recipient_name"]
                );
                ?>!
            </p>

            <p>
                Someone has left a special message
                for you. 💌
            </p>

        <?php

        } else {

        ?>

            <p>
                This message is not ready yet.
            </p>

        <?php

        }

        ?>


        <p>
            <strong>
                It will unlock on:
            </strong>
        </p>


        <p>

            <?php

            echo $unlockDateTime->format("d F Y");

            ?>

            at

            <?php

            echo $unlockDateTime->format("h:i A");

            ?>

        </p>


        <!-- ==========================================
             LIVE COUNTDOWN
             ========================================== -->

        <p id="countdown"
           class="countdown">

            ⏳ Calculating countdown...

        </p>


        <p>
            ⏳ Come back after the unlock time.
        </p>

    </div>


<?php

}

?>


</main>


<?php

/*
 * Only run the countdown if the capsule
 * is still locked.
 */

if (!$isUnlocked) {

?>

<script>

/* ============================================
   LIVE COUNTDOWN
   ============================================ */

var countdownElement =
    document.getElementById("countdown");


/*
 * Unlock time sent from PHP to JavaScript.
 *
 * The date/time is converted into a timestamp
 * in milliseconds.
 */

var unlockTime =
    new Date(
        <?php
        echo json_encode(
            $unlockDateTime->format("Y-m-d H:i:s")
        );
        ?>.replace(" ", "T")
    ).getTime();


function updateCountdown() {

    var now =
        new Date().getTime();


    var difference =
        unlockTime - now;


    /*
     * Unlock time has arrived.
     */

    if (difference <= 0) {

        countdownElement.textContent =
            "🔓 Unlocking your message...";


        /*
         * Reload the page so PHP can show
         * the actual message.
         */

        setTimeout(function() {

            location.reload();

        }, 1000);


        return;
    }


    /*
     * Convert milliseconds to seconds.
     */

    var totalSeconds =
        Math.floor(difference / 1000);


    var days =
        Math.floor(
            totalSeconds / (24 * 60 * 60)
        );


    var hours =
        Math.floor(
            (totalSeconds % (24 * 60 * 60))
            / (60 * 60)
        );


    var minutes =
        Math.floor(
            (totalSeconds % (60 * 60))
            / 60
        );


    var seconds =
        totalSeconds % 60;


    /*
     * Add leading zero to seconds.
     */

    if (seconds < 10) {
        seconds = "0" + seconds;
    }


    /*
     * Add leading zero to minutes.
     */

    if (minutes < 10) {
        minutes = "0" + minutes;
    }


    /*
     * Add leading zero to hours.
     */

    if (hours < 10) {
        hours = "0" + hours;
    }


    countdownElement.textContent =
        
        + days + "d : "
        + hours + "h : "
        + minutes + "m : "
        + seconds + "s";

}


/*
 * Show countdown immediately.
 */

updateCountdown();


/*
 * Update every second.
 */

setInterval(updateCountdown, 1000);

</script>

<?php

}

?>


</body>

</html>


<?php

$stmt->close();

$conn->close();

?>
```
