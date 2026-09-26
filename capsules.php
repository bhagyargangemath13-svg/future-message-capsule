<?php

include "db.php";

/* Use Indian Standard Time */
date_default_timezone_set("Asia/Kolkata");


/*
 * IMPORTANT:
 * Show ONLY capsules created for yourself.
 * Friend capsules will NOT appear on this page.
 * They can only be opened using their unique link.
 */

$sql = "SELECT * FROM capsules
        WHERE type = 'myself'
        ORDER BY unlock_date ASC, unlock_time ASC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Capsules - Future Message Capsule</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>


<header class="header">

    <h1>📦 My Capsules</h1>

</header>


<main class="capsules-main">


    <div id="capsuleList" class="capsule-list">


        <?php

        if ($result && $result->num_rows > 0) {


            while ($capsule = $result->fetch_assoc()) {


                /* Create unlock date/time */

                $unlockDateTime = new DateTime(
                    $capsule["unlock_date"] . " " . $capsule["unlock_time"],
                    new DateTimeZone("Asia/Kolkata")
                );


                $currentDateTime = new DateTime(
                    "now",
                    new DateTimeZone("Asia/Kolkata")
                );


                /* Check whether unlocked */

                $unlocked =
                    $currentDateTime >= $unlockDateTime;


                ?>


                <div class="capsule-card">


                    <h3>
                        👤 Future Me
                    </h3>


                    <p>

                        <strong>From:</strong>

                        <?php

                        echo htmlspecialchars(
                            $capsule["sender_name"]
                        );

                        ?>

                    </p>


                    <p>

                        <strong>Unlocks:</strong>

                        <?php

                        echo $unlockDateTime->format(
                            "d F Y"
                        );

                        ?>

                        at

                        <?php

                        echo $unlockDateTime->format(
                            "h:i A"
                        );

                        ?>

                    </p>


                    <?php

                    /*
                     * =========================
                     * UNLOCKED
                     * =========================
                     */

                    if ($unlocked) {

                    ?>

                        <p class="status-unlocked">

                            🔓 UNLOCKED

                        </p>


                        <p class="countdown">

                            🔓 CAPSULE READY TO OPEN!

                        </p>


                    <?php

                    }

                    /*
                     * =========================
                     * LOCKED
                     * =========================
                     */

                    else {

                    ?>

                        <p class="status-locked">

                            🔒 LOCKED

                        </p>


                        <!--
                            Live countdown.
                            JavaScript will update this every second.
                        -->

                        <p class="countdown"
                           data-unlock="<?php
                               echo $unlockDateTime->format(
                                   'Y-m-d H:i:s'
                               );
                           ?>">

                            Calculating countdown...

                        </p>


                    <?php

                    }

                    ?>


                    <div class="card-buttons">


                        <?php

                        if ($unlocked) {

                        ?>

                            <a href="open.php?id=<?php
                                echo $capsule["id"];
                            ?>"
                               class="btn btn-primary">

                                Open Capsule

                            </a>


                        <?php

                        } else {

                        ?>

                            <a href="open.php?id=<?php
                                echo $capsule["id"];
                            ?>"
                               class="btn btn-secondary">

                                View

                            </a>


                        <?php

                        }

                        ?>


                        <!-- DELETE BUTTON -->

                        <form action="delete_capsule.php"
                              method="POST"
                              style="display:inline;">

                            <input type="hidden"
                                   name="id"
                                   value="<?php
                                       echo $capsule["id"];
                                   ?>">


                            <button type="submit"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this capsule?');">

                                Delete

                            </button>

                        </form>


                    </div>


                </div>


                <?php

            }


        } else {

        ?>


            <p class="note">

                You have no personal capsules yet.
                Create one for your future self! 🔐

            </p>


        <?php

        }

        ?>


    </div>


    <a href="index.php"
       class="btn btn-secondary back-btn">

        ← Back to Home

    </a>


</main>



<script>

/*
 * =====================================================
 * LIVE COUNTDOWN
 * =====================================================
 *
 * The countdown updates every second.
 * When the time reaches zero, the page automatically
 * reloads so the capsule becomes unlocked.
 */

function updateCountdowns() {

    var countdownElements =
        document.querySelectorAll(".countdown[data-unlock]");


    var now =
        new Date();


    countdownElements.forEach(function(element) {


        var unlockString =
            element.getAttribute("data-unlock");


        /*
         * Convert database date/time to a JavaScript date.
         * Database time is Indian Standard Time.
         */

        var unlockTime =
            new Date(
                unlockString.replace(" ", "T") + "+05:30"
            );


        var difference =
            unlockTime.getTime() - now.getTime();


        /*
         * If unlock time has arrived
         */

        if (difference <= 0) {

            element.textContent =
                "🔓 CAPSULE UNLOCKED!";


            /*
             * Refresh after a short delay so
             * the Open Capsule button also changes.
             */

            setTimeout(function() {

                location.reload();

            }, 1000);


            return;

        }


        /*
         * Convert milliseconds
         * into days, hours, minutes and seconds.
         */

        var totalSeconds =
            Math.floor(
                difference / 1000
            );


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
         * Add leading zero to
         * hours/minutes/seconds.
         */

        hours =
            String(hours).padStart(2, "0");


        minutes =
            String(minutes).padStart(2, "0");


        seconds =
            String(seconds).padStart(2, "0");


        element.textContent =
            "Unlocks in: "
            + days + "d : "
            + hours + "h : "
            + minutes + "m : "
            + seconds + "s";

    });

}


/*
 * Run immediately
 */

updateCountdowns();


/*
 * Update every second
 */

setInterval(
    updateCountdowns,
    1000
);

</script>


</body>

</html>


<?php

$conn->close();

?>