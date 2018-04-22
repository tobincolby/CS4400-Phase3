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
    header("Location: ../user/login.php");
    exit();
}

$admin_username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['form'] == 'DELETEACCT') {
        $username = $_POST['username'];
        $result = $mysqli->query("DELETE FROM User WHERE User.Username = '$username'");
        $owners = $mysqli->query("SELECT * FROM (SELECT User.Username, User.Email, Count(Property.ID) AS Properties, 
          UserType FROM User LEFT JOIN Property ON User.Username = Property.Owner GROUP BY User.Username) 
          AS OwnerProperties WHERE UserType = 'OWNER'");
    }

} else {
    $searchtext = "";
    $searchtype = "";
    $searchurl = "";
    if (isset($_GET['searchtype']) && $_GET['searchtype'] != "" && $_GET['searchtext'] != "") {
        $searchtext = "LIKE '%".$_GET['searchtext']."%'";
        $searchtype = "AND ".$_GET['searchtype'];
        $searchurl = "&searchtype=".$_GET['searchtype']."&searchtext=".$_GET['searchtext'];
    }

    $sort_type = "";
    $sort_direction = "";
    if (isset($_GET['sort'])) {
        $sort_type = "ORDER BY ".$_GET['sort'];
        $sort_direction = $_GET['sort_direction'];
    }

    $owners = $mysqli->query("SELECT * FROM (SELECT User.Username, User.Email, Count(Property.ID) AS Properties, 
          UserType FROM User LEFT JOIN Property ON User.Username = Property.Owner GROUP BY User.Username) 
          AS OwnerProperties WHERE UserType = 'OWNER' $searchtype $searchtext $sort_type $sort_direction");
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
    <h1>Owners in the System</h1>
    <table>
        <tr><td>Username
            <br>
                <a href="view_owners.php?sort=Username&sort_direction=ASC<?php echo $searchurl; ?>">ASC</a>
                <a href="view_owners.php?sort=Username&sort_direction=DESC<?php echo $searchurl; ?>">DESC</a>
            </td>
            <td>Email
            <br>
                <a href="view_owners.php?sort=Email&sort_direction=ASC<?php echo $searchurl; ?>">ASC</a>
                <a href="view_owners.php?sort=Email&sort_direction=DESC<?php echo $searchurl; ?>">DESC</a>

            </td>
            <td>Properties
            <br>
                <a href="view_owners.php?sort=Properties&sort_direction=ASC<?php echo $searchurl; ?>">ASC</a>
                <a href="view_owners.php?sort=Properties&sort_direction=DESC<?php echo $searchurl; ?>">DESC</a>
            </td>
            <td>Delete Account?</td></tr>
        <?php
        while ($row = mysqli_fetch_assoc($owners)) {
            ?>
            <tr>
                <td><?php echo $row['Username']; ?></td>
                <td><?php echo $row['Email']; ?></td>
                <td><?php echo $row['Properties']; ?></td>
                <td>
                    <form id="deleteacct" name="deleteacct" method="post" action="view_owners.php">
                        <input name="form" id="form" value="DELETEACCT" type="hidden"/>
                        <input name="username" id="username" value="<?php echo $row['Username']; ?>" type="hidden"/>
                        <input type="submit" value="Delete Account"/>

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
    <input type="text" id="searchtext" name="searchtext"/>
    <input type="button" value="Search" onclick="onSearchClick()"/>

    <br>
    <a href="../user/mainpage.php">Back</a>

</body>



</center>


</body>


</html>
