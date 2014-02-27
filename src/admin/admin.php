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
?>

<html>
<head>
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width">
	<link rel="shortcut icon" href="../client/main.png">
	<title>Admin &middot; Berry Bus</title>

    <!-- Styles -->
    <link href="../../assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
    	body {
        	padding: 10px;

      	}
    </style>
    <link href="../../assets/css/bootstrap-responsive.css" rel="stylesheet">

</head>

<body>

	<div class="masthead">
        <ul class="nav nav-pills pull-right">   
		  <!--Logout Button-->
          <li><a href="../logout.php">Sign Out</a></li>
        </ul>
    </div>
	
	<h1><p class="text-center">Administrator Page</p></h1>
	
	<br/>

	<div class="row">
	<div class="span3">
	<div id="buses"><h4>...Loading Buses...</h4> </div>
	</div>

	<div class="span3">
	<div id="message"> <h4>...Loading Current Message...</h4> </div>
	<h4>Alert Message:</h4>

	<form action="message.php" method="post" id="message">
		<textarea name="message" cols="50" rows="1" maxLength="100" placeholder="Enter your alert message..."></textarea><br>
		<button type="submit" class="btn" value="Submit" />Submit</button>
	</form>
	</div>


	<div class="span3">
	<h4>Create Bus:</h4>
	<form action="createBus.php" method="post" id="create">
		<fieldset>
		Bus Name: <input type="text" name="type" placeholder="Name of Bus"><br/>
		Bus Type: <label class="radio ">
					<input type="radio" name="marker" value="main.png">Main Route</label>
				  <label class="radio">
					<input type="radio" name="marker" value="mountain.png">Mountain Route</label>
		<button type="submit" class="btn">Submit</button>
	</fieldset>
	</form>


	<h4>Remove Bus: </h4>
	<form action="removeBus.php" method="post" id="select" >

		<label> Select Bus: </label>

		<select name="type" form="select">
			
			<?php
				$query = "SELECT* FROM Coordinates WHERE Active = 0";
				$result = mysql_query($query) or die("Unable to select");
				while($row=mysql_fetch_array($result)){
					extract($row);
			?> 
					<option value="<?=$Type?>"><?=$Type?></option>
			<?php
				}
			?>
		</select>
		<br/>
		<button type="submit" class="btn"/>Submit</button>
		
	</form>
	</div>

	<div class="span3">
	<h4>Change Password: </h4>
	<form action="changePassword.php" method="post" id="pass" >

		<label> Select User: </label>

		<select name="username" form="pass">
			
			<?php
				$query = "SELECT* FROM Credentials";
				$result = mysql_query($query) or die("Unable to select");
				while($row=mysql_fetch_array($result)){
					extract($row);
					
					if($Username=="driver"){
						?> 
						<option selected="selected" value="<?=$Username?>"><?=$Username?></option>
						<?php
					}else{					
						?> 
						<option value="<?=$Username?>"><?=$Username?></option>
						<?php
					}					
				}
			?>
		</select>
		<br/>
		<fieldset>
		Enter Admin Password: <input type="password" name="adminPassword" placeholder="admin password"><br/> 
		Enter New Password: <input type="password" name="newPassword" placeholder="new password"><br/>
		Enter New Password Again: <input type="password" name="newPassword2" placeholder="new password"><br/>
		<button type="submit" class="btn"/>Submit</button>
		</fieldset>
	</form>
	</div>
	</div>

</body>

<script>			

	setTimeout(getBusInfo,2000);

	//gets the bus info from the server every 2 seconds
	function getBusInfo(){
			jQuery.ajax({
				type: "GET", 
				url:  "adminIndex.php", 
				cache: false,
				dataType: "json",
				success: function(data) { displayInfo(data);
										  setTimeout(getBusInfo,2000);}
			})
			.fail(function() { /*alert("error");*/ });
	}

	//displays buses info on page
	function displayInfo(data) {
			var active;
			var buses = document.getElementById("buses");
			var message = document.getElementById("message");
			buses.innerHTML = "";
			message.innerHTML ="";
			
			for(var i=0;i<data.length-1;i++){
				if (data[i].active==0){
					active = "No";
				}else{
					active = "Yes";
				}
					
				buses.innerHTML += "<h4>Bus: " + data[i].type + "</h4><h5>Active: " +  active + 
								   "</br>Last Updated: " + data[i].timestamp + "</h5>" +
								   //End Route button
								   "<form action='endRoute.php' method='post' id='end' > <button class='btn' type='submit' name='type' value='"+ data[i].type +"'>End "+ data[i].type +" Route</button> </form> <br/>";		
			}
			
			message.innerHTML += "<h4>Current Message: " + data[data.length-1] + "</h4>" +
							   "<form action='message.php' method='post' id='clear' > <button type='submit' name='message' class='btn'> Clear Message </button> </form>";
	}
	
</script>

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
<!--<script src="../assets/js/bootstrap-form.js"></script>-->

</html>

<?php
}
?>