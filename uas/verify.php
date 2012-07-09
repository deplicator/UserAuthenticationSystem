<?php
include 'class.authenticate.php';

if(isset($_GET['reset'])) {
	header("Location: verify-reset.php");
}

$email = '';
$code = '';

//just going to verify.php redirects to signup.php
if(!isset($_GET['email']) || !isset($_GET['code'])) {
	header("Location:signup.php");
//seems redundant, but what the heck.
} elseif($_GET['email'] == '' || $_GET['code'] == '') {
	header("Location:signup.php");
} else {
	$email = $_GET['email'];
	$code = $_GET['code'];

	$verification = new authenticate;
	$verification->verifyEmail($email, $code);
}