<?php 
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

<div id="signin">
<h2>Sign in</h2>
	<form method="POST">
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
</div>