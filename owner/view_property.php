<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:44 PM
 */
require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
    header("Location: ../user/login.php");
    exit();
}

$owner_username = $_SESSION['username'];

$property_id = $_GET['pid'];

$property = $mysqli->query("SELECT * FROM (SELECT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.ApprovedBy, Property.IsCommercial, Property.ID,
                COUNT(Visit.PropertyID) AS Visits, AVG(Visit.Rating) AS Rating FROM Property  LEFT JOIN Visit ON (Property.ID = Visit.PropertyID) GROUP BY Property.ID) AS PropertyVisit 
                WHERE ID = $property_id ");

$row = mysqli_fetch_assoc($property);

$farmitems = $mysqli->query("SELECT * FROM FarmItem WHERE FarmItem.Name IN (SELECT Has.FarmItemName FROM HAS WHERE Has.PropertyID = $property_id)");

?>

<!Doctype HTML>
<html>
<head>
    <title>View Property</title>
</head>
<body>
<h1><?php echo $row['Name']; ?> Details</h1>
<br>
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
        <td>ID:</td><td><?php echo $row['ID']; ?></td>
    </tr>



</table>
<br>
<?php if($row['PropertyType'] == 'FARM') {

    ?>
    Animals:
    <?php
    while ($farm_row = mysqli_fetch_assoc($farmitems)) {
        if ($farm_row['Type'] == 'ANIMAL') {
            echo $farm_row['Name'].", ";
        }
    }
    ?>
    <?php
}
?>
<br>
Crops:
<?php
while ($farm_row = mysqli_fetch_assoc($farmitems)) {
    if ($farm_row['Type'] != 'ANIMAL') {
        echo $farm_row['Name'].", ";
    }
}
?>

<a href="other_owner_properties.php">Back</a>

</body>


</html>
