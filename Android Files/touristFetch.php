<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	$result = pg_query($conn, "SELECT * FROM \"Tourist\"");
		
		while($row = pg_fetch_assoc($result)) {
			$response[] = $row;
		}	
		echo json_encode($response); 
		
	pg_close($conn);
?>