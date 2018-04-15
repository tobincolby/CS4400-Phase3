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
        $owners = $mysqli->query("SELECT User.Username, User.Email, Count(*) FROM User, Property WHERE User.UserType = 'OWNER'
          AND User.Username = Property.Owner GROUP BY User.Username");
    } else {
        $searchtext = "%".$_POST['searchtext']."%";
        $searchtype = $_POST['searchtype'];
        if ($searchtype == 'Email') {
            $owners = $mysqli->query("SELECT User.Username, User.Email, Count(*) FROM User, Property WHERE User.UserType = 'OWNER'
          AND User.Username = Property.Owner AND User.Email LIKE $searchtext GROUP BY User.Username");
        } else {
            $owners = $mysqli->query("SELECT User.Username, User.Email, Count(*) FROM User, Property WHERE User.UserType = 'OWNER'
          AND User.Username = Property.Owner AND User.Username LIKE $searchtext GROUP BY User.Username");
        }
    }

} else {
    $owners = $mysqli->query("SELECT User.Username, User.Email, Count(*) FROM User, Property WHERE User.UserType = 'OWNER'
          AND User.Username = Property.Owner GROUP BY User.Username");
}

?>