<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 6:18 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
    header("Location: ../user/login.php");
    exit();
}

$username = $_SESSION['username'];
$property_id = $_GET['property_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'LOG') {
        $rating = $_POST['rating'];
        if ($rating >=1 && $rating <= 5) {
            $result = $mysqli->query("INSERT INTO Visit VALUES ('$username', $property_id, NOW(), $rating)");
        } else {
            $error_msg = "Rating must be between 1 and 5.";
        }
    } else {
        $result = $mysqli->query("DELETE FROM Visit WHERE Username = '$username' AND PropertyID = $property_id");
    }
}

$property = $mysqli->query("SELECT * FROM (SELECT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.ApprovedBy, Property.IsCommercial, Property.ID,
                COUNT(Visit.PropertyID) AS Visits, AVG(Visit.Rating) AS Rating, Property.Owner FROM Property  LEFT JOIN Visit ON (Property.ID = Visit.PropertyID) GROUP BY Property.ID) AS PropertyVisit 
                WHERE ID = $property_id");

$row = mysqli_fetch_assoc($property);

$owner = $row['Owner'];

$owner_row = mysqli_fetch_assoc($mysqli->query("SELECT * FROM User WHERE Username = '$owner'"));

$farmitems = $mysqli->query("SELECT * FROM FarmItem WHERE FarmItem.Name IN (SELECT Has.ItemName FROM Has WHERE Has.PropertyID = $property_id)");


$result = $mysqli->query("SELECT PropertyID FROM Visit WHERE PropertyID = $property_id AND Username = '$username'");
if (mysqli_num_rows($result) == 0) {
    $loggable = true;
} else {
    $loggable = false;
}

$animals = "";
$crops = "";
while ($farm_row = mysqli_fetch_assoc($farmitems)) {
    if ($farm_row['Type'] == 'ANIMAL') {
        $animals.=" ".$farm_row['Name'].",";
    } else {
        $crops.=" ".$farm_row['Name'].",";
    }
}

?>
<!Doctype HTML>
<html>
<head>
<title>View Property</title>
</head>
<body>
<h1><?php echo $row['Name']; ?> Details</h1>
<br>
<?php if (isset($error_msg) && $error_msg != "") {
  echo "<h3>".$error_msg."</h3>";
}
?>
<table>
    <tr>
        <td>Name:</td><td><?php echo $row['Name']; ?></td>
    </tr>
    <tr>
        <td>Owner:</td><td><?php echo $row['Owner']; ?></td>
    </tr>
    <tr>
        <td>Owner Email:</td><td><?php echo $owner_row['Email']; ?></td>
    </tr>
    <tr>
        <td>Visits:</td><td><?php echo $row['Visits']; ?></td>
    </tr>
    <tr>
        <td>Address:</td><td><?php echo $row['Street']; ?></td>
    </tr>
    <tr>
        <td>City:</td><td><?php echo $row['City']; ?></td>
    </tr>
    <tr>
        <td>Zip:</td><td><?php echo $row['Zip']; ?></td>
    </tr>
    <tr>
        <td>Size:</td><td><?php echo $row['Size']; ?></td>
    </tr>
    <tr>
        <td>Avg. Rating:</td><td><?php echo $row['Rating']; ?></td>
    </tr>
    <tr>
        <td>Type:</td><td><?php echo $row['PropertyType']; ?></td>
    </tr>
    <tr>
        <td>Public:</td><td><?php echo $row['IsPublic'] == 1 ? "True" : "False"; ?></td>
    </tr>
    <tr>
        <td>Commercial:</td><td><?php echo $row['IsCommercial'] == 1 ? "True" : "False"; ?></td>
    </tr>
    <tr>
        <td>ID:</td><td><?php echo str_pad($row['ID'], 5, '0', STR_PAD_LEFT); ?></td>
    </tr>



</table>
<br>
<?php if($row['PropertyType'] == 'FARM') {

    ?>
Animals:
    <?php
        echo $animals;
    ?>
<?php
}
?>
<br>
Crops:
<?php
echo $crops;

if ($loggable) {
    ?>
<form id="log" name="log" method="post" action="view_property.php?property_id=<?php echo $property_id; ?>">

    <input type="hidden" value="LOG" name="form" id="form"/>
    <input type="text" value="" name="rating" id="rating"/>
    <input type="submit" value="Rate Property"/>

</form>
    <?php
} else {
    ?>
    <form id="unlog" name="unlog" method="post" action="view_property.php?property_id=<?php echo $property_id; ?>">

        <input type="hidden" value="UNLOG" name="form" id="form"/>
        <input type="submit" value="Unlog Visit"/>

    </form>
    <?php
}
?>

<a href="../user/mainpage.php">Back</a>

</body>


</html>