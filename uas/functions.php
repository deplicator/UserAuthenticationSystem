<?php

/*
 * Makes connection to database, optional parameter of 'write' to make
* writeable connection to database assuming database permissions are set
* correctly on database users.
*/
function databaseConnect($how = 'readonly') {
	$DSN = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
	try {
		if($how === 'readonly') {
			$conn = new PDO($DSN, DBROUSER, DBROPASS);
			return $conn;
		} elseif ($how === 'write') {
			$conn = new PDO($DSN, DBWUSER, DBWPASS);
			return $conn;
		} else {
			$file = './errors.txt';
			$error = date('Y-m-d H:i:s') . ' - Function: ' . __FUNCTION__ . ' - Invalid Parameter.' . PHP_EOL;
			file_put_contents($file, $error, FILE_APPEND | LOCK_EX);
		}
	} catch (PDOException $e) {
		$file = './errors.txt';
		$error = date('Y-m-d H:i:s') . ' - Function: ' . __FUNCTION__ . ' - ' . $e->getMessage() . PHP_EOL;
		file_put_contents($file, $error, FILE_APPEND | LOCK_EX);
	}
}

/*
 * Returns messages.
 */
function getMessage($message) {
	switch ($message) {
		case 'emailnotfound':
			$message = 'That email is not not found in the database.';
			break;
	
		case 'password':
			$message = 'Incorrect password.';
			break;
	
		case 'blank':
			$message = 'A field was left blank.';
			break;
	
		case 'account':
			$message = 'There is a problem with your account.';
			break;
				
		case 'emailinuse':
			$message = 'That email address is already in use.';
			break;
				
		case 'mismatch':
			$message = 'The password fields do not match.';
			break;
	
		case 'invalidemail':
			$message = 'Invalid email address.';
			break;
				
		case 'checkemail':
			$message = 'New accounts are locked until email has been verified.';
			break;
	
		case 'emailreset':
			$message = 'A reset email has been sent.';
			break;

		case 'verified':
			$message = 'Account email verified, you can now sign in.';
			break;
				
		default:
			$message = 'An unknown error has occured.';
			break;
	}
	
	return $message;
}