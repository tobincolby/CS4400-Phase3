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
    header("Location: ../user/login.php");
    exit();
}

$admin_username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'DELETEACCT') {

        $username = $_POST['username'];
        $result = $mysqli->query("DELETE FROM User WHERE User.Username = '$username'");
        $visitors = $mysqli->query("SELECT * FROM (SELECT User.Username, User.Email, Count(Visit.PropertyID) AS Visits, 
            UserType FROM User LEFT JOIN Visit ON User.Username = Visit.Username GROUP BY User.Username) 
            AS OwnerProperties WHERE UserType = 'VISITOR'");


    } else if ($_POST['form'] == 'DELETELOGS') {
        $username = $_POST['username'];
        $result = $mysqli->query("DELETE FROM Visit WHERE Visit.Username = '$username'");
        $visitors = $mysqli->query("SELECT * FROM (SELECT User.Username, User.Email, Count(Visit.PropertyID) AS Visits, 
              UserType FROM User LEFT JOIN Visit ON User.Username = Visit.Username GROUP BY User.Username) 
              AS OwnerProperties WHERE UserType = 'VISITOR'");
    }
} else {
    $searchtext = "";
    $searchtype = "";
    if (isset($_GET['searchtype'])) {
        $searchtext = "LIKE '%".$_GET['searchtext']."%'";
        $searchtype = "AND ".$_GET['searchtype'];
    }

    $sort_type = "";
    $sort_direction = "";
    if (isset($_GET['sort'])) {
        $sort_type = "ORDER BY ".$_GET['sort'];
        $sort_direction = $_GET['sort_direction'];
    }

    $visitors = $mysqli->query("SELECT * FROM (SELECT User.Username, User.Email, Count(Visit.PropertyID) AS Visits, 
              UserType FROM User LEFT JOIN Visit ON User.Username = Visit.Username GROUP BY User.Username) 
              AS OwnerProperties WHERE UserType = 'VISITOR' $searchtype $searchtext $sort_type $sort_direction");
}





?>

<!Doctype HTML>

<html>
<head>
    <title>Visitors in the System</title>
    <script>
        function onSearchClick() {
            var searchtype = document.getElementById("searchtype").value;
            var searchtext = document.getElementById("searchtext").value;
            window.location.replace("view_visitors.php?searchtype=" + searchtype + "&searchtext="+searchtext);
        }
    </script>
</head>
<body>

<center>
    <h1>Visitors in the System</h1>
    <table>
        <tr><td>Username</td><td>Email</td><td>Logged Visits</td><td>Delete Account?</td><td>Delete Visits?</td></tr>
        <?php
        while ($row = mysqli_fetch_assoc($visitors)) {
            ?>
            <tr>
                <td><?php echo $row['Username']; ?></td>
                <td><?php echo $row['Email']; ?></td>
                <td><?php echo $row['Visits']; ?></td>
                <td>
                    <form id="deleteacct" name="deleteacct" method="post" action="view_visitors.php">
                        <input name="form" id="form" value="DELETEACCT" type="hidden"/>
                        <input name="username" id="username" value="<?php echo $row['Username']; ?>" type="hidden"/>
                        <input type="submit" value="Delete Account"/>

                    </form>
                </td>
                <td>
                    <form id="deletelogs" name="deletelogs" method="post" action="view_visitors.php">
                        <input name="form" id="form" value="DELETELOGS" type="hidden"/>
                        <input name="username" id="username" value="<?php echo $row['Username']; ?>" type="hidden"/>
                        <input type="submit" value="Delete Vists"/>

                    </form>
                </td>


            </tr>

            <?php
        }
        ?>

    </table>

    <br>
    <h3>Search</h3>
    <br>
    <select name="searchtype" id="searchtype">
        <option value="Username">Username</option>
        <option value="Email">Email</option>
    </select>
    <br>
    <input type="text" id="searchtext" name="searchtext" placeholder="Search Term"/>
    <input type="button" value="Search" onclick="onSearchClick()"/>

    <br>
    <a href="../user/mainpage.php">Back</a>

</body>



</center>


</body>


</html>
