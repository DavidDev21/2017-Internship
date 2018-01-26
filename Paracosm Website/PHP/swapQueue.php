<?php

	//	File: swapQueue.php
	//	Author: David Zheng
	//	Last Updated Date: 8/18/2017

	// PHP to handle the swapping of two queue items
	// Note: Only swaps the data and not the actual queue number. (For the sake of keeping the table on the database organized)

	// Weird bug: The UI doesn't always refresh after the swap. Sometimes it shows that the two elements are the same even when the original data still exists.
	// Dev Update (8/18/2017): It is not a bug. The main culprit is the time between sql query execution versus the time it takes to refresh the table. This has been fixed via an execution delay on index.php using setTimeout().

	/*
		Basic Process for Swap:

		1.) Checks if the user gives two items to swap
		2.) Execute two MySQL request to the server to grab the two items
		3.) Checks if the server returned two items. If there are no two items received from the server, then there are nothing to swap.
		4.) Performs swap via two simple SET querys to the server (should be relatively fast. Need to do some testing benchmarks to see how much we can push the system)
	*/

	// Needed for MySQL database connection
	require "devServerConnection.php";

	// If there are not two items selected for swap. Then return an error
	if($_POST['source'] == "" || $_POST['target'] == "")
	{
		die(header("HTTP/1.1 404 Data Not Found"));
	}

	// Extra check if server died
	if(!$connection)
	{
		echo "Connection Failure: ". mysqli_connect_error($connection);
	}

	// ================================================= GRABS THE TWO RECORD FROM TABLE IF ABLE

	// Would use string formatting but sometimes the best solution is the simpliest (It wasn't working)

	$sql_query = "SELECT `Design Title`, `Related Project`,`File Path` FROM `Queue_Table` WHERE `Queue ID` = ".$_POST['source'];
	$sql_query_2 = "SELECT `Design Title`, `Related Project`,`File Path` FROM `Queue_Table` WHERE `Queue ID` = ".$_POST['target'];


	$resultOne = mysqli_query($connection,$sql_query);
	$resultTwo = mysqli_query($connection,$sql_query_2);

	$itemOne = mysqli_fetch_assoc($resultOne);
	$itemTwo = mysqli_fetch_assoc($resultTwo);

	if($itemOne && $itemTwo)
	{
		// SQL QUERY Ready to set a record with new data
		$sql_query = "UPDATE `Queue_Table` SET `Design Title`=\"".$itemOne['Design Title']."\", `Related Project`=\"".$itemOne['Related Project']."\", `File Path`=\"".$itemOne['File Path']."\" WHERE `Queue ID` = ".$_POST['target'];

		echo mysqli_error($connection);

		// Testing if sprintf modifies the variable it is given to format
		$sql_query_2 = "UPDATE `Queue_Table` SET `Design Title`=\"".$itemTwo['Design Title']."\", `Related Project`=\"".$itemTwo['Related Project']."\", `File Path`=\"".$itemTwo['File Path']."\" WHERE `Queue ID` = ".$_POST['source'];

		$resultOne = mysqli_query($connection, $sql_query);
		$resultTwo = mysqli_query($connection, $sql_query_2);

		// Not sure if this actually works (The error output)
		// Dev Update (8/18/2017): Most likely never reaches this point cause of the upper level error checks
		if(!$resultOne && !$resultTwo) echo "Swap Failure: ". mysqli_error($connection);

	}
	else
	{
		echo "<script>";
		echo "alert(\"Data Not Found\nPlease make sure you have entered existing items\");";
		echo "</script>";
		die(header("HTTP/1.1 404 Data Not Found"));
	}
	//require "refreshTable.php"; // Buggy

	// Closes connection to database
	mysqli_close($connection);
?>