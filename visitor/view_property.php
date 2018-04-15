<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 6:18 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pid = $_POST['property_id'];
    if ($_POST['form'] == 'LOG') {
        $rating = $_POST['rating'];
        $result = $mysqli->query("INSERT INTO Visit VALUES ($username, $pid, NOW(), $pid)");
    } else {
        $result = $mysqli->query("DELETE FROM Visit WHERE Username = $username AND PropertyID = $pid");
    }
} else {
    $pid = $_POST['property_id'];
}

$property = $mysqli->query("SELECT DISTINCT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.IsCommercial, Property.ID,
                COUNT(*), AVG(Visit.Rating) FROM Property, Visit WHERE Property.ID = $property_id AND 
                Visit.PropertyID = $property_id GROUP BY Property.ID ");

$farmitems = $mysqli->query("SELECT * FROM FarmItem WHERE FarmItem.Name IN (SELECT Has.FarmItemName FROM HAS WHERE Has.PropertyID = $property_id)");


$result = $mysqli->query("SELECT COUNT(*) FROM Visit WHERE PropertyID = $pid AND Username = $username");
if (mysqli_num_rows($result) == 0) {
    $loggable = true;
} else {
    $loggable = false;
}

?>