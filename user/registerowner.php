<?php
/**
 * Created by PhpStorm.
 * User: colby
 * Date: 4/12/18
 * Time: 3:52 PM
 */


require "../include/connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false && strlen($password) >= 8) {

        if ($password != $confirm_pass) {
            $error_msg = "Passwords Don't Match";
        } else {
            $result = $mysqli->query("SELECT Username FROM User WHERE Username='$username' OR Email='$email'");
            if (mysqli_num_rows($result) != 0) {
                $error_msg = "Email/Username Already exists";
            } else {

                $newpass = md5($password);

                //Add the Owner's Property
                $property_name = $_POST['property_name'];
                $street_address = $_POST['address'];
                $city = $_POST['city'];
                $zip = $_POST['zip'];
                $size = $_POST['size'];
                $property_type = $_POST['property_type'];
                $is_public = $_POST['is_public'];
                $is_commercial = $_POST['is_commercial'];
                if ($property_type == 'FARM') {
                    $farm_items = array($_POST['animal_type'], $_POST['farm_type']);
                } else if ($property_type == 'GARDEN') {
                    $farm_items = array($_POST['garden_type']);
                } else {
                    $farm_items = array($_POST['orchard_type']);
                }
                $result = $mysqli->query("SELECT Name FROM Property WHERE Name='$property_name'");
                if (mysqli_num_rows($result) > 0) {
                    $error_msg = "Property Name Already Exists";
                } else {
                    $result = $mysqli->query("INSERT INTO User VALUES ('$username', '$email', '$newpass', 'OWNER')");

                    $result = $mysqli->query("SELECT MAX(ID) AS ID FROM Property");
                    $new_id = mysqli_fetch_assoc($result)["ID"] + 1;
                    $owner_id = $username;
                    $result = $mysqli->query("INSERT INTO Property (ID, Name, Size, IsCommercial, IsPublic, Street, City, Zip, PropertyType, Owner, ApprovedBy)
                                                VALUES ('$new_id', '$property_name', '$size', '$is_commercial', '$is_public', '$street_address', '$city', '$zip', '$property_type', '$owner_id', NULL)");
                    foreach ($farm_items as $farm_item) {
                        $result = $mysqli->query("INSERT INTO Has VALUES($new_id, '$farm_item')");
                    }
                    header("Location: login.php"); /* Redirect browser */
                    exit();
                }

            }

        }
    } else {
        $error_msg = "Your Email must be valid and your password must be at least 8 characters";
    }

}

$garden_crops = $mysqli->query("SELECT Name FROM FarmItem WHERE Type IN ('VEGETABLE', 'FLOWER') AND IsApproved = 1");

$orchard_crops = $mysqli->query("SELECT Name FROM FarmItem WHERE Type IN ('NUT', 'FRUIT') AND IsApproved = 1");

$farm_animals = $mysqli->query("SELECT Name FROM FarmItem WHERE Type = 'ANIMAL' AND IsApproved = 1");
$farm_crops = $mysqli->query("SELECT Name FROM FarmItem WHERE NOT (Type = 'ANIMAL') AND IsApproved = 1");

$garden_html = "";
while ($row = mysqli_fetch_assoc($garden_crops)) {
    $garden_html.= "<option value='".$row['Name']."'>".$row['Name']."</option>";
}

$orchard_html = "";
while ($orchardrow = mysqli_fetch_assoc($orchard_crops)) {
    $orchard_html.= "<option value='".$orchardrow['Name']."'>".$orchardrow['Name']."</option>";
}

$farm_html = "";
while ($farmrow = mysqli_fetch_assoc($farm_crops)) {
    $farm_html.= "<option value='".$farmrow['Name']."'>".$farmrow['Name']."</option>";
}

$animal_html = "";
while ($animalrow = mysqli_fetch_assoc($farm_animals)) {
    $animal_html.= "<option value='".$animalrow['Name']."'>".$animalrow['Name']."</option>";
}

?>

<!Doctype HTML>


