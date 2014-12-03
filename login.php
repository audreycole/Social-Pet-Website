<?php ob_start(); ?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Neopets - Login</title>
        <link rel="stylesheet" type="text/css" href="login.css" >
    </head>
      
    <body>
  
        <?php
          
            if(!empty($_POST['sign_up'])){
                header("Location: register.php");
            }
              
            else if(!empty($_POST['login'])){
                require 'database.php';
       
                $user = $_POST['username'];
                  
                // Use a prepared statement
                $stmt = $mysqli->prepare("SELECT crypted_password FROM users WHERE username=?");
                   
                // Bind the parameter
                $stmt->bind_param('s', $user);
                  
                $stmt->execute();
                   
                // Bind the results
                $stmt->bind_result($pwd_hash);
                $stmt->fetch();
                   
                $pwd_guess = $_POST['password'];
                // Compare the submitted password to the actual password hash
                if(crypt($pwd_guess, $pwd_hash)==$pwd_hash){
                    session_start();
                    $_SESSION['username'] = $user;
                    $_SESSION['token'] = substr(md5(rand()), 0, 10); // generate a 10-character random string
                    header("Location: neopets.php");
                    exit;
                }else{
                    header("Location: login.php");
                    exit;
                }
                  
            }
              
            else if(isset($_POST['game'])){
                header("Location: cardGame.php");
                exit;
            }
  
            else{ ?>
                <div class="container">
                    <h1> Neopets 2.0! </h1>
                    <hr>
                    <h3> Log in to be able to be a part of the Neopets 2.0 world! </h3>
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
                        <input type="submit" value="Login" name="login" />
                        <input type="reset" />
                    </p>
                    <hr>
                    <p class="left"> Don't have an account? Click here to sign up for free!
                        <input type="submit" value="Sign up" name="sign_up"/>
                    </p>
                    <p class="left"> Or continue to our site where you can still play our game!
                        <input type="submit" value="Play Game" name="game" />
                    </p>
                      
                </form>
            </div>
                  
            <?php
            }   
            ?>
    </body>
  
</html>