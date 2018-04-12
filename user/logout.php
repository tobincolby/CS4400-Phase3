<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/12/18
 * Time: 3:35 PM
 */

require "../include/connection.php";

$_SESSION['logged_in'] = 0;
session_destroy();

header("Location: login.php");
exit();

?>