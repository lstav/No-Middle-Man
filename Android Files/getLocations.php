<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	$result = pg_query($conn, "Select Distinct \"L_key\" as l_key, \"City\" as city, 
	\"State-Province\" as state, \"Country\" as country
	From \"Location\" NATURAL JOIN \"Tour\" Order By \"Country\",\"State-Province\",\"City\" ASC");
	
	if(pg_num_rows($result) > 0) {
		
		$response['locations'] = array();
		
		while($row = pg_fetch_array($result)) {
			$location = array();
			$location['l_key'] = $row['l_key'];
			$location['city'] = $row['city'];
			$location['state'] = $row['state'];
			$location['country'] = $row['country'];
			
			array_push($response['locations'], $location);
		}

		$response['success'] = 1;
		
		echo json_encode($response); 
	} else {
		$response["success"] = 0;
		$response["message"] = "No categories found";
		
		echo json_encode($response);
	}
	
	pg_close($conn);
?>