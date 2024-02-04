<?php
session_start();
include("header.php"); // Include your header file
?>

<div style="width: 80%; margin: auto;">
    <form method="post" action="validatePassword.php">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="email">Email Address:</label>
            <div class="col-sm-9">
                <input class="form-control" name="email" id="email" type="email" required />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit">Submit</button>
            </div>
        </div>
    </form>
</div>

<?php
include("footer.php"); // Include your footer file
?>