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
	
	//sets the variable
	$message = mysql_escape_string($_POST["message"]);
	
	//passes the query to the database
	$query = "UPDATE `Alert Message` SET `Message` = '$message'";

	mysql_query($query);

	//closes the connection
	mysql_close($con);
	
	//redircts this page automatically back to startRoute page
   	header( 'Location: admin.php' ) ;
	
	}

?>
