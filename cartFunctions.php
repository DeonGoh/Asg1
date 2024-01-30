<?php 
session_start();
if (isset($_POST['action'])) {
 	switch ($_POST['action']) {
    	case 'add':
        	addItem();
            break;
        case 'update':
            updateItem();
            break;
		case 'remove':
            removeItem();
            break;
		case 'deliver':
			changeShippingMethod();
			break;
    }
}

function addItem() {
	// Check if user logged in 
	if (!isset($_SESSION["ShopperID"])) {
		// redirect to login page if the session variable shopperid is not set
		header ("Location: login.php");
		exit;
	}
	// TO DO 1
	// Write code to implement: if a user clicks on "Add to Cart" button, insert/update the 
	// database and also the session variable for counting number of items in shopping cart.
	include_once("mysql_conn.php"); // Establish database connection handle: $conn
	// Check if a shopping cart exist, if not create a new shopping cart
	if(!isset($_SESSION["Cart"])){
		// Create a new shopping cart for the customer
		$qry = "INSERT INTO Shopcart(ShopperID) VALUES(?)";
		$stmt = $conn ->prepare($qry);
		$stmt ->bind_param("i", $_SESSION["ShopperID"]);
		$stmt ->execute();
		$stmt ->close();
		$qry = "SELECT LAST_INSERT_ID() AS ShopCartID";
		$result = $conn->query($qry);
		$row = $result->fetch_array();
		$_SESSION["Cart"] = $row["ShopCartID"];
	}
  	// If the ProductID exists in the shopping cart, 
  	// update the quantity, else add the item to the Shopping Cart.
  	$pid = $_POST["product_id"];
	$quantity = $_POST["quantity"];
	$qry = "SELECT * FROM ShopCartItem WHERE ShopCartID=? AND ProductID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("ii", $_SESSION["Cart"], $pid);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	$addNewItem = 0;
	if ($result->num_rows>0){
		$currentQuantity = getCurrentQuantity($conn, $cartid, $pid);
		if ($currentQuantity + $quantity > 10){
			$addNewItem = 10 - $currentQuantity;
		}
		else{
			$addNewItem = $quantity;
		}
		$qry = "UPDATE ShopCartItem SET Quantity=LEAST(Quantity+?,10) WHERE ShopCartID=? AND ProductID=?";
		$stmt = $conn->prepare($qry);
		$stmt->bind_param("iii",$quantity,$_SESSION["Cart"],$pid);
		$stmt->execute();
		$stmt->close();
	}
	else{
		$qry = "INSERT INTO ShopCartItem(ShopCartID, ProductID, Price, Name, Quantity) SELECT ?, ?, Price, ProductTitle, ? FROM Product WHERE ProductID=?";
		$stmt = $conn->prepare($qry);
		$stmt->bind_param("iiii", $_SESSION["Cart"], $pid, $quantity, $pid);
		$stmt->execute();
		$stmt->close();
		$addNewItem = $quantity;
	}
  	$conn->close();
  	// Update session variable used for counting number of items in the shopping cart.
	if (isset($_SESSION["NumCartItem"])){
		$_SESSION["NumCartItem"] = $_SESSION["NumCartItem"] + $addNewItem;
	}
	else{
		$_SESSION["NumCartItem"] = $addNewItem;
	}
	// Redirect shopper to shopping cart page
	header("Location:shoppingCart.php");
	exit;
}

function updateItem() {
	// Check if shopping cart exists 
	if (! isset($_SESSION["Cart"])) {
		// redirect to login page if the session variable cart is not set
		header ("Location: login.php");
		exit;
	}
	// TO DO 2
	// Write code to implement: if a user clicks on "Update" button, update the database
	// and also the session variable for counting number of items in shopping cart.
	$cartid = $_SESSION["Cart"];
	$pid = $_POST["product_id"];
	$quantity = $_POST["quantity"];
	include_once("mysql_conn.php");
	$currentQuantity = getCurrentQuantity($conn, $cartid, $pid);
	$_SESSION["NumCartItem"] = $_SESSION["NumCartItem"] + $quantity - $currentQuantity;
	$qry = "UPDATE ShopCartItem SET Quantity=? WHERE ProductID=? AND ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("iii",$quantity, $pid, $cartid);
	$stmt->execute();
	$stmt->close();
	$conn->close();
	header ("Location: shoppingCart.php");
	exit;
}

function removeItem() {
	if (! isset($_SESSION["Cart"])) {
		// redirect to login page if the session variable cart is not set
		header ("Location: login.php");
		exit;
	}
	// TO DO 3
	// Write code to implement: if a user clicks on "Remove" button, update the database
	// and also the session variable for counting number of items in shopping cart.
	$cartid = $_SESSION["Cart"];
	$pid = $_POST["product_id"];
	include_once("mysql_conn.php");
	$quantity = getCurrentQuantity($conn, $cartid, $pid);
	$qry = "DELETE FROM ShopCartItem WHERE ProductID=? AND ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("ii",$pid, $cartid);
	$stmt->execute();
	$stmt->close();
	$conn->close();
	$_SESSION["NumCartItem"] = $_SESSION["NumCartItem"] - $quantity;
	header ("Location: shoppingCart.php");
	exit;
}

function changeShippingMethod(){
	switch($_POST['deliveryMethod']){
		case 'Normal':
			$deliveryDate = addWorkingDays(date("Y-m-d"), 2);
			$deliveryType = "Normal delivery";
			$charge = 5;
			break;
		case 'Express':
			$charge = 10;
			if($_SESSION["SubTotal"] > 200){
				$charge = 0;	
			}
			$deliveryType = "Express delivery";
			$deliveryDate = date_add(new DateTime(), date_interval_create_from_date_string("1 days"));
			break;
	}
	$_SESSION['DeliveryDate'] = $deliveryDate;
	$_SESSION['DeliveryCharge'] = $charge;
	$_SESSION['DeliveryType'] = $deliveryType;
	header ("Location: shoppingCart.php");
	exit;
}

function addWorkingDays($startDate, $numWorkingDays) {
    $startDate = new DateTime($startDate);
    
    // Iterate through each day and skip weekends
    for ($i = 0; $i < $numWorkingDays; $i++) {
        $startDate->modify('+1 day');
        
        // Skip weekends (Saturday and Sunday)
		// 6 stands for Saturday & 7 stands for sunday
        while ($startDate->format('N') >= 6) {
            $startDate->modify('+1 day');
        }
    }
    
    return $startDate;
}

function getCurrentQuantity($conn, $cartid, $pid) {
	$quantity = 0;
	$qry = "SELECT Quantity FROM ShopCartItem WHERE ProductID=? AND ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("ii",$pid, $cartid);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	while($row = $result->fetch_array()){
		$quantity = $row["Quantity"];
	}
	return $quantity;
}
?>
