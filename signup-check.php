<?php
require ('functions.php');

//Setup variables
$username = $_POST["username"];
$password = $_POST['password'];
$passwordconfirm = $_POST['passwordconfirm'];
$email = $_POST['email'];
$accountLock = '1';
$accountCreationDate = date('Y-m-d H:i:s');

//Return to signup page with error.
if($username == '' || $password == '' || $passwordconfirm == '' || $email == '') {
	header("Location:signup.php?fail=blank");
} else if(userCheck($username) === true) {
	header("Location:signup.php?fail=usernameinuse");
} else if($password !== $passwordconfirm) {
	header("Location:signup.php?fail=passwordmatch");
} else if(strpos($email,'@') === false) {
	header("Location:signup.php?fail=invalidemail");	
}

//Put new user in database, accountLock set to 1.
$conn = databaseConnect('write');
$sql = "INSERT INTO users (username, password, email, accountLock, accountCreationDate) 
		VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute(array($username, $password, $email, $accountLock, $accountCreationDate));
$stmt->closeCursor();

//Send email to user with some way to set accountLock to 0.
//A way to resend email.

//Go to another page