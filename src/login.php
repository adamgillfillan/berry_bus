<?php session_start(); 

	//for added safety, we store connection info in to a separate php file
	require("php_connInfo.php");
	
	//connect to mySql
	$con = mysql_connect("localhost",$user,$password);
	
	//selects the correct database
	mysql_select_db($database)or die("Unable to select database");
	
	
	$user = $_REQUEST["user"];
	$pass = $_REQUEST["pass"];
	
	if($user!="admin"&&$user!="driver"){
		echo("Invalid username. Please try again.");
		header( "Refresh:2; url=login.html" ) ;
		die();
	}
	
	//echo($user);
	//echo($pass);
	
	//passed bus query to database
	$query = "SELECT* FROM Credentials WHERE `Username` = '$user'";
	$result = mysql_query($query) or die("Unable to select");
	$row = mysql_fetch_array($result);
	extract($row);
	
	$hashedPass = hash("md5",$pass);
	
	if($row['Password'] == $hashedPass){
		if($user=="admin"){
			$_SESSION['loggedin'] = 1;
			header( 'Location: admin/admin.php' ) ;
		}else if($user=="driver"){
			$_SESSION['loggedin'] = 1;
			header( 'Location: bus/startRoute.php' ) ;
		}
	}else{
		echo("Invalid password. Please try again.");
		header( "Refresh:2; url=login.html" ) ;		
	}
	
	
	//closes the connection
	mysql_close($con);

?>