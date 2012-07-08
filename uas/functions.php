<?php
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
				
		case 'success':
			$message = 'User created, you can now sign in.';
			break;
	
		case 'emailreset':
			$message = 'A reset email has been sent.';
			break;
				
		default:
			$message = 'An unknown error has occured.';
			break;
	}
	
	return $message;
}