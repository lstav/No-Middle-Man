<?php
include_once("dbConnect.php");
	session_start();
	if(isset($_SESSION['uid']))
	{
		$response = array();
	//var_dump($_POST);
	if(isset($_POST['largetid']) && isset($_POST['largeDate']) && isset($_POST['largeQuantity'])) {
		$t_key = $_SESSION['uid'];
		$tour_key = $_POST['largetid'];
		$quantity = $_POST['largeQuantity'];
		$day = strtotime($_POST['largeDate']);
		$day = date('Y-m-d', $day);
		//var_dump($day);
			
		$result = pg_query($dbconn, "Select \"BigGroup\"($t_key::bigint, $tour_key::bigint, '$day'::date, $quantity::integer)");
		
		if($result) {
			
			$response['success'] = 1;
			$response['message'] = "Added to cart";
			
			header("Location:cart.php");
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
		pg_close($dbconn);
	}
	else
	{
		header("Location: login.php");
	}
	
?>