<?php
include("class.login.php");
$log = new logmein();
$log->encrypt = true; //set encryption
//parameters here are (form name, form id and form action)
$log->loginform("loginformname", "loginformid", "form_action.php");