<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Capsule - Future Message Capsule</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="header">
    <h1>Create Your Future Capsule 🔐</h1>
</header>

<main class="create-main">

    <!-- Choice buttons -->
    <div class="choice-group">

        <button id="myselfBtn" class="btn btn-choice active">
            👤 For Myself
        </button>

        <button id="friendBtn" class="btn btn-choice">
            💌 For a Friend
        </button>

    </div>


    <!-- ================= FOR MYSELF ================= -->

    <form id="myselfForm" class="capsule-form">

        <label>Your Name</label>

        <input type="text"
               id="myselfName"
               placeholder="Enter your name">


        <label>Your Message</label>

        <textarea id="myselfMessage"
                  rows="4"
                  placeholder="Write a message to your future self..."></textarea>


        <label>Unlock Date</label>

        <input type="date" id="myselfDate">


        <label>Unlock Time</label>

        <input type="time" id="myselfTime">


        <button type="button"
                id="lockMyselfBtn"
                class="btn btn-primary">

            🔒 Lock Capsule

        </button>

    </form>



    <!-- ================= FOR FRIEND ================= -->

    <form id="friendForm" class="capsule-form hidden">

        <label>Your Name</label>

        <input type="text"
               id="friendSenderName"
               placeholder="Enter your name">


        <label>Friend's Name</label>

        <input type="text"
               id="friendName"
               placeholder="Enter your friend's name">


        <label>Friend's Email</label>

        <input type="email"
               id="friendEmail"
               placeholder="Enter your friend's email">


        <label>Your Message</label>

        <textarea id="friendMessage"
                  rows="4"
                  placeholder="Write a message for your friend..."></textarea>


        <label>Unlock Date</label>

        <input type="date" id="friendDate">


        <label>Unlock Time</label>

        <input type="time" id="friendTime">


        <button type="button"
                id="lockFriendBtn"
                class="btn btn-primary">

            💌 Lock Capsule

        </button>

    </form>


    <p class="note">
        📌 Your message will be stored safely and remain locked
        until the selected date and time.
    </p>

</main>



<!-- ================= CONFIRMATION POPUP ================= -->

<div id="confirmOverlay" class="popup-overlay hidden">

    <div class="popup-card">

        <div class="popup-icon">
            🔒
        </div>

        <h2>CAPSULE LOCKED!</h2>

        <p>Your message has been safely locked.</p>

        <p>
            <strong>Unlocks on:</strong>
        </p>

        <p id="confirmDateText"></p>


        <!-- SHARE LINK AREA -->

        <div id="shareArea" class="hidden">

            <p>
                💌 Your friend's capsule link:
            </p>

            <input type="text"
                   id="shareLink"
                   readonly
                   style="width:100%; padding:10px; margin:10px 0; border-radius:8px; border:1px solid #ccc;">

            <button type="button"
                    id="copyLinkBtn"
                    class="btn btn-primary">

                📋 Copy Link

            </button>

            <p id="copyMessage"
               style="margin-top:10px;">
            </p>

        </div>


        <p>
            ⏳ Your message is waiting for the future...
        </p>


        <a href="index.php"
           class="btn btn-secondary">

            ← Back to Home

        </a>

    </div>

</div>



<script>


// ======================================================
// SWITCH BETWEEN MYSELF AND FRIEND
// ======================================================

var myselfBtn =
    document.getElementById("myselfBtn");

var friendBtn =
    document.getElementById("friendBtn");

var myselfForm =
    document.getElementById("myselfForm");

var friendForm =
    document.getElementById("friendForm");


// For Myself

myselfBtn.addEventListener("click", function () {

    myselfForm.classList.remove("hidden");

    friendForm.classList.add("hidden");

    myselfBtn.classList.add("active");

    friendBtn.classList.remove("active");

});


// For Friend

friendBtn.addEventListener("click", function () {

    friendForm.classList.remove("hidden");

    myselfForm.classList.add("hidden");

    friendBtn.classList.add("active");

    myselfBtn.classList.remove("active");

});



// ======================================================
// MYSELF CAPSULE
// ======================================================

document.getElementById("lockMyselfBtn")
.addEventListener("click", function () {


    var name =
        document.getElementById("myselfName").value.trim();

    var message =
        document.getElementById("myselfMessage").value.trim();

    var date =
        document.getElementById("myselfDate").value;

    var time =
        document.getElementById("myselfTime").value;


    if (
        name === "" ||
        message === "" ||
        date === "" ||
        time === ""
    ) {

        alert("Please fill in all required fields.");

        return;

    }


    if (!isFutureDateTime(date, time)) {

        alert("Please choose a future date and time.");

        return;

    }


    saveToDatabase(
        "myself",
        name,
        "",
        "",
        message,
        date,
        time
    );

});



