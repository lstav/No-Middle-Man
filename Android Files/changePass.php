<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['key']) && isset($_POST['password']) && isset($_POST['oldpass'])) {
		$key = $_POST['key'];
		//$res_email = "Select \"GetEmail\"(t_key::bigint)";
		
		//$email = $res_email['t_Email'];
		
		$password = trim($_POST['password']);
		$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
		$password = $password.$salt; //.$email;
		$password = sha1($password);
		
		$oldpassword = trim($_POST['oldpass']);
		$oldpassword = $oldpassword.$salt; //.$t_Email;
		$oldpassword = sha1($oldpassword);
		
		$oldpassver = pg_query($conn, "SELECT T.\"t_key\" as key, T.\"t_password\" as password FROM \"Active Tourist\" as T WHERE T.\"t_key\" = $key");
		
		if(!empty($oldpassver)) {
			
			if(pg_num_rows($oldpassver) > 0) {
				$row = pg_fetch_array($oldpassver);
					$tour = array();
					$tour['key'] = $row['key'];
					$tour['password'] = trim($row['password']);
					
					if($tour['password'] == $oldpassword) {
						$response['success'] = 1;
						//$response['key'] = $tour['key'];
						$result = pg_query($conn, "UPDATE \"Tourist\" as t SET \"t_password\" = '$password' WHERE t.\"t_key\" = $key");
		
						if($result) {
			
							$response['success'] = 1;
							$response['message'] = "Password Changed";
			
							echo json_encode($response);
						} else {
							$response['success'] = 0;
							$response['message'] = "No tours found";
				
							echo json_encode($response);
						}
							echo json_encode($response);
						} else {
							$response['success'] = 0;
							$response['message'] = "Wrong Login";
					
							echo json_encode($response);
						}
			} else {
				$response['success'] = 0;
				$response['message'] = "No login";
					
				echo json_encode($response);
			}	
		}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	pg_close($conn);
?>