<?php
	
	$response = array();
	
	if(isset($_POST['tour_key'])) {
		$tour_key = $_POST['tour_key'];
		
		
		
		$result = pg_query($conn, "Select \"tour_key\", \"t_key\", \"ts_key\", \"Text\" as review, 
		\"Rate\" as rating, \"Date\" as date From \"All Reviews\" Where \"tour_key\" = $tour_key");
		
		if(!empty($result)) {
			if(pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				
				$response['reviews'] = array();
				
				$tour = array();
				$tour['tour_key'] = $row['tour_key'];
				$tour['t_key'] = $row['t_key'];
				$tour['ts_key'] = $row['ts_key'];
				$tour['review'] = $row['review'];
				$tour['rating'] = $row['rating'];
				$tour['date'] = $row['date'];
				
				$response['success'] = 1;
				
				array_push($response['reviews'], $tour);
				
				echo json_encode($response);
			} else {
				$response['success'] = 0;
				$response['message'] = "No tours found";
				
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
?>