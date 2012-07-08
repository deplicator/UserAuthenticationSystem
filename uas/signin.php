<?php 
include '../header.php';
include 'functions.php';
$message = '';
$email = '';

if(isset($_REQUEST['message'])) {
	$message = getMessage($_REQUEST['message']);
}

if(isset($_REQUEST['email'])) {
	$email = $_REQUEST['email'];
}

if(isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
	include("class.authenticate.php");

	$user = new authenticate();
	$user->signin($_REQUEST['email'], $_REQUEST['password']);
}

?>

<div class="uasinfo" id="signin">
<h2>Sign in</h2>
	<form id="form" method="POST">
		<div id="email">
			<label for="username">email</label>
			<input type="text" name="email" value="<?php echo $email; ?>" spellcheck="false" />
		</div>
		
		<div id="password">
			<label for="password">Password</label>
			<input type="password" name="password" />
		</div>

		<input id="submit" type="submit" value="Submit"/>
	</form>
	<p><?php echo $message; ?></p>
	<div id="signin-menu-border">
		<div id="signin-menu">
		<a href="./signup.php">Sign up</a>
		<a href="./reset.php">Reset</a>
		<a href="mailto:<?php echo SUPPORT_EMAIL; ?>">Support</a>
		</div>
	</div>
</div>