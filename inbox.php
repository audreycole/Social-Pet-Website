<?php ob_start(); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>HalaudreyPets</title>
		<link rel="stylesheet" href="items.css" type="text/css" />
	</head>
	
	<body>
		<div class="main">
			<button><a href="neopets.php" class="right"> Home </a></button>
			<button><a href="inventory.php" class="right"> Inventory  </a> </button>
			<button><a href="marketplace.php" class="right"> Marketplace  </a> </button>
			<button><a href="cardGame.php" class="right"> Play Game </a></button>
			<hr>
			<button><a href="mailOptions.php?arg=send&id=">Compose Mail</a></button>
			


			<h1 class="center"> Inbox </h1> 
			
		<?php
			session_start();
			$user= $_SESSION['username'];

				
			if($user != "not_registered"){

				require 'database.php';
					
		?>
		
				<table id="inbox">
					<tr>
						<th> From </th>
						<th> Time Sent </th>
						<th> Message </th>
						<th> Options </th>
					</tr>
				<?php
					
					$stmt = $mysqli->prepare("SELECT id, sender, time_sent, message FROM mail WHERE recipient=?");
					
					$stmt->bind_param('s', $user);
					
					$stmt->execute();
					 
					// Bind the results
					$stmt->bind_result($id, $sender, $time_sent, $message);
					
					while($stmt->fetch()){
						echo "<tr>\n";
						printf("<td>%s</td>\n <td>%s</td>\n <td>%s</td>\n",
							htmlspecialchars($sender),
							htmlspecialchars($time_sent), //convention: image_url will be item_name.jpg
							htmlspecialchars($message)
						);
						printf("<td><button><a href=\"mailOptions.php?arg=delete&id=%d\">Delete</a></button></td>\n", $id);

					}
					echo "</tr>\n";
				 
					$stmt->close();
				?>
				
				</table>
				<hr>
				<h1 class="center"> Sent Mail </h1> 
					<table id="inbox">
					<tr>
						<th> To </th>
						<th> Time sent </th>
						<th> Message </th>
					</tr>
				<?php
					
					$stmt = $mysqli->prepare("SELECT id, recipient, time_sent, message FROM mail WHERE sender=?");
					
					$stmt->bind_param('s', $user);
					
					$stmt->execute();
					 
					// Bind the results
					$stmt->bind_result($id, $recipient, $time_sent, $message);
					
					while($stmt->fetch()){
						echo "<tr>\n";
						printf("<td>%s</td>\n <td>%s</td>\n <td>%s</td>\n",
							htmlspecialchars($recipient),
							htmlspecialchars($time_sent), //convention: image_url will be item_name.jpg
							htmlspecialchars($message)
						);

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