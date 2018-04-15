<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 6:28 PM
 */


require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$username = $_SESSION['username'];

$visits = $mysqli->query("SELECT VisitDate, Rating, Name, ID AS PropertyID FROM (SELECT Visit.VisitDate, Visit.Rating, Property.Name, Property.ID, Visit.Username FROM Visit JOIN 
            Property ON (Property.ID = Visit.PropertyID)) AS VisitHistory WHERE Username = $username");

?>