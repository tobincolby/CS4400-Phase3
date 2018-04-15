<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:43 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$owner_username = $_SESSION['username'];

$properties = $mysqli->query("SELECT DISTINCT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.IsCommercial, Property.ID,
                COUNT(*), AVG(Visit.Rating) FROM Property, Visit WHERE NOT (Property.Owner = $owner_username) AND 
                Property.ID = Visit.PropertyID AND Property.ApprovedBy IS NOT NULL GROUP BY Property.ID ");
?>