<?php
include 'header.php';
require 'functions.php';

$username = '';
$email = '';
$failType = '';

if(isset($_GET['username']) || isset($_GET['email'])) {
	$username = $_GET['username'];
	$email = $_GET['email'];
	
	if($_GET['username'] == '' || $_GET['email'] == '') {
		//add fail message
		//echo $_SERVER["REQUEST_URI"];
	}
}

if(isset($_GET['fail'])) {
	$failType = failType($_GET['fail']);
}

if(userCheck($username) == true || emailCheck($email) == true) {
	
}

var_dump(userCheck($username));
var_dump(emailCheck($email));

?>

<div id="signin">
	<h2>Reset Account</h2>
	<p><?php echo $failType; ?></p>
	
	<form method="GET">
		<div id="username">
			<label for="username">Username</label>
			<input type="text" name="username" size="20" spellcheck="false" />
		</div>
		
		<div id="email">
			<label for="email">Email</label>
			<input type="text" name="email" size="20" spellcheck="false" />
		</div>
		<input id="submit" type="submit" value="Submit"/>
	</form>
	<div id="signin-menu">
		<a href="signup.php">Sign up</a>
		<a href="signin.php">Sign in</a>
		<a href="mailto:<?php echo SUPPORT_EMAIL; ?>">Support</a>
	</div>
</div>