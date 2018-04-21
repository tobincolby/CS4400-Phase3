
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
    $confirm_pass = $_POST['confirm_password'];

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Registration</title>

    <style>
        #title {
            text-align:center;
            padding:40px;
            color: #edf5e1;
            text-shadow: 2px 2px #000000;
            font-family: Open Sans, Arial;
            font-weight: 300;
            font-size: 3em;

        }

        body {
            background-color: #5cdb95;
        }

        form {
            text-align:center;

        }

        .button{
            background-color: #edf5e1;
            border: none;
            color: #000000;
            padding: 10px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        div {

            border-radius: 5px;
            padding: 20px;

        }

        input[type=text] {
            width: 400px;
            padding: 20px 10px;
            margin: 8px 0;
            display: inline;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: Open Sans, Arial;
            font-weight: 300;
        }

        input[type=password] {
            width: 400px;
            padding: 20px 10px;
            margin: 8px 0;
            display: inline;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: Open Sans, Arial;
            font-weight: 300;
        }

        label {
            font-family: Open Sans, Arial;
            font-weight: 200;
        }

    </style>

</head>
<body>

<h1 id="title"><strong>New Visitor Registration</strong></h1>
<br>
<?php if ($error_msg != "") {
    echo "<h4>".$error_msg."</h4><br>";
}
?>
<div>

    <form name="register" id="register" method="post" action="registervisitor.php">

        <center>

            <table size="75%">
                <tr>
                    <td><label for="email" text-align>Email*:   </label></td>
                    <td><input type="text" id="email" name="email" placeholder="50@hardlyknowher.tke" width=300px></td>
                </tr>
                <tr>
                    <td><label for="username" text-align>Username*: </label></td>
                    <td><input type="text" id="username" name="username" placeholder="joe" width=300px></td>
                </tr>
                <tr>
                    <td><label for="password" text-align>Password*: </label></td>
                    <td><input type="password" id="password" name="password" placeholder="password" width=300px></td>
                </tr>
                <tr>
                    <td><label for="confirm_password" text-align>Confirm Password*: </label></td>
                    <td><input type="password" id="confirm_password" name="confirm_password" placeholder="password" width=300px></td>
                </tr>
            </table>

        </center>

        <button type="button" name="register" class="button">Register Visitor</button>
        <button type="button" name="login" class="button">Cancel</button>

    </form>

</div>


</body>
</html>
