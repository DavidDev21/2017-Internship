<?php
	/*
	//	File: devTest.php
	//	Author: David Zheng
	//	Last Updated Date: 8/18/2017
	
	File Description: Handles file uploads to server
	
	Basic Process of File upload:

	* The file has a preset target path of where the server should be storing all the files. Note: The server must have read and write access to this path, otherwise the server will not be able to upload the files correctly.*

	1.) Check if all the fields on the file upload form are filled

	2.) Do a double check if there is a file 
		(Might be redundant but doesn't seem to hurt anything as of now) Step one was added after the thing basic file upload worked.

	3.) Grabs the file extension from the temporary file path that the server gave to the file and double check if 	   the file has the allowed file extensions which are currently: PNG, JPEG, JPG.

	4.) Moves the file to the server folder as indicated by $target_folder.

	5.) Execute MySQL query to insert a new record to be added onto the queue table with correctly formatted file 	  path reference for the server to use.

	Critical Variables
	1.) $target_folder: indicates the folder for the server to store files.
	2.) $target_file_dest: the file path for the actual file once its stored onto the server folder, which will be stored as the reference to that file on the MySQL Table
						   (Could potentially break down the system if it is on a different machine ... maybe not)
	3.) $allowed_extensions: a list of allowed file type extensions
	*/

	$target_folder = "/var/www/html/Images/"; // Should be avaliable for all scope of this file

	// Checks if all fields are completed by user
	if($_POST['designName'] == "" || $_POST['relatedProject'] == "" || $_FILES['designFileUpload']['name'] == "")
	{
		// throws some random error out so the error can be caught by "index.php"
		die(header("HTTP/1.1 404 Not Found"));
	}

	// The random double check (Potentially redundant, but poses no huge performance hinderance)
	if(isset($_FILES["designFileUpload"]))
	{
		$target_file_dest = $target_folder . basename($_FILES["designFileUpload"]["name"]);

		$file_tmp_name = $_FILES["designFileUpload"]["tmp_name"];

		// PHP gives notice: only variables should be passed in by reference
		// Which suggest that strtolower would modify the variable directly rather than making a copy
		// Not too big of an issue in this situation. Just something to consider.

		// Dev Update (8/18/2017): Strangely, PHP doesn't complain about it anymore
		$file_extension = strtolower(end(explode('.',$_FILES["designFileUpload"]["name"])));

		// A list of allowed file type extensions
		// Feel free to add more if needed 
		// This system does not discriminate against different races
		$allowed_extensions = array("png","jpeg","jpg");

		// Throws error if the file tpye isn't valid. 
		if(in_array($file_extension, $allowed_extensions) === false)
		{
			echo "Not allowed file type";
			die(header("HTTP/1.1 Foreign File Type Detected"));
		}
		else
		{
			// If the file does not get moved the target destination, check who has rights to the destination
			$uploadResult = move_uploaded_file($file_tmp_name,$target_file_dest);
			if($uploadResult)
			{
				echo "Upload Success. Upload Path: ".$target_file_dest."<br>";
			}
			else
			{
				echo "Upload Fail. Please check if apache has rights to the destination";
				die(header("HTTP/1.1 500 Internal Failure"));
			}

			// Super useful deployment troubleshooting tool once the system is ready for deployment.
			//echo exec('whoami'); Used to check is the owner of the apache process 

		}

		// File upload Successful. Proceed to adding record to MySQL database for queuing.
		// ================================================================
		// php file containing all information needed for mysql connection
		require "devServerConnection.php";

		// Check connection ($connection is provided by "devServerConnection.php")
		if(!$connection)
		{
			die ("Connection Failure".mysqli_connect_error());
		}

		
		$sql_query = "INSERT INTO `Queue_Table` VALUES (NULL,'".$_POST['designName']."','".$_POST['relatedProject']."','".$target_file_dest."',NULL,'".$_POST['author']."')";

		// Executes MySQL query to insert new record to MySQL table
		$result = mysqli_query($connection, $sql_query);

		if($result)
		{
			echo "New record was created succesfully";
		}

		else
		{
			die("Error: " . $sql_query . "<br>" . mysqli_error($connection));
		}

		// Close connection to the database
		mysqli_close($connection);
	}

	// Throws error if the life fails on you.
	else
	{
		die(header("HTTP/1.1 500 Internal Failure"));
	}
?>
