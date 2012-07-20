<?php
include_once 'functions.php';
include_once 'class.authenticate.php';
include_once 'class.bcrypt.php';
include_once 'class.profile.php';

//Site specific constants.
define('SERVERPATH', $_SERVER["SERVER_NAME"] . '/UserAuthenticationSystem');	//Path to install
define('SITENAME', 'User Authentication System');															//For titles ans such
define('SUPPORT_EMAIL', 'someoneelse@somewhereesle.com');						//Support email

//Database connection constants.
define('DBHOST', 'localhost');													//Database server location
define('DBNAME', 'UserAuthenticationSystem');									//Database name
define('DBROUSER', 'uasuser');													//Database read only user
define('DBROPASS', 'u5YzpCaZQfF');												//Database read only password
define('DBWUSER', 'uasuser');													//Database read/write user
define('DBWPASS', 'u5YzpCaZQfF');											//Database read/write password