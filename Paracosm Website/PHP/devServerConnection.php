<?php
	// PHP file that contains all necessary info for database connection
	// SUPER HELPFUL COMPARED TO COPY AND PASTING

	$serverName = "localhost";
	$username = "root";
	$password = "password";
	$db_name = "devTest";

	$connection = mysqli_connect($serverName,$username,$password,$db_name);

?>