<?php 
include_once("dbConnect.php");
if(isset($_GET))
{
	//var_dump($_GET);
	$tid = $_GET['tid'];
	$tsid = $_GET['ts_key'];
	$tkey = $_GET['t_key'];
	$query = pg_query($dbconn, "SELECT \"deactivate_review\"($tkey, $tsid)");
	header('Location: tour_page.php?tid='.$tid.'');
}

?>