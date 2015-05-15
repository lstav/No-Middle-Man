<?php
	include_once("dbconnection.php");
	
	pg_query($conn, "SELECT \"DailyLookAhead\"()");
	
?>