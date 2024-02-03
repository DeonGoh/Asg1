<?php
// Detect the current session
session_start();
// Include the Page Layout header
include("header.php");

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["ShopperID"])) {
    header("Location: login.php");
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shopperID = $_SESSION["ShopperID"];
    $subject = $_POST["subject"];
    $content = $_POST["content"];
    $rank = $_POST["rank"];

    // Include your database connection file
    include_once("mysql_conn.php");

    $qry = "INSERT INTO Feedback (ShopperID, Subject, Content, Rank, DateTimeCreated) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("issi", $shopperID, $subject, $content, $rank);

    if ($stmt->execute()) {
        echo "<div style='text-align: center; margin-top: 20px;'>";
        echo "<p>Feedback submitted successfully!</p>";
        echo "</div>";
    } else {
        echo "<div style='text-align: center; margin-top: 20px;'>";
        echo "<p>Error submitting feedback.</p>";
        echo "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- Your HTML form for submitting feedback -->
<div class="container" style="max-width: 600px; margin: auto; margin-top: 20px;">
    <h2 class="text-center">Submit Feedback</h2>
    <form method="post" action="submitFeedback.php">
        <div class="mb-3">
            <input class="form-control" type="text" name="subject" placeholder="Subject" required>
        </div>
        <div class="mb-3">
            <textarea class="form-control" name="content" placeholder="Content" required></textarea>
        </div>
        <div class="mb-3">
            <input class="form-control" type="number" name="rank" min="1" max="5" placeholder="Rank" required>
        </div>
        <button class="btn btn-primary" type="submit">Submit Feedback</button>
    </form>
</div>

<?php
// Include the Page Layout footer
include("footer.php");
?>