<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key']) && isset($_POST['ts_key']) && isset($_POST['Quantity'])) {
		$t_key = $_POST['t_key'];
		$ts_key = $_POST['ts_key'];
		$Quantity = $_POST['Quantity'];
		
		$result = pg_query($conn, "Delete from \"Participants\" 
			Where P.\"$t_key\"=$t_key and P.\"$ts_key\"=$ts_key ");
		
		if($result) {
			
			$response['success'] = 1;
			$response['message'] = "Added to cart";
			
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