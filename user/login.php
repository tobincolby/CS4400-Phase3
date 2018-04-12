<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/12/18
 * Time: 3:10 PM
 */

require "../include/connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $pass = $_POST['password'];

    $newpass = md5($pass);

    $result = $mysqli->query("SELECT * FROM User WHERE Username=$username AND Password=$newpass LIMIT 1");
    if (mysqli_num_rows($result) == 0) {
        $errormsg = "Login Credentials Invalid";
    } else {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['Username'];
        $_SESSION['email'] = $row["Email"];
        $_SESSION['UserType'] = $row["UserType"];
        $_SESSION['logged_in'] = 1;
        header("Location: mainpage.php");
        exit();
    }
}

?>