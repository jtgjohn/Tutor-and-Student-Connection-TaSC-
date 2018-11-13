<!DOCTYPE html>

<!-- Starts up the session to give access to creating and calling $_SESSION[NAME] variables -->
<?php 
  session_start();
?>

<html>

<!-- function sets the the course to the proper one clicked for use later when dynamically
showing the relevant information that follows. -->
<?php
	function setSession($course){
		$_SESSION['course']=$course;
	}
	
?>
<head>
	<title>TaSC</title>
	<link href="Resources/forum.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="Resources/jquery-1.4.3.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</head>


<body>
	<h1> 
		<div id="header" class="jumbotron text-center"> Tutor and Student Connection 
		</div>
	</h1>

	<div class="sidenav">
	  <a id="navlink" href="connect.php">Connect Page</a>
	  <a id ="post" href="makepost.php">New Post</a>

	  <!-- below is the php to dynamically pull from the database and display all the 
	  subjects the user is signed up for, in order to view their respective threads -->
	  <?php
	  	//setting up database connection
	  	@ $db =  new mysqli('localhost', 'root', 'password', 'TaSC');
	  	
	  	//Querying the database for all the course names that the user is signed up for
	  	//this uses the "$_SESSION['userid']" which stores the current users id, which
	  	//is setup during login and is accessable all througout the code
	  	$q="select course from user_subjects where userid=".'"'.$_SESSION['userid'].'"';
	  	$prepCourses=$db->query($q);
		$numRecords = $prepCourses->num_rows;

		// $_SESSION['course']=1;
		for($i=0; $i<$numRecords; $i++){
			$course=$prepCourses->fetch_assoc();
			if($i==0){
				//Defaults the subject in view to the first one the user
				//is signed up for
				$_SESSION['course']=$course['course'];
			}

			//Dynamically creates the html for each "link"
			//link set up so that each respective course's id gets 
			//sent to the url so when using $_GET['course'], the right courses
			//id is there so that it can be dynamically pulled for proper viewing.
			echo "<a href='forum.php?course=".$course['course']."'>".$course['course']."</a>";
		}
		//sets the current $_SESSION['course'] to the course that was selected
		if (isset($_GET['course'])) {
    		setSession($_GET['course']);
  		}
	  ?>
	  <a id="logout" href="index.php"> Logout </a>

	</div>
	<div id="discussion">

		<!-- php to dynamically pull and display all threads for the relevant subject selected
		by the user -->
		<?php
			$dbOk = false;

			//Sets up the connection with the database
			@ $db =  new mysqli('localhost', 'root', 'password', 'TaSC');

			//Displays error if the database connection request fails.
			if ($db->connect_error) {
				echo '<div class="messages">Could not connect to the database. Error: ';
				echo $db->connect_errno . ' - ' . $db->connect_error . '</div>';
			} else {
				$dbOk = true; 
			}

			//if the connection is successful
			if($dbOk){
				//Querying the database for the subject id using the current course name
				//in the subject table
				$q='select subjectid from subject where course='."'". $_SESSION['course']."'";
		    	$courses=$db->query($q);
		    	$courseid=$courses->fetch_assoc();

		    	//Querying the database for all of the threads that have the subjectid of the selected
		    	//subject filtered to have the newest comments on top
				$query = 'select * from forum where courseid='.'"'.$courseid["subjectid"].'"'.' order by postdate DESC';
    			$result = $db->query($query);
    			$numRecords = $result->num_rows;

    			//Dynamically creates the html to display all of the threads for the respective subject
    			//selected
    			for($i=0; $i <$numRecords; $i++) {
			    	$post = $result->fetch_assoc();
					echo "<ul>";

					//using href="comment.php?post='.$post['postid'].'" so that when on the comment page
					//the selected thread's postid can be pulled using $_GET['post'] so the right thread and
					//comments can be displayed dynamically
		   			echo '<a href=comment.php?post='.$post['postid'].' id="discussion">' . $post['topic'] . '</a>';
		    		
		    		echo '<li class="internalDisc">' . $post['post']. '</li>';
		    		//querying the database, using the thread's userid, to get the 
		    		//poster's first name from the user table.
		    		$q='select first_names from users where userid='. $post['userid'];
		    		$fn=$db->query($q);
		    		$fname=$fn->fetch_assoc();
		    		
		    		echo '<li class="author">' . "Posted by ".$fname['first_names'] ." ".$post['postdate'].'</li>';
		    		echo "</ul>";
			    }
			}
		?>
	</div>



</html>