<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/15/18
 * Time: 4:51 PM
 */

require "../include/connection.php";


if (!(isset($_SESSION['username']) && $_SESSION['logged_in'] == 1)) {
    //TODO redirect to login page
    header("Location: ../user/login.php");
    exit();
}

$admin_username = $_SESSION['username'];

$property_id = $_GET['property_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'modify') {
        $deleted_items = explode(',', $_POST['deleted_items']);
        if ($_POST['property_type'] == 'FARM') {
            $added_items = array($_POST['animal_type'], $_POST['crop_type']);
        } else {
            $added_items = array($_POST['crop_type']);
        }
        $name = $_POST['name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $zip = $_POST['zip'];
        $size = $_POST['size'];

        $public = $_POST['is_public'];
        $commercial = $_POST['is_commercial'];

        $result = $mysqli->query("SELECT Name FROM Property WHERE Name = '$name' AND NOT (ID = $property_id)");
        if (mysqli_num_rows($result) == 0) {
            $result = $mysqli->query("UPDATE Property SET Name = '$name', Street = '$address', City = '$city', Zip = $zip,
                        Size = $size, IsPublic = $public, IsCommercial = $commercial, ApprovedBy = '$admin_username' WHERE ID = $property_id");
            foreach ($deleted_items as $item) {
                if ($item != "")
                $delete_result = $mysqli->query("DELETE FROM Has WHERE PropertyID = $property_id AND ItemName = '$item'");
            }

            foreach ($added_items as $item) {
                if ($item != "")
                $add_result = $mysqli->query("INSERT INTO Has VALUES ($property_id, '$item')");
            }
            if ($_POST['property_type'] == 'FARM') {
                $farm_animal_count = mysqli_fetch_assoc($mysqli->query("SELECT Count(*) AS Items FROM FarmItem WHERE Type = 'ANIMAL' AND Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id)"))["Items"];
                $farm_crop_count = mysqli_fetch_assoc($mysqli->query("SELECT Count(*) AS Items FROM FarmItem WHERE NOT (Type = 'ANIMAL') AND Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id)"))["Items"];
                if ($farm_animal_count == 0 || $farm_crop_count == 0) {
                    foreach ($deleted_items as $item) {
                        if ($item != "0")
                            $add_result = $mysqli->query("INSERT INTO Has VALUES ($property_id, '$item')");
                    }
                    foreach ($added_items as $item) {
                        if ($item != "0")
                            $delete_result = $mysqli->query("DELETE FROM Has WHERE PropertyID = $property_id AND ItemName = '$item'");
                    }
                    $errormsg = "You can't remove all of your items without adding some";
                }
            } else {
                $farm_crop_count = mysqli_fetch_assoc($mysqli->query("SELECT Count(*) AS Items FROM FarmItem WHERE NOT (Type = 'ANIMAL') AND Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id)"))["Items"];
                if ($farm_crop_count == 0) {
                    foreach ($deleted_items as $item) {
                        if ($item != "0")
                            $add_result = $mysqli->query("INSERT INTO Has VALUES ($property_id, '$item')");
                    }
                    foreach ($added_items as $item) {
                        if ($item != "0")
                            $delete_result = $mysqli->query("DELETE FROM Has WHERE PropertyID = $property_id AND ItemName = '$item'");
                    }
                    $errormsg = "You can't remove all of your items without adding some";
                }
            }
        } else {
            $errormsg = "The name you are changing the property to already exists";
        }
    } else {
        $result = $mysqli->query("DELETE FROM Property WHERE ID = $property_id");
        header("Location: ../user/mainpage.php");
        exit();

    }
}

$property = $mysqli->query("SELECT * FROM Property WHERE ID = $property_id");

$property_row = mysqli_fetch_assoc($property);
$farm_items = $mysqli->query("SELECT * FROM FarmItem WHERE Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id)");

if ($property_row['PropertyType'] == 'GARDEN') {
    $crops = $mysqli->query("SELECT Name FROM FarmItem WHERE Type IN ('VEGETABLE', 'FLOWER') AND IsApproved = 1
            AND Name NOT IN (SELECT Name FROM FarmItem WHERE Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id))");
} else if ($property_row['PropertyType'] == 'ORCHARD') {

    $crops = $mysqli->query("SELECT Name FROM FarmItem WHERE Type IN ('NUT', 'FRUIT') AND IsApproved = 1
            AND Name NOT IN (SELECT Name FROM FarmItem WHERE Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id))");
} else {
    $animals = $mysqli->query("SELECT Name FROM FarmItem WHERE Type = 'ANIMAL' AND IsApproved = 1
            AND Name NOT IN (SELECT Name FROM FarmItem WHERE Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id))");
    $crops = $mysqli->query("SELECT Name FROM FarmItem WHERE NOT (Type = 'ANIMAL') AND IsApproved = 1
            AND Name NOT IN (SELECT Name FROM FarmItem WHERE Name IN (SELECT ItemName FROM Has WHERE PropertyID = $property_id))");
    $animal_html = "";
    while ($row = mysqli_fetch_assoc($animals)) {
        $animal_html.= "<option value='".$row['Name']."'>".$row['Name']."</option>";
    }
}

$crop_html = "";
while ($row = mysqli_fetch_assoc($crops)) {
    $crop_html.= "<option value='".$row['Name']."'>".$row['Name']."</option>";
}

$item_animals = "";
$item_crops = "";
while ($farm_item_row = mysqli_fetch_assoc($farm_items)) {
    if ($farm_item_row['Type'] == 'ANIMAL') {
        $item_animals.="<tr id='".$farm_item_row['Name']."'>
                    <th>".$farm_item_row['Name']."</th><th><input type='button' onclick='onRemoveItem(\"".$farm_item_row['Name']."\")' value='X'/> </th>
</tr>";
    } else {
        $item_crops.="<tr id='".$farm_item_row['Name']."'>
                    <th>".$farm_item_row['Name']."</th><th><input type='button' onclick='onRemoveItem(\"".$farm_item_row['Name']."\")' value='X'/> </th>
</tr>";
    }
}

?>

<!Doctype HTML>


<html>
<head>

    <meta charset="UTF-8">
    <title>Manage Property</title>

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

        #animal_crop_table {

            font-family: Open Sans, Arial;
            border-collapse: collapse;
            width: 250px;
        }

        #animal_crop_table td, #animal_crop_table th {

            border: 1px solid #000000;
            padding: 8px;

        }

        #animal_crop_table tr:hover {background-color: #ddd;}

        #animal_crop_table th {

            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;

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
            padding: 5px;
            font-family: Open Sans, Arial;
            font-weight: 200;
        }




    </style>

    <script>

        function onRemoveItem(name) {
            var value = document.getElementById("deleted_items").value;
            if (value == "") {
                value = name;
            } else {
                value = value + "," + name;
            }
            document.getElementById("deleted_items").value = value;
            document.getElementById(name).style.visibility = "hidden";
        }
    </script>
