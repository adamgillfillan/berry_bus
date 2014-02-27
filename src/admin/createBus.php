<?php
	error_reporting(0);
	
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
	$marker = mysql_escape_string($_POST["marker"]);
	
	
	//check if type can be a legit bus
	if(strlen($type)<3){
		echo("Bus name invalid. Must be longer than 3 characters.");
		header( "Refresh: 5; url=admin.php" ) ;	
		die();
	}
	
	//check if radio button/marker is selected
	if($marker==null){
		echo("Bus type invalid. Must select a bus type.");
		header( "Refresh: 3; url=admin.php" ) ;	
		die();
	}
	
	//checks if given name is already a current bus
	$query = "SELECT* FROM Coordinates WHERE Active = 0";
	$result = mysql_query($query) or die("Unable to select");
	while($row=mysql_fetch_array($result)){
		extract($row);
		if($row['Type']==$type||$type==""){
			echo("Bus name already in use or invalid. Please choose a new name.");
			header( "Refresh: 3; url=admin.php" ) ;	
			die();
		}
	}
	
	$timestamp = time() * 1000;
	//echo($timestamp);
	
	//passes the query to the database
	$query = "INSERT INTO `berrybus`.`Coordinates` (`ID`, `Lat`, `Long`, `Timestamp`, `Accuracy`, `Direction`, `Type`, `Active`, `Marker`)
			  VALUES (NULL, '', '', '$timestamp', '', '', '$type', '', '$marker')";

	mysql_query($query);

	//closes the connection
	mysql_close($con);
	
	//redircts this page automatically back to startRoute page
   	header( 'Location: admin.php' ) ;
	
	}

?>
