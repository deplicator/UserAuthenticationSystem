<?php
include("class.authenticate.php");

$user = new authenticate();
$user->logout();
header("Location: ../index.php");