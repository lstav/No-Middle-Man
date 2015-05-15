<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key'])) {
		$t_key = $_POST['t_key'];
		
		$result = pg_query($conn, "Select T.tour_key as Key, T.ts_key as ts_key, upper(T.\"tour_Name\") as Name, 
		T.\"tour_Desc\" as Description, T.\"total\" as Price, T.\"extremeness\" as Extremeness, 
		T.\"tour_photo\" as Photo, T.\"s_Time\" as Time, T.\"p_quantity\" as Qty, T.\"s_isActive\" as isactive, 
		T.\"passed\" as passed, T.\"isfull\" as isfull, T.\"g_Email\" as gEmail
		FROM \"Shopping Cart\" as T 
		Where \"t_key\"=$t_key and T.\"s_isActive\" = True and passed = False and isfull = False");
		
		if(pg_num_rows($result) > 0) {
			$response['tours'] = array();
			
			while($row = pg_fetch_array($result)) {
				$tour = array();
				$tour['key'] = $row['key'];
				$tour['ts_key'] = $row['ts_key'];
				$tour['name'] = $row['name'];
				$tour['price'] = $row['price'];
				$tour['extremeness'] = $row['extremeness'];
				$tour['photo'] = $row['photo'];
				
				$tour['isActive'] = $row['isactive'];
				
				if($row['passed'] == "t" || $row['full'] == "t") {
					$tour['isActive'] = "f";
				}
				
				//$tour['isCancelled'] = ($row['isactive'] and !$row['passed']);
				
				$tour['passed'] = $row['passed'];
				$tour['time'] = date("g:i:s A" , strtotime($row['time']));
				$tour['date'] = date("M-d-Y", strtotime($row['time']));
				$tour['quantity'] = $row['qty'];
				$tour['gEmail'] = $row['gemail'];
				
				array_push($response['tours'], $tour);
			}
			
			$response['success'] = 1;
			echo json_encode($response);
		} else {
			$response['success'] = 0;
			$response['message'] = "No tours found on checkout";
				
			echo json_encode($response);
		}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	pg_close($conn);
?>