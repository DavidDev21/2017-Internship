<!DOCTYPE html>
<html>
	<title>PQS Completed Projects</title>
<!-- 
	Author: David Zheng
	Date: 8/18/2017
	Project: Paracosm Data Managment System
	File: history.php

	Database: devTest
	User: root
	Pass: password
	Tables: Queue_Table, History_Table

	VM: LinuxTest
	User: mygod
	Pass: 123 (Cause hackers can't touch the holy numbers of mygod >.>)

Feast your eyes upon this glorious piece of golden trash (it is PRETTY ugly)
	Core functionalities:
		1.) List all previously completed design / fabrications
		2.) Additional filter options compared to the filter on "index.php" I.E, Date Completed
			(Good thing "filterTable.php" is flexible with categories)
		3.) Allows for image preview and download

	Things to be fixed (Basic the same with "index.php":
		1.) Ugly UI
		2.) Weird looking CSS formatting
		3.) Some performance boost for the server side code to improve user experience

	Dev Comment: Yes, this is almost an exact clone of the "index.php" minus the pager, and projection feature

	Things to consider:
		1.) As the system grows with more and more data, the items to display onto the browser for this specific page may be too large for a quick load time. It may be a good idea to implement the pager for this one as well. It's a reasonable concern.
			Example: Try loading 1 million entries from MySQL table. No matter how fast MySQL query executes it, you will definitely be able to feel the slow down as entries start to pile up.

			Not as worry about how the user goes through all the information without a pager to limit the items on display since there is a filter that can cut off a ton of irrevelant information.
		
-->
	<head>
		<!--
			CSS and jQuery imports
		-->
		<link rel="stylesheet" type="text/css" href="CSS/historyTracker.css"/>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>

		<script>
			$(document).ready(function()
			{
				// Image preview functionality (Identical to the one on "index.php")
				$(document).on("mouseover","td a",function()
				{
					$("#image-preview img").attr("src",$(this).attr("href"));
					$("#image-preview img").attr("onerror","this.onerror=null;this.src='Images/no-image.png';");
				});

				// Filter functionality. (Identical to the one on "index.php" minus the "History_Table")
				$("#filter-form").on('submit', function(event)
				{
					event.preventDefault();
					$.post("PHP/filterTable.php" , {categories: $("#categories option:selected").val(), keyword: $("#keyword").val(), table: "History_Table", date: $("#date").val()}, function(data){
						$("#history-table").html(data);
					});
				});
			});

		// Useful function to hide and show different input fields for the filter form
		// Identical to the one in "index.php"
		function dateSelect(event)
		{
			if(event.value == "Start Date" || event.value == "Date Completion")
			{
				document.getElementById("dateInput").style.display = "block";
				document.getElementById("keywordInput").style.display = "none";
			}
			else
			{
				document.getElementById("keywordInput").style.display = "block";
				document.getElementById("dateInput").style.display = "none";
			}
		}

		</script>


	</head>


	<body>
		<h1>PQS Completed Design List</h1>


		<div>
			<form id="filter-form" action="PHP/filterTable.php" method="POST">
				<div class="input-form" align="center">
					<h2>Filter</h2>
					<select id="categories" name="categories" onchange="dateSelect(this);">
						<option value="Related Project">Related Project</option>
						<option value="Author">Author</option>
						<option value="Start Date">Start Date</option>
						<option value="Date Completion">Date Completed</option>
					</select>

					<div id="dateInput" style="display:none;">
						<input type="date" id="date" name="date">
					</div>

					<div id="keywordInput">
						<input type="text" id="keyword" name="keyword" placeholder="Input search keyword">
					</div>

					<input type="submit" id="filter-btn" value="Execute Filter">
				</div>
			</form>
		</div>

		<div ID = "history-table-div">
			<div>
				<table ID = "history-table">
					<?php
						// Generated History_Table on load
						require "PHP/devServerConnection.php";
						$sql_query = "SELECT * FROM `History_Table` ORDER BY `Queue ID` ASC";
						$result = mysqli_query($connection,$sql_query);

						// If there is anything from the result
						if(mysqli_num_rows($result)>0)
						{	
							echo "<tbody>";
							// Recreates the header (probably not the best way)
							echo	"<tr>
										<th class = \"table-header\">Queue Number</th>
										<th class = \"table-header\">Design Title</th>
										<th class = \"table-header\">Related Project</th>
										<th class = \"table-header\">File</th>
										<th class = \"table-header\">Start Date</td>
										<th class = \"table-header\">Date Completed</th>
										<th class = \"table-header\">Author</th>
								 	</tr>";

						
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
								echo $row['Date Completion'];
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

					?>
				</table>
			</div>

			<div ID = "image-preview">
				<h1>Image Previewer
					<h1></h1>
					<img src="" onerror="this.onerror=null;this.src='Images/no-image.png';"/>
				</h1>
			</div>
		</div>

	</body>
</html>