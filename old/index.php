<?php

include('functions.php');
session_start();
if(!$_SESSION['loggedIn']) { // If the user IS NOT logged in, forward them back to the login page
		echo 'no';
} else { //If the user IS logged in, then echo the page contents:
	$currentUser = $_SESSION['username']; //Gets username from cookie created in login-check.php.
}
echo $currentUser . " is signed in.";