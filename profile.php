<?php
	session_start();
?>

<!DOCTYPE html>


<html>
<head>
	<title>TaSC Connections</title>
	<link href="Resources/connect.css" rel="stylesheet" type="text/css"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <script type="text/javascript" src="Resources/jquery-1.4.3.min.js"></script>
</head>


<body>
	<h1> 
		<div id="header"> Tutor and Student Connection 
		</div>
	</h1>

	<div class="sidebar">
		<a id="navlink" href="forum.php"> Discussion Forum </a>
		<a href="find.php">Make a Connection </a>
		<a id="logout" href="index.php"> Logout </a>
	</div>
    <div class="connections">
        <h2> Personal Info </h2>
        <?php

            $dbOk = false;

            //connects to database 
            @ $db =  new mysqli('localhost', 'root', 'password', 'TaSC');

            //error message if connection to database fails
            if ($db->connect_error) {
                echo '<div class="messages">Could not connect to the database. Error: ';
                echo $db->connect_errno . ' - ' . $db->connect_error . '</div>';
            } else {
                $dbOk = true; 
            }

            //pulls the user id from the session and accesses user table 
            //to find if the user is a tutor or a student 
			$userid = $_SESSION["userid"]; //gets user id from session
            $namequery = "SELECT first_names,last_name from users where userid='". $userid ."'";
            $nameCall = $db->query($namequery);
            $resCall = $nameCall->fetch_assoc();

            //if the user is a tutor, find all connections where the tutorid
            //matches the userid to output connections
            echo "<h3>".$resCall["first_names"]." ".$resCall["last_name"]."</h3>";
			echo "<p>Hello I am a new student trying to learn how to code</p>";
			

			/*************************************** RANKING SYSTEM ***************************************/
			
			
			
			//						                Calculating Percentile
			
			//getting single users Score
			$userid = $_SESSION["userid"]; //gets user id from session
			$scorequery = "SELECT score from users where userid='". $userid ."'";
			$scoreCall = $db->query($scorequery);
			$score = $scoreCall->fetch_assoc();

			//getting avg of all scores
			$averageScoreQ= "SELECT AVG(score) from users";
			$avgScore = $db->query($averageScoreQ);
			$maybe = $avgScore->fetch_assoc();

			//getting all scores
			$allScoresQ="SELECT score FROM users";
			$allScores = $db->query($allScoresQ);
			$numScores = $allScoresCall->num_rows;

			//Calculating Standard Deviation
			$summation = 0;
			for ($i = 0; i<$numScores; $i++){
				$currScore = $allScores->fetch_assoc();
				$summation += pow(($currScore - $maybe),2);
			}
			$standardDev = sqrt(((float)$summation / (float)$numScores));
			
			//finding Percentile
			$percentile=0;
			$zScore = ($score - $maybe)/$standardDev;
			
			//Using a Z score -> Percentile on Normal Curve Chart
			if($zScore < -2.5){
				$percentile = 0;
			}else if($zScore < -2 and $zScore>= -2.5){
				$percentile = 1;
			}else if($zScore < -1.5 and $zScore>= -2){
				$percentile = 2-($zScore+2);
			}else if($zScore < -1 and $zScore>= -1.5){
				$percentile = 6-($zScore+1.5);
			}else if($zScore < -0.5 and $zScore>= -1){
				$percentile = 15-($zScore+1);
			}else if($zScore < 0 and  $zScore>=-0.5){
				$percentile = 30-($zScore+0.5);
			}else if($zScore > 0 and $zScore <= 0.5){
				$percentile = 69-(0.5-$zScore);
			}else if($zScore > 0.5 and $zScore <= 1){
				$percentile = 84-(1-$zScore);
			}else if($zScore >1 and $zScore <=1.5){
				$percentile = 93-(1.5-$zScore);
			}else if($zScore >1.5 and $zScore <=2){
				$percentile = 97-(2-$zScore);
			}else if($zScore >2 and $zScore<=2.5){
				$percentile = 99-(1.5-$zScore);
			}else if($zScore>2.5){
				$percentile = 100-(3-$zScore);
			}else{
				//ERROR
				$percentile=-7768;
			}
			
			
			if($percentile>100){
				$percentile = 100;
			}
			if($percentile<0){
				$percentile=0;
			}
		
			//										Done with Percentile
			
			
			
			
			// if($averageScore == 0){
			// 	if($score['score']!=$averageScore){
			// 		//rank above average
			// 	}
			// 	//rank at average
			// }else{
			// 	$percentile = ((double) $score['score'] / (double) $averageScore)*100;
			// 	//getting the ranking of user
			// 	switch($percentile){
			// 		case()
			// 	}
			// }
			

			/*********************************** END OF RANKING SYSTEM ***********************************/

        ?>
        
    </div>
	<div class="connections">
		<h2> Connections </h2>

			<?php

				$dbOk = false;

				//connects to database 
				@ $db =  new mysqli('localhost', 'root', 'password', 'TaSC');

				//error message if connection to database fails
				if ($db->connect_error) {
					echo '<div class="messages">Could not connect to the database. Error: ';
					echo $db->connect_errno . ' - ' . $db->connect_error . '</div>';
				} else {
					$dbOk = true; 
				}

				//pulls the user id from the session and accesses user table 
				//to find if the user is a tutor or a student 
				$userid = $_SESSION["userid"]; //gets user id from session
				$tutorquery = "SELECT * from users where userid='". $userid ."'";
				$istutor = $db->query($tutorquery);
				$tutorrecord = $istutor->fetch_assoc();
				$tutor = $tutorrecord["tutor"]; //boolean for user being a tutor

				//if the user is a tutor, find all connections where the tutorid
				//matches the userid to output connections
				if ($tutor) {
					$query = "SELECT * from connections where tutorid='" . $userid . "'";
					$result = $db->query($query);
					$numRecords = $result->num_rows;
                    echo '<h4>Who I tutor:</h4>';
					//for every connection the tutor has, print out the information
					//of the other user
					for ($i=0; $i < $numRecords; $i++) {
						$record = $result->fetch_assoc();
						$sid = $record["studentid"];

						$infoQuery = "SELECT * from users where userid='" . $sid . "'";
						$infoResult = $db->query($infoQuery);
						$info = $infoResult->fetch_assoc();

						echo "<h3> " . htmlspecialchars($info["first_names"]) . " ";
						echo htmlspecialchars($info["last_name"]) . "</h3>";
						echo '<p> Email: ' . $info["email"] . '</p>';
						echo "<p> Course(s): ";

						//If the user is in multiple subjects, output all of the subjects that 
						//the user is in
						$subjquery = "SELECT course from user_subjects where userid='" . $sid . "'";
						$subjresults = $db->query($subjquery);
						$numSubjects = $subjresults->num_rows;

						for ($j=0; $j < ($numSubjects-1); $j++) {
							$subj = $subjresults->fetch_assoc();
							echo $subj["course"] . ", ";
						}
						$subj = $subjresults->fetch_assoc();
						echo $subj["course"] . "</p>";

						echo "<p> Year: " . $info["year"] . "</p>";
						echo "<p> " . $info["description"] . "</p>";
					}

				} else { //assume the user is a student
					//uses the same method as above but matches userid to studentid instead of tutorid
					$query = "SELECT * from connections where studentid='" . $userid . "'";
					$result = $db->query($query);
					$numRecords = $result->num_rows;
                    echo "<h4>Tutors:</h4>";
					//prints out info for each connection made by user to tutors
					for ($i=0; $i < $numRecords; $i++) {
						$record = $result->fetch_assoc();
						$tid = $record["tutorid"];

						$infoQuery = "SELECT * from users where userid='" . $tid . "'";
						$infoResult = $db->query($infoQuery);
						$info = $infoResult->fetch_assoc();

						echo "<h3> " . htmlspecialchars($info["first_names"]) . " ";
						echo htmlspecialchars($info["last_name"]) . "</h3>";
						echo '<p> Email: ' . $info["email"] . '</p>';
						echo "<p> Course(s): ";

						$subjquery = "SELECT course from user_subjects where userid='" . $tid . "'";
						$subjresults = $db->query($subjquery);
						$numSubjects = $subjresults->num_rows;

						for ($j=0; $j < ($numSubjects-1); $j++) {
							$subj = $subjresults->fetch_assoc();
							echo $subj["course"] . ", ";
						}
						$subj = $subjresults->fetch_assoc();
						echo $subj["course"] . "</p>";

						echo "<p> Year: " . $info["year"] . "</p>";
						echo "<p> " . $info["description"] . "</p>";
                    }
				}

			?>

	</div>
</body>


</html>