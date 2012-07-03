<?php require "functions.php";

	session_start();

	$username = $_POST["username"]; //Input from login.php.
	$passwordhash = $_POST["passwordhash"]; //Input from login.php.

	//Connects to database with a read only user.
	connectReadOnly();

	$result=mysql_query("SELECT * FROM users WHERE username = '$username';"); //Query by username only to verify it exists.
	$check=mysql_fetch_assoc($result);

	$hint = $check[passwordhint];
	$id = $check[id]; //Grabbing user id for later
	$attempts = $check[loginAttempts]; //Grabbing previous number of log in attempts.

	if($check[username] !== $username) { //If username doesn't exist...
		header("Location:login.php?fail=username"); //...return to login.php with failed username message.
	}
	else { //If username does exist carry on.
		$sql = "SELECT * FROM users WHERE username = '$username' AND passwordhash = AES_ENCRYPT('$passwordhash', '6fcd83b987a18a9fa12e9df46cccfc3e');";
		$result=mysql_query($sql);

		$count=mysql_num_rows($result); //Check if supplied username and password has a match in the database.

		if($count==1) { //If there is a match...
			$_SESSION["username"] = $username; //Create a cookie saving the username.
			$_SESSION["loggedIn"] = true; //Create a cookie saying the user is logged in.

			connect(); //Could this be moved to index.php?
			mysql_query("UPDATE users SET `loginAttempts` = '0' WHERE `id` = '$id';"); //Resets login attempts to 0.

			//records user log ins to file.
			$logfile = "admin/log.php";
			$fh = fopen($logfile, 'a');
			$time = date('F d, Y G:i:s');
			$entry = "<a class=\"logentry\">".$time." - ".$username." successfully logged into the system.<a><br />\n";
			fwrite($fh, $entry);
			fclose($fh);

			header("Location:index.php");
		}
		else { //If this query is invalid then it has to be a bad password.
			connect();//connect with write access to updated login attempts.
			$i = $attempts;
			$i++;
			mysql_query("UPDATE users SET `loginAttempts` = '$i' WHERE `id` = '$id';"); //Add one to loginAttempts field for lock out purposes.

			//Records failed user logins to file.
			$logfile = "admin/log.php";
			$fh = fopen($logfile, 'a');
			$time = date('F d, Y G:i:s');
			$entry = "<a class=\"logentry\">".$time." - ".$username." failed to log into the system. Attempt #: ".$i."<a><br />\n";
			fwrite($fh, $entry);
			fclose($fh);
			
			header("Location:login.php?fail=password&username=". $username ."&pwhint=". $hint .""); //Return to login.php with failed password message.
		}
	}
?>