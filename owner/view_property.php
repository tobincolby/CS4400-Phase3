<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:44 PM
 */
require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$owner_username = $_SESSION['username'];

$property_id = $_GET['pid'];

$property = $mysqli->query("SELECT DISTINCT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.IsCommercial, Property.ID,
                COUNT(*), AVG(Visit.Rating) FROM Property, Visit WHERE Property.ID = $property_id AND 
                Visit.PropertyID = $property_id GROUP BY Property.ID ");

$farmitems = $mysqli->query("SELECT * FROM FarmItem WHERE FarmItem.Name IN (SELECT Has.FarmItemName FROM HAS WHERE Has.PropertyID = $property_id)");

?>