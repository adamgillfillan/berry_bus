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
	
	//gets the type of route this bus is running (i.e main or mountain)
	$type = mysql_escape_string($_POST["type"]);
	
	
	//check type is a legit bus type
	//checks if given name a bus
	$query = "SELECT* FROM Coordinates";
	$result = mysql_query($query) or die("Unable to select");
	$legit = 0;
	while($row=mysql_fetch_array($result)){
		extract($row);
		if($row['Type']==$type){
			$legit = 1;
		}
	}
	
	if($legit!=1){
			echo("Invalid Bus.");
			header( "Refresh: 3; url=startRoute.php" ) ;	
			die();
	}
	
	
	//update bus to active
	$query = "UPDATE Coordinates SET `Active` = '1' WHERE `Active` = '0' AND `Type` = '$type'" ;
	
	mysql_query($query) or die("Unable to select");
	
	//returns number of rows affected by the UPDATE
	$result = mysql_affected_rows();
		
	//check result to make sure a row was actually changed
	if($result==1){
		//echo("Running");
	}else{
		echo("Bus already running");	
		header( 'Location: startRoute.php' );
	}	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
    <meta charset="utf-8">
    <title>Bus Running &middot; Berry Bus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="shortcut icon" href="../client/main.png">

    <!-- Styles -->
    <link href="../../assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
    <link href="../../assets/css/bootstrap-responsive.css" rel="stylesheet">
</head>

<body>

<div class='hero-unit' id="current"> </div>

<script>

var lastUpdate = new Date().getTime();

//watches and updates your current position every second
function updatePage(){
	navigator.geolocation.getCurrentPosition(
		updatePosition,
		function(err) { alert("Geolocation error. Please enable location services."); }
		//{enableHighAccuracy: true}
	);
}

setTimeout(updatePage, 1000);

//stores the route type
var type = '<?php echo $type; ?>';

//updates local current position variable
function updatePosition(position){
	//checks to see if bus is still active
	
	// to handle getCurrentPosition bug in some browsers
	var now = new Date().getTime(); 
	if (now-lastUpdate < 500) {
		return;
	}
	lastUpdate = now;
	
	//bus position variables
    var lat = position.coords.latitude;
	var lng = position.coords.longitude;
	var tmstp = position.timestamp;
	var acc = position.coords.accuracy;
	var dir = position.coords.heading;

	//posts the bus location variables to the setBusLocation PHP page
	jQuery.ajax({
		type: "POST", 
		url:  "setBusLocation.php", 
		cache: false,
		data: 'x='+lat+'&y='+lng+'&ts='+tmstp+'&acc='+acc+'&dir='+dir+'&type='+type,
		//if bus is not active then redirect back to startRoute
		success: function(data){ if(data==0){ window.location.href = "startRoute.php"; } }
	})
	
	//once post is completed runs the updateLocOnPage function		
	.done(function() { updateLocOnPage(lat,lng,tmstp,acc,dir); 
					   setTimeout(updatePage, 1000);
	})
	//if post fails then an error alert is diplayed
	.fail(function() { /*alert("AJAX error");*/ });
}

//displays status of bus on the page
function updateLocOnPage(lat,lng,tmstp,acc,dir) {
	var current = document.getElementById("current");	
	
	//converts epoch time into readable format
	var date = new Date(tmstp),
	ampm = 'AM',
	h = date.getHours(),
	m = date.getMinutes(),
	s = date.getSeconds();
	
	if(h>=12){
		if(h>12) h-=12;
		ampm = 'PM';
	}
	
	if(m<10) m= '0'+m;
	if(s<10) s= '0'+s;
		
    var formattedTime = date.toLocaleDateString() + ' '+h+':'+m+':'+s+' '+ampm;
	
  	current.innerHTML="<h4>Bus: " + type + "</h4> <p>Last Updated: " + formattedTime + "</p> <form action='endRoute.php' method='post' id='end' > <button class='btn btn-large btn-primary' type='submit' name='type' value='<?php echo $type; ?>'>End <?php echo $type; ?> Route</button></form>";
}

</script>

    <!-- Javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../../assets/js/jquery.js"></script>
    <script src="../../assets/js/bootstrap-transition.js"></script>
    <script src="../../assets/js/bootstrap-alert.js"></script>
    <script src="../../assets/js/bootstrap-modal.js"></script>
    <script src="../../assets/js/bootstrap-dropdown.js"></script>
    <script src="../../assets/js/bootstrap-scrollspy.js"></script>
    <script src="../../assets/js/bootstrap-tab.js"></script>
    <script src="../../assets/js/bootstrap-tooltip.js"></script>
    <script src="../../assets/js/bootstrap-popover.js"></script>
    <script src="../../assets/js/bootstrap-button.js"></script>
    <script src="../../assets/js/bootstrap-collapse.js"></script>
    <script src="../../assets/js/bootstrap-carousel.js"></script>
    <script src="../../assets/js/bootstrap-typeahead.js"></script>

 </body>

</html>

<?php
	//closes the connection
	mysql_close($con);
	
	}
?>


