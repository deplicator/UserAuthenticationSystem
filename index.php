<?php 
include 'header.php';

if(isset($_SESSION['signedin']) && $_SESSION['signedin'] === true) {
	$getUser = new authenticate;
	$user = $getUser->userInfo($_SESSION['email']);
	echo 'Signed in as <a href="uas/profile.php?id=' . $user['id'] . '" 
		title="Click to edit your profile.">' . $user['email'] . '</a>.<br>';
	echo '<a href="./uas/logout.php">Logout</a>';

} else {
		echo '<a href="./uas/signin.php">Sign in</a><br>';
		echo '<a href="./uas/signup.php">Sign up</a><br>';
}