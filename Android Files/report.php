<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key']) && isset($_POST['message'])) {
		$t_key = $_POST['t_key'];
		$message = $_POST['message'];		
		
		$result = pg_query($conn, "Select \"ReportFromTourist\"($t_key::bigint, '$message'::text)");
		
		if($result) {
			
			$response['success'] = 1;
			$response['message'] = "Report Completed";
			
			echo json_encode($response);
		} else {
			$response['success'] = 0;
			$response['message'] = "No tours found";
				
			echo json_encode($response);
		}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	pg_close($conn);
?>