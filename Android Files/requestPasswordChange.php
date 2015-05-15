<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if($_POST['t_Email']) {
		$email = $_POST['t_Email'];
		//$password = 123;
		$pass = substr( md5(rand()), 0, 8);
		$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
		$password = $pass.$salt;//.$email;
		$password = sha1($password);
			
		$result = pg_query($conn, "UPDATE \"Tourist\" as t SET \"t_password\" = '$password' 
		WHERE t.\"t_Email\" = '$email'");
		
		if(pg_affected_rows($result) > 0) {
			
			$response['success'] = 1;
			$response['message'] = "Password Changed";
			
			$to      = $email;
			$subject = 'Password Change Request for No Middle Man';
			$message = 'You have requested a new password for No Middle Man. Your password has been changed to 
						<b>'.$pass.'</b>';
			$headers = 'From: luis.tavarez@outlook.com' . "\r\n" .
				'Reply-To: luis.tavarez@outlook.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion().' MIME-Version: 1.0'."\r\n".
				'Content-type: text/html; charset=utf-8' . "\r\n";
			mail($to, $subject, $message, $headers);
			
			echo json_encode($response);
		} else {
			$response['success'] = 0;
			$response['message'] = "No emails found";
				
			echo json_encode($response);
		}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	pg_close($conn);
?>