<?php
	
	$response = array();
	
	include_once("dbconnection.php");
	
	if(isset($_POST['t_key']) && isset($_POST['ts_key']) && isset($_POST['quantity'])) {
		$t_key = $_POST['t_key'];
		$ts_key = $_POST['ts_key'];
		$quantity = $_POST['quantity'];
		
		$result = pg_query($conn, "Select * From \"Participants\" Where \"t_key\" = $t_key and \"ts_key\" = $ts_key");
		
		$qty = 0;
		if(pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			$quantity = $quantity + $row['p_quantity'];
			$result = pg_query($conn, "UPDATE \"Participants\" as t SET \"p_quantity\" = $quantity, \"p_isActive\" = True 
			WHERE t.\"t_key\" = $t_key and t.\"ts_key\" = $ts_key
			Returning \"p_quantity\"");
			$quant = pg_fetch_array($result);
		
			$qty = $quant['p_quantity'];
			
		} else {
			$result = pg_query($conn, "Insert into \"Participants\" (\"t_key\",\"ts_key\",\"p_quantity\") Values($t_key,$ts_key,$quantity)");
			$qty = $quantity;
		}
		if($result) {
			
			$response['success'] = 1;
			$response['message'] = "Added to cart";
			$response['cartQty'] = $qty;
			$response['addedQty'] = $quantity;

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