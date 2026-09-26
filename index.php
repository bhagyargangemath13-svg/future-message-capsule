<?php

include "db.php";

/*
 * Find ONLY unlocked "myself" capsules
 * that have not been opened yet.
 *
 * Friend capsules are intentionally excluded.
 * They can only be opened using their unique link.
 */

$sql = "SELECT id, sender_name, type, recipient_name
        FROM capsules
        WHERE type = 'myself'
        AND CONCAT(unlock_date, ' ', unlock_time) <= NOW()
        AND is_opened = 0
        ORDER BY unlock_date ASC, unlock_time ASC";

$result = $conn->query($sql);

$unlockedCount = 0;
$firstCapsuleId = null;
$firstSender = "";

if ($result) {

    $unlockedCount = $result->num_rows;

    if ($unlockedCount > 0) {

        $firstCapsule = $result->fetch_assoc();

        $firstCapsuleId = $firstCapsule["id"];
        $firstSender = $firstCapsule["sender_name"];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Future Message Capsule</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>


<!-- ================= HEADER ================= -->

<header class="header">

    <h1>🔐 Future Message Capsule</h1>

    <p class="tagline">
        "Write today. Lock it. Open it in the future."
    </p>

</header>



<!-- ================= MAIN CONTENT ================= -->

<main class="home-main">


    <div class="emoji-row">
        🔐 💌 ⏳ ✨
    </div>


    <p class="description">

        Future Message Capsule lets you write a message today
        and lock it until a future date and time you choose.

        You can write a message for your future self,
        or a message for a friend.

        The capsule stays locked until the unlock time arrives,
        and then it can be opened!

    </p>



    <!-- ================= BUTTONS ================= -->

    <div class="button-group">


        <a href="create.php"
           class="btn btn-primary">

            ➕ Create Capsule

        </a>


        <a href="capsules.php"
           class="btn btn-secondary">

            📦 My Capsules

        </a>


    </div>


</main>



<!-- ================= SECRET MESSAGE POPUP ================= -->

<?php

/*
 * Show popup ONLY for your own capsules.
 *
 * Friend capsules will NEVER appear here.
 */

if ($unlockedCount > 0) {

?>

<div id="popupOverlay"
     class="popup-overlay">


    <div class="popup-card">


        <div class="popup-icon">
            🔐
        </div>


        <h2>
            You Have a Secret Message!
        </h2>


        <?php

        if ($unlockedCount == 1) {

        ?>

            <p>

                A message from

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $firstSender
                    );

                    ?>

                </strong>

                is waiting for you!

            </p>

        <?php

        } else {

        ?>

            <p>

                You have

                <strong>

                    <?php

                    echo $unlockedCount;

                    ?>

                </strong>

                secret messages waiting for you!

            </p>

        <?php

        }

        ?>


        <a href="open.php?id=<?php

            echo $firstCapsuleId;

        ?>"
           class="btn btn-primary">

            Open Secret Message

        </a>


    </div>


</div>

<?php

}

?>


</body>

</html>


<?php

$conn->close();

?>