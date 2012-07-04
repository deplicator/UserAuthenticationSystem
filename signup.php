<?php 
include 'header.php';
require 'functions.php';

$failType = '';

if(isset($_GET['fail'])){
	$failType = failType($_GET['fail']);
}

?>

<div id="signin">
<h2>Sign up</h2>
	<p><?php echo $failType; ?></p>
	<form action="signup-check.php" method="POST">
		<div id="username">
			<label for="username">Username</label>
			<input type="text" name="username" size="20" spellcheck="false" />
		</div>
		
		<div id="password">
			<label for="password">Password</label>
			<input type="password" name="password" />
		</div>
		
		<div id="passwordconfirm">
			<label for="passwordconfirm">Confirm Password</label>
			<input type="password" name="passwordconfirm" />
		</div>
		
		<div id="email">
			<label for="email">email</label>
			<input type="text" name="email" size="20" spellcheck="false" />
		</div>
		<input type="hidden" value="newuser">
		<input id="submit" type="submit" value="Submit"/>
	</form>
	<div id="signin-menu">
		<a href="signin.php">Sign in</a>
		<a href="reset.php">Reset</a>
		<a href="mailto:<?php echo $support; ?>">Support</a>
	</div>
</div>

<?php include 'footer.php';?>