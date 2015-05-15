<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key']) && isset($_POST['tour_key']) && isset($_POST['quantity']) && isset($_POST['day'])) {
		$t_key = $_POST['t_key'];
		$tour_key = $_POST['tour_key'];
		$quantity = $_POST['quantity'];
		$day = $_POST['day'];
		
		$result = pg_query($conn, "Select \"BigGroup\"($t_key::bigint, $tour_key::bigint, '$day'::date, $quantity::integer)");
		
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