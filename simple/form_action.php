<?php
//instantiate if needed
include("class.login.php");
$log = new logmein();
$log->encrypt = true; //set encryption
if($_REQUEST['action'] == "login"){
	if($log->login("logon", $_REQUEST['username'], $_REQUEST['password']) == true){
		echo 'yay';
	}else{
		echo 'not logged in';
	}
}