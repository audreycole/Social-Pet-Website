<?php ob_start(); ?>
<!DOCTYPE html>
<?php
    session_start();
?>
 
<head>
    <title>Neopets Card Matching Game </title>
    <!-- Jquery -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <style type="text/css">
    div#encasement{
    }
    div#memory_board{
        background:#CCC;
        border:#999 1px solid;
        width:800px;
        height:540px;
        padding:24px;
        float: right;
    }
    #canvas{
    	margin-top: 50px;
    	left: 180px;
    	background: #FFF;
    	position: relative;
    }
    div#highScoresMatch{
        background: #CCC;
        border:#999 1px solid;
        width: 100px;
        height: 610px;
        padding: 24px;
        float: left;
    }
    div#highScoresSnake{
        background: #CCC;
        border:#999 1px solid;
        width: 100px;
        height: 610px;
        padding: 24px;
        float: left;
    }
    div#score_board{
        background: #CCC;
        border: #999 1px solid;
        width: 800px;
        height: 20px;
        padding: 24px;
        margin: 0px auto;
        float: right;
    }
    div#memory_board > div{
        background: url(neopetsCard.jpg) no-repeat;
        border:#000 1px solid;
        width:71px;
        height:71px;
        float:left;
        margin:10px;
        padding:20px;
        font-size:64px;
        cursor:pointer;
        text-align:center;
    }
	</style>
	<script>
	//score is global variable for access in other fxns
	var score;

//we want the snake to constantly be moving
	$(document).ready(function(){
	//Canvas stuffsss
	var canvas = $("#canvas")[0];
	var ctx = canvas.getContext("2d");
	var w = $("#canvas").width();
	var h = $("#canvas").height();
	
	//Saving the cell width in a variable
	var cw = 10;
	var d;
	var food;
	
	//creating the snake now!!!
	var snake_array; //an array of cells to make up the snake
	
	function init()
	{
		d = "right"; //default direction

		create_snake();
		create_food(); //Now we can see the food particle
		//finally lets display the score
		score = 0;
		updateScore(score);
		//Lets move the snake now using a timer which will trigger the paint function
		//every 60ms....
		if(typeof game_loop != "undefined") clearInterval(game_loop);
		game_loop = setInterval(paint, 60);
	}
	init();
	
	function create_snake()
	{
		var length = 5; //length of the snake
		snake_array = []; //empty snake array to start with
		for(var i = length-1; i>=0; i--)
		{
			//This will create a horizontal snake starting from the top left
			snake_array.push({x: i, y:0});
		}
	}
	
	//Lets create the food now
	function create_food()
	{
		//put food in random spots 
		food = {
			x: Math.round(Math.random()*(w-cw)/cw), 
			y: Math.round(Math.random()*(h-cw)/cw), 
		};
	}
	
	//painting snake
	function paint()
	{
		//To avoid the snake trail we need to paint the BG on every frame
		//painting canvass
		ctx.fillStyle = "white";
		ctx.fillRect(0, 0, w, h);
		ctx.strokeStyle = "black";
		ctx.strokeRect(0, 0, w, h);
		
		//The movement code for the snake to come here.
		//Pop out the tail cell and place it infront of the head cell
		var nx = snake_array[0].x;
		var ny = snake_array[0].y;
		//These were the position of the head cell.
		//increment it to get the new head position
		//adding direction based movement 
		if(d == "right") nx++;
		else if(d == "left") nx--;
		else if(d == "up") ny--;
		else if(d == "down") ny++;
		
		//if the head of the snake bumps into its body, the game restarts! 
		if(nx == -1 || nx == w/cw || ny == -1 || ny == h/cw || check_collision(nx, ny, snake_array))
		{
			if(score > 0) {
				alert("Game over! ... generating new Snake!");
            	storePoints();
            	init();
            	return;
            } else {
			//restart game
				init();
				return;
			}
		}
		
		//Code to make the snake eat the food
		//If the new head position matches with that of the food,
		//Create a new head instead of moving the tail
		if(nx == food.x && ny == food.y)
		{
			var tail = {x: nx, y: ny};
			score+=10;
			//call updateScore function
			updateScore(score);
			//Create new food
			create_food();
		}
		else
		{
			var tail = snake_array.pop(); //pops out the last cell
			tail.x = nx; tail.y = ny;
		}
		//The snake can now eat the food!!!!
		
		snake_array.unshift(tail); //puts back the tail as the first cell
		
		for(var i = 0; i < snake_array.length; i++)
		{
			var c = snake_array[i];
			//Lets paint 10px wide cells
			paint_cell(c.x, c.y);
		}
		
		//paint the food
		paint_cell(food.x, food.y);
	}
	
	//create a generic function to paint cells
	function paint_cell(x, y)
	{
		ctx.fillStyle = "purple";
		ctx.fillRect(x*cw, y*cw, cw, cw);
		ctx.strokeStyle = "white";
		ctx.strokeRect(x*cw, y*cw, cw, cw);
	}
	
	function check_collision(x, y, array)
	{
		//This function will check if the provided x/y coordinates exist
		//in an array of cells or not
		for(var i = 0; i < array.length; i++)
		{
			if(array[i].x == x && array[i].y == y)
			 return true;
		}
		return false;
	}
	
	//keyboard controls
	$(document).keydown(function(e){
		var key = e.which;
		//prevents reverse gear
		if(key == "37" && d != "right") d = "left";
		else if(key == "38" && d != "down") d = "up";
		else if(key == "39" && d != "left") d = "right";
		else if(key == "40" && d != "up") d = "down";
		//The snake is now keyboard controllable! 
	})
	})
	//updateScore in score div! 
	function updateScore(score) {
        document.getElementById('score_board').innerHTML = "Score = " + score;
    }
    //store points after game ends in highestScores table 
    function storePoints(){
        // Make a URL-encoded string for passing POST data:
        var dataString = "points=" + encodeURIComponent(score) + "&game=" + encodeURIComponent("snake");;
        var xmlHttp = new XMLHttpRequest(); // Initialize our XMLHttpRequest instance
        xmlHttp.open("POST", "updatePoints.php", true); 
        xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // It's easy to forget this line for POST requests
        xmlHttp.addEventListener("load", function(event){
            var jsonData = JSON.parse(event.target.responseText); // parse the JSON into a JavaScript object
            if(jsonData.success){  // in PHP, this was the "success" key in the associative array; in JavaScript, it's the .success property of jsonData
                alert("Your Neopoints: " + jsonData.message);
            }
            else{
                alert(jsonData.message);
            }
        }, false); // Bind the callback to the load event
        xmlHttp.send(dataString); // Send the data
    }

	</script>
</head>
<body>
    <?php
        if($_SESSION['username'] != "not_registered"){
    ?>
            <button><a href="neopets.php" class="right"> Home </a></button>
            <button><a href="marketplace.php" class="right"> Marketplace  </a> </button>
            <button><a href="inventory.php" class="right"> Inventory </a></button>
            <button><a href="cardGame.php" class="right"> Play Match </a></button>
    <?php
        }
        else{
    ?>
            <button><a href="login.php" class="right"> Login  </a> </button>
            <button><a href="register.php" class="right"> Sign Up  </a> </button>
    <?php
        }
    ?>
    <div id="encasement">
    	<div id="highScoresMatch">
    		<?php
            require 'database.php';
            $stmt = $mysqli->prepare("select username, score from highscores where game ='match' order by score desc");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->execute();
            $stmt->bind_result($username, $score);
            $count = 0;
            printf("<h3>Match High Scores</h3><hr>");
            while($stmt->fetch() && $count < 10){
                printf("<tr>\n");
                printf("<td>%s</td>\n <td>%s</td>\n",
                    htmlspecialchars($username),
                    htmlspecialchars($score) //convention: image_url will be item_name.jpg
                                
                );
                printf("</tr>\n");
                printf("<br>");
                $count ++;
            }
            
            $stmt->close();
        ?>
    	</div>
    	<div id="highScoresSnake">
    		<?php
                require 'database.php';
                $stmt = $mysqli->prepare("select username, score from highscores where game ='snake' order by score desc");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->execute();
                $stmt->bind_result($username, $score);
                $count = 0;
                printf("<h3>Snake High Scores</h3><hr>");
                while($stmt->fetch() && $count < 10){
                    printf("<tr>\n");
                    printf("<td>%s</td>\n <td>%s</td>\n",
                        htmlspecialchars($username),
                        htmlspecialchars($score) //convention: image_url will be item_name.jpg
                                    
                    );
                    printf("</tr>\n");
                    printf("<br>");
                    $count ++;
                }
                
                $stmt->close();
            ?>
    	</div>
    	<div id="memory_board">
    		<!-- Snake needs a canvas to draw the snake and such -->
			<canvas id="canvas" width="450" height="450"></canvas>
    	</div>
    </div>
    <div id="score_board"></div>
    <script>init(); </script>

