<!DOCTYPE html>
<html>

<title>Paracosm Queue System</title>

<!-- 
	Author: David Zheng
	Date: 8/18/2017
	Project: Paracosm Data Managment System
	File: index.php

	Database: devTest
	User: root
	Pass: password
	Tables: Queue_Table, History_Table

	VM: LinuxTest
	User: mygod
	Pass: 123 (Cause hackers can't touch the holy numbers of mygod >.>)

Feast your eyes upon this glorious piece of golden trash (it is PRETTY ugly)
	Core functionalities:
		1.) Swap items on queue table
		2.) Upload files
		3.) Filter results (Non-case sensitive)
		4.) Pager functionality
		5.) Design image projection (Not completed)
		6.) Allows for image preview and download

	Things to be fixed:
		1.) Ugly UI
		2.) Weird looking CSS formatting
		3.) Some performance boost for the server side code to improve user experience
		
	Super Awesome things about this system: PHP files are kind of modular to the point where I can reuse things very easily (Full Stack Modular :D)
-->
<head>
	<!-- Javascript, jQuery, CSS file includes -->
	<link rel= "stylesheet" type="text/css" href= "CSS/devTest.css"/>

	<!-- 
		Most useless piece of javascript code in the world
		<script src="JSfiles/devTest.js"></script>
	-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>

	<script>
		//jQuery code

		// queueItemCount is the current starting row on the display
		// ITEMS_PER_PAGE is the max number to be displayed per page
		let queueItemCount = 0; // should be initalized to 0 in the beginning
		const ITEMS_PER_PAGE = 50;

		// Runs after the html page is done loading
		$(document).ready(function()
		{

			// Submit File button functionality
			$("#upload-form").on('submit', function(event)
			{
				// Prevent default action (goes to another page)
				event.preventDefault();

				// Sends data to devTest.php via POST which handles file uploads
				$.ajax(
				{
					type: "POST",
					url: "PHP/devTest.php",
					data: new FormData($("#upload-form")[0]),
					success: function () {
						alert("Submission Successful");
					},
					error: function()
					{
						alert("Please do not leave empty fields\nBe sure to have the allowed file types: PNG, JPEG, JPG");
					},

					// Not to sure if all of these three settings are needed (Believe one of them broke the code when removed)
					cache: false,
					contentType: false,
					processData: false
				});

				// refreshes the table after upload to show instant update
				// the refreshTable is put on a 100 millisec delay in order to ensure that the MySQL query is fully executed before the site tries to refresh the table with new data
				// This delay may be reduced if need be. I found 100 milliseconds was a good insurance during testing phase.
				setTimeout(function() {$("#queue-table").load("PHP/refreshTable.php", {queueCounter: queueItemCount, itemPerPage : ITEMS_PER_PAGE});},100);
			});

			// Swap queue form
			$("#swap-form").on('submit', function(event)
			{
				// Prevents site from doing a default action after submission (going to another page)
				event.preventDefault();

				// ajax method to send data
				$.ajax(
				{
					type: "POST",
					url: "PHP/swapQueue.php",
					data: $("#swap-form").serialize(),

					// messages for the user to indicate whether or not the action was successful
					success: function()
					{
						alert("Swap Query Submited");
					},
					error: function()
					{
						alert("Please do not leave empty fields\nBe sure to enter existing queues");
					}
				});

				// Delays the execution by 2 seconds (could be lower. At best 1 second so far)
				// Delay is needed to ensure MySQL queries have enough time to execute

				// Dev Note (8/18/2017):
				// The swap execution seems to be slightly slower than all the other actions
				// which is why the delay is so much higher than the other actions to refresh the page
				// 1 second may be too much, has potential to go lower, even with current algorithm (Which shouldn't be so slow to begin with. My theory is the way I run my MySQL could be hindering slight performance) 
				setTimeout(function() {$("#queue-table").load("PHP/refreshTable.php", {queueCounter: queueItemCount, itemPerPage : ITEMS_PER_PAGE});},1000);

			})

			// Filter form functionality
			$("#filter-form").on('submit', function(event)
			{
				// Prevents default action (as explained previously)
				event.preventDefault();

				// Sends user input data to filterTable.php via POST then updates the table with the filtered data
				// Note: the filter data has no limit as to how many items are displayed on the table
				// Reason: I believe the filter data should be significantly less than 200 items at scale (Could be super super wrong). Although I think depending on which filter option is used

				// Runtime: near instant
				$.post("PHP/filterTable.php" , {categories: $("#categories option:selected").val(), keyword: $("#keyword").val(), table: "Queue_Table", date: $("#startDate").val()}, function(data){
					$("#queue-table").html(data);
				});	
			})

			// "Load More" button functionality
			// Check Relative PHP for more info on how the button is implemented
			$("#load_next_queue").click(function()
			{
				// what it does is basically replace the original html code with the new html code that is generated by the loadQueue.php to show the new table
				$("#queue-table").load("PHP/loadQueue.php", {queueCounter: queueItemCount, itemPerPage : ITEMS_PER_PAGE});

				setTimeout(function() {$("#queue-table").load("PHP/refreshTable.php", {queueCounter: queueItemCount, itemPerPage : ITEMS_PER_PAGE});},100);
			});

			// "Load Previous" button functionality
			$("#load_previous_queue").click(function()
			{
				// Very similar idea to the "load_next_queue" method
				// the only difference is the logic behind the loadPrevious and loadQueue (as expected)
				$("#queue-table").load("PHP/loadPrevious.php", {queueCounter: queueItemCount, itemPerPage : ITEMS_PER_PAGE});

				setTimeout(function() {$("#queue-table").load("PHP/refreshTable.php", {queueCounter: queueItemCount, itemPerPage : ITEMS_PER_PAGE});},100);

			});

			// "dequeue element" or offload the data to be displayed or projected onto the framming table
			// Dev Note (8/18/2017):
			/*
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
			$("#dequeue-element").click(function()
			{
				$("#queue-table").load("PHP/dequeue.php", {queueCounter: queueItemCount, itemPerPage: ITEMS_PER_PAGE});

				setTimeout(function() {$("#queue-table").load("PHP/refreshTable.php", {queueCounter: queueItemCount, itemPerPage : ITEMS_PER_PAGE});},100);
 
			});

			// Shows a list of completed projects in a new window
			// Reason: Avoid any major reworks on the PQS system that is already in place
			// Could be changed later on with a click and drag UI (sortable)
			$("#history").click(function()
			{
				let historyWindow = window.open("history.php");
			});

			
			// Image preview functionality
			// The jQuery selector has to be $(document) (To my knowledge as of 8/11/2017)
			// Previous: $("td a")
			// Problem: The mouseover will stop working once new data is loaded onto queue table
			// Hypothesis: Once the document is loaded, the eventhandlers are binded to the elements
			//             Once the table is refreshed with new data, the original eventhandlers are removed
			$(document).on("mouseover","td a",function()
			{
				$("#image-preview img").attr("src",$(this).attr("href"));
				$("#image-preview img").attr("onerror","this.onerror=null;this.src='Images/no-image.png';");
			});
		});
	
		// Helpful function to hide and show different input options for the filter-form
		// All it does is hide and show the date selector (the thing with the calendar dropdown) and the normal text field
		function dateSelect(event)
		{
			if(event.value == "Start Date")
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

	<h1 ID = "page-title">Welcome to the Paracosm Queue System</h1>


	<button ID = "load_previous_queue">Load Last 50</button>
	<button ID = "load_next_queue">Load Next 50</button>
	<button ID = "dequeue-element">Display Next Design</button>
	<button ID = "history">Show completed designs</button>

	<!--Queue Submission Form-->
	<div >

		<!-- File upload form -->
		<div id="input-form-div">
			<!--Not too sure the purpose of the href tag (Could be a popup window attemp)-->
			<a href="#addDesignForm" data-rel="popup"></a>
				<div data-role="popup" id="#addDesignForm">
					<form id="upload-form" action="PHP/devTest.php" method="POST" enctype="multipart/form-data">
						<div class="input-form" align="center">
							<h2>Add File Information</h2>
							<input type="text" id="designName" name="designName" placeholder="Design Name">
							<input type="text" id="relatedProject" name="relatedProject" placeholder="Related Project">
							<h3></h3>
							<input type="text" id="author" name="author" placeholder="Author">
							<p></p>
							<label for="designFileUpload">Allowed file types: PNG, JPEG, JPG</label>
							<h3></h3>
							<input type="file" id="designFileUpload" name="designFileUpload">
							<input type="submit" id="submit-btn" value="Submit File">
						</div>
					</form>

					<!-- Swap Form -->
					<form id="swap-form" action="PHP/swapQueue.php" method="POST">
						<div class="input-form" align="center">
							<h2>Swap Queues</h2>
							<input type="number" id="source" name="source" placeholder="From">
							<input type="number" id="target" name="target" placeholder="To">
							<p style="margin-top:5px;">Pick two queue numbers you want to swap</p>
							<input type="submit" id="swap-btn" value="Perform Swap">
						</div>
					</form>
				</div>
		</div>
	</div>

	<!-- Filter Form -->
	<div style="margin-top: 50px;">

		<form id="filter-form" action="PHP/filterTable.php" method="POST" style="margin-left:42%;margin-right:42%;">
			<div class="input-form" align="center">
				<h2>Filter</h2>
				<select id="categories" name="categories" onchange="dateSelect(this);" style="float:left;margin-right: 20px;">
					<option value="Related Project">Related Project</option>
					<option value="Author">Author</option>
					<option value="Start Date">Start Date</option>
				</select>

				<div id="keywordInput" style="float:right;">
					<input type="text" id="keyword" name="keyword" placeholder="Input search keyword">
				</div>
				<div id="dateInput" style="display:none;float:right;">
					<input type="date" id="startDate" name="startDate">
				</div>
				<h3></h3>
				<input type="submit" id="filter-btn" value="Execute Filter" style="">
			</div>
		</form>
	</div>

	<!-- The queue table starts here -->

	<div id="queue-table-section">
		<div id="table-name">
			<h1>Queue Table</h1>
		</div>

		<!-- Image previewer -->
		<!-- Special Note: This should be above the queue table for the html since the browser does it anyways -->
		<!-- Image previewer is put under the same div as the queue table so that it can go to the side of the table rather than beneath it -->
		<div ID = "image-preview" align="right">
			<h1>Image Previewer
				<h1></h1>
				<img src="" onerror="this.onerror=null;this.src='Images/no-image.png';"/>
			</h1>
		</div>

		<table id = "queue-table" align="center">
			<tr>
				<th class = "table-header">Queue Number</th>
				<th class = "table-header">Design Title</th>
				<th class = "table-header">Related Project</th>
				<th class = "table-header">File</th>
				<th class = "table-header">Start Date</th>
				<th class = "table-header">Last Modified</th>
				<th class = "table-header">Author</th>
			</tr>

			<!-- Some php injection to get data from the server to be displayed upon the loading of the site -->
			<!-- This injection is replicated under refreshTable.php -->
				<?php
					require "PHP/devServerConnection.php";

					$sql_query = "SELECT * FROM `Queue_Table` ORDER BY `Queue ID` ASC LIMIT 0,50";
					$result = mysqli_query($connection,$sql_query);
					$itemsToDisplay = mysqli_num_rows($result);

					if($itemsToDisplay>0)
					{
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
								$temp = substr($filePath,$file_name_pos+1);
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
							echo $row['Last Date Modified'];
							echo "</td>";

							echo "<td>";

							if($row['Author'] == "") echo "N/A";
							else echo $row['Author'];

							echo "</td>";

							echo "</tr>";

						}
					}
					else
					{
						echo "<h1 id=\"noResult\">NO RESULT TO DISPLAY</h1>";
					}
				?>
		</table>

	</div>



</body>

</html>
