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
	
	//sets the variables 
	$x = mysql_escape_string($_POST['x']);
	$y = mysql_escape_string($_POST['y']);
	$ts = mysql_escape_string($_POST['ts']);
	$acc = mysql_escape_string($_POST['acc']);
	$dir = mysql_escape_string($_POST['dir']);
	$type = mysql_escape_string($_POST['type']);
	
	//passes the query to the database
	$query = "UPDATE Coordinates SET `Lat` = '$x', `Long`='$y', `Timestamp` = '$ts', `Accuracy` = '$acc', `Direction` = '$dir' WHERE `Type` = '$type'";
	mysql_query($query);
	
	//Returns the status of the given bus
	$query = "SELECT `Active` FROM Coordinates WHERE `Type` = '$type'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	echo $row[0];
	
	//closes the connection
	mysql_close($con);
	
	}
?>
