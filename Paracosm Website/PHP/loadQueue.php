
<?php
	// File: loadPrevious.php
	// Author: David Zheng
	// Last Updated Date: 8/18/2017

	// File description: Just increase the queueItemCount as apprioately as possible so the pager can go forward
	// This has a terrible way of being one click ahead / behind of what it should be once refreshTable.php pick up the remaining work for the pager

	// BIG WARNING: comments on this file and "loadQueue.php"	may be poorly written as there is not much going on in this code
	//======================================================
	//				YOU HAVE BEEN WARNED
	//======================================================
	require "devServerConnection.php";
	
	
	// Dev Note (8/18/2017): "refreshTable.php" probably has a better description of these two variables
	// 						This and "loadPrevious.php"	does not do very interesting things
	$queueDisplay = $_POST['queueCounter'];
	$itemPerPage = $_POST['itemPerPage'];

	$noResult = False;
	// queueDisplay = will be the "starting row" for the MYSQL query
	$sql_query = "SELECT * FROM `Queue_Table` ORDER BY `Queue ID` ASC LIMIT ".$queueDisplay.",".$itemPerPage;
	$result = mysqli_query($connection,$sql_query);

	// offset is used to account for the out-of-range sql_queries that have no result
	// Only used when correcting the queueCounter value on javascript via php echo
	$offset = 0;

	// Just show everything left Seems super redundant. (Doesn't let user to load more if there isn't anymore to load)
	while(mysqli_num_rows($result) == 0)
	{
		++$offset;
		$noResult = True;

		// Performs the correction needed to ensure there is something to display
		$queueDisplay -= $itemPerPage;

		$sql_query = "SELECT * FROM `Queue_Table` ORDER BY `Queue ID` ASC LIMIT ".$queueDisplay.",".$itemPerPage;
		$result = mysqli_query($connection,$sql_query);
	}

	if($noResult)
	{
		echo "<script>";
		echo "alert(\"No more result to load\");";
		echo "queueItemCount = queueItemCount - (ITEMS_PER_PAGE * ".$offset.");";
		echo "</script>";

		// Safety procaution. Making sure the variable get reset.
		$noResult = False;
	}
	else
	{
		echo "<script>";
		echo "queueItemCount = queueItemCount + ITEMS_PER_PAGE;";
		echo "</script>";
	}
	mysqli_close($connection);


?>