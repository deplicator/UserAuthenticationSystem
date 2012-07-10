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

	echo $_REQUEST['email'] . "<br>";
	echo $_REQUEST['password'] . "<br>";
	echo $_REQUEST['passwordconfirm'] . "<br>";
	echo $_REQUEST['spnCode'] . "<br>";
	echo $_REQUEST['spnCodeConfirm'] . "<br>";

	$user = new authenticate();
	$user->newUser($_POST['email'], $_POST['password'], $_POST['passwordconfirm'],
		$_REQUEST['spnCode'], $_REQUEST['spnCodeConfirm']);
}

?>
<script src="testscript.js"></script>
<div id="info">
<p>Key stroke level password. The password and password confirmation box must 
match both inputed text and the keystrokes used to input that text. For example 
if the user entered "password" in the password field, but typed 'p' 'a' 's' 's' 
'q' 'backspace' 'w' 'o' 'r' 'd'. The 'q' and the 'backspace' that erased it 
would also have the be done in the password confirm field. This pattern would 
also have to be matched when the user logs in.</p>
</div>
<div class="uasinfo" id="signup">
<h2>Sign up</h2>
	<form id="form" method="POST">
		<div id="email">
			<label for="username">Email</label>
			<input type="text" name="email" value="<?php echo $email; ?>" spellcheck="false" />
		</div>
		
		<div id="password">
			<label for="password">Password</label>
			<input type="password" name="password" ONKEYDOWN="javascript: displayKeyCode(event, 'spnCode')"/>
		</div>
		
		<div id="passwordconfirm">
			<label for="passwordconfirm">Confirm Password</label>
			<input type="password" name="passwordconfirm" ONKEYDOWN="javascript: displayKeyCode(event, 'spnCodeConfirm')" />
		</div>
		<div id="lastinput">
			<label>Last Key Press</label>
			<input id="txtChar" onkeypress="javacript: return false;" onkeydown="javascript: return displayKeyCode(event)" TYPE="text" NAME="txtChar"></span>
		</div>
		<input type="hidden" id="spnCode" name="spnCode" />
		<input type="hidden" id="spnCodeConfirm" name="spnCodeConfirm" />
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