<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 5:55 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'DELETE') {
        $name = $_POST['name'];
        $result = $mysqli->query("DELETE FROM FarmItem WHERE Name = $name");
        $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1");
    } else if ($_POST['form'] == 'SEARCH') {
        $searchtype = $_POST['searchtype'];
        $searchtext = "%".$_POST['searchtext']."%";
        if ($searchtype == 'Name') {
            $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1 AND Name LIKE $searchtext");
        } else {
            $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1 AND Type LIKE $searchtext");
        }
    } else {
        $type = $_POST['type'];
        $name = $_POST['name'];
        $result = $mysqli->query("INSERT INTO FarmItem VALUES($name, 1, $type)");
        $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1");
    }
} else {
    $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1");

}

?>