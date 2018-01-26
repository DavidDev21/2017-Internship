
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
	
	// SHOULD BE LOGICAL TO GO BACKWARDS (TO BE FIXED)
	// AT THE MOMENT (8/7/2017 1:25 PM EST), IT SHOULD MIRROR loadQueue.php
	
	// Dev Note (8/18/2017): "refreshTable.php" probably has a better description of these two variables
	// 						This and "loadPrevious.php"	does not do very interesting things
	$queueDisplay = $_POST['queueCounter'];
	$itemPerPage = $_POST['itemPerPage'];

	$noResult = False;
	// queueDisplay = will be the "starting row" for the MYSQL query
	$sql_query = "SELECT * FROM `Queue_Table` ORDER BY `Queue ID` ASC LIMIT ".$queueDisplay.",".$itemPerPage;
	$result = mysqli_query($connection,$sql_query);

	// Just show everything left Seems super redundant. (Doesn't let user to load more if there isn't anymore to load)
	if(mysqli_num_rows($result) == 0)
	{
		// This special check is needed in the case you are trying to load previous pages from the last page
		// (This check is duplicated in refreshTable.php)
		//echo $queueDisplay;
		if($queueDisplay < 0)
		{
			$queueDisplay = $queueDisplay + $itemPerPage;
			$noResult = True;
		}
		else
		{
			$queueDisplay = $queueDisplay - $itemPerPage;
		}
	}

	// NOTE TO SELF: THE SPECIAL CASE CHECK NEEDS TO BE APPLIED TO THESE BOTTOM CASES.
	if($noResult || $queueDisplay < 0)
	{
		echo "<script>";
		echo "alert(\"No more result to load\");";
		echo "queueItemCount = queueItemCount + ITEMS_PER_PAGE;";
		echo "</script>";

		// Safety procaution. Making sure the variable get reset.
		$noResult = False;
	}
	else if($queueDisplay != 0)
	{
		echo "<script>";
		echo "queueItemCount = queueItemCount - ITEMS_PER_PAGE;";
		echo "</script>";
	}


?>