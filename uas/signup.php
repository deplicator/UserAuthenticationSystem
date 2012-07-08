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
	$user->newUser($_POST['email'], $_POST['password'], $_POST['passwordconfirm']);
}

?>

<div class="uasinfo" id="signup">
<h2>Sign up</h2>
	<form id="form" method="POST">
		<div id="email">
			<label for="username">Email</label>
			<input type="text" name="email" value="<?php echo $email; ?>" spellcheck="false" />
		</div>
		
		<div id="password">
			<label for="password">Password</label>
			<input type="password" name="password" />
		</div>
		
		<div id="passwordconfirm">
			<label for="passwordconfirm">Confirm Password</label>
			<input type="password" name="passwordconfirm" />
		</div>
		<input id="submit" type="submit" value="Submit"/>
	</form>
	<p><?php echo $message; ?></p>
	<div id="signin-menu-border">
		<div id="signin-menu">
		<a href="./signin.php">Sign in</a>
		<a href="./reset.php">Reset</a>
		<a href="mailto:<?php echo SUPPORT_EMAIL; ?>">Support</a>
		</div>
	</div>
</div>