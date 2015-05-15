<?php 

include_once("dbConnect.php");

if(isset($_GET['tour_key']))
{
	$tour_key  = $_GET['tour_key'];
 	$query = pg_query($dbconn, "SELECT \"deactivate_tours\"($tour_key)");
	if($query)
	{
		//var_dump($tour_key);
		header("Location: tour-guide-home.php");
	}
	else
	{
		echo "<h3> Operation could not be completed </h3>";
	}
}


?>