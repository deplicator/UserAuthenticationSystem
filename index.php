<?php 
include 'header.php';
include './uas/class.authenticate.php';

if($_SESSION['signedin'] === true) {
	echo $_SESSION['email'] . "<br>";
	echo '<a href="./uas/logout.php">Logout</a>';

} else {
	echo '<a href="./uas/signin.php">Sign in</a><br>';
	echo '<a href="./uas/signup.php">Sign up</a><br>';
}
