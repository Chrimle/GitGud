<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="stylebuttons.css">
		<link rel="stylesheet" href="styleauthsvar.css">
    </head>
    <body class="body">
		<?php
		session_start();

		if ($_SESSION["loginT"]) { 
            
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "bildr";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $conn->set_charset("utf8");
            $sql = "SELECT bildid From imagedata";
            $result = $conn->query($sql);

            $newimageid = base_convert(rand(0, 1679615), 10, 36);
            $imageids = array();

            if ($result === false) {
                die(mysqli_error($conn)); 
            }
            elseif ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
                    array_push($imageids, $row["bildid"]);
				}
                $newimageid = checkimageid($imageids, $newimageid);

                $target_dir = "images/";
                $target_file = $target_dir . basename($newimageid);
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                // Check if image file is a actual image or fake image
                if(isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["image"]["tmp_name"]);
                    if($check !== false) {
                        echo "<h2>File is an image - " . $check["mime"] . ".</h2>";
                        $uploadOk = 1;
                    } else {
                        echo "<h2>File is not an image.</h2>";
                        $uploadOk = 0;
                    }
                }
                // Check file size
                if ($_FILES["image"]["size"] > 1999999) {
                    echo "<h2>Sorry, your file is too large.</h2>";
                    $uploadOk = 0;
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "<h2>Sorry, your file was not uploaded.</h2>";
                // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        echo "<h2>The file ". basename( $_FILES["image"]["name"]). " has been uploaded.</h2>";
                    } else {
                        echo "<h2>Din bild är för stor!</h2>";
                    }
                }
                $sql = "INSERT INTO imagedata VALUES ('".$newimageid."','". $_POST["title"] ."', '". $_POST["description"] ."', '". $_SESSION["userid"] ."', '". date("Y-m-d H:i:s") ."', ".$_POST["radio"].");"; //to do, make säker, förklara vad du vill göra /hugo
                $svar = mysqli_query($conn, $sql);
                echo "<h2>Bilden finns nu i databasen! </h2> <br>";
                echo '<a href="allimages.php"><h2>Kolla alla bilder här!</h2></a>';
			}
        }
        else {
            header("Location: login.html");
            die();
        }

        function checkimageid($db, $co){
                foreach ($db as $key => $value){
                    if ($value == $co){
                        $newimageid = base_convert(rand(0, 1679615), 10, 36);
                        checkuserid($imageids, $newimageid);
                    }
                    else{
                        return $co;
                    }
                }
            }

		?>		
        <img class="logo" src="bildr.png"/>
	</body>
</html>


