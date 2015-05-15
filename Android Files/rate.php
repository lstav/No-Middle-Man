<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key']) && isset($_POST['ts_key']) && isset($_POST['review']) && isset($_POST['rate'])) {
		$t_key = $_POST['t_key'];
		$ts_key = $_POST['ts_key'];
		$review = $_POST['review'];
		$rate = $_POST['rate'];
			
		$result = pg_query($conn, "Insert into \"Review\" (\"t_key\",\"ts_key\",\"Text\",\"Rate\")
									Values($t_key,$ts_key,'$review',$rate)");
		
		if($result) {
			
			$response['success'] = 1;
			$response['message'] = "Tour Rated";
			
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