<?php
include 'header.php';
require 'functions.php';

$failType = '';

if(isset($_GET['fail'])) {
	$failType = failType($_GET['fail']);
}

?>

<div id="signin">
	<h2>Reset Account</h2>
	<p><?php echo $failType; ?></p>
	
	<form action="./include/check.php" method="POST">
		<div id="username">
			<label for="username">Username</label>
			<input type="text" name="username" size="20" spellcheck="false" />
		</div>
		
		<div id="email">
			<label for="email">Email</label>
			<input type="text" name="email" size="20" spellcheck="false" />
		</div>
		<input type="hidden" name="from" value="reset" />
		<input id="submit" type="submit" value="Submit"/>
	</form>
	<div id="signin-menu">
		<a href="signup.php">Sign up</a>
		<a href="signin.php">Sign in</a>
		<a href="mailto:<?php echo SUPPORT_EMAIL; ?>">Support</a>
	</div>
</div>