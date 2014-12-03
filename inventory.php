		<?php
			session_start();
				
			if($_SESSION['username'] != "not_registered"){
		?>
		<!DOCTYPE HTML>
		<html>
				<head>
					<title>HalaudreyPets</title>
					<link rel="stylesheet" href="items.css" type="text/css" />
				</head>
				
				<body>
				<h3> Neopoints: 
					<?php
						require 'database.php';
						
						$stmt = $mysqli->prepare("select points from users where username=?");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						  
						$stmt->bind_param('s', $_SESSION['username']);

						$stmt->execute();
						  
						$stmt->bind_result($points);
						
						$stmt->fetch();
						
						$stmt->close();
					
						echo $points;
					?>
				</h3>
		
				<div class="main">
					<button><a href="neopets.php" class="right"> Home </a></button>
					<button><a href="inbox.php" class="right"> Mail </a></button>
					<button><a href="marketplace.php" class="right"> Marketplace  </a> </button>
					<button><a href="cardGame.php" class="right"> Play Game </a></button>
					<hr>
					<h1 class="center"> Inventory </h1> 
					<table id="inventory">
						<tr>
							<th> Item name </th>
							<th> Item image </th>
							<th> Item type </th>
							<th> Options </th>
						</tr>
					<?php
					
						$user= $_SESSION['username'];
						
						$stmt = $mysqli->prepare("SELECT id, item_name, type FROM inventory WHERE user=?");
						
						$stmt->bind_param('s', $user);
						
						$stmt->execute();
						 
						// Bind the results
						$stmt->bind_result($id, $name, $type);
						
						while($stmt->fetch()){
							echo "<tr>\n";
							printf("<td>%s</td>\n <td><img src=\"%s.jpg\" alt=\"item\"/></td>\n <td>%s</td>\n",
								htmlspecialchars($name),
								htmlspecialchars($name), //convention: image_url will be item_name.jpg
								htmlspecialchars($type)
							);
							if($type == "food"){ //type determines which button/action is available
								printf("<td><button><a href=\"feed.php?id=%d\">Feed</a></button>", $id);
							}
							else if($type == "play"){
								printf("<td><button><a href=\"play.php?id=%d\">Play</a></button>", $id);
							}
							printf("<button><a href=\"sell.php?id=%d\">Sell</a></button></td>\n", $id);
						}
						echo "</tr>\n";
					 
					$stmt->close();
					?>
					
					</table>
				</div>
		<?php
			}
		?>
	</body>
</html>