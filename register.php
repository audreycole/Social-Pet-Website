<?php ob_start(); ?>
<!DOCTYPE HTML>
<html>
      
    <head>
        <title>Neopets - Sign up </title>
        <link rel="stylesheet" type="text/css" href="login.css" >
    </head>
  
    <body>
        <?php    
            if(!empty($_POST)){ 
                require 'database.php';
                   
                   
                $username = $_POST['username'];
                $password = crypt($_POST['password']);
                  
                if(!preg_match('/^[\w_\-]+$/', $username) ){
                    echo "<p class=\"container\"> Invalid username </p>";
                    exit;
                }
                else{ 
                    $stmt = $mysqli->prepare("insert into users (username, crypted_password) values (?, ?)");
                      
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                       
                    $stmt->bind_param('ss', $username, $password);
                       
                    $stmt->execute();
                       
                    $stmt->close();
                      
                     session_start();
                    $_SESSION['username'] = $username;
                     
                    $stmt = $mysqli->prepare("insert into inventory (item_name, user, type, value) values (?, ?, ?,?), (?,?,?,?)");
                      
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                     
                    $item1 = array("name"=> "pasta", "type" => "food", "value"=>150);
                    $item2 = array("name"=> "book", "type" => "play", "value"=>100);
 
                    $stmt->bind_param('sssisssi', $item1['name'], $username, $item1['type'], $item1['value'], $item2['name'], $username, $item2['type'], $item2['value']);
                       
                    $stmt->execute();
                       
                    $stmt->close();
                      
                    header("Location: petMain.php");
                }
                           
            }
            else {
        ?>
            <div class="container">
                <h2> Welcome, new user! Neopets 2.0 is the place to be!</h2>
                <p> We're super excited you want to join our site, so we'll make it super easy for you.
                You don't need to provide extraneous information or sell your soul or anything. Just
                choose a username and password and then you're good to go! </p>
                  
                <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                    <p>
                        <label>Username:</label>
                        <input type="text" name="username" id="username_input" />
                    </p>
                    <p> 
                        <label>Password:</label>
                        <input type="password" name="password" id="password_input" size="20"/>
                    </p>
                    <p>
                        <input type="submit" value="Submit" name="submit" />
                        <input type="reset" />
                    </p>
                </form>
                 
                <p>
                    Already have an account?
                    <button><a href="login.php">Login</a></button>
                </p>
                <p> 
                    Want to play the game without an account?
                    <button><a href="cardGame.php">Play Game</a></button>
                </p>
            </div>
  
        <?php
            }
        ?>
    </body>
</html>