<?php
include_once '../header.php';

if(isset($_SESSION['signedin']) && $_SESSION['signedin'] === true) {
	$getUser = new authenticate;
	$user = $getUser->userInfo($_SESSION['email']); 
	
	$getProfile = new profile;
	$profile = $getProfile->getProfile($user['id']);
?>

<h2>Profile for <?php echo $user['email']?></h2>
<table>
	<tr><td>Name: </td><td><?php echo $profile['username']; ?></td></tr>
	<tr><td>Email: </td><td><?php echo $user['email']; ?></td></tr>
	<tr><td>Creation Date: </td><td><?php echo $user['creationDate']; ?></td></tr>
	<tr><td>Sign in Count: </td><td><?php echo $user['signinCount']; ?></td></tr>

</table>
	
	
<?php 
} else {
	header("Location: ../index.php");
}
?>