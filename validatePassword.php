<?php
session_start();
include("header.php"); // Include your header file

if (isset($_POST["email"])) {
    $email = $_POST["email"];

    include_once("mysql_conn.php");

    $qry = "SELECT PwdQuestion, PwdAnswer FROM Shopper WHERE Email=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pwdQuestion = $row["PwdQuestion"];
        $pwdAnswer = $row["PwdAnswer"];

        // Display the password question and a form to answer
        echo "<div style='width: 80%; margin: auto;'>";
        echo "<form method='post'>";
        echo "<div class='form-group row'>";
        echo "<label class='col-sm-3 col-form-label' for='answer'>Password Question:</label>";
        echo "<div class='col-sm-9'>";
        echo "<p>$pwdQuestion</p>";
        echo "</div>";
        echo "</div>";
        echo "<div class='form-group row'>";
        echo "<label class='col-sm-3 col-form-label' for='answer'>Your Answer:</label>";
        echo "<div class='col-sm-9'>";
        echo "<input class='form-control' name='answer' id='answer' type='text' required />";
        echo "</div>";
        echo "</div>";
        echo "<div class='form-group row'>";
        echo "<div class='col-sm-9 offset-sm-3'>";
        echo "<button type='submit'>Submit</button>";
        echo "</div>";
        echo "</div>";
        echo "</form>";

        if (isset($_POST["answer"])) {
            $userAnswer = $_POST["answer"];

            if ($userAnswer === $pwdAnswer) {
                // Display the new password
                echo "<p>New Password: password</p>";
            } else {
                echo "<p>Incorrect answer. Please try again.</p>";
            }
        }

        echo "</div>"; // Closing container
    } else {
        echo "<p>Email not found in our records.</p>";
    }

    $conn->close();
}

include("footer.php"); // Include your footer file
