<!DOCTYPE html>


<html>
<head>
	<title>TaSC Login</title>
	<link href="Resources/index.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="Scripts/index.js"></script>
</head>

<body>
	<h1> Tutor and Student Connection </h1>
	<form id="old_user" name="old_user" action="index.php" method="post" onsubmit="return validateSignIn(this);">
		<fieldset>
			<legend>Sign in</legend>
			<div class="formData">
				
				<label class="field">Email</label>
	            <div class="value"><input type="text" size="60" value="" name="my_username" id="my_username"/></div>
	            
	            <label class="field">Password</label>
	            <div class="value"><input type="password" size="60" value="" name="my_password" id="my_password"/></div>

	            <input type="submit" value="I'm Ready!" id="sign_in" name="sign_in"/>
			</div>
		</fieldset>
	</form>
	<button type = "button">Forgot Email/Password?</button>
    <br>
    <a href="signup.php"> Don't have an account? Sign up here! </a>
</body>






</html>