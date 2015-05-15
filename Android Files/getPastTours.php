<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key'])) {
		$keyword = $_POST['t_key'];
		
		$result = pg_query($conn, "Select T.tour_key as key, 
		T.ts_key as ts_key, 
		upper(T.\"tour_Name\") as Name, 
		T.\"extremeness\" as Extremeness, 
		T.\"tour_photo\" as Photo, 
		T.\"Price\" as price,
		T.\"s_Time\" as Time, 
		T.\"Payed\" as Qty, 
		T.\"s_isActive\" as isactive,
		T.\"total\" as total
		FROM \"Past Tour\" as T 
		Where \"t_key\"=$keyword
		Order By T.\"s_Time\" DESC");
		
		if(pg_num_rows($result) > 0) {
			$response['tours'] = array();
			
			while($row = pg_fetch_array($result)) {
				$tour = array();
				$tour['key'] = $row['key'];
				$tour['ts_key'] = $row['ts_key'];
				$tour['name'] = $row['name'];
				$tour['extremeness'] = $row['extremeness'];
				$tour['photo'] = $row['photo'];
				$tour['price'] = $row['price'];
				$tour['total'] = $row['total'];
				
				$tour['isActive'] = $row['isactive'];
				
				$ts_key = $tour['ts_key'];
				$rated = pg_query($conn, "Select * 
						From \"Review\"
						Where t_key = $keyword and ts_key = $ts_key");
				
				if(pg_num_rows($rated) > 0) {
					$tour['isRated'] = "t";
				} else {
					$tour['isRated'] = "f";
				}
				
				$tour['time'] = date("g:i A" , strtotime(substr($row['time'], 0, -3)));
				$tour['date'] = date("M-d-Y", strtotime($row['time']));
				$tour['quantity'] = $row['qty'];
				
				array_push($response['tours'], $tour);
			}
			
			$response['success'] = 1;
			echo json_encode($response);
		} else {
			$response['success'] = 0;
			$response['message'] = "No past tours";
				
			echo json_encode($response);
		}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	pg_close($conn);
?>