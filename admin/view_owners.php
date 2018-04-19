<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 5:37 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$admin_username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['form'] == 'DELETEACCT') {
        $username = $_POST['username'];
        $result = $mysqli->query("DELETE FROM User WHERE User.Username = $username");
        $owners = $mysqli->query("SELECT User.Username, User.Email, Count(*) AS Properties FROM User, Property WHERE User.UserType = 'OWNER'
          AND User.Username = Property.Owner GROUP BY User.Username");
    }

} else {
    $searchtext = "";
    $searchtype = "";
    if (isset($_GET['searchtype'])) {
        $searchtext = "LIKE %".$_GET['searchtext']."%";
        $searchtype = " AND ".$_GET['searchtype'];
    }

    $sort_type = "";
    $sort_direction = "";
    if (isset($_GET['sort'])) {
        $sort_type = "ORDER BY ".$_GET['sort'];
        $sort_direction = $_GET['sort_direction'];
    }

    $owners = $mysqli->query("SELECT User.Username, User.Email, Count(*) AS Properties FROM User, Property WHERE User.UserType = 'OWNER'
          AND User.Username = Property.Owner $searchtype $searchtext GROUP BY User.Username $sort_type $sort_direction");
}

?>