<?php

	session_start(); 

	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 1) {
		header('Location: ../login.html');
	} else {
	
	//for added safety, we store connection info in to a separate php file
	require("../php_connInfo.php");
	
	//connect to mySql
	$con = mysql_connect("localhost",$user,$password);
	
	//selects the correct database
	mysql_select_db($database)or die("Unable to select database");

	$username = mysql_escape_string($_POST["username"]);
	$adminPassword = mysql_escape_string($_POST["adminPassword"]);
	$newPassword = mysql_escape_string($_POST["newPassword"]);
	$newPassword2 = mysql_escape_string($_POST["newPassword2"]);
	
	//check if passwords are equivelent and are longer than 3 characters
	if(strlen($newPassword)<3 || $newPassword!=$newPassword2){
		echo("New password invalid. Password must be longer than 3 characters or the passwords do not match.");
		header( "Refresh: 3; url=admin.php" ) ;	
		die();
	}
	
	//hashes given passwords
	$hashedAdminPass = hash("md5",$adminPassword);
	$hashedNewPass = hash("md5",$newPassword);
	
	//passed query to database to get admin password
	$query = "SELECT* FROM Credentials WHERE `Username` = 'admin'";
	$result = mysql_query($query) or die("Unable to select");
	$row = mysql_fetch_array($result);
	extract($row);	
	
	//checks if the admin password is correct	
	if($row['Password'] == $hashedAdminPass){
		$query = "UPDATE Credentials SET `Password` = '$hashedNewPass' WHERE `Username` = '$username'";
		mysql_query($query) or die("Unable to select");
	}else{
		echo("Invalid admin password. Please try again.");
		header( "Refresh:2; url=admin.php" ) ;
		die();
	}
	
	//closes the connection
	mysql_close($con);
	
	//redircts this page automatically back to startRoute page
	echo("Password reset successfully. Automatically redirecting..");
   	header( "Refresh:2; url=admin.php") ;

	}
?>
