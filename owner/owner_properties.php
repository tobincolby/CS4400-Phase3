<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:24 PM
 */


require "../include/connection.php";

if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$owner_username = $_SESSION['username'];

$properties = $mysqli->query("SELECT DISTINCT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.IsCommercial, Property.ID,
                COUNT(*), AVG(Visit.Rating) FROM Property, Visit WHERE Property.Owner = $owner_username AND 
                Property.ID = Visit.PropertyID GROUP BY Property.ID ");

?>