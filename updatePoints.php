<?php
    ini_set("session.cookie_httponly", 1);
    session_start();
     
    header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
 
    $mysqli = new mysqli('localhost', 'root', 'root', 'neopets');
  
    if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        echo json_encode(array(
            "success" => false,
            "message" => "Connection fail"
        ));
        exit;
    }
     
    $user = $_SESSION['username'];
     
    if($user == "not_registered"){
        echo json_encode(array(
            "success" => false,
            "message" => "You Won!"
        ));
        exit;
    }
    else{
        $stmt = $mysqli->prepare("select points from users where username=?");
            if(!$stmt){
                echo json_encode(array(
                "success" => false,
                "message" => "Incorrect format supplied retrieving existing point vals"
            ));
            exit;
            }
              
            $stmt->bind_param('s', $user);
              
            $stmt->execute();
              
            $stmt->bind_result($totalPoints);
         
            $stmt->fetch();
             
            $stmt->close();

        
             
        $pointsWon = $_POST['points'];
        $game = $_POST['game'];
        $addPoints = $totalPoints + $pointsWon; // add new points to running total
         
        $stmt = $mysqli->prepare("update users set points=? where username=?");
        if(!$stmt){
            echo json_encode(array(
                "success" => false,
                "message" => "Incorrect format supplied for points"
            ));
            exit;
        }
          
        $stmt->bind_param('is', $addPoints, $user);
          
        $stmt->execute();
          
        $stmt->close();

        //insert score into highscores table
        $stmt = $mysqli->prepare("insert into highscores(username, score, game) values(?, ?, ?)");
        if(!$stmt) {
            echo json_encode(array(
                "success" => false, 
                "message" => "Incorrect format supplied retrieving existing point vals"
        ));
        exit;
        }
        $stmt->bind_param('sis', $user, $pointsWon, $game);
        $stmt->execute();
        $stmt->close();   

        echo json_encode(array(
            "success" => true,
            "message" => $addPoints
        ));
        exit;

    }
?>