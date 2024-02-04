<?php
session_start();

// Read the data input from previous page
$name = $_POST["name"];
$birthdate = $_POST["birthdate"];
$address = $_POST["address"];
$country = $_POST["country"];
$phone = $_POST["phone"];
$email = $_POST["email"];
$password = $_POST["password"];
$pwdQuestion = $_POST["pwdQuestion"];
$pwdAnswer = $_POST["pwdAnswer"];

include_once("mysql_conn.php");

// Check if the email is already in use
$checkEmailQuery = "SELECT ShopperID FROM Shopper WHERE Email=?";
$checkEmailStmt = $conn->prepare($checkEmailQuery);
$checkEmailStmt->bind_param("s", $email);
$checkEmailStmt->execute();
$checkEmailResult = $checkEmailStmt->get_result();

if ($checkEmailResult->num_rows > 0) {
    // Display an error message
    echo "<div style='color:red; text-align:center;'>Email is already in use by another member. Please choose a different email.</div>";
} else {
    $qry = "INSERT INTO Shopper (Name, BirthDate, Address, Country, Phone, Email, Password, PwdQuestion, PwdAnswer)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("sssssssss", $name, $birthdate, $address, $country, $phone, $email, $password, $pwdQuestion, $pwdAnswer);

    if ($stmt->execute()) { // SQL statement executed successfully
        // Retrieve the Shooper ID assigned to the new shopper
        $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
        $result = $conn->query($qry); // Execute the SQL and get the returned result
        while ($row = $result->fetch_array()) {
            $_SESSION["ShopperID"] = $row["ShopperID"];
        }

        // Successful message and Shopper ID
        $Message = "Registration successful!<br />
                Your ShopperID is $_SESSION[ShopperID]<br />";
        //Display message
        echo $Message;
        // Save the Shopper Name in a session variable
        $_SESSION["ShopperName"] = $name;
    } else {
        // Error message
        $Message = "<h3 style='color:red'>Error in inserting record</h3>";
    }

    $stmt->close();
}
$conn->close();

include("header.php");
include("footer.php");
