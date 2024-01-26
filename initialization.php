<?php 
// FOR DEBUGGING; TO BE REMOVED
session_start();

if (isset($_POST['action'])) {
 	switch ($_POST['action']) {
    	case 'lg':
        	login();
    }
}
function login(){
    $_SESSION["ShopperName"] = "James Ecader";
    $_SESSION["ShopperID"] = 1;
    include_once("mysql_conn.php");
    $qry = "SELECT i.* FROM shopcartitem i LEFT JOIN shopcart s ON i.ShopCartID = s.ShopCartID WHERE OrderPlaced=0 AND ShopperID=?";
	$stmt1 = $conn->prepare($qry);
	$stmt1->bind_param("i",$_SESSION["ShopperID"]);
	$stmt1->execute();
	$result = $stmt1->get_result();
	while ($row = $result->fetch_array()){
		$_SESSION["Cart"] = $row["ShopCartID"];
		$_SESSION["NumCartItem"] = $_SESSION["NumCartItem"] + 1;
	}
	$stmt1->close();
	$conn->close();
    header ("Location: index.php"); 
	exit;
}
?>