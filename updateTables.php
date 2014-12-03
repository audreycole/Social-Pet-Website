<?php ob_start(); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title> Neopets - update</title>
		<link rel="stylesheet" type="text/css" href="items.css" >
	</head>

	<body>
		<?php	
			session_start();
			
				$id= $_GET['id'];
				$user = $_SESSION['username'];

				require 'database.php';		

				//get the item info from the marketplace using the passed in id
				$stmt = $mysqli->prepare("select item_name, type, price, quantity from market where id=?");
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				 
				$stmt->bind_param('i', $id);
				 
				$stmt->execute();
				 
				$stmt->bind_result($name, $type, $price, $quantity);
			
				$stmt->fetch();
				
				$stmt->close();
				
				//get points from users
				$stmt = $mysqli->prepare("select points from users where username=?");
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				 
				$stmt->bind_param('s', $user);
				 
				$stmt->execute();
				 
				$stmt->bind_result($points);
				
				$stmt->fetch();
				
				$stmt->close();
				
				//if user can afford item
				if($points >= $price){
					$newPoints = $points- $price;
					//then use the just-retrieved info to store it in the user's inventory
					$stmt = $mysqli->prepare("insert into inventory (item_name, type, user, value) values ( ?, ?, ?, ?)");
					if(!$stmt){
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}
					 
					$stmt->bind_param('sss', $name, $type, $user, $price);
					 
					$stmt->execute();
					 
					$stmt->close();
					
					//update point value
					$stmt = $mysqli->prepare("update users set points=? where username=?");
					if(!$stmt){
						printf("Query Prep Failed: %s\n", $mysqli->error);
						exit;
					}
				 
					$stmt->bind_param('is', $newPoints, $user);
					 
					$stmt->execute();
					
					$stmt->close();
				
				
					//then delete the item from the marketplace if quantity = 0
					if($quantity-1 == 0){
						$stmt = $mysqli->prepare("delete from market where id=?");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);

							exit;
						}
						 
						$stmt->bind_param('i', $id);
						 
						$stmt->execute();
						
						$stmt->close();
					}
					//if quantity is not equal to zero, update quantity in the table
					else{
						$quantity--;
						$stmt = $mysqli->prepare("update market set quantity=? where id=?");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
					 
						$stmt->bind_param('ii', $quantity, $id);
						 
						$stmt->execute();
						
						$stmt->close();
					}
				}
				//then go back to marketplace
				header("Location: marketplace.php");
		?>
</html>