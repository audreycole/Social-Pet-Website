<?php ob_start(); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>HalaudreyPets</title>
		<!--<link rel="stylesheet" href="login.css" type="text/css" /> -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	</head>
	
	<body>
		<?php
			session_start();
			
			if(!(isset($_SESSION['username']))){
				$_SESSION['username'] = "not_registered";
			}
			$user = $_SESSION['username'];
			//if a registered user is logged in, let them post/logout
			if($user != "not_registered"){
			
		?>
				<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
					<p>
						<input type="submit" value="Mail" name="inbox" class="right" />
						<input type="submit" value="Inventory" name="inventory" class="right"/>
						<input type="submit" value="Marketplace" name="market" class="right"/>	
						<input type="submit" value="Play Game" name="game" class="right" />
						<input type="submit" value="Logout" name="logout" class="right" />
					</p>
				</form>
		<?php
			}
			
			//otherwise, their options other than viewing are to log in or sign out
			else {
		?>
			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
				<p>
					<input type="submit" value="Login" name="login" class="right"/>
					<input type="submit" value="Sign Up" name="sign_up" class="right" />
				</p>
			</form>
		<?php
			}
			
			//clicking log in lets you do so
			if(isset($_POST['login'])){
				header("Location: login.php");
				exit;
			}
			
			//clicking sign up lets you do so
			else if(isset($_POST['sign_up'])){
				header("Location: register.php");
				exit;
			}
			else if(isset($_POST['inventory'])){
				header("Location: inventory.php");
				exit;
			}
			else if(isset($_POST['inbox'])){
				header("Location: inbox.php");
				exit;
			}
			else if(isset($_POST['market'])){
				header("Location: marketplace.php");
				exit;
			}
			else if(isset($_POST['game'])){
				header("Location: cardGame.php");
				exit;
			}
			
			//clicking logout, ends current session and starts a new one as the "not_registered" user
			//so like, you can keep viewing, or re-login to do stuffs, or switch users or what have you
			else if(isset($_POST['logout'])){
				session_destroy();
				session_start();
				$_SESSION['username'] = "not_registered";
				$_SESSION['token'] = substr(md5(rand()), 0, 10); // generate a 10-character random string
				header("Location: neopets.php");
				exit;
			}
		?>
		
		<h1 id="greeting">Welcome to HalaudreyPets</h1>
		
		<div class="pet">
			<?php 
				require 'database.php';
				
				// get basic pet info
				$stmt = $mysqli->prepare("select name, gender, color, type, DAY(last_fed), DAY(last_played) from pets where owner=?");
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				  
				$stmt->bind_param('s', $user);
				$stmt->execute();
				  
				$stmt->bind_result($petName, $gender, $color, $type, $hunger, $mood);
				
				$stmt->fetch();
				
				$stmt->close();
				
				//if user has pet, show them their pet
				if($petName != ""){
					$phptime = time();
					date_default_timezone_set('America/Chicago');
					$mysqltime = date ("d", $phptime);
					
					if($mysqltime-$hunger >= 1){
						$hungry = true;
						$status = "Sad";	
					}
					else{
						$hungry = false;
						$status = "Happy";
					}
					
					if($mysqltime-$mood >= 1){
						$badMood = true;
						$status = "Sad";
					}
					else {
						$badMood = false;
						$status = "Happy";
					}
					
					// update info for mood/hunger values
					$stmt = $mysqli->prepare("update pets set status=? where owner=?");
					if(!$stmt){
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}
					 
					$stmt->bind_param('ss', $status, $user);
					 
					$stmt->execute();
					 
					$stmt->close();
					
				?>
				<div id="pet" onclick="goLeft()" style="position: relative">
					<img src="<?php printf("%s%s%s.jpg", $color, $status, $type); ?>" alt="pet image" /> 
				</div>
				<script>
					$(document).ready(function() {
					    
					    function petLeft() {
					        $("#pet").animate({top: "-=50"}, 1500, "swing", petRight);
					    }
					    function petRight() {
					        $("#pet").animate({top: "+=50"}, 1500, "swing", petLeft);
					    }
					    
					    petRight();
					});
				</script>
				<br>
				<br>
				<h2> <?php echo htmlentities($petName); ?> </h2>
				<h3> Hunger: 
					<?php
						if($hungry){
							echo " Very Hungry";
						}
						else{
							echo " Satisfied";
						}
					?>
				</h3>
				<h3> Mood: 
					<?php
						if($badMood){
							echo "Not happy";
						}
						else{
							echo "Very happy";
						}
					?>
				</h3>
				</div>
		<?php
		}
		?>
		<div class="main">
		<!--	<a href="marketplace.php" class="right"> Marketplace  </a>
			<a href="inventory.php" class="right"> Inventory </a> -->
			
		</div>
	</body>
</html>