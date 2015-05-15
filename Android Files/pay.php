<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key'])) {
		$t_key = $_POST['t_key'];
		
		$emailQuery = pg_query($conn,"Select \"t_Email\" FROM \"Tourist\" WHERE \"t_key\" = $t_key");
		$email = pg_fetch_array($emailQuery);
		$em = $email['t_Email'];
		
		$to = $em;
		$subject = 'No Middle Man: Invoice';
		$message = '
		<html>
		<body>';
			
		$message.='<p>Order Confirmed for:'."\r\n".'</p>';
		$message.='<table cellpadding="10"><tr><th></th><th>Tour Name</th><th>Date</th><th>Quantity</th><th>Total</th></tr>';
		
		$result = pg_query($conn, "Select T.ts_key as ts_key, T.p_quantity as quantity, T.\"total\" as total,
			T.\"tour_Name\" as tourname, T.\"s_Time\" as time FROM \"Shopping Cart\" as T 
		Where T.t_key=$t_key and T.\"s_isActive\" = True and T.\"passed\" = False and T.\"isfull\" = False");

		if(pg_num_rows($result) > 0) {
			//$response['tours'] = array();
			
			$ite = 1;
			$totaltotal =0;	
			
			while($row = pg_fetch_array($result)) {
				$ts_key = $row['ts_key'];
				$qty = $row['quantity'];
				$total = $row['total'];
				
				$tour = $row['tourname'];
				$time = $row['time'];
				
				$time = date("g:i A" , strtotime(substr($row['time'], 0, -3)));
				$date = date("M-d-Y", strtotime($row['time']));
				
				$message .= '<tr>
				<td>'.$ite.'. </td><td>'.$tour.'</td><td>'.$date.' '.$time.'</td><td>'.$qty.'</td>
				<td>'.$total.'</td></tr>';
				$totaltotal += substr($total,1);
				$ite+=1;
				
				$pay = pg_query($conn, "UPDATE \"Participants\" SET \"Payed\" = (\"Payed\" + $qty), \"p_quantity\" = 0, \"totalPayed\" = (\"totalPayed\" + '$total')
				WHERE \"t_key\" = $t_key  AND \"ts_key\" = $ts_key "); 
 				/*$quantity = pg_query(" SELECT \"p_quantity\" FROM \"Participants\"  WHERE \"t_key\" = $t_key  AND \"ts_key\" = $ts_key "); 
 				$row = pg_fetch_array($quantity); 
 				$qty = $row['p_quantity']; */
 				$tsupdt = pg_query($conn, " UPDATE \"Tour Session\" SET \"Availability\" = (\"Availability\" - $qty) WHERE \"ts_key\" = $ts_key "); 
				
				//array_push($response['tours'], $tour);
			}
			
			$message .= '</table><p>Total:  $'.$totaltotal.'</p></body></html>';
			$headers = 'From: luis.tavarez@outlook.com' . "\r\n" .
				'Reply-To: luis.tavarez@outlook.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion().' MIME-Version: 1.0'."\r\n".
				'Content-type: text/html; charset=utf-8' . "\r\n";

			mail($to, $subject, $message, $headers);
			
			$response['success'] = 1;
			echo json_encode($response);
		} else {
			$response['success'] = 0;
			$response['message'] = "No tours found on cart";
				
			echo json_encode($response);
		}
	} else {
		$response['success'] = 0;
		$response['message'] = "Required field(s) is missing";
		
		echo json_encode($response);
	}
	pg_close($conn);
?>