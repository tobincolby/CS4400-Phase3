<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:51 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$owner_username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_id = $_POST['property_id'];
} else {
    $property_id = $_GET['property_id'];

}

//TODO FINISH THIS PAGE
?>