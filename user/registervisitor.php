
<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/12/18
 * Time: 3:13 PM
 */

require "../include/connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_pass = $_POST['confirm_pass'];

    if ($password != $confirm_pass) {
        $error_msg = "Passwords Don't Match";
    } else {
        $result = $mysqli->query("SELECT username FROM User WHERE Username=$username OR Email=$email");
        if (mysqli_num_rows($result) != 0) {
            $error_msg = "Email/Username Already exists";
        } else {

            $newpass = md5($password);
            $result = $mysqli->query("INSERT INTO User VALUES ($username, $email, $newpass, 'VISITOR')");
            header("Location: login.php"); /* Redirect browser */
            exit();

        }

    }

}

?>