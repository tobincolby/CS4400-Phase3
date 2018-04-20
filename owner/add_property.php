<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:57 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$owner_username = $_SESSION['username'];

if ( $_SERVER['REQUEST_METHOD'] == 'POST') {

    $property_name = $_POST['property_name'];
    $street_address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $size = $_POST['size'];
    $property_type = $_POST['property_type'];
    $is_public = ($_POST['is_public'] == "Yes" ? true : false);
    $is_commercial = ($_POST['is_commercial'] == "Yes" ? true : false);
    $farm_items = explode(",", $_POST['farm_items']);

    $result = $mysqli->query("SELECT Count(*) FROM Property WHERE PropertyName=$property_name");
    if (mysqli_num_rows($result) > 0) {
        $error_msg = "Property Name Already Exists";
    } else {

        $result = $mysqli->query("SELECT ID FROM Property ORDER BY ID");
        $new_id = mysqli_num_rows($result);
        $result = $mysqli->query("INSERT INTO Property VALUES($new_id, $property_name, $size, $is_commercial, 
                        $is_public, $street_address, $city, $zip, $property_type, $owner_username, NULL)");
        foreach ($farm_items as $farm_item) {
            $result = $mysqli->query("INSERT INTO Has VALUES($new_id, $farm_item)");
        }

        header("Location: owner_properties.php");
        exit();
    }
}

?>