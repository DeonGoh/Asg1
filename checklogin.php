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

$invalidCredentials = false;

$qry = "SELECT * FROM Shopper";
$stmt = $conn -> prepare($qry);

if($stmt->execute()){
	$result = $stmt->get_result();
	foreach($result as $row){
		if (($email == $row['Email']) && ($pwd == $row['Password'])) {
			// Save user's info in session variables
			$_SESSION["ShopperName"] = $row['Name'];
			$_SESSION["ShopperID"] = $row['ShopperID'];
			
			// To Do 2 (Practical 4): Get active shopping cart
			// Include the PHP file that establishes database connection handle: $conn
			include_once("mysql_conn.php");

			$qry = "SELECT ShopCart.ShopCartID
					FROM ShopCart
					INNER JOIN ShopCartItem
					ON ShopCartItem.ShopCartID = ShopCart.ShopCartID 
					WHERE ShopperID=? AND OrderPlaced=0
					HAVING COUNT(ShopCartItem.ShopCartID) > 0";

			$stmt = $conn->prepare($qry);
			$stmt->bind_param("i", $_SESSION["ShopperID"]); // "i" - integer
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();

			// If there is a result add the shopcartid else session["Cart"] will still be null
			while ($row = $result->fetch_array()) {
				$_SESSION["Cart"] = $row["ShopCartID"];
			}

			// Initialize Number of Cart Items in session
			$_SESSION["NumCartItem"] = 0;
			$qry = "SELECT Quantity FROM ShopCartItem WHERE ShopCartID=?";
			$stmt = $conn->prepare($qry);
			$stmt->bind_param("i", $_SESSION["Cart"]);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();
			while ($row = $result->fetch_array()) {
				$_SESSION["NumCartItem"] = $_SESSION["NumCartItem"] + $row["Quantity"];
			}
			
			// Redirect to home page
			header("Location: index.php");
			exit;
		}
		else {
			$invalidCredentials = true;
		}
	}
    if ($invalidCredentials){
		echo  "<h3 style='color:red'>Invalid Login Credentials</h3>";
	}
}
else{ // Error Message
    $Message = "<h3 style='color:red'>Error in getting shoppers records</h3>";
}


	
// Include the Page Layout footer
include("footer.php");
?>