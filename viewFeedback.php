<?php

session_start();

include("header.php");

include_once("mysql_conn.php");
?>

<div class="container">
    <h2 class="text-center">View Feedback</h2>

    <?php
    $qry = "SELECT * FROM Feedback ORDER BY DateTimeCreated DESC";
    $result = $conn->query($qry);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='feedback-item'>";
            echo "<strong>Subject:</strong> " . $row["Subject"] . "<br>";
            echo "<strong>Content:</strong> " . $row["Content"] . "<br>";
            echo "<strong>Rank:</strong> " . $row["Rank"] . "<br>";
            echo "</div>";
            echo "<hr>";
        }
    } else {
        echo "<p>No feedback available.</p>";
    }

    // Check if the user is logged in
    if (isset($_SESSION["ShopperID"])) {
        // Display a button to submit feedback
        echo "<div style='text-align: center; margin-top: 20px;'>";
        echo "<a class='btn btn-primary' href='submitFeedback.php'>Submit Feedback</a>";
        echo "</div>";
    }
    ?>

</div>

<?php
include("footer.php");
?>