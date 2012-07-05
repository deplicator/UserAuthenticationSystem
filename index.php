<?php

include('functions.php');

if(isset($_SESSION['username'])) {
	$username = $_SESSION['username'];
	echo $_SESSION;
} else {
	echo 'no';
}