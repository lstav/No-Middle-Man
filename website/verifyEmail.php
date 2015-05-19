<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'header.php';?>
</head>
<body>
<?php include 'navbar.php';?>
<script>
$(document).ready(function(){
	$("#language").attr("style", "");
});
</script>
	
</body>
</html>

<?php
	
	$response = array();
	
	include_once("dbConnect.php");
	
	if(isset($_POST['t_Email']) && isset($_POST['t_password'])) {
		$t_Email = $_POST['t_Email'];
		$t_password = trim($_POST['t_password']);
		$salt = '6e663cc2478ebdc49cbce5609ba0305b60d10844';
		$t_password = $t_password.$salt;//.$t_Email;
		$t_password = sha1($t_password);	
		
		$result = pg_query($dbconn, "SELECT T.\"t_key\" as key, T.\"verification\" as password 
		FROM \"Tourist\" as T WHERE T.\"t_Email\" = '$t_Email'");
		
		if(!empty($result)) {
		
			if(pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
					$tour = array();
					$tour['key'] = $row['key'];
					$tour['password'] = trim($row['password']);
					
					if($tour['password'] == $t_password) {
						$response['success'] = 1;
						$response['key'] = $tour['key'];
						$key = $tour['key'];
						//$response['login'] = array();
						//array_push($response['login'], $tour);
						//echo json_encode($response);
						$verify = pg_query($dbconn, "UPDATE \"Tourist\" as t 
													SET \"t_isActive\" = True 
													WHERE t.\"t_key\" = $key");
						header("Location: login.php");
					} else {
						echo '<div class = "container"><h3>Email and code combination not found. Please try again <a href = "verifyForm.php">here</a></h3></div>';
					
						//echo json_encode($response);
					}
			} else {
				echo '<div class = "container"><h3>Email and code combination not found. Please try again <a href = "verifyForm.php">here</a></h3></div>';
					
				//echo json_encode($response);
			}
		} else {
			echo '<div class = "container"><h3>Email and code combination not found. Please try again <a href = "verifyForm.php">here</a></h3></div>';
				
			//echo json_encode($response);
		}
	} else {
		echo '<div class = "container"><h3>Email and code combination not found. Please try again <a href = "verifyForm.php">here</a></h3></div>';
		
		//echo json_encode($response);
	}
	pg_close($conn);
?>