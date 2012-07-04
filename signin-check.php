<?php
require ('functions.php');

session_start();

//One of the fields was left blank.
if($_POST['username'] == '' || $_POST['password'] == '') {
	header("Location:signin.php?fail=blank");
}

$username = $_POST["username"];
$password = $_POST["password"];

//Username doens't exsist, return with error.
if(userCheck($username) === false) {
	header("Location:signin.php?fail=username");

//Username does exsit, but password is incorrect, update loginAttempt field in database and return with error.
} else if(userCheck($username) === true && passwordCheck($username, $password) === false) {
	updateLoginAttempt($username);
	header("Location:signin.php?fail=password&username=$username");

//username and password are good, create a cookie, 
} else {
	passwordCheck($username, $password);
	$_SESSION['username'] = $username; //Create a cookie saving the username.
	$_SESSION['loggedIn'] = true; //Create a cookie saying the user is logged in.
	updateLoginAttempt($username, true);
	updateLoginCount($username);
}

//cookie is not working right, fix that

//