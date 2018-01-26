<?php
	//	File: refreshTable.php
	//	Author: David Zheng
	//	Last Updated Date: 8/18/2017

	// A simple php script that reloads the same items on the page (used after performing an action)

	// RELATED TO: swapQueue.php
	// Developer's note: Strange that it does not reload the table consistently
	// 					Possible curpurit would be how the Perform swap button is written at index.php
	// Update: refreshTable.php now works (Matter of giving MySQL enough time to execute query)

	// Required for MySQL database connection
	require "devServerConnection.php";

	// variables
	$queueDisplay = $_POST['queueCounter']; // Current Row (queueItemCount on "index.php") couldn't come up with a 
											// better name
	$itemPerPage = $_POST['itemPerPage'];	// Items per page limit

	// Boolean flag to indicate if there are no items to display
	$noResult = False;

	// queueDisplay = will be the "starting row" for the MYSQL query
	// Query description: 
	// 		starting from row X, load Y amount from Queue_Table using Queue ID to order everything in ascending order
	$sql_query = "SELECT * FROM `Queue_Table` ORDER BY `Queue ID` ASC LIMIT ".$queueDisplay.",".$itemPerPage;
	$result = mysqli_query($connection,$sql_query);

	// Just show everything left Seems super redundant. (Doesn't let user to load more if there isn't anymore to load) <<< Old Comment. I am keeping just in case I am questioning myself

	/*
		If there is no result from the sql query, all that means is that our pager has either gone one step too far or one step to late. The pager is a little weird because of how the queueItemCount in "index.php" gets updated. There is a one click delay between the number this php file gets versus the number that queueItemCount has in "index.php"

		This check is a failsafe

		Dev Update (8/18/2017): may not be needed, but will not risk to try (Cause it is pretty important for the pager)

		For more info on the logic of the page, check php files: "loadQueue.php" and "loadPrevious.php"
	*/
	if(mysqli_num_rows($result) == 0)
	{
		if($queueDisplay < 0)
		{
			$queueDisplay = $queueDisplay + $itemPerPage;
		}
		else
		{
			$queueDisplay = $queueDisplay - $itemPerPage;
		}
		$noResult = True;
	}

	$sql_query = "SELECT * FROM `Queue_Table` ORDER BY `Queue ID` ASC LIMIT ".$queueDisplay.",".$itemPerPage;
	$result = mysqli_query($connection,$sql_query);

	// If there is anything from the result
	if(mysqli_num_rows($result)>0)
	{	
		echo "<tbody>";
		// Recreates the header
		echo	"<tr>
					<th class = \"table-header\">Queue Number</th>
					<th class = \"table-header\">Design Title</th>
					<th class = \"table-header\">Related Project</th>
					<th class = \"table-header\">File</th>
					<th class = \"table-header\">Start Date</th>
					<th class = \"table-header\">Last Modified</th>
					<th class = \"table-header\">Author</th>
			 	</tr>";

		// Generates html code for each record on the MySQL table
		while($row = mysqli_fetch_assoc($result))
		{
			echo "<tr class=\"table-row\">";
			echo "<td>";
			echo $row['Queue ID'];
			echo "</td>";

			echo "<td>";
			echo $row['Design Title'];
			echo "</td>";

			echo "<td>";
			echo $row['Related Project'];
			echo "</td>";

			echo "<td>";

			// Useful variable. (Just a slight performance optimization)
			// Dev Update (8/18/2017): As my professor says "BULLSHIT", there is no performance gain here. Data access for an array of items is almost instant. I am going to keep it for the sake of readability.
			$filePath = $row['File Path'];

			// Gives the position of where the file name actually starts
			// from the full file path
			$file_name_pos = strrpos($filePath,'/');

			// Since the full path is stored on the database (For ease of use on the server side code)
			// We must grab the filename from the full path
			if($file_name_pos == False)
			{
				$image = "Images/".substr($filePath,$file_name_pos);
				echo "<a href=\"".$image."\" download>";
				echo substr($filePath,$file_name_pos);
				echo "</a>";
			}
			else
			{
				// The result should just be the file name
				$temp = substr($filePath,$file_name_pos+1); // Spare me the bad variable name
				if($temp != "")
				{
					$image = "Images/".$temp;
					echo "<a href=\"".$image."\" download>";
					echo $temp;
					echo "</a>";
				}
				else
				{
					echo "No File Found";
				}
			}

			echo "</td>";

			echo "<td>";
			echo $row["Start Date"];
			echo "</td>";

			echo "<td>";
			echo $row['Last Date Modified'];
			echo "</td>";

			echo "<td>";
			
			if($row['Author'] == "") echo "N/A";
			else echo $row['Author'];

			echo "</td>";
			
			echo "</tr>";

		}
		echo "</tbody>";

	}
	else
	{
		echo "<h1 id=\"noResult\">NO RESULT TO DISPLAY</h1>";
	}

	// Closes connection (always do it to be clean and not messy)
	mysqli_close($connection);
?>