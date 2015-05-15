<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_Email']) && isset($_POST['t_password']) && isset($_POST['t_FName']) && isset($_POST['t_LName']) && isset($_POST['t_address']) && isset($_POST['t_telephone'])) {
		$t_Email = $_POST['t_Email'];
		$password = $_POST['t_password'];
		$t_FName = $_POST['t_FName'];
		$t_LName = $_POST['t_LName'];
		$t_address = $_POST['t_address'];
		$t_telephone = $_POST['t_telephone'];
		$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
		$password = $password.$salt;//.$t_Email;
		$password = sha1($password);
		
		$verifCode = substr( md5(rand()), 0, 8);
		$verif = $verifCode;
		$verifCode = $verifCode.$salt;//.$t_Email;
		$verifCode = sha1($verifCode);
		
		
		
		$result = pg_query($conn, "Insert into \"Tourist\" (\"t_Email\",\"t_password\",\"t_FName\",\"t_LName\",\"t_telephone\",\"t_Address\",\"verification\") 
		Values('$t_Email','$password','$t_FName','$t_LName',$t_telephone,'$t_address','$verifCode') ");
		
		if($result) {
			
			$response['success'] = 1;
			$response['message'] = "Register Completed";
			
			$to      = $t_Email;
			$subject = 'Verify Email for No Middle Man';
			$message = "Please follow this link and use this code <b>".$verif."</b> to verify your account in No Middle Man<br><a href='http://kiwiteam.ece.uprm.edu/NoMiddleMan/website/verifyForm.php'>Verify email</a><br>If you are unable to click on the link, copy and paste it on the address bar.";
			$headers = 'From: luis.tavarez@outlook.com' . "\r\n" .
				'Reply-To: luis.tavarez@outlook.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion().' MIME-Version: 1.0'."\r\n".
				'Content-type: text/html; charset=utf-8' . "\r\n";
			mail($to, $subject, $message, $headers);
			
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