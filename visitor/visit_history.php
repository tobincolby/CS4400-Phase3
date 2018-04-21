<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 6:28 PM
 */


require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
}

$username = $_SESSION['username'];

if (isset($_GET['sort'])) {
    $sort_type = $_GET['sort'];
    $sort_direction = $_GET['sort_direction'];

    $visits = $mysqli->query("SELECT VisitDate, Rating, Name, ID AS PropertyID FROM (SELECT Visit.VisitDate, Visit.Rating, Property.Name, Property.ID, Visit.Username FROM Visit JOIN 
            Property ON (Property.ID = Visit.PropertyID)) AS VisitHistory WHERE Username = $username ORDER BY $sort_type $sort_direction");
} else {

    $visits = $mysqli->query("SELECT VisitDate, Rating, Name, ID AS PropertyID FROM (SELECT Visit.VisitDate, Visit.Rating, Property.Name, Property.ID, Visit.Username FROM Visit JOIN 
            Property ON (Property.ID = Visit.PropertyID)) AS VisitHistory WHERE Username = $username");
}

?>

<!Doctype HTML>
<html>
<head>
    <title>Visit History</title>
</head>
<body>
<h1>Your History</h1>
<hr>
<table>
    <tr>
        <td>Name</td><td>Date Logged</td><td>Rating</td>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($visits)) {

        ?>
        <tr>
            <td><a href="view_property.php?property_id=<?php echo $row['PropertyID']; ?>"><?php echo $row['Name']; ?></a> </td><td><?php echo $row['VisitDate']; ?></td><td><?php echo $row['Rating']; ?></td>
        </tr>
        <?php
    }
    ?>

</table>
<br>
<a href="../user/mainpage.php">Back</a>

</body>


</html>
