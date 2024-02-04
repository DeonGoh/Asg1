<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

// Check if user logged in
if (!isset($_SESSION["ShopperID"])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

include_once("mysql_conn.php"); // Include your database connection

$shopperID = $_SESSION["ShopperID"];

// Fetch the existing profile data for the logged-in shopper
$qry = "SELECT * FROM Shopper WHERE ShopperID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $shopperID);
$stmt->execute();
$result = $stmt->get_result();
$profileData = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating the profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the input data
    $name = htmlspecialchars(trim($_POST["Name"]));
    $birthdate = htmlspecialchars(trim($_POST["BirthDate"]));
    $address = htmlspecialchars(trim($_POST["Address"]));
    $country = htmlspecialchars(trim($_POST["Country"]));
    $phone = htmlspecialchars(trim($_POST["Phone"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $pwdquestion = htmlspecialchars(trim($_POST["pwdquestion"]));
    $pwdanswer = htmlspecialchars(trim($_POST["pwdanswer"]));

    // Check if the email is unique in the database
    $checkEmailQuery = "SELECT ShopperID FROM Shopper WHERE Email=? AND ShopperID != ?";
    $checkEmailStmt = $conn->prepare($checkEmailQuery);
    $checkEmailStmt->bind_param("si", $email, $shopperID);
    $checkEmailStmt->execute();
    $checkEmailResult = $checkEmailStmt->get_result();

    if ($checkEmailResult->num_rows > 0) {
        echo "<div style='color:red; text-align:center;'>Email is already in use by another member. Please choose a different email.</div>";
    } else {
        // Update the profile in the database
        $updateQuery = "UPDATE Shopper SET Name=?, BirthDate=?, Address=?, Country=?, Phone=?, Email=?, Password=?, PwdQuestion=?, PwdAnswer=? WHERE ShopperID=?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sssssssssi", $name, $birthdate, $address, $country, $phone, $email, $password, $pwdquestion, $pwdanswer, $shopperID);

        if ($updateStmt->execute()) {
            echo "<div style='color:green; text-align:center;'>Profile updated successfully!</div>";
        } else {
            echo "<div style='color:red; text-align:center;'>Error updating profile. Please try again.</div>";
        }

        $updateStmt->close();
    }

    $checkEmailStmt->close();
}

?>

<div style="width:80%; margin:auto;">
    <form method="post">
        <div class="form-group row">
            <div class="col-sm-9 offset-sm-3">
                <span class="page-title">Update Profile</span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="Name">Name:</label>
            <div class="col-sm-9">
                <input class="form-control" name="Name" id="Name" type="text" value="<?php echo $profileData['Name']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="BirthDate">Birth Date:</label>
            <div class="col-sm-9">
                <input class="form-control" name="BirthDate" id="BirthDate" type="date" value="<?php echo $profileData['BirthDate']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="Address">Address:</label>
            <div class="col-sm-9">
                <input class="form-control" name="Address" id="Address" type="text" value="<?php echo $profileData['Address']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="Country">Country:</label>
            <div class="col-sm-9">
                <input class="form-control" name="Country" id="Country" type="text" value="<?php echo $profileData['Country']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="Phone">Phone:</label>
            <div class="col-sm-9">
                <input class="form-control" name="Phone" id="Phone" type="text" value="<?php echo $profileData['Phone']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="email">Email:</label>
            <div class="col-sm-9">
                <input class="form-control" name="email" id="email" type="email" value="<?php echo $profileData['Email']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="password">Password:</label>
            <div class="col-sm-9">
                <input class="form-control" name="password" id="password" type="password" value="<?php echo $profileData['Password']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="pwdquestion">Password Question:</label>
            <div class="col-sm-9">
                <input class="form-control" name="pwdquestion" id="pwdquestion" type="text" value="<?php echo $profileData['PwdQuestion']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="pwdanswer">Password Answer:</label>
            <div class="col-sm-9">
                <input class="form-control" name="pwdanswer" id="pwdanswer" type="text" value="<?php echo $profileData['PwdAnswer']; ?>" required />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit">Update</button>
            </div>
        </div>
    </form>
</div>

<?php
include("footer.php");
?>