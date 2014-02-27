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
	$type = mysql_escape_string($_POST["type"]);
	
	//passes the query to the database
	$query = "UPDATE Coordinates SET `Active` = '0' WHERE `Type` = '$type'";

	mysql_query($query);

	//closes the connection
	mysql_close($con);
	
	//redircts this page automatically back to startRoute page
   	header( 'Location: admin.php' );
	
	}

?>
