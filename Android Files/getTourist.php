<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_REQUEST['t_key'])) {
		$key = $_REQUEST['t_key'];
		
		$result = pg_query($conn, "SELECT * FROM \"Tourist\" as T WHERE \"t_key\"=$key");
		
		if(!empty($result)) {
			
			if(pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				
				$tourist = array();
				$tourist['t_key'] = $row['t_key'];
				$tourist['t_Email'] = $row['t_Email'];
				$tourist['t_password'] = $row['t_password'];
				$tourist['t_FName'] = $row['t_FName'];
				$tourist['t_LName'] = $row['t_LName'];
				$tourist['t_Address'] = $row['t_Address'];
				$tourist['t_telephone'] = $row['t_telephone'];
				
				$response['success'] = 1;
				$response['tourist'] = array();
				
				array_push($response['tourist'], $tourist);
				
				echo json_encode($response);
			} else {
				$response['success'] = 0;
				$response['message'] = "No tourist found";
				
				echo json_encode($response);
			}
		} else {
				$response['success'] = 0;
				$response['message'] = "No tourist found";
				
				echo json_encode($response);
			}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	} 
	pg_close($conn);
?>