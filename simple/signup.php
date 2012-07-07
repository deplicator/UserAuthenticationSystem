<?php
include("class.login.php");
$log = new logmein();
$log->encrypt = true; //set encryption
//parameters here are (form name, form id and form action)
$log->signupform("signupformname", "signupformid", "signup_action.php");