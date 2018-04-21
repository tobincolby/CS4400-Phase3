<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:43 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] != 1)) {
    //TODO redirect to login page
    header("Location: ../user/login.php");
    exit();
}

$owner_username = $_SESSION['username'];

$sorttype = "";
$sortdirection = "";
if (isset($_GET['sort'])) {
    $sorttype = "ORDER BY ".$_GET['sort'];
    $sortdirection = $_GET['sort_direction'];
}

$searchquery = "";
if (isset($_GET['search'])) {
    $searchtype = $_GET['search'];
    if ($searchtype == 'Size' || $searchtype == 'Visits' || $searchtype == 'Rating') {
        $lowbound = $_GET['lower'];
        $upperbound = $_GET['upper'];
        $searchquery = "AND ".$searchtype." BETWEEN ".$lowbound." AND ".$upperbound;
    } else if ($searchtype == 'Zip') {
        $searchtext = $_GET['searchtext'];
        $searchquery = "AND Zip = ".$searchtext;
    } else {
        $searchtext = "";
        $searchquery = "AND ".$searchtype." LIKE %".$searchtext."%";
    }
}

$properties = $mysqli->query("SELECT * FROM (SELECT Property.Name, Property.Street, Property.City,
                Property.Zip, Property.Size, Property.PropertyType, Property.IsPublic, Property.ApprovedBy, Property.IsCommercial, Property.ID,
                COUNT(Visit.PropertyID) AS Visits, AVG(Visit.Rating) AS Rating, Property.Owner FROM Property  LEFT JOIN Visit ON (Property.ID = Visit.PropertyID) GROUP BY Property.ID) AS PropertyVisit 
                WHERE NOT (Owner = $owner_username) AND ApprovedBy IS NOT NULL $searchquery $sorttype $sortdirection");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Properties</title>

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

        #property_table {

            font-family: Open Sans, Arial;
            border-collapse: collapse;
            width: 100%;
        }

        #property_table td, #property_table th {

            border: 1px solid #000000;
            padding: 8px;

        }

        #property_table tr:hover {background-color: #ddd;}

        #property_table th {

            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;

        }

        body {
            background-color: #5cdb95;
        }

        .button{
            width: 200px;
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
            padding: 10px 10px;
            margin: 8px 0;
            display: inline;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: Open Sans, Arial;
            font-weight: 300;
        }

        .owner_options {
            width: 200px;
        }


    </style>

    <script>

        function onSelectChange() {
            var selectValue = document.getElementById("searchtype").value;
            if (selectValue == "Size" || selectValue == "Rating" || selectValue == "Visits") {
                document.getElementById("lowerbound").style.visibility = "visible";
                document.getElementById("upperbound").style.visibility = "visible";
                document.getElementById("searchtext").style.visibility = "hidden";
            } else {
                document.getElementById("lowerbound").style.visibility = "hidden";
                document.getElementById("upperbound").style.visibility = "hidden";
                document.getElementById("searchtext").style.visibility = "visible";
            }
        }

        function onSearchPressed() {
            var selectValue = document.getElementById("searchtype").value;
            if (selectValue == "Size" || selectValue == "Rating" || selectValue == "Visits") {
                var lowBound = document.getElementById("lowerbound").value;
                var upBound = document.getElementById("upperbound").value;

                window.location.replace("other_owner_properties.php?searchtype="+selectValue+"&upper="+upBound+"&lower="+lowBound);
            } else {
                var searchText = document.getElementById("searchtext").value;
                window.location.replace("other_owner_properties.php?searchtype="+selectValue+"&searchtext="+searchText);
            }
        }

    </script>
</head>


<body>
<h1 id="title"><strong>All Other Properties!</strong></h1>


<table size="100%" id="property_table">

    <tr>
        <th>Name</th>
        <th>Address</th>
        <th>City</th>
        <th>Zip Code</th>
        <th>Size</th>
        <th>Type</th>
        <th>Public</th>
        <th>Commercial</th>
        <th>ID</th>
        <th>Visits</th>
        <th>Avg. Rating</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($properties)) {
        ?>
        <tr>
            <th><a href="view_property.php?property_id=<?php echo $row['ID']; ?>"><?php echo $row['Name']; ?></a></th>
            <th><?php echo $row['Street']; ?></th>
            <th><?php echo $row['City']; ?></th>
            <th><?php echo $row['Zip']; ?></th>
            <th><?php echo $row['Size']; ?></th>
            <th><?php echo $row['PropertyType']; ?></th>
            <th><?php echo $row['IsPublic']; ?></th>
            <th><?php echo $row['IsCommerical']; ?></th>
            <th><?php echo $row['ID']; ?></th>
            <th><?php echo $row['Visits']; ?></th>
            <th><?php echo $row['Rating']; ?></th>
        </tr>
        <?php
    }
    ?>

</table>
<br>

<table width="100%">

    <tr>
        <td>
            <select id="searchtype" class="owner_options" onchange="onSelectChange()">
                <option value="Name">Name</option>
                <option value="Street">Address</option>
                <option value="City">City</option>
                <option value="Zip">Zip</option>
                <option value="Size">Size</option>
                <option value="PropertyType">Type</option>
                <option value="Visits">Visits</option>
                <option value="Rating">Average Rating</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><input type="text" id="searchtext" name="searchtext" placeholder="Search Term" class="owner_options"></td>
        <td><input type="text" id="lowerbound" name="lowerbound" placeholder="Lower Bound of Search" class="owner_options" style="visibility: hidden;"></td>
        <td><input type="text" id="upperbound" name="upperbound" placeholder="Upper Bound of Search" class="owner_options" style="visibility: hidden;"></td>
    </tr>
    <tr>
        <td><button type="button" class="button" onclick="onSearchPressed()">Search Properties</button></td>
        <td><a href="../user/mainpage.php">Back</a></td>
    </tr>



</table>



</body>
</html>
