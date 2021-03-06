<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 5:55 PM
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
        $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1");
    }  else {
        $type = $_POST['type'];
        $name = $_POST['name'];
        $result = $mysqli->query("INSERT INTO FarmItem VALUES('$name', 1, '$type')");
        if (!$result) {
            $errormsg = "That animal name already exists";
        }
        $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1");
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


    $farm_items = $mysqli->query("SELECT Name, Type FROM FarmItem WHERE IsApproved = 1 $searchtype $searchtext $sort_type $sort_direction");

}




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
    <?php
    if (isset($errormsg) && $errormsg != "") {
        echo "<h3>".$errormsg."</h3>";
    }

    ?>
    <table>
        <tr>
            <td>Name
                <br>
                <a href="approved_items.php?sort=Name&sort_direction=ASC<?php echo $searchurl; ?>">ASC</a>
                <a href="approved_items.php?sort=Name&sort_direction=DESC<?php echo $searchurl; ?>">DESC</a>

            </td>
            <td>Type
                <br>
                <a href="approved_items.php?sort=Type&sort_direction=ASC<?php echo $searchurl; ?>">ASC</a>
                <a href="approved_items.php?sort=Type&sort_direction=DESC<?php echo $searchurl; ?>">DESC</a>
            </td>
            <td>X</td>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($farm_items)) {

            ?>
            <tr>
                <td><?php echo $row['Name']; ?></td><td><?php echo $row['Type']; ?></td>
                <td>
                    <form name="delete" action="approved_items.php" method="post">
                        <input type="hidden" value="DELETE" name="form" id="form"/>
                        <input type="hidden" value="<?php echo $row['Name']; ?>" name="name" id="name"/>
                        <input type="submit" value="Delete"/>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>


    </table>


</center>
<br>
<h3>Approve Items</h3>
<form name="approve" action="approved_items.php" method="post">

    <select name="type" id="type">
        <option value="ANIMAL">Animal</option>
        <option value="FRUIT">Fruit</option>
        <option value="NUT">Nut</option>
        <option value="VEGETABLE">Vegetable</option>
        <option value="FLOWER">Flower</option>
    </select>
    <br>
    <input type="text" placeholder="Item Name" id="name" name="name"/>
    <input type="hidden" name="form" id="form" value="Approve"/>
    <input type="submit" value="Add To Approved List"/>

</form>
<br>
<h3>Search</h3>
<br>
<select name="searchtype" id="searchtype">
    <option value="Name">Name</option>
    <option value="Type">Type</option>
</select>
<br>
<input type="text" id="searchtext" name="searchtext"/>
<input type="button" value="Search" onclick="onSearchClick()"/>

<br>
<a href="../user/mainpage.php">Back</a>

<br>
<br>
<br>
</body>





</html>
