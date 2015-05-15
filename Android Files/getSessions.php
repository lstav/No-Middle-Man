<?php
	
	$response = array();
	
	if(isset($_POST['tour_key'])) {
		$tour_key = $_POST['tour_key'];
		
		$result = pg_query($conn, "select \"ts_key\" as key, \"s_Time\" as time, \"Availability\" as availability
		from \"Tour Session\" where \"tour_key\" = $tour_key and \"s_isActive\" = true");
		
		if(!empty($result)) {
			if(pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				
				$response['session'] = array();
				
				$tour = array();
				$tour['key'] = $row['key'];
				$tour['time'] = date("g:i:s A" , strtotime($row['time']));
				$tour['date'] = date("M-d-Y", strtotime($row['time']));
				$tour['availability'] = $row['availability'];
				
				$response['success'] = 1;
				
				array_push($response['session'], $tour);
				
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