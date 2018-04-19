<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 5:19 PM
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
        $visitors = $mysqli->query("SELECT DISTINCT User.Username, User.Email, Count(*) AS Visits FROM User, Visit WHERE User.UserType = 'VISITOR'
      AND User.Username = Visit.Username GROUP BY User.Username");


    } else if ($_POST['form'] == 'DELETELOGS') {
        $username = $_POST['username'];
        $result = $mysqli->query("DELETE FROM Visit WHERE Visit.Username = $username");
        $visitors = $mysqli->query("SELECT DISTINCT User.Username, User.Email, Count(*) AS Visits FROM User, Visit WHERE User.UserType = 'VISITOR'
      AND User.Username = Visit.Username GROUP BY User.Username");
    }
} else {
    $searchtext = "";
    $searchtype = "";
    if (isset($_GET['searchtype'])) {
        $searchtext = "LIKE %".$_POST['searchtext']."%";
        $searchtype = "AND ".$_POST['searchtype'];
    }

    $sort_type = "";
    $sort_direction = "";
    if (isset($_GET['sort'])) {
        $sort_type = "ORDER BY ".$_GET['sort'];
        $sort_direction = $_GET['sort_direction'];
    }

    $visitors = $mysqli->query("SELECT DISTINCT User.Username, User.Email, Count(*) AS Visits FROM User, Visit WHERE User.UserType = 'VISITOR'
      AND User.Username = Visit.Username $searchtype $searchtext GROUP BY User.Username $sort_type $sort_direction");
}





?>