</head>

<body>


<h1 id="title"><strong>Manage <?php echo $property_row['Name']; ?></strong></h1>
<?php if (isset($errormsg) && $errormsg != "") {
    echo "<h3>".$errormsg."</h3>";
}
?>
<div>

    <form id="modify" name="modify" method="post" action="manage_property.php?property_id=<?php echo $property_id; ?>">

        <center>

            <table size="75%">
                <tr>
                    <td><label for="name" text-align>Property Name: </label></td>
                    <td><input type="text" id="name" name="name" value="<?php echo $property_row['Name']; ?>"></td>
                </tr>
                <tr>
                    <td><label for="addr" text-align>Address: </label></td>
                    <td><input type="text" id="address" name="address" value="<?php echo $property_row['Street']; ?>"></td>
                </tr>
                <tr>
                    <td><label for="city" text-align>City: </label></td>
                    <td><input type="text" id="city" name="city" value="<?php echo $property_row['City']; ?>"></td>
                </tr>
                <tr>
                    <td><label for="zip" text-align>Zip Code: </label></td>
                    <td><input type="text" id="zip" name="zip" value="<?php echo $property_row['Zip']; ?>"></td>
                </tr>
                <tr>
                    <td><label for="size" text-align>Size (Acres): </label></td>
                    <td><input type="text" id="size" name="size" value="<?php echo $property_row['Size']; ?>"></td>
                </tr>
                <br>
                <br>
                <tr>
                    <td><label for="prop_type">Type: </label></td>
                    <td><label id="property_type"><?php echo $property_row['PropertyType']; ?> </label></td>
                </tr>
                <tr>
                    <td><label for="prop_id">ID: </label></td>
                    <td><label id="prop_id"><?php echo str_pad($property_row['ID'], 5, '0', STR_PAD_LEFT); ?> </label></td>
                </tr>
                <tr>
                    <td><label for="is_public" text-align>Public: </label></td>
                    <td>
                        <select id="is_public" name="is_public">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="is_commercial" text-align>Commercial: </label></td>
                    <td>
                        <select id="is_commercial" name="is_commercial">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </td>
                </tr>
            </table>

            <br>
            <?php if ($property_row['PropertyType'] == 'FARM') { ?>
            <table id="animal_crop_table">

                <tr>
                    <th>Animals</th><th>X</th>
                </tr>
                <?php echo $item_animals; ?>

            </table>
            <?php } ?>

            <br>

            <table id="animal_crop_table">

                <tr>
                    <th>Crops</th><th>X</th>
                </tr>
                <?php echo $item_crops; ?>

            </table>

            <br>

            <table>
                <?php if ($property_row['PropertyType'] == 'FARM') {?>
                <tr>
                    <td><label for="select_animal" text-align>Add New Animal: </label></td>
                    <td>
                        <select id="animal_type" name="animal_type">
                            <option value="0">Add Nothing</option>
                            <?php echo $animal_html; ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td><label for="select_crop" text-align>Add New Crop: </label></td>
                    <td>
                        <select id="crop_type" name="crop_type">
                            <option value="0">Add Nothing</option>
                            <?php echo $crop_html; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" class="button" value="Save Changes (Confirm Property)"/></td>
                    <td><a href="unconfirmed_properties.php">Back</a> </td>
                </tr>

            </table>

        </center>
        <input type="hidden" value="" name="deleted_items", id="deleted_items"/>
        <input type="hidden" value="modify" name="form" id="form"/>
        <input type="hidden" value="<?php echo $property_row['PropertyType']; ?>" name="property_type" id="property_type"/>
    </form>
    <form id="delete" name="delete" method="post" action="manage_property.php?property_id=<?php echo $property_id;?>">
        <input type="hidden" value="delete" name="form" id="form"/>
    <input type="submit" class="button" value="Delete Property">
    </form>


</div>
<br>
<br>
<br>

</body>


</html>
