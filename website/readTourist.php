<?php
include_once("dbConnect.php");
if($_GET['key'])
{
	$key = $_GET['key'];
	$query = pg_query("SELECT \"ReadTRep\"($key)");
	if($query)
	{
		header("Location: reports.php");
	}
}
 
?>