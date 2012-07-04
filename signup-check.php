<?php
include 'header.php';
require 'functions.php';

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

?>

<!-- put successful page info here -->
<p>You'll get a verification email to enable your new account.</p>
<p>Didn't get it? Check your spam box or <a href="reset.php">resend verification email</a>.</p>

<?php include 'footer.php';?>
