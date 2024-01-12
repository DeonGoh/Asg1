<?php 
session_start();

//Read the data input from the previous page
$name = $_POST["name"];
$address = $_POST["address"];
$country = $_POST["country"];
$phone = $_POST["phone"];
$email = $_POST["email"];
$password = $_POST["password"];

// Include the PHP file that establishes database connection handle
include_once("mysql_conn.php");

//Define the INSERT SQL statement
$qry = "INSERT INTO Shopper (Name, Address, Country, Phone, Email, Password)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn -> prepare($qry);
// "ssssss" - 6 string parameters
$stmt->bind_param("ssssss", $name, $address, $country, $phone, $email, $password);

if($stmt->execute()){
    // Retrieve the shopper ID assigned to the new shopper
    $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
    $result = $conn -> query($qry); // Execute the SQL and get the returned result
    while($row = $result -> fetch_assoc()) {
        $_SESSION["ShopperID"] = $row["ShopperID"];
    }

    // Successful message and Shopper ID
    $Message = "Registration succssful!<br/>
                Your ShopperID is $_SESSION[ShopperID]<br/>";
    // Save the Shopper Name in a session variables
    $_SESSION["ShopperName"] = $name;
}
else{ // Error Message
    $Message = "<h3 style='color:red'>Error in inserting record</h3>";
}

// Release the resource allocated for prepared statement
$stmt->close();
// Close database
$conn->close();

// Display Page Layout header with updated session state and links
include("header.php");
// Display Message
echo $Message;
// Display Page Layout footer
include("footer.php");
?>