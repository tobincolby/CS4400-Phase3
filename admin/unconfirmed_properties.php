<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/20/18
 * Time: 2:58 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$sorttype = "";
$sortdirection = "";
if (isset($_GET['sort'])) {
    $sorttype = "ORDER BY ".$_GET['sort'];
    $sortdirection = $_GET['sort_direction'];
}

$searchquery = "";
if (isset($_GET['search'])) {
    $searchtype = $_GET['search'];
    if ($searchtype == 'Size' || $searchtype == 'Visits' || $searchtype == 'Rating') {
        $lowbound = $_GET['lower'];
        $upperbound = $_GET['upper'];
        $searchquery = "AND ".$searchtype." BETWEEN ".$lowbound." AND ".$upperbound;
    } else if ($searchtype == 'Zip') {
        $searchtext = $_GET['searchtext'];
        $searchquery = "AND Zip = ".$searchtext;
    } else {
        $searchtext = "";
        $searchquery = "AND ".$searchtype." LIKE %".$searchtext."%";
    }
}

$properties = $mysqli->query("SELECT * FROM (SELECT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.ApprovedBy, Property.IsCommercial, Property.ID,
                COUNT(Visit.PropertyID) AS Visits, AVG(Visit.Rating) AS Rating, Property.Owner FROM Property  LEFT JOIN Visit ON (Property.ID = Visit.PropertyID) GROUP BY Property.ID) AS PropertyVisit 
                WHERE ApprovedBy IS NULL $searchquery $sorttype $sortdirection");

?>