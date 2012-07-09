<?php
include 'class.bcrypt.php';

//For security reasons, don't display any errors or warnings.
//error_reporting(0);

//start session
session_start();
class authenticate {
	
	var $db_hostname = 'localhost';					//Database server LOCATION
	var $db_name = 'UserAuthenticationSystem';		//Database NAME
	var $db_ro_user = 'uasuser';					//Database read only user
	var $db_ro_password = 'u5YzpCaZQfF';			//Database read only password
	var $db_user = 'uasuser';						//Database read/write user
	var $db_password = 'u5YzpCaZQfF';				//Database read/write password

	
	/*
	 * Makes connection to database, optional parameter of 'write' to make 
	 * writeable connection to database assuming database permissions are set 
	 * correctly on database users.
	 */
	function databaseConnect($how = 'readonly') {
		$DSN = 'mysql:host=' . $this->db_hostname . ';dbname=' . $this->db_name;
		try {
			if($how === 'readonly') {
				$conn = new PDO($DSN, $this->db_ro_user, $this->db_ro_password);
				return $conn;
			} elseif ($how === 'write') {
				$conn = new PDO($DSN, $this->db_user, $this->db_password);
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
	 * Creat a new user in the database and send verification email.
	 */
	function newUser($email, $password, $passwordconfirm) {
		if($email == '' || $password == '' || $passwordconfirm == '') {
			header("Location: signup.php?message=blank");
			exit;
		} elseif($password != $passwordconfirm) {
			header("Location: signup.php?message=mismatch&email=$email");
			exit;
		} elseif(strpos($email,'@') === false) {
			header("Location: signup.php?message=invalidemail");
			exit;
		} elseif($this->checkEmail($email) == true) {
			header("Location: signup.php?message=emailinuse");
			exit;
		} else {
			$CreationDate = date('Y-m-d H:i:s');
			
			$passwordbcrypt = new Bcrypt(10);
			$hash = $passwordbcrypt->hash($password);
			
			$emailhashbcrypt = new Bcrypt(15);
			$emailhash = $emailhashbcrypt->hash(rand(0,10000));
			
			$conn = $this->databaseConnect('write');
			$sql = "INSERT INTO users (email, password, CreationDate, emailhash)
			VALUES (?, ?, ?, ?)";
			$stmt = $conn->prepare($sql);
			$stmt->execute(array($email, $hash, $CreationDate, $emailhash));
			$conn = null;
			$this->sendEmail($email);
			header("Location: signin.php?message=checkemail");
		}
	}
	
	
	/*
	 * Sign in function, runs checks against username and password and returns 
	 * message if unsuccessful.
	 */
	function signin($email, $password) {
		//Blank field.
		if($email == '' || $password == '') {
			header("Location: signin.php?message=blank&email=$email");
			exit;
		//Email not found in database.
		} elseif($this->checkEmail($email) == false) {
			header("Location: signin.php?message=emailnotfound");
			exit;
		//Email found, ...
		} elseif($this->checkEmail($email) == true) {
			// ...but account is locked.
			if($this->checkLock($email) == true) {
				header("Location: signin.php?message=account");
				exit;
			// ...but password incorrect.
			} elseif($this->checkPassword($email, $password) == false) {
				$this->updateAttempts($email); //Increment failed attempts by 1.
				header("Location: signin.php?message=password&email=$email");
				exit;
			// ...and do sign in procedure.
			} else {
				$_SESSION['email'] = $email; //Create session.
				$_SESSION['signedin'] = true;
				$this->updateAttempts($email, true); //Reset failed attempts.
				$this->updateSigninCount($email); //Add 1 to signinCount.
				header("Location: ../index.php");
				exit();
			}
		}
	}
	
	
	/*
	 * Logs a user out.
	 */
	function logout(){
		session_destroy();
		return;
	}
	
	
	/*
	 * Sends reset email to email in database with reset instructions.
	 */
	function reset($email) {
		if($email == '') {
			header("Location: reset.php?message=blank");
			exit;
		} elseif($this->checkEmail($email) == false) {
			header("Location: reset.php?message=emailnotfound");
			exit;
		} else {
			$this->sendEmail($email, 'reset');
			header("Location: reset.php?message=emailreset");
		}
	}

	
	/*
	 * Checks to see if email is already in the database.
	 */
	function checkEmail($email) {
		$conn = $this->databaseConnect();
		$sql = "SELECT email FROM users WHERE email = '$email'";
		$stmt = $conn->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result['email'] === $email) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/*
	 * Checks account password. Requires email and password.
	 */
	function checkPassword($email, $password) {
		$conn = $this->databaseConnect();
		$sql = "SELECT password FROM users WHERE email = '$email' LIMIT 1";
		$stmt = $conn->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$bcrypt = new Bcrypt(10);
		$check = $bcrypt->verify($password, $result['password']);
		
		if($check === true) {
			return true;
		} else {
			return false;
		}
	}

	
	/*
	 * Check if account is locked, account is locked if it is new and email has 
	 * not been verified, or if there have been too many failed login attempts.
	 * If $reset parameter set to true the function will unlock the account.
	 */
	function checkLock($email, $reset = false) {
		if($reset === false) {
			$conn = $this->databaseConnect();
			$sql = "SELECT locked FROM users WHERE email = '$email' LIMIT 1";
			$stmt = $conn->query($sql);
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result['locked'] == '1') {
				return true;
			} else {
				return false;
			}
		} elseif ($reset === true) {
			$conn = $this->databaseConnect('write');
			$sql = "UPDATE users SET locked = '0' WHERE email = '$email' LIMIT 1";
			$stmt = $conn->exec($sql);
			$conn = null;
		}
	}
	
	
	/* 
	 * Updates number of login attempts by email. When $reset parameter is set 
	 * to true it will reset attempts to 0. Also will lock account if the 
	 * number of attempts reachs the admin specified number (default 10).
	 */
	function updateAttempts($email, $reset = false) {
		$conn = $this->databaseConnect();
		$sql = "SELECT attempts FROM users WHERE email = '$email' LIMIT 1";
		$stmt = $conn->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$i = $result['attempts'];
		$i++;

		$conn = $this->databaseConnect('write');
		if($reset === false) {
			$sql = "UPDATE users SET attempts = '$i' WHERE email = '$email' LIMIT 1";
		} else {
			$sql = "UPDATE users SET attempts = '0' WHERE email = '$email' LIMIT 1";
		}
		$stmt = $conn->exec($sql);
		
		if($i >= 10) {
			$sql = "UPDATE users SET locked = '1' WHERE email = '$email' LIMIT 1";
			$stmt = $conn->exec($sql);
		}
		
		$conn = null;
	}
	
	
	/*
	 * Tracks total number of times a user has logged in.
	 */
	function updateSigninCount($email) {
		$conn = $this->databaseConnect();
		$sql = "SELECT signinCount FROM users WHERE email = '$email' LIMIT 1";
		$stmt = $conn->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$i = $result['signinCount'];
		$i++;
		
		$conn = $this->databaseConnect('write');
		$sql = "UPDATE users set signinCount = '$i' WHERE email = '$email' LIMIT 1";
		$stmt = $conn->exec($sql);
		$conn = null;
	}
	
	
	/*
	 * Creates new email hash from random number when called. Sends an email 
	 * with random number generated for initial account verification or account 
	 * resets. Optional parameter $reset = 'reset' for account reset.
	 */
	function sendEmail($email, $reset = 'no') {
		$random = rand(0,100000000000);
		$emailhashbcrypt = new Bcrypt(15);
		$emailhash = $emailhashbcrypt->hash($random);
		
		$conn = $this->databaseConnect('write');
		$sql = "UPDATE users SET emailhash = ? WHERE email = ?";
		$stmt = $conn->prepare($sql);
		$stmt->execute(array($emailhash, $email));
		$conn = null;
		
		//email
		if($reset = 'no') {
			$subject = 'New User Created - Please Verify Email';
			$link = 'http://localhost/verify.php?email=' . $email . '&code=' . $random;
			$message = '		
			Thanks for signing up ' . $email . '! Please click the following link to 
			activate account.<br>
			<a href="' . $link . '">' . $link . '</a>
			';
			$from = 'SUPPORT_EMAIL';
			$headers = "From:" . $from;
			mail($email,$subject,$message,$headers);
		} elseif($reset = 'reset') {
			$subject = 'Account Reset - Please Verify Email';
			$link = 'http://localhost/verify.php?email=' . $email . '&code=' . $random . '&reset=reset';
			$message = '
			Please click the following link to reset account.<br>
			<a href="' . $link . '">' . $link . '</a>
			';
			$from = 'SUPPORT_EMAIL';
			$headers = "From:" . $from;
			mail($email,$subject,$message,$headers);
		}
	}
	
	
	/*
	 * Checks for email in database, verifies code against emailhash.
	 */
	function verifyEmail($email, $code) {
		if($this->checkEmail($email) === true) {
			$conn = $this->databaseConnect();
			$sql = "SELECT emailhash FROM users WHERE email = '$email'";
			$stmt = $conn->query($sql);
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			$bcrypt = new Bcrypt(10);
			$check = $bcrypt->verify($code, $result['emailhash']);
			
			if($check === true) {
				$this->checkLock($email, true);
				header("Location: signin.php?message=verified");
				exit();
			} elseif($check === false) {
				header("Location: signup.php");
			}
		} else {
			header("Location: signup.php");
		}
	}
	
	
}