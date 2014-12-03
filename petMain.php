<?php ob_start(); ?>
<html>
    <head>
        <title> Pet Creation </title>
        <link rel="stylesheet" type="text/css" href="login.css" />
    </head>
  
    <body>
        <?php
            session_start();
            if(!(isset($_SESSION['username']))){
                $_SESSION['username'] = "not_registered";
            }
            $username = $_SESSION['username'];
            echo $username;
            require 'database.php';
               
            //check to see if there is a pet belonging to that user already 
            $stmt = $mysqli->prepare("select name, gender from pets where owner=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
               
            $stmt->bind_param('s', $username);
 
            $stmt->execute();
               
            $stmt->bind_result($petName, $gender);
             
            $stmt->fetch();
             
            $stmt->close();
 
            $Pet = htmlspecialchars($petName);
             
            //if a registered user is logged in and they don't have a pet already, let them make one
            if($username != "not_registered" && $Pet==""){
        ?>
                <h1>Time to make your pet!</h1>
                <hr>
                <br>
                <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                        <p>
                            <label>Pet Name:</label>
                            <input type="text" name="petName" id="petName" />
                        </p>
                        <!--put in images of bun and cat -->
  
                        <p> 
                            <label>Type:</label>
                            <select name="type">
                              <option value="Bun">Bun</option>
                              <option value="Cat">Cat</option>
                            </select>
                        </p>
                        <p> 
                            <label>Color:</label>
                            <select name="color">
                              <option value="yellow">Yellow</option>
                              <option value="blue">Blue</option>
                            </select>
                        </p>
                        <p> 
                            <label>Gender:</label>
                            <select name="gender">
                              <option value="f">female</option>
                              <option value="m">male</option>
                            </select>
                        </p>
                        <p> 
                            <label>Favorite Activity:</label>
                            <select name="activity">
                              <option value="Making new friends!">Making new friends!</option>
                              <option value="Swimming in the sea!">Swimming in the sea!</option>
                              <option value="Cuddling with you!">Cuddling with you!</option>
                              <option value="Painting a masterpiece!">Painting a masterpiece!</option>
                            </select>
                        </p>
                        <p>
                            <input type="submit" value="Finish" name="Finish" />
                            <input type="reset"/>
                        </p>
                        <hr>
                </form>
        <?php                      
                 //if we don't have a name (which we must for insertion to be successful
                if(!(isset($_POST['petName']))){
                        exit;
                }
                  
                $owner = $_SESSION['username'];
                echo $owner;
                   
                $stmt = $mysqli->prepare("insert into pets (name, owner, gender, activity, color, status, type, last_fed, last_played) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    //header("Location: comment.php");
                    exit;
                }
                //figure out what the image_id is based on type and color of pet
               /* $image_id = "yellowHappyBun.jpg";
                if($_POST['color'] == "blue" && $_POST['type'] == "bun") {
                    $image_id = "blueHappyBun.jpg";
                } else if ($_POST['color'] == "blue" && $_POST['type'] == "cat") {
                    $image_id = "blueHappyCat.jpg";
                } else if ($_POST['color'] == "yellow" && $_POST['type'] == "cat") {
                    $image_id = "yellowHappyCat.jpg";
                } */
                 
                $phptime = time();
                date_default_timezone_set('America/Chicago');
                $mysqltime = date ("Y-m-d H:i:s", $phptime);
                $status = "Happy";
  
                $stmt->bind_param('sssssssss', $_POST['petName'], $_SESSION['username'], $_POST['gender'], $_POST['activity'], $_POST['color'], $status, $_POST['type'], $time, $time);
                   
                $stmt->execute();
                   
                $stmt->close();
  
                //send them to their homepage with their new pet! --> yet to be made
                header("Location: neopets.php");
  
            }
            //if they already have a pet, send them to their home page ---> yet to be made! 
            else if ($username != "not_registered" && $Pet != ""){
                header("Location: neopets.php");
            } 
            //if they're not registered send them to the game page
            else {
               header("Location: neopets.php");
            }
        ?>
        </body>
</html>