// ======================================================
// FRIEND CAPSULE
// ======================================================

document.getElementById("lockFriendBtn")
.addEventListener("click", function () {


    var senderName =
        document.getElementById("friendSenderName").value.trim();

    var friendName =
        document.getElementById("friendName").value.trim();

    var friendEmail =
        document.getElementById("friendEmail").value.trim();

    var message =
        document.getElementById("friendMessage").value.trim();

    var date =
        document.getElementById("friendDate").value;

    var time =
        document.getElementById("friendTime").value;


    if (
        senderName === "" ||
        friendName === "" ||
        friendEmail === "" ||
        message === "" ||
        date === "" ||
        time === ""
    ) {

        alert("Please fill in all required fields.");

        return;

    }


    if (!isFutureDateTime(date, time)) {

        alert("Please choose a future date and time.");

        return;

    }


    saveToDatabase(
        "friend",
        senderName,
        friendName,
        friendEmail,
        message,
        date,
        time
    );

});



// ======================================================
// SAVE TO DATABASE
// ======================================================

function saveToDatabase(
    type,
    sender,
    recipient,
    email,
    message,
    date,
    time
) {


    var formData =
        new FormData();


    formData.append(
        "type",
        type
    );

    formData.append(
        "sender_name",
        sender
    );

    formData.append(
        "recipient_name",
        recipient
    );

    formData.append(
        "recipient_email",
        email
    );

    formData.append(
        "message",
        message
    );

    formData.append(
        "unlock_date",
        date
    );

    formData.append(
        "unlock_time",
        time
    );


    fetch("save_capsule.php", {

        method: "POST",

        body: formData

    })


    .then(function(response) {

        return response.text();

    })


    .then(function(result) {


        var parts =
            result.trim().split("|");


        if (parts[0] === "success") {


            var capsuleId =
                parts[1];


            showConfirmation(
                date,
                time,
                type,
                capsuleId
            );


        } else {

            alert(
                "Something went wrong: " +
                result
            );

        }

    })


    .catch(function(error) {

        console.log(error);

        alert(
            "Could not connect to the server."
        );

    });

}



// ======================================================
// SHOW CONFIRMATION
// ======================================================

function showConfirmation(
    date,
    time,
    type,
    capsuleId
) {


    var overlay =
        document.getElementById(
            "confirmOverlay"
        );


    var dateText =
        document.getElementById(
            "confirmDateText"
        );


    dateText.textContent =
        formatDateTime(
            date,
            time
        );


    // Show share link only for friend capsules

    if (type === "friend") {


        var shareArea =
            document.getElementById(
                "shareArea"
            );


        var shareLink =
            document.getElementById(
                "shareLink"
            );


        var link =
            window.location.origin +
            window.location.pathname
                .replace("create.php", "open.php") +
            "?id=" +
            capsuleId;


        shareLink.value =
            link;


        shareArea.classList.remove(
            "hidden"
        );


    }


    overlay.classList.remove(
        "hidden"
    );

}



// ======================================================
// COPY LINK
// ======================================================

document.getElementById("copyLinkBtn")
.addEventListener("click", function() {


    var shareLink =
        document.getElementById(
            "shareLink"
        );


    navigator.clipboard.writeText(
        shareLink.value
    )


    .then(function() {


        document.getElementById(
            "copyMessage"
        ).textContent =
            "✅ Link copied!";


    })


    .catch(function() {


        shareLink.select();

        document.execCommand(
            "copy"
        );


        document.getElementById(
            "copyMessage"
        ).textContent =
            "✅ Link copied!";

    });

});



// ======================================================
// CHECK FUTURE DATE AND TIME
// ======================================================

function isFutureDateTime(
    date,
    time
) {


    var unlockTime =
        new Date(
            date + "T" + time
        );


    var now =
        new Date();


    return unlockTime > now;

}



// ======================================================
// FORMAT DATE AND TIME
// ======================================================

function formatDateTime(
    date,
    time
) {


    var dateTime =
        new Date(
            date + "T" + time
        );


    var options = {

        day: "numeric",

        month: "long",

        year: "numeric"

    };


    var dateText =
        dateTime.toLocaleDateString(
            "en-US",
            options
        );


    var timeText =
        dateTime.toLocaleTimeString(
            "en-US",
            {
                hour: "2-digit",
                minute: "2-digit"
            }
        );


    return dateText +
           " at " +
           timeText;

}


</script>


</body>
</html>