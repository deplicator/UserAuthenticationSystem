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
			} else if ($how === 'write') {
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
		} else if($password != $passwordconfirm) {
			header("Location: signup.php?message=mismatch&email=$email");
			exit;
		} else if(strpos($email,'@') === false) {
			header("Location: signup.php?message=invalidemail");
			exit;
		} else if($this->checkEmail($email) == true) {
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
	
	//Signin
	function signin($email, $password) {
		if($email == '' || $password == '') {
			header("Location: signin.php?message=blank");
			exit;
		} else if($this->checkEmail($email) == false) {
			header("Location: signin.php?message=emailnotfound");
			exit;
		} else if($this->checkEmail($email) == true) {
			if($this->checkPassword($email, $password) == false) {
				header("Location: signin.php?message=password");
				exit;
			} else {
				$_SESSION['email'] = $email;
				$_SESSION['signedin'] = true;
				header("Location: ../index.php");
			}
		}
	}
	
	//Logout
	function logout(){
		session_destroy();
		return;
	}
	
	//Checks to see if email is already in database
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
	
	//Checks password
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
	

	
}