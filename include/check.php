<?php
include '../header.php';
require '../functions.php';

$username = '';
$password = '';
$email = '';

if(!isset($_REQUEST['from'])) {
	echo 'error';
}

/*
 * from signin.php
 */
if($_REQUEST['from'] == 'signin') {
	session_start();
	
	//One of the fields was left blank.
	if($_POST['username'] == '' || $_POST['password'] == '') {
		header("Location:../signin.php?fail=blank");
	} else {	
		$username = $_POST["username"];
		$password = $_POST["password"];
		
		//Username doens't exsist, return with error.
		if(userCheck($username) === false) {
			header("Location:../signin.php?fail=username");
		
		//Username does exsit, but password is incorrect, update loginAttempt field in database and return with error.
		} else if(userCheck($username) === true && passwordCheck($username, $password) === false) {
			updateLoginAttempt($username);
			header("Location:../signin.php?fail=password&username=$username");
		
			//username and password are good, create a cookie,
		} else {
			passwordCheck($username, $password);
			$_SESSION['username'] = $username; //Create a cookie saving the username.
			$_SESSION['loggedIn'] = true; //Create a cookie saying the user is logged in.
			updateLoginAttempt($username, true);
			updateLoginCount($username);
		}
	}
	//cookie is not working right, fix that
	
	//
}



/*
 * from signup.php
 */
if($_REQUEST['from'] == 'signup') {
	//Setup variables
	$username = $_POST["username"];
	$password = $_POST['password'];
	$passwordconfirm = $_POST['passwordconfirm'];
	$email = $_POST['email'];
	$accountLock = '1';
	$accountCreationDate = date('Y-m-d H:i:s');
	$emailhash = md5( rand(0,10000) ); // Generate random 32 character hash
	
	//Return to signup page with error.
	if($username == '' || $password == '' || $passwordconfirm == '' || $email == '') {
		header("Location:../signup.php?fail=blank");
	} else if(userCheck($username) === true) {
		header("Location:../signup.php?fail=usernameinuse");
	} else if($password !== $passwordconfirm) {
		header("Location:../signup.php?fail=passwordmatch");
	} else if(strpos($email,'@') === false) {
		header("Location:../signup.php?fail=invalidemail");
	}
	
	//Put new user in database, accountLock set to 1.
	$conn = databaseConnect('write');
	$sql = "INSERT INTO users (username, password, email, accountLock, accountCreationDate, emailhash)
	VALUES (?, ?, ?, ?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$stmt->execute(array($username, $password, $email, $accountLock, $accountCreationDate, $emailhash));
	$stmt->closeCursor();
	
	$subject = 'New User Created - Please Verify Email';
	$message = '
	
	Thanks for signing up as ' . $username . '! Please click the following link to activate your account.
	
	<a href="http://localhost/verify.php?email='.$email.'&emailhash='.$emailhash.'">http://www.yourwebsite.com/verify.php?email='.$email.'&emailhash='.$emailhash.'</a>
	
	';
	
	
	$from = 'SUPPORT_EMAIL';
	$headers = "From:" . $from;
	mail($email,$subject,$message,$headers);

}


/*
 * can verify.php be added to this?
 */


/*
 * Reset account code, from reset.php
 */
if($_REQUEST['from'] == 'reset') {
	if($_POST['username'] == '' && $_POST['email'] == '') {
		header("Location:../reset.php?fail=blank");
		break;
	} else {
		$username = $_POST['username'];
		$email = $_POST['email'];
	}
	
	do
		if(userCheck($username) == true) {
			echo 'username is good';
			break;
		} elseif(emailCheck($email) == true) {
			echo 'email is good';
			break;
		} elseif(userCheck($username) == false) {
			header("Location:../reset.php?fail=username");
		} else {
			header("Location:../reset.php?fail=invalidemail");
		}
	while (false);

}