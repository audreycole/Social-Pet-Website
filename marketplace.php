<?php ob_start(); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>HalaudreyPets</title>
		<link rel="stylesheet" href="items.css" type="text/css" />
	</head>
	
	<body>
		<?php
			session_start();
				
			if($_SESSION['username'] != "not_registered"){
		?>
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
					<button><a href="inventory.php" class="right"> Inventory </a></button>
					<button><a href="cardGame.php" class="right"> Play Game </a></button>

					<hr>
					<h1 class="center"> Marketplace </h1> 
					<table id="marketplace">
						<tr>
							<th> Item name </th>
							<th> Item image </th>
							<th> Item type </th>
							<th> Item price </th>
							<th> # Available </th>
							<th> Options </th>
						</tr>
						<?php
							$user= $_SESSION['username'];
							
							$stmt = $mysqli->prepare("SELECT id, item_name, price, quantity, type FROM market");
											
							$stmt->execute();
							 
							// Bind the results
							$stmt->bind_result($id, $name, $price, $quantity, $type);
							
							while($stmt->fetch()){
								printf("<td>%s</td>\n <td><img src=\"%s.jpg\" alt=\"item\"/></td>\n <td>%s</td>\n <td>%d</td>\n <td>%d</td>\n",
									htmlspecialchars($name),
									htmlspecialchars($name),
									htmlspecialchars($type),
									$price,
									$quantity
								);
								
								 //put item id on button to know which item is being bought
								printf("<td><button><a href=\"updateTables.php?id=%d\">Buy</a></button></td>\n", $id); 
							
								echo "</tr>\n";
							}
							$stmt->close();
						?>
					</table>					
				</div>
		<?php
			}
		?>
	</body>
</html>