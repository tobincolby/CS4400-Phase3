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

$owner_username = $_SESSION['username'];

$property_id = $_GET['property_id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form'] == 'modify') {
        $deleted_items = explode(',', $_POST['deleted_items']);
        $added_items = explode(',', $_POST['added_items']);

        $name = $_POST['name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $zip = $_POST['zip'];
        $size = $_POST['size'];

        $public = $_POST['is_public'];
        $commercial = $_POST['is_commercial'];

        $result = $mysqli->query("SELECT Name FROM Property WHERE Name = $name AND NOT (ID = $property_id)");
        if (mysqli_num_rows($result) == 0) {
            $result = $mysqli->query("UPDATE Property SET Name = $name, Street = $address, City = $city, Zip = $zip,
                        Size = $size, IsPublic = $public, IsCommerical = $commercial, ApprovedBy = NULL WHERE ID = $property_id");

            foreach ($item as $deleted_items) {
                $delete_result = $mysqli->query("DELETE FROM Has WHERE PropertyID = $property_id AND ItemName = $item");
            }

            foreach ($item as $added_items) {
                $add_result = $mysqli->query("INSERT INTO Has VALUES ($property_id, $item)");
            }
        } else {
            $errormsg = "The name you are changing the property to already exists";
        }
    } else if ($_POST['form'] == 'request') {
        $new_crop = $_POST['crop_name'];
        $crop_type = $_POST['crop_type'];

        $result = $mysqli->query("INSERT INTO FarmItem VALUES ($new_crop, 0, $crop_type)");

    } else {
        $result = $mysqli->query("DELETE FROM Property WHERE ID = $property_id");
        //TODO add page redirect to viewing page
        header("Location: owner_properties.php");
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

<?php if ($errormsg == "") {
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
                    <td><input type="text" id="addr" name="addr" value="<?php echo $property_row['Street']; ?>"></td>
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
                    <td><label id="prop_type"><?php echo $property_row['PropertyType']; ?> </label></td>
                </tr>
                <tr>
                    <td><label for="prop_id">ID: </label></td>
                    <td><label id="prop_id"><?php echo $property_row['ID']; ?> </label></td>
                </tr>
                <tr>
                    <td><label for="is_public" text-align>Public: </label></td>
                    <td>
                        <select id="is_public">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="is_commercial" text-align>Commercial: </label></td>
                    <td>
                        <select id="is_commercial">
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
                        <th>Animals</th>
                    </tr>
                    <?php while ($animal_row = mysqli_fetch_assoc($farm_items)) {
                        if ($animal_row['Type'] == 'Animal') {
                            ?>
                            <tr id="<?php echo $animal_row['Name'];?>">
                                <th><?php echo $animal_row['Name']; ?></th><th><input type="button" onclick="onRemoveItem('<?php echo $animal_row['Name']; ?>')" value="X"/> </th>
                            </tr>
                        <?php }} ?>

                </table>
            <?php } ?>

            <br>

            <table id="animal_crop_table">

                <tr>
                    <th>Crops</th>
                </tr>
                <?php while ($animal_row = mysqli_fetch_assoc($farm_items)) {
                    if ($animal_row['Type'] != 'Animal') {
                        ?>
                        <tr id="<?php echo $animal_row['Name'];?>">
                            <th><?php echo $animal_row['Name']; ?></th><th><input type="button" onclick="onRemoveItem('<?php echo $animal_row['Name']; ?>')" value="X"/> </th>
                        </tr>
                    <?php }} ?>

            </table>

            <br>

            <table>
                <?php if ($property_row['PropertyType'] == 'FARM') {?>
                    <tr>
                        <td><label for="select_animal" text-align>Add New Animal: </label></td>
                        <td>
                            <select id="animal_type">
                                <?php echo $animal_html; ?>
                            </select>
                        </td>
                        <td><button type="button" class="button">Add Animal</button></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label for="select_crop" text-align>Add New Crop: </label></td>
                    <td>
                        <select id="crop_type">
                            <?php echo $crop_html; ?>
                        </select>
                    </td>
                    <td><button type="button" class="button">Add Crop</button></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Save Changes"/> </td>
                    <td><a href="owner_properties.php">Back</a> </td>
                </tr>

            </table>

        </center>
        <input type="hidden" value="" name="deleted_items", id="deleted_items"/>
        <input type="hidden" value="modify" name="form" id="form"/>
    </form>
    <form method="post" action="manage_property.php?property_id=<?php echo $property_id; ?>">
        <table>

            <tr>
                <td><label for="crop_approval" text-align>Request Farm Item Approval: </label></td>
                <td><input type="text" id="crop_name" name="crop_name"></td>
                <td>
                    <select id="crop_type" name="crop_type">
                        <option value="FRUIT">Fruit</option>
                        <option value="VEGETABLE">Vegetable</option>
                        <option value="NUT">Nut</option>
                        <option value="FLOWER">Flower</option>
                        <option value="ANIMAL">Animal</option>
                    </select>
                </td>
                <td></td><td><input type="submit" value="Make Request"/> </td>
            </tr>
        </table>
        <input type="hidden" name="form" id="form" value="request"/>
    </form>

    <form method="post" action="manage_property.php?property_id=<?php echo $property_id; ?>">
        <input type="hidden" name="form" id="form" value="delete"/>
        <input type="submit" class="button" value="Delete Property"/>


    </form>

</div>

</body>


</html>
