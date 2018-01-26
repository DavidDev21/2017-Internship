<?php

	//	File: dequeue.php
	//	Author: David Zheng
	//	Last Updated Date: 8/18/2017

	// Dev Note (8/18/2017)
	// This implementation is incomplete. For full process overview. go to "index.php" and above $("#dequeue-element")

	/*	Copied directly from "index.php" to ensure this does not disappear and for your ease of access.
				SUPER IMPORTANT FUNCTIONALITY. THIS IS THE WHOLE POINT OF THE SYSTEM

				Basic breakdown of the process:
				1.) Takes first item / record from the MySQL "Queue_Table"

				2.) Follow the file path that currently referencing to the desired design that is about to be projected. (Which is on a folder that the apache server has access to read and write to)
					*Implementation Note*
					If we look at it from the actual implementation of this system, the server would be running on the Paracosm machine with read and write access (Yes to both) to a folder of its own where it stores all the design files and the folder where the Paracosm software reads from.

				3.) The system then proceeds to remove everything from the Paracosm folder (which it should be able to read and write to. I don't believe this should be an issue.)

				4.) Copies the design file that it has on the server folder (The location where all the design files are stored. As of 8/18/2017, it is the "Images" folder found under ~./var/www/html/Images on the "LinuxTest VM") to the Paracosm folder

				// Not yet implemented
				5.) The system should be setup to run a skukuli (pardon my spelling) script which does the following:
					I.) If the Paracosm software is open, then close it. Else, Open Paracosm software
					II.) Select Image (which should be only one as guarenteed by the system)
					III.) Press start on the Paracosm software
				The system should continue to display the current image under the user requests the next image to display

				System will alert the user if there are no more items in the queue table if he or she tries to request for an design to project.

				As of 8/18/2017, The system has not gone through all the rigorous testing from David's Dumb User Testing Standards or DUTS.
			*/

	// PHP script that handles the offloading of queue elements
	
	// "devServerConnection.php" is pretty much needed for any interaction with server
	// It has all the infomation and setup needed
	require "devServerConnection.php";

	// Grab the first record on the table to dequeue
	$sql_query = "SELECT * FROM `Queue_Table` ORDER BY `Queue ID` ASC LIMIT 1";
	$result = mysqli_query($connection,$sql_query);

	$dequeueElement = mysqli_fetch_assoc($result);

// ======================================================================================
	// Checks if a record even exists
	if($dequeueElement)
	{
		// The target_folder would be the Paracosm folder if the system was fully deployed
		$target_folder = "/home/mygod/Desktop/dummyParacosmTrash/"; // current path on the VM
		$source_folder = $dequeueElement['File Path']; //Should have the full file path stored on the database

		// (Copied from loadQueue.php)
		// Grabs just the file name from the full file path
		// Useful variable. (Just a slight performance optimization)
		$filePath = $dequeueElement['File Path'];

		// Gives the position of where the file name actually starts
		// from the full file path
		$file_name_pos = strrpos($filePath,'/');

		// Since the full path is stored on the database (For ease of use on the server side code)
		// We must grab the filename from the full path
		if($file_name_pos == False)
		{
			$filePath = substr($filePath,$file_name_pos);
		}
		else
		{
			// The result should just be the file name
			$temp = substr($filePath,$file_name_pos+1); // Spare me the bad variable name
			if($temp != "")
			{
				$filePath = $temp;
			}
			else
			{
				echo "<script>";
				echo "alert(\"No File to Dequeue\");";
				echo "</script>";
			}
		}

// ======================================================================================
		// Clear everything that might be in the target folder
		$targetFiles = glob($target_folder."*");

		// Deletes all files in the target directory
		foreach($targetFiles as $file)
		{
			if(is_file($file))
			{
				unlink($file); // Deletes the file
			}
		}

// ======================================================================================
		// Moves the source file to target destination
		// Works. This is promised an existing file. devTest.php guarantees this premise
		copy($source_folder,$target_folder.$filePath);

		// Copies the queue record to another table for tracking purposes (History_Table)

		$sql_query = "INSERT INTO `History_Table` (`Queue ID`, `Design Title`, `Related Project`,`File Path`, `Author`) SELECT `Queue ID`, `Design Title`, `Related Project`,`File Path`, `Author` FROM `Queue_Table` LIMIT 1";
		$result = mysqli_query($connection,$sql_query);
		
		// removes the element from the database record (Mind not need two parameters for LIMIT)
		$sql_query = "DELETE FROM `Queue_Table` LIMIT 1";
		$result = mysqli_query($connection,$sql_query);

		// Does not need error check cause the if statement guarentees that there is a record to be deleted
// ======================================================================================
		// Refresh the table
		//require "refreshTable.php";
	}
	else
	{
		echo "<script>";
		echo "alert(\"No File to Dequeue\");";
		echo "</script>";
	}
?>