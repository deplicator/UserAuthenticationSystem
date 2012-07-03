<?php
define('DATABASEHOST', 'localhost');
define('DATABASE_NAME', 'userlogin');
define('DATABASE_READONLY_USER', 'userlogin');
define('DATABASE_READONLY_PASSWORD', 'uJQuRqEuR');
define('DATABASE_WRITE_USER', 'userlogin');
define('DATABASE_WRITE_PASSWORD', 'uJQuRqEuR');
define('SUPPORT_EMAIL', 'support@anemailaddress.com');

function databaseConnect($how = 'readonly') {
	$DSN = 'mysql:host=' . DATABASEHOST . ';dbname=' . DATABASE_NAME;
	try {
		if($how === 'readonly') {
			$conn = new PDO($DSN, DATABASE_READONLY_USER, DATABASE_READONLY_PASSWORD);
			return $conn;
		} else if ($how === 'write') {
			$conn = new PDO($DSN, DATABASE_WRITE_USER, DATABASE_WRITE_PASSWORD);
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

function failType($fail) {
	switch ($fail) {
		case 'username':
			$failType = 'Invalid username';
			break;

		case 'password':
			$failType = 'Incorrect password';
			break;

		case 'blank':
			$failType = 'Field left blank';
			break;

		case 'account':
			$failType = 'Problem with account: contact support.';
			break;
			
		case 'usernameinuse':
			$failType = 'That username is unavailable.';
			break;
			
		case 'passwordmatch':
			$failType = 'Password mistype.';
			break;
		
		case 'invalidemail':
			$failType = 'Invalid email address.';
			break;

		default:
			$failType = 'unknown error';
			break;
	}
	
	return $failType;
}

function userCheck($username) {
	$conn = databaseConnect();
	$sql = "SELECT username FROM users WHERE username = '$username'";
	$stmt = $conn->query($sql);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if($result['username'] === $username) {
		return true;
	} else {
		return false;
	}
}

function passwordCheck($username, $password) {
	$conn = databaseConnect();
	$sql = "SELECT password FROM users WHERE username = '$username' && password = '$password' LIMIT 1";
	$stmt = $conn->query($sql);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($result['password'] === $password) {
		return true;
	} else {
		return false;
	}
}

function updateLoginCount($username) {
	$conn = databaseConnect();
	$sql = "SELECT loginCount FROM users WHERE username = '$username' LIMIT 1";
	$stmt = $conn->query($sql);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$i = $result['loginCount'];
	$i++;
	$conn = databaseConnect('write');
	$sql = "UPDATE users SET loginCount = '$i' WHERE username = '$username' LIMIT 1";
	$stmt = $conn->exec($sql);
	$conn = null;
}

function updateLoginAttempt($username, $reset = false) {
	$conn = databaseConnect();
	$sql = "SELECT attemptCount FROM users WHERE username = '$username' LIMIT 1";
	$stmt = $conn->query($sql);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$i = $result['attemptCount'];
	$i++;
	$conn = databaseConnect('write');
	if($reset === false) {
		$sql = "UPDATE users SET attemptCount = '$i' WHERE username = '$username' LIMIT 1";
		$stmt = $conn->exec($sql);
	} else {
		$sql = "UPDATE users SET attemptCount = '0' WHERE username = '$username' LIMIT 1";
		$stmt = $conn->exec($sql);
	}
	$conn = null;
}

/*
class Encryption {
	var $skey = "yourSecretKey"; // you can change it

	public function safe_b64encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
		return $data;
	}

	public function safe_b64decode($string) {
		$data = str_replace(array('-', '_'), array('+', '/'), $string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}

	public function encode($value) {
		if (!$value) {
			return false;
		}
		$text = $value;
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->skey), $text, MCRYPT_MODE_ECB, $iv);
		return trim($this->safe_b64encode($crypttext));
	}

	public function decode($value) {
		if (!$value) {
			return false;
		}
		$crypttext = $this->safe_b64decode($value);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->skey), $crypttext, MCRYPT_MODE_ECB, $iv);
		return trim($decrypttext);
	}
}
*/