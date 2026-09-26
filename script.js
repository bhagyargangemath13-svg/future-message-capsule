// ==========================================================
// script.js
// This file has all the common functions used by every page.
// ==========================================================


// ---------- Get all capsules from localStorage ----------
function getAllCapsules() {
    var data = localStorage.getItem("capsules");

    // If nothing is saved yet, return an empty list
    if (data === null) {
        return [];
    }

    return JSON.parse(data);
}


// ---------- Save a new capsule to localStorage ----------
function saveCapsule(capsule) {
    var capsules = getAllCapsules();   // get existing capsules
    capsules.push(capsule);            // add the new one
    localStorage.setItem("capsules", JSON.stringify(capsules));
}


// ---------- Find one capsule using its id ----------
function findCapsuleById(id) {
    var capsules = getAllCapsules();

    for (var i = 0; i < capsules.length; i++) {
        if (capsules[i].id === id) {
            return capsules[i];
        }
    }

    return null; // not found
}


// ---------- Delete a capsule using its id ----------
function deleteCapsuleById(id) {
    var capsules = getAllCapsules();
    var newList = [];

    // Keep every capsule except the one we want to delete
    for (var i = 0; i < capsules.length; i++) {
        if (capsules[i].id !== id) {
            newList.push(capsules[i]);
        }
    }

    localStorage.setItem("capsules", JSON.stringify(newList));
}


// ---------- Check if a date/time is in the future ----------
function isFutureDateTime(dateString, timeString) {
    var unlockMoment = new Date(dateString + "T" + timeString);
    var now = new Date();
    return unlockMoment > now;
}


// ---------- Check if a capsule is already unlocked ----------
function isCapsuleUnlocked(capsule) {
    var unlockMoment = new Date(capsule.unlockDate + "T" + capsule.unlockTime);
    var now = new Date();
    return now >= unlockMoment;
}


// ---------- Turn a date + time into a nice readable text ----------
function formatDateTime(dateString, timeString) {
    var unlockMoment = new Date(dateString + "T" + timeString);

    var options = { day: "numeric", month: "long", year: "numeric" };
    var dateText = unlockMoment.toLocaleDateString("en-US", options);
    var timeText = unlockMoment.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" });

    return dateText + " at " + timeText;
}


// ---------- Get a countdown text like "5d 12h 20m 30s" ----------
function getCountdownText(capsule) {
    var unlockMoment = new Date(capsule.unlockDate + "T" + capsule.unlockTime);
    var now = new Date();

    var diff = unlockMoment - now; // difference in milliseconds

    if (diff <= 0) {
        return "🔓 CAPSULE UNLOCKED!";
    }

    // Convert milliseconds into days, hours, minutes, seconds
    var totalSeconds = Math.floor(diff / 1000);
    var days = Math.floor(totalSeconds / (24 * 60 * 60));
    var hours = Math.floor((totalSeconds % (24 * 60 * 60)) / (60 * 60));
    var minutes = Math.floor((totalSeconds % (60 * 60)) / 60);
    var seconds = totalSeconds % 60;

    return "Unlocks in: " + days + "d : " + hours + "h : " + minutes + "m : " + seconds + "s";
}


// ==========================================================
// HOME PAGE FUNCTION
// Checks if there is any unlocked capsule and shows the popup
// ==========================================================
function checkForUnlockedCapsules() {
    var capsules = getAllCapsules();
    var unlockedCount = 0;

    // Count how many capsules are unlocked
    for (var i = 0; i < capsules.length; i++) {
        if (isCapsuleUnlocked(capsules[i])) {
            unlockedCount = unlockedCount + 1;
        }
    }

    // If at least one capsule is unlocked, show the popup
    if (unlockedCount > 0) {
        var popupOverlay = document.getElementById("popupOverlay");
        var popupText = document.getElementById("popupText");

        if (unlockedCount === 1) {
            popupText.textContent = "A message has been waiting for you...";
        } else {
            popupText.textContent = "You have " + unlockedCount + " secret messages waiting!";
        }

        popupOverlay.classList.remove("hidden");
    }
}


// ==========================================================
// MY CAPSULES PAGE FUNCTION
// Builds and shows all the capsule cards on the page
// ==========================================================
function displayAllCapsules() {
    var capsuleList = document.getElementById("capsuleList");
    var noCapsulesText = document.getElementById("noCapsulesText");

    // Stop here if this function is called on a page without these elements
    if (!capsuleList) {
        return;
    }

    var capsules = getAllCapsules();

    // Show message if there are no capsules
    if (capsules.length === 0) {
        noCapsulesText.classList.remove("hidden");
        capsuleList.innerHTML = "";
        return;
    }

    noCapsulesText.classList.add("hidden");

    // Clear the list first, then rebuild it
    capsuleList.innerHTML = "";

    for (var i = 0; i < capsules.length; i++) {
        var capsule = capsules[i];
        var unlocked = isCapsuleUnlocked(capsule);

        // Decide the title based on capsule type
        var title = capsule.type === "myself" ? "👤 Future Me" : "💌 For " + capsule.recipientName;

        // Build the card as a string of HTML
        var cardHTML = "<h3>" + title + "</h3>";
        cardHTML += "<p><strong>Created:</strong> " + capsule.createdAt + "</p>";
        cardHTML += "<p><strong>Unlocks:</strong> " + formatDateTime(capsule.unlockDate, capsule.unlockTime) + "</p>";

        if (unlocked) {
            cardHTML += "<p class='status-unlocked'>🔓 UNLOCKED</p>";
        } else {
            cardHTML += "<p class='status-locked'>🔒 LOCKED</p>";
            cardHTML += "<p class='countdown'>" + getCountdownText(capsule) + "</p>";
        }

        // Create the card element
        var card = document.createElement("div");
        card.className = "capsule-card";
        card.innerHTML = cardHTML;

        // Buttons row (Open + Delete)
        var buttonRow = document.createElement("div");
        buttonRow.className = "card-buttons";

        var openBtn = document.createElement("a");
        openBtn.href = "open.html?id=" + capsule.id;
        openBtn.className = "btn btn-primary";
        openBtn.textContent = unlocked ? "Open Capsule" : "View";
        buttonRow.appendChild(openBtn);

        var deleteBtn = document.createElement("button");
        deleteBtn.className = "delete-btn";
        deleteBtn.textContent = "Delete";
        deleteBtn.setAttribute("data-id", capsule.id);
        deleteBtn.addEventListener("click", function () {
            var idToDelete = Number(this.getAttribute("data-id"));
            var sure = confirm("Are you sure you want to delete this capsule?");
            if (sure) {
                deleteCapsuleById(idToDelete);
                displayAllCapsules(); // refresh the list
            }
        });
        buttonRow.appendChild(deleteBtn);

        card.appendChild(buttonRow);
        capsuleList.appendChild(card);
    }
}