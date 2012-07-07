<?php 
include("class.login.php");

$log = new logmein();
$log->encrypt = true; //set encryption
//parameters are(SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck($_SESSION['loggedin'], "logon", "password", "useremail") == false){
    header("Location: login.php");
}else{
    echo 'you\'re loged in.';
}


$bcrypt = new Bcrypt(15);

$hash = $bcrypt->hash('dd');
$isGood = $bcrypt->verify('dd', $hash);
var_dump($isGood);