<?php
include '../header.php';
require '../functions.php';

$username = '';
$email = '';

if(!isset($_REQUEST['from'])) {
	echo 'error';
}

/*
 * from signin.php
 */


/*
 * from signup.php
 */


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