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

<!Doctype HTML>


<html>
<head>

    <meta charset="UTF-8">
    <title>ATL Gardens, Farms, and Orchards</title>

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
    width: 250px;
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
    width: 250px;
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


<h1 id="title"><strong>Atlanta Gardens, Farms, and Orchards</strong></h1>
<div>

    <form name="login_form" action="login.php" method="post">
        <center>

        <table size="75%">
            <tr>
                <td><label for="email" text-align>Email:   </label></td>
                <td><input type="text" id="email" name="email" placeholder="50@hardlyknowher.tke" width=300px></td>
            </tr>
            <tr>
                <td><label for="password" text-align>Password: </label></td>
                <td><input type="password" id="password" name="password" placeholder="password" width=300px></td>
            </tr>
        </table>
        </center>
            <br>
            <button type="submit" name="login" class="button">Login</button>
            <button type="button" name="registerVisitor" class="button">Register Visitor</button>
            <button type="button" name="registerOwner" class="button">Register Owner</button>

    </form>

</div>

</body>
</html>

