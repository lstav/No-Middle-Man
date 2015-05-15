<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	$result = pg_query($conn, "SELECT * FROM \"Tour Category\" Order By \"Category_Name\" ASC");
	
	if(pg_num_rows($result) > 0) {
		
		$response['categories'] = array();
		
		while($row = pg_fetch_array($result)) {
			$category = array();
			$category['cat_key'] = $row['cat_key'];
			$category['Category_Name'] = $row['Category_Name'];
			
			array_push($response['categories'], $category);
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