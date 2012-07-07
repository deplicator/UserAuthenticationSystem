<?php
require '../uasConfig.php';
require APPPATH . '/uas/functions.php';

/*
 * Attempting to check.php without coming from another page will return to
 * index.php.
 */
if(!$_POST['submit']) {
	header('Location: ../index.php');
}
	

/*
 * From signin.php
 */
if($_POST['submit'] == 'Sign in') {
	echo 'do this';
}