<?php	
	session_start();
	
		$id= $_GET['id'];
		$user = $_SESSION['username'];

		require 'database.php';		

		//get the item info from the inventory using the passed in id
		$stmt = $mysqli->prepare("select item_name, type, value from inventory where id=?");
		if(!$stmt){
			printf("1 Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		 
		$stmt->bind_param('i', $id);
		 
		$stmt->execute();
		 
		$stmt->bind_result($name, $type, $value);
	
		$stmt->fetch();
		
		$stmt->close();
		
		/* try to find the item in market */
		$stmt = $mysqli->prepare("select id, quantity from market where item_name=?");
		if(!$stmt){
			printf(" 2 Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		 
		$stmt->bind_param('s', $name);
		 
		$stmt->execute();
		 
		$stmt->bind_result($market_id, $quantity);
	
		//if it is there, update the quantity in the market
		if($stmt->fetch()){
			echo "found it in the market";
			$stmt->close();
			
			$quantity++;
			$stmt = $mysqli->prepare("update market set quantity=? where id=?");
			if(!$stmt){
				printf("3?? Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
		 
			$stmt->bind_param('ii', $quantity, $market_id);
			 
			$stmt->execute();
			
			$stmt->close();
		}
		//else, add it to the market
		else{		
			echo "no find :(";
			$stmt->close();

			$quantity=1;
			$stmt = $mysqli->prepare("insert into market set item_name=?, type=?, price=?, quantity=?");
			if(!$stmt){
				printf("4 Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			
			$stmt->bind_param('ssii', $name, $type, $value, $quantity);
			 
			$stmt->execute();
			
			$stmt->close();
		}
		
		/* Get how many points user has */
		$stmt = $mysqli->prepare("select points from users where username=?");
		if(!$stmt){
			printf("5 Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		 
		$stmt->bind_param('s', $user);
		 
		$stmt->execute();
		 
		$stmt->bind_result($totalPoints);
		
		$stmt->fetch();
		
		$stmt->close();
		
		/* Give user points for selling item */
		$stmt = $mysqli->prepare("update users set points=? where username=?");
		if(!$stmt){
			printf("6 Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		
		$newPoints = $value + $totalPoints;
				printf("value: %d, totalPoints: %d, newPoints: %d", $value, $totalPoints, $newPoints);

		$stmt->bind_param('is', $newPoints, $user);
		 
		$stmt->execute();
		
		$stmt->close();
			
		/* delete item from inventory */
		$stmt = $mysqli->prepare("delete from inventory where id=?");
		if(!$stmt){
			printf("7 Query Prep Failed: %s\n", $mysqli->error);

			exit;
		}
		 
		$stmt->bind_param('i', $id);
		 
		$stmt->execute();
		
		$stmt->close();
	
		
		//then go back to inventory
		header("Location: inventory.php");
?>
