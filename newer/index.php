<?php
include 'header.php';



session_start();

if(!$_SESSION) { // If the user IS NOT logged in, forward them back to the login page
		echo 'No user signed in.<br>'; ?>

<a href="./uas/signin.php">Sign in</a><br>
<a href="./uas/signup.php">Sign up</a><br>
<a href="./uas/reset.php">Reset</a>		

<?php 
} else { //If the user IS logged in, then echo the page contents:
	$currentUser = $_SESSION['username']; //Gets username from cookie created in login-check.php.
	echo $currentUser . " is signed in.<br>"; ?>
	<a href="">Sign out</a>	

<?php } include 'footer.php';?>