<html>
<head>

    <meta charset="UTF-8">
    <title>Owner Registration</title>

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

        input[type=text].text1 {
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

        input[type=text].text2 {
            width: 95px;
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

        function onSelectChange() {
            var selectValue = document.getElementById("property_type").value;
            if (selectValue == "FARM") {
                document.getElementById("animal_type").style.visibility = "visible";
                document.getElementById("farm_type").style.visibility = "visible";
                document.getElementById("garden_type").style.visibility = "hidden";
                document.getElementById("orchard_type").style.visibility = "hidden";


            } else if (selectValue == "GARDEN") {
                document.getElementById("animal_type").style.visibility = "hidden";
                document.getElementById("farm_type").style.visibility = "hidden";
                document.getElementById("garden_type").style.visibility = "visible";
                document.getElementById("orchard_type").style.visibility = "hidden";

            } else {
                document.getElementById("animal_type").style.visibility = "hidden";
                document.getElementById("farm_type").style.visibility = "hidden";
                document.getElementById("garden_type").style.visibility = "hidden";
                document.getElementById("orchard_type").style.visibility = "visible";
            }
        }

    </script>
</head>

<body>


<h1 id="title"><strong>New Owner Registration</strong></h1>
<?php if (isset($error_msg) && $error_msg != "") {
  echo "<h3>".$error_msg."</h3>";
}

?>
<div>
    <input type = "hidden" value="<?php echo $garden_html; ?>" id="garden_html"/>
    <input type = "hidden" value="<?php echo $orchard_html; ?>" id="orchard_html"/>
    <input type = "hidden" value="<?php echo $farm_html; ?>" id="farm_html"/>

    <form id="registerowner" name="registerowner" method="post" action="registerowner.php">
        <center>

            <table size="75%">
                <tr>
                    <td><label for="email" text-align>Email*: </label></td>
                    <td><input type="text" id="email" name="email" placeholder="50@hardlyknowher.tke" class="text1"></td>
                </tr>
                <tr>
                    <td><label for="username" text-align>Username*: </label></td>
                    <td><input type="text" id="username" name="username" placeholder="joe" class="text1"></td>
                </tr>
                <tr>
                    <td><label for="password" text-align>Password*: </label></td>
                    <td><input type="password" id="password" name="password" placeholder="password" class="text1"></td>
                </tr>
                <tr>
                    <td><label for="confirm_password" text-align>Confirm Password*: </label></td>
                    <td><input type="password" id="confirm_password" name="confirm_password" placeholder="password" class="text1"></td>
                </tr>
                <tr>
                    <td><label for="property_name" text-align>Property Name*: </label></td>
                    <td><input type="text" id="property_name" name="property_name" placeholder="Anarchy Acres" class="text1"></td>
                </tr>
                <tr>
                    <td><label for="street_addr" text-align>Street Address*: </label></td>
                    <td><input type="text" id="address" name="address" placeholder="159 5th Street NW" class="text1"></td>
                </tr>
            </table>
            <table size="75%">

                <tr>
                    <td><label for="city" text-align>City*: </label></td>
                    <td><input type="text" id="city" name="city" placeholder="Atlanta" class="text2"></td>
                    <td><label for="zip" text-align>Zip*: </label></td>
                    <td><input type="text" id="zip" name="zip" placeholder="30313" class="text2"></td>
                    <td><label for="size" text-align>Acres*: </label></td>
                    <td><input type="text" id="size" name="size" placeholder="100" class="text2"></td>
                </tr>
                <tr>
                    <td><label for="property_type" text-align>Property Type*: </label></td>
                    <td>
                        <select id="property_type" name="property_type" onchange="onSelectChange()">
                            <option value="GARDEN">Garden</option>
                            <option value="ORCHARD">Orchard</option>
                            <option value="FARM">Farm</option>
                        </select>
                    </td>

                    <td><label for="animal_type" text-align >Animal*: </label></td>
                    <td>
                        <select id="animal_type" name="animal_type" style="visibility: hidden">
                            <?php echo $animal_html; ?>
                        </select>
                    </td>

                    <td><label for="farm_type" text-align>Crop*: </label></td>
                    <td>
                        <select id="farm_type" name="farm_type" style="visibility: hidden">
                            <?php
                            echo $farm_html;
                            ?>
                        </select>
                    </td>
                    <td>
                        <select id="garden_type" name="garden_type">
                            <?php
                            echo $garden_html;
                            ?>
                        </select>
                    </td>
                    <td>
                        <select id="orchard_type" name="orchard_type" style="visibility: hidden;">
                            <?php
                            echo $orchard_html;
                            ?>
                        </select>
                    </td>

                </tr>
                <tr>
                    <td><label for="is_public" text-align>Public?*: </label></td>
                    <td>
                        <select id="is_public" name="is_public">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="is_commercial" text-align>Commercial?*: </label></td>
                    <td>
                        <select id="is_commercial" name="is_commercial">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </td>
                </tr>

            </table>
        </center>
        <br>

        <input type="submit" name="register" class="button" value="Register"/>
        <a href="login.php">Cancel</a>

    </form>

</div>

<br/>
<br/>
<br/>
<hr/>


</body>


</html>


