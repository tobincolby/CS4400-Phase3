<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/21/18
 * Time: 1:29 PM
 */

require "../include/connection.php";

if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] != 1)) {
    //TODO redirect to login page
    header("Location: ../user/login.php");
    exit();
}

if ($_SESSION['UserType'] == 'ADMIN') {

    ?>

    <!Doctype HTML>
    <html>
    <head>
        <title>Admin Main Page</title>
    </head>
    <body>

    <table>
        <tr><td><a href="../admin/view_visitors.php">View Visitor List</a> </td></tr>
        <tr><td><a href="../admin/view_owners.php">View Owner List</a> </td></tr>
        <tr><td><a href="../admin/unconfirmed_properties.php">View Unconfirmed Properties List</a> </td></tr>
        <tr><td><a href="../admin/confirmed_properties.php">View Confirmed Properties List</a> </td></tr>
        <tr><td><a href="../admin/approved_items.php">View Approved Item List</a> </td></tr>
        <tr><td><a href="../admin/pending_items.php">View Pending Item List</a> </td></tr>
    </table>


    </body>



    </html>


    <?php

} else if ($_SESSION['UserType'] == 'OWNER') {

    header("Location: ../owner/owner_properties.php");
    exit();

} else {
    header("Location: ../visitor/view_properties.php");
    exit();
}

?>
