<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 6:11 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}



$properties = $mysqli->query("SELECT * FROM (SELECT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.ApprovedBy, Property.IsCommercial, Property.ID,
                COUNT(Visit.PropertyID) AS Visits, AVG(Visit.Rating) AS AVGRating FROM Property  LEFT JOIN Visit ON (Property.ID = Visit.PropertyID) GROUP BY Property.ID) AS PropertyVisit 
                WHERE IsPublic = 1 AND ApprovedBy IS NOT NULL");





?>