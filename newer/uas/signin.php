<?php include '../header.php';?>

<div id="signin" class="uasinfo">
	<h2>Sign in</h2>
	
	<form id="form" action="check.php" method="POST">
		<div id="username">
			<label for="username">Username</label>
			<input type="text" name="username" value="" size="20" spellcheck="false" />
		</div>
		
		<div id="password">
			<label for="password">Password</label>
			<input type="password" name="password" />
		</div>
		<input id="submit" name="submit" type="submit" value="Sign in"/>
	</form>
	<div id="signin-menu-border">
		<div id="signin-menu">
			<a href="./signup.php">Sign up</a>
			<a href="./reset.php">Reset</a>
			<a href="mailto:<?php echo SUPPORT_EMAIL; ?>">Support</a>
		</div>
	</div>
</div>

<?php include '../footer.php';?>