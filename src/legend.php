<?php 
	//for added safety, we store connection info in to a separate php file
	require("php_connInfo.php");
	
	//connect to mySql
	$con = mysql_connect("localhost",$user,$password);

	//selects the correct database
	mysql_select_db($database) or die ( "Unable to select database");
	
	//passes the query to the database
	$query = "SELECT* FROM Coordinates";
	$result = mysql_query($query) or die("Unable to select");

	//declares buses array
	$buses = array();
	
	//returns each row in the table
	while($row=mysql_fetch_array($result)){
		
		extract($row);				
		
		//changes the epoch time to a normal formatted date and time
		$realTime = ($row['Timestamp'] / 1000);
		$formattedTime = date("M d g:i A", $realTime);
		
		$bus = array("timestamp"=>$formattedTime,"type"=>$row['Type'],"marker"=>$row['Marker']);		
		$buses[] = $bus;		
	}

	//passes alert message query to database
	$query = "SELECT* FROM `Alert Message`";
	$result = mysql_query($query) or die("Unable to select");
	$row = mysql_fetch_array($result);		
	extract($row);	
	$message = $row['Message'];
	
	$buses[] = $message;
	
	//if there are buses in the buses array then return the array else return there are no active buses
	if(count($buses)>0){
		echo json_encode($buses);
	}else{
		echo json_encode("No buses");
	}

	//closes the connection
	mysql_close($con);
?>
