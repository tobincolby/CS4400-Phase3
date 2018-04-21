<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:51 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$admin_username = $_SESSION['username'];

$property_id = $_GET['property_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'modify') {
        $deleted_items = explode(',', $_POST['deleted_items']);
        $added_items = explode(',', $_POST['added_items']);

        $name = $_POST['name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $zip = $_POST['zip'];
        $size = $_POST['size'];

        $public = $_POST['is_public'];
        $commercial = $_POST['is_commercial'];

        $result = $mysqli->query("SELECT Name FROM Property WHERE Name = $name AND NOT (ID = $property_id)");
        if (mysqli_num_rows($result) == 0) {
            $result = $mysqli->query("UPDATE Property SET Name = $name, Street = $address, City = $city, Zip = $zip,
                        Size = $size, IsPublic = $public, IsCommerical = $commercial, ApprovedBy = $admin_username WHERE ID = $property_id");

            foreach ($item as $deleted_items) {
                $delete_result = $mysqli->query("DELETE FROM Has WHERE PropertyID = $property_id AND ItemName = $item");
            }

            foreach ($item as $added_items) {
                $add_result = $mysqli->query("INSERT INTO Has VALUES ($property_id, $item)");
            }
        } else {
            $errormsg = "The name you are changing the property to already exists";
        }
    } else {
        $result = $mysqli->query("DELETE FROM Property WHERE ID = $property_id");
        //TODO add page redirect to viewing page

    }
}

$property = $mysqli->query("SELECT * FROM Property WHERE ID = $property_id");

$farm_items = $mysqli->query("SELECT * FROM FarmItem WHERE Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id)");

?>