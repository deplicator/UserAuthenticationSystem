<?php 
include '../header.php';

$message = '';

if(isset($_REQUEST['message'])) {
	$message = getMessage($_REQUEST['message']);
}

if(isset($_REQUEST['email'])) {
	include("class.authenticate.php");

	$reset = new authenticate();
	$reset->reset($_REQUEST['email']);
}

?>

<div class="uasinfo" id="reset">
<h2>Reset Account</h2>
	<form id="form" method="POST">
		<div id="email">
			<label for="username">email</label>
			<input type="text" name="email" spellcheck="false" />
		</div>
		<input id="submit" type="submit" value="Submit"/>
	</form>
	<p><?php echo $message; ?></p>
	<div id="signin-menu-border">
		<div id="signin-menu">
			<a href="./signup.php">Sign up</a>
			<a href="./signin.php">Sign in</a>
			<a href="mailto:<?php echo SUPPORT_EMAIL; ?>">Support</a>
		</div>
	</div>
</div>