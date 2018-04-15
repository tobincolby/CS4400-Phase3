<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/12/18
 * Time: 3:52 PM
 */


require "../include/connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($password != $confirm_pass) {
        $error_msg = "Passwords Don't Match";
    } else {
        $result = $mysqli->query("SELECT username FROM User WHERE Username=$username OR Email=$email");
        if (mysqli_num_rows($result) != 0) {
            $error_msg = "Email/Username Already exists";
        } else {

            $newpass = md5($password);
            $result = $mysqli->query("INSERT INTO User VALUES ($username, $email, $newpass, 'OWNER')");

            //Add the Owner's Property
            $property_name = $_POST['property_name'];
            $street_address = $_POST['address'];
            $city = $_POST['city'];
            $zip = $_POST['zip'];
            $size = $_POST['size'];
            $property_type = $_POST['property_type'];
            $is_public = ($_POST['is_public'] == "Yes" ? true : false);
            $is_commercial = ($_POST['is_commercial'] == "Yes" ? true : false);
            $farm_items = $_POST['farm_items'];
            $result = $mysqli->query("SELECT Count(*) FROM Property WHERE PropertyName=$property_name");
            if (mysqli_num_rows($result) > 0) {
                $error_msg = "Property Name Already Exists";
            } else {

                $result = $mysqli->query("SELECT ID FROM Property ORDER BY ID");
                $new_id = mysqli_num_rows($result);
                $owner_id = $_SESSION['username'];
                $result = $mysqli->query("INSERT INTO Property VALUES($new_id, $property_name, $size, $is_commercial, 
                        $is_public, $street_address, $city, $zip, $property_type, $owner_id, NULL)");
                foreach ($farm_items as $farm_item) {
                    $result = $mysqli->query("INSERT INTO Has VALUES($new_id, $farm_item)");
                }
                header("Location: login.php"); /* Redirect browser */
                exit();
            }

        }

    }

}

?>