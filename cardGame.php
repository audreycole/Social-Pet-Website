<?php ob_start(); ?>
<!DOCTYPE html>
<?php
    session_start();
?>
 
<head>
    <title>Neopets Card Matching Game </title>
    <style type="text/css">
    div#encasement{
    }
    div#memory_board{
        background:#CCC;
        border:#999 1px solid;
        width:800px;
        height:540px;
        padding:24px;
        margin:0px auto;
        float: right;
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
 
    //store values for cards
    var memory_array = ['A','A','B','B','C','C','D','D','E','E','F','F','G','G','H','H','I','I','J','J','K','K','L','L'];
    var memory_values = [];
    //keep track of the index/location of cards 
    var memory_card_ids = [];
    //keep track of cards flipped --> no more than 2 
    var cards_flipped = 0;
    var score = 0;
 
    //memory_tile_shuffle affects the Array class which includes memory_array so every time you call this function, it shuffles 
    //the letters
    Array.prototype.memory_card_shuffle = function(){
        var i = this.length, j, temp;
        while(--i > 0){
            j = Math.floor(Math.random() * (i+1));
            temp = this[j];
            this[j] = this[i];
            this[i] = temp;
        }
    }
    function updateScore(score) {
        document.getElementById('score_board').innerHTML = "Score = " + score;
    }
    function newBoard(){
        cards_flipped = 0;
        score = 0;
        var output = '';
        //shuffle the cards
        memory_array.memory_card_shuffle();
        //fill out the board with values 
        for(var i = 0; i < memory_array.length; i++){
            //set the output equal to a div id with an onclick call to memoryFlipTile function 
            //so whenever we click on it, it call memoryFlipCard fxn and pass in the html element (the card) and it's value
            //derp derp this better work -__-
            output += '<div id="card_'+i+'" onclick="memoryFlipCard(this,\''+memory_array[i]+'\')"></div>';
        }
        document.getElementById('memory_board').innerHTML = output;
        updateScore(score);
    }
    function memoryFlipCard(card,val){
        //if the player flips ANY of the cards--> execute these statements
        if(card.innerHTML == "" && memory_values.length < 2){
            card.style.background = '#FFF';
            card.innerHTML = val;
            //if no cards are flipped over
            if(memory_values.length == 0){
                memory_values.push(val);
                memory_card_ids.push(card.id);
            //if there is already one card flipped over
            } else if(memory_values.length == 1){
                memory_values.push(val);
                memory_card_ids.push(card.id);
                //if they are a match!!!
                if(memory_values[0] == memory_values[1]){
                    cards_flipped += 2;
                    score += 10;
                    // Clear both arrays
                    memory_values = [];
                    memory_card_ids = [];
                    // Check to see if the whole board is cleared
                    if(cards_flipped == memory_array.length){
                        alert("Way to go! You won! ... generating new board");
                        storePoints();
                        document.getElementById('memory_board').innerHTML = "";
                        newBoard();
                    }
                } else {
                    //if the two cards are NOT a match
                    score -= 2;
                    function flipBack(){
                        // Flip the 2 tiles back over
                        var card_1 = document.getElementById(memory_card_ids[0]);
                        var card_2 = document.getElementById(memory_card_ids[1]);
                        card_1.style.background = 'url(neopetsCard.jpg) no-repeat';
                        card_1.innerHTML = "";
                        card_2.style.background = 'url(neopetsCard.jpg) no-repeat';
                        card_2.innerHTML = "";
                        // Clear both arrays
                        memory_values = [];
                        memory_card_ids = [];
                    }
                    //make the two cards flip back in a little over half a second
                    setTimeout(flipBack, 700);
                }
                updateScore(score);
            }
        }
         
    }
    function storePoints(){
        // Make a URL-encoded string for passing POST data:
        var dataString = "points=" + encodeURIComponent(score) + "&game=" + encodeURIComponent("match");;
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
            <button><a href="snake.php" class="right"> Play Snake </a></button>
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
        <div id="memory_board"></div>
    </div>
    <div id="score_board"></div>
    <script>newBoard(); </script>
</body>
</html>