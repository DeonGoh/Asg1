<?php
// Detect the current session
session_start();
// Include the Page Layout header
include("header.php"); 

// Reading inputs entered in previous page
$email = $_POST["email"];
$pwd = $_POST["password"];

// To Do 1 (Practical 2): Validate login credentials with database
include_once("mysql_conn.php");

$qry = "SELECT * FROM Shopper";
$stmt = $conn -> prepare($qry);

if($stmt->execute()){
	$result = $stmt->get_result();
	foreach($result as $row){
		if (($email == $row['Email']) && ($pwd == $row['Password'])) {
			// Save user's info in session variables
			$_SESSION["ShopperName"] = $row['Name'];
			$_SESSION["ShopperID"] = $row['ShopperID'];
			
			// Redirect to home page
			header("Location: index.php");
			exit;
		}
		else {
			echo  "<h3 style='color:red'>Invalid Login Credentials</h3>";
		}
	}
    
}
else{ // Error Message
    $Message = "<h3 style='color:red'>Error in getting shoppers records</h3>";
}


	
// Include the Page Layout footer
include("footer.php");
?>