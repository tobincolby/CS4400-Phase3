<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 5:48 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'DELETE') {
        $name = $_POST['name'];
        $result = $mysqli->query("DELETE FROM FarmItem WHERE Name = $name");
    } else {
        $name = $_POST['name'];
        $result = $mysqli->query("UPDATE FarmItem SET IsApproved = 1 WHERE Name = $name");
    }

}

$sort_type = "";
$sort_direction = "";
if (isset($_GET['sort'])) {
    $sort_type = "ORDER BY ".$_GET['sort'];
    $sort_direction = $_GET['sort_direction'];
}

$farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 0 $sort_type $sort_direction");


?>