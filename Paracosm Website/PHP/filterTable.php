<?php
	/*	
		File: filterTable.php
		Author: David Zheng
		Last Updated Date: 8/18/2017
		
		filterTable.php allows the user to filter out results on the tables based on a set of predetermined categories found in index.php under "<form id=filter-form>".

		** It is non-case sensitive **

		The basics of how this is implemented:

		1.) a form allows the user to select categories via dropdown menu and is prompted to input a search term into a text field. Once the user is done, the data is sent to filterTable.php via POST. The POST variables that should be avaliable or related to this php file are:

			"table" = which indicates the table that is current being displayed on the user's browser
			"categories" = the selection the user made on the dropdown menu
			"date" = holds the specific date (if any) the user had inputted as a search term. The format that
					 php file recieves is YYYY-MM-DD
			"keyword" = basically what the variable suggests. A keyword used for search term.
						(The keyword can be empty. The php will just load the table normally. User will not be able to tell if anything happened as there is no execution delay with this php file)
	*/

	require "devServerConnection.php";


	// Method to differentiate between the queue table and the completed design table (History_table)
	$tableSelection = "Queue_Table";
	$historyTable = false;

	if($_POST["table"] == "History_Table")
	{
		$historyTable = true;
		$tableSelection = "History_Table";
	}

	// Allows user to filter by the start date (if date is empty then we know user did not select to filter by date)
	if($_POST["categories"] == "Start Date" || $_POST["categories"] == "Date Completion")
	{
		$sql_query = "SELECT * FROM `".$tableSelection."` WHERE `".$_POST["categories"]."` LIKE \"%".$_POST["date"]."%\"";
	}
	else
	{
		$sql_query = "SELECT * FROM `".$tableSelection."` WHERE `".$_POST["categories"]."` LIKE \"%".$_POST["keyword"]."%\"";
	}

	// MySQL query request
	$result = mysqli_query($connection,$sql_query);

	// If there is anything from the query. Display the table with the result
	if(mysqli_num_rows($result)>0)
	{	
		echo "<tbody>";
			echo	"<tr>
						<th class = \"table-header\">Queue Number</th>
						<th class = \"table-header\">Design Title</th>
						<th class = \"table-header\">Related Project</th>
						<th class = \"table-header\">File</th>
						<th class = \"table-header\">Start Date</th>";
			if($historyTable)
			{
				echo "<th class = \"table-header\">Date Completed</th>";
			}
			else
			{
				echo "<th class = \"table-header\">Last Modified</th>";
			}

			echo "<th class = \"table-header\">Author</th>
				 	</tr>";

		echo "</tbody>";

		// Fetchs an associative array from $result (which indicates a row on the MySQL table)
		// Each index in $row is directly related to the column names on the MySQL tables
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
			echo $row['Start Date'];
			echo "</td>";

			echo "<td>";
			if($historyTable)
			{
				echo $row['Date Completion'];
			}
			else
			{
				echo $row['Last Date Modified'];
			}
			echo "</td>";

			echo "<td>";
			
			if($row['Author'] == "") echo "N/A";
			else echo $row['Author'];

			echo "</td>";
			
			echo "</tr>";

		}
	}

	// If there is no result, show a message to the user
	else
	{
		echo "<tbody>";
			// Recreates the header (probably not the best way)
			echo	"<tr>
						<th class = \"table-header\">Queue Number</th>
						<th class = \"table-header\">Design Title</th>
						<th class = \"table-header\">Related Project</th>
						<th class = \"table-header\">File</th>
						<th class = \"table-header\">Start Date</th>";
			if($historyTable)
			{
				echo "<th class = \"table-header\">Date Completed</th>";
			}
			else
			{
				echo "<th class = \"table-header\">Last Modified</th>";
			}

			echo "<th class = \"table-header\">Author</th>
				 	</tr>";

			echo "<tr class=\"table-row\">";
				echo "<td colspan=\"6\"><h1 style=\"text-align:center;\">No Items Found</h1></td>";
			echo "</tr>";
		echo "</tbody>";
	}
?>