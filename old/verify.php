<?php
include 'header.php';
require 'functions.php';

$email = '';
$emailhash = '';

if(!isset($_GET['email']) || !isset($_GET['emailhash'])) {
	header("Location:signup.php");
} elseif($_GET['email'] == '' || $_GET['emailhash'] == '') {
	header("Location:signup.php");
} else {
	$email = $_GET['email'];
	$emailhash = $_GET['emailhash'];
}

if(emailhashCheck($email, $emailhash) === true) {
	emailVerified($email);
	header("Location:signin.php?fail=verified");
} else {
	header("Location:signup.php");
}
