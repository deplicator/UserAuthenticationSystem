<?php 
include 'header.php';
require 'functions.php';

$username = '';
$failType = '';

if(isset($_GET['fail'])) {
	$failType = failType($_GET['fail']);
}

if(isset($_GET['username'])) {
	$username = $_GET['username'];
}

?>

<div id="signin">
	<h2>Sign in</h2>
	<p><?php echo $failType; ?></p>
	
	<form action="signin-check.php" method="POST">
		<div id="username">
			<label for="username">Username</label>
			<input type="text" name="username" value="<?php echo $username ?>" size="20" spellcheck="false" />
		</div>
		
		<div id="password">
			<label for="password">Password</label>
			<input type="password" name="password" />
		</div>
		<input id="submit" type="submit" value="Submit"/>
	</form>
	<div id="signin-menu">
		<a href="./signup.php">Sign up</a>
		<a href="./reset.php">Reset</a>
		<a href="mailto:<?php echo SUPPORT_EMAIL; ?>">Support</a>
	</div>
</div>

<?php include 'footer.php';?>
