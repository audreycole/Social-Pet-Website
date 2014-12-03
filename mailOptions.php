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
				$arg= $_GET['arg'];
				$id= $_GET['id'];
				
				echo $arg;
				$user = $_SESSION['username'];

				require 'database.php';		
				
				if($arg == "delete"){
					$stmt = $mysqli->prepare("delete from mail where id=?");
					if(!$stmt){
						printf("Query Prep Failed: %s\n", $mysqli->error);

						exit;
					}
					 
					$stmt->bind_param('i', $id);
					 
					$stmt->execute();
					
					$stmt->close();
				}
				else if($arg == "send"){
					if(!isset($_POST['send'])){
		?>
						<form action="mailOptions.php?arg=send&id=" method="POST">
							<p>
								<label>To:</label>
								<input type="text" name="to" id="to" />
							</p>
							<p>
								<label>Message:</label>
								<input type="textarea" name="message" id="message" />
							</p>
							<p> <input type="submit" value="Send" name="send" /> </p>
						</form>
						
			<?php
					}
					else{
						$stmt = $mysqli->prepare("insert into mail (sender, recipient, message) values ( ?, ?, ?)");
						if(!$stmt){
							printf("Query Prep Failed: %s\n", $mysqli->error);
							exit;
						}
						 
						$stmt->bind_param('sss', $user,  $_POST['to'], $_POST['message']);
						 
						$stmt->execute();
						 
						$stmt->close();
					}
				}
			
				//then go back to marketplace
			//	header("Location: inbox.php");
			?>
</html>