<?php    
    session_start();
     
        $id= $_GET['id'];
        $user = $_SESSION['username'];
 
        require 'database.php';     
         
        //update mood of pet being played with
        $phptime = time();
        date_default_timezone_set('America/Chicago');
        $time = date ("Y-m-d H:i:s", $phptime);
         
        $stmt = $mysqli->prepare("update pets set last_played=? where owner=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
      
        $stmt->bind_param('ss', $time, $user);
          
        $stmt->execute();
         
        $stmt->close();
         
        //delete item from inventory
        $stmt = $mysqli->prepare("delete from inventory where id=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
 
            exit;
        }
          
        $stmt->bind_param('i', $id);
          
        $stmt->execute();
         
        $stmt->close();
     
         
        //then go back to inventory
        header("Location: inventory.php");
?>