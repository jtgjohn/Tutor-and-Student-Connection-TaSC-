<!DOCTYPE html>
<?php 
  session_start();
?>
<html>
<head>
	<title>TaSC</title>
	<link href="Resources/forum.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<div class="sidenav">
	  <a id="navlink" href="connect.html">Connect Page</a>
	  <a id="ds" href="#DS">Data Structures</a>
	  <a href="#P1">Physics1</a>
	  <a id="logout" href="index.html"> Logout </a>

	</div>
	<h1> Tutor and Student Connection </h1>
	<form id="old_user" name="new_user" action="makepost.php" method="post" onsubmit="return validate(this);">
		<fieldset>
			<legend>New Post</legend>
			<div class="formData">
				<label class="field">Subject:</label>
	            <div class="value"><input type="text" size="60" value="" name="subject" id="subject"/></div>
	            
	            <label class="field">Description:</label>
	            <div class="value">
	            	<textarea rows=4 cols=80 value="" name="context" id="context">
	            	</textarea>
	            </div>

	            <input type="submit" value="save" id="save" name="save"/>
			</div>
		</fieldset>
	</form>

	<?php
	  // We'll need a database connection both for retrieving records and for 
	  // inserting them.  Let's get it up front and use it for both processes
	  // to avoid opening the connection twice.  If we make a good connection, 
	  // we'll change the $dbOk flag.
	  $dbOk = false;
	  
	  /* Create a new database connection object, passing in the host, username,
	     password, and database to use. The "@" suppresses errors. */
	  @ $db = new mysqli('localhost', 'root', 'Mets2014', 'TaSC');
	  
	  if ($db->connect_error) {
	    echo '<div class="messages">Could not connect to the database. Error: ';
	    echo $db->connect_errno . ' - ' . $db->connect_error . '</div>';
	  } else {
	    $dbOk = true; 
	  }

	  // Now let's process our form:
	  // Have we posted?
	  $havePost = isset($_POST["save"]);
	  
	  // Let's do some basic validation
	  $errors = '';
	  if ($havePost) {
	    
	    // Get the output and clean it for output on-screen.
	    // First, let's get the output one param at a time.
	    // Could also output escape with htmlentities()
	    $subject = htmlspecialchars(trim($_POST["subject"])); 
	    $context = htmlspecialchars(trim($_POST["context"]));
	    
	    // special handling for the date of birth
	   	//$dobTime = strtotime($dob); // parse the date of birth into a Unix timestamp (seconds since Jan 1, 1970)
	    //$dateFormat = 'YYYY'; // the date format we expect, yyyy-mm-dd
	    // Now convert the $dobTime into a date using the specfied format.
	    // Does the outcome match the input the user supplied?  
	    // The right side will evaluate true or false, and this will be assigned to $dobOk
	    //$dobOk = date($dateFormat, $year) == $dob;  
	    
	    $focusId = ''; // trap the first field that needs updating, better would be to save errors in an array
	    
	    if ($subject == '') {
	      $errors .= '<li>First name may not be blank</li>';
	      if ($focusId == '') $focusId = '#subject';
	    }
	    if ($context == '') {
	      $errors .= '<li>First name may not be blank</li>';
	      if ($focusId == '') $focusId = '#context';
	    }
	  
	    if ($errors != '') {
	      echo '<div class="messages"><h4>Please correct the following errors:</h4><ul>';
	      echo $errors;
	      echo '</ul></div>';
	      echo '<script type="text/javascript">';
	      echo '  $(document).ready(function() {';
	      echo '    $("' . $focusId . '").focus();';
	      echo '  });';
	      echo '</script>';
	    } else { 
	      if ($dbOk) {
	        // Let's trim the input for inserting into mysql
	        // Note that aside from trimming, we'll do no further escaping because we
	        // use prepared statements to put these values in the database.
	        $topicForDb = trim($_POST["subject"]); 
	        $postForDb=trim($_POST["context"]);
	        // Setup a prepared statement. Alternately, we could write an insert statement - but 
	        // *only* if we escape our data using addslashes() or (better) mysqli_real_escape_string().
	        $insQuery = "insert into forum (`courseid`,`topic`,`post`,`postdate`,`userid`) values(?,?,?,?,?)";
	        $statement = $db->prepare($insQuery);
	        // bind our variables to the question marks
	        //make cID
	        $q="select course from subject where subjectid=".'1';
	        $getQ=$db->query($q);
			$cID=$getQ->fetch_assoc();
			//NEEDS COURSE ID
			$cID='1';$d=date('Y-m-d');$id=$_SESSION['userid'];
	        $statement->bind_param("sssss",$cID,$topicForDb,$postForDb,$d,$id);
	        // make it so:
	        $statement->execute();
	        
	        // give the user some feedback
	        echo '<div class="makepost">';
	        echo "Thread about: ".$postForDb." has been created". '</div>';
	        
	        // close the prepared statement obj 
	        $statement->close();
	      }
	    } 
	  }
	?>
</body>
</html>