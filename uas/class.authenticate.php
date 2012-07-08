<?php
include 'class.bcrypt.php';

//For security reasons, don't display any errors or warnings.
error_reporting(0);

//start session
session_start();
class authenticate {
	
	var $db_hostname = 'localhost';					//Database server LOCATION
	var $db_name = 'UserAuthenticationSystem';		//Database NAME
	var $db_ro_user = 'uasuser';					//Database read only user
	var $db_ro_password = 'u5YzpCaZQfF';			//Database read only password
	var $db_user = 'uasuser';						//Database read/write user
	var $db_password = 'u5YzpCaZQfF';				//Database read/write password

	//new database connection
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
	
	//Create user
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
			$stmt->closeCursor();
			header("Location: signin.php?message=success");
		}
	}
	
	/*
	 * Sign in function, runs checks against username and password and returns 
	 * message if unsuccessful.
	 */
	function signin($email, $password) {
		//Blank field.
		if($email == '' || $password == '') {
			header("Location: signin.php?message=blank");
			exit;
		//Email not found in database.
		} elseif($this->checkEmail($email) == false) {
			header("Location: signin.php?message=emailnotfound");
			exit;
		//Email found, ...
		} elseif($this->checkEmail($email) == true) {
			// ...but password incorrect.
			if($this->checkPassword($email, $password) == false) {
				header("Location: signin.php?message=password&email=$email");
				$this->updateAttempts($email); //Increment failed attempts by 1.
				exit;
			// ...but account is locked.
			} elseif($this->checkLock($email) == true) {
				header("Location: signin.php?message=account");
				exit;
			// ...and do sign in procedure.
			} else {
//!!This is not working as expected!!
//fix it - fucntion work individually, else statement only runs first line.
//It worked fine until I added the two functions. Similar weirdness on line 90 and 91.
				$this->updateSigninCount($email); //Add 1 to signinCount.
				$this->updateAttempts($email, true); //Reset failed attempts.
				$_SESSION['email'] = $email; //Create session.
				$_SESSION['signedin'] = true;
				header("Location: ../index.php");
				exit();
			}
		}
	}
	
	//Logout
	function logout(){
		session_destroy();
		return;
	}
	
	//Reset account
	function reset($email) {
		if($email == '') {
			header("Location: reset.php?message=blank");
			exit;
		} elseif($this->checkEmail($email) == false) {
			header("Location: reset.php?message=emailnotfound");
			exit;
		} else {
			$this->sendEmail($email);
			header("Location: reset.php?message=emailreset");
		}
	}
	
//The following are generic functions used more than once (in most cases).
	
	/*
	 * Checks to see if email is already in the database.
	 */
	private function checkEmail($email) {
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
	private function checkPassword($email, $password) {
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
	 * Check if account is locked, account can be locked if it is new and email
	 * has not been verified, or if there have been too many failed login
	 * attempts.
	 */
	private function checkLock($email) {
		$conn = $this->databaseConnect();
		$sql = "SELECT locked FROM users WHERE email = '$email' LIMIT 1";
		$stmt = $conn->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result['locked'] == '1') {
			return true;
		} else {
			return false;
		}
	}
	
	/* 
	 * Updates number of login attempts by email. When $reset parameter is set 
	 * to true it will reset attempts to 0.
	 */
	private function updateAttempts($email, $reset = false) {
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
		$stmt->closeCursor();
	}
	
	/*
	 * Tracks total number of times a user has logged in.
	 */
	private function updateSigninCount($email) {
		$conn = $this->databaseConnect();
		$sql = "SELECT signinCount FROM users WHERE email = '$email' LIMIT 1";
		$stmt = $conn->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$i = $result['signinCount'];
		$i++;
		
		$conn = $this->databaseConnect('write');
		$sql = "UPDATE users set signinCount = '$i' WHERE email = '$email' LIMIT 1";
		$stmt = $conn->exec($sql);
		$stmt->closeCursor();
	}
	
	/*
	 * Sends an email with email hash for initial account verification or 
	 * account resets.
	 */
	private function sendEmail($email) {
		//insert magic here
	}

	
}