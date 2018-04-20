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

$property = $mysqli->query("SELECT * FROM (SELECT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.ApprovedBy, Property.IsCommercial, Property.ID,
                COUNT(Visit.PropertyID) AS Visits, AVG(Visit.Rating) AS Rating FROM Property  LEFT JOIN Visit ON (Property.ID = Visit.PropertyID) GROUP BY Property.ID) AS PropertyVisit 
                WHERE ID = $property_id ");

$farmitems = $mysqli->query("SELECT * FROM FarmItem WHERE FarmItem.Name IN (SELECT Has.FarmItemName FROM HAS WHERE Has.PropertyID = $property_id)");

?>