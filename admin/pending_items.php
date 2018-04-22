<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 5:48 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
    header("Location: ../user/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'DELETE') {
        $name = $_POST['name'];
        $result = $mysqli->query("DELETE FROM FarmItem WHERE Name = '$name'");
    } else {
        $name = $_POST['name'];
        $result = $mysqli->query("UPDATE FarmItem SET IsApproved = 1 WHERE Name = '$name'");
    }

}

$sort_type = "";
$sort_direction = "";
if (isset($_GET['sort'])) {
    $sort_type = "ORDER BY ".$_GET['sort'];
    $sort_direction = $_GET['sort_direction'];
}

$farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 0 $sort_type $sort_direction");


?>

<!Doctype HTML>

<html>
<head>
    <title>Approved Farm Items</title>

    <script>
        function onSearchClick() {
            var searchtype = document.getElementById("searchtype").value;
            var searchtext = document.getElementById("searchtext").value;
            window.location.replace("approved_items.php?searchtype=" + searchtype + "&searchtext="+searchtext);
        }
    </script>
</head>

<body>
<center>
    <h1>Approved Farm Items</h1>
    <table>
        <tr>
            <td>Name</td><td>Type</td><td>X</td><td>Approve?</td>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($farm_items)) {

            ?>
            <tr>
                <td><?php echo $row['Name']; ?></td><td><?php echo $row['Type']; ?></td>
                <td>
                    <form name="delete" action="pending_items_items.php" method="post">
                        <input type="hidden" value="DELETE" name="form" id="form"/>
                        <input type="hidden" value="<?php echo $row['Name']; ?>" name="name" id="name"/>
                        <input type="submit" value="Delete"/>
                    </form>
                </td>
                <td>
                    <form name="approve" action="pending_items.php" method="post">
                        <input type="hidden" value="APPROVE" name="form" id="form"/>
                        <input type="hidden" value="<?php echo $row['Name']; ?>" name="name" id="name"/>
                        <input type="submit" value="Approve"/>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>


    </table>


</center>
<br>
<a href="../user/mainpage.php">Back</a>

</body>





</html>
