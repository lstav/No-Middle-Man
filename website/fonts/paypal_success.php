<?php 
	session_start();
	include_once("dbConnect.php");
	if($_SESSION['uid'])
	{
		$t_key = $_SESSION['uid'];
		
		$result = pg_query($dbconn, "Select T.ts_key as ts_key, T.p_quantity as quantity FROM \"Shopping Cart\" as T 
		Where T.t_key=$t_key and T.\"s_isActive\" = True and T.\"passed\" = False and T.\"isfull\" = False");
		if(pg_num_rows($result) > 0) {
			//$response['tours'] = array();
			
			while($row = pg_fetch_array($result)) {
				$ts_key = $row['ts_key'];
				$qty = $row['quantity'];
				
				$pay = pg_query($dbconn, "UPDATE \"Participants\" SET \"Payed\" = (\"Payed\" + $qty), \"p_quantity\" = 0
				WHERE \"t_key\" = $t_key  AND \"ts_key\" = $ts_key "); 
 				/*$quantity = pg_query(" SELECT \"p_quantity\" FROM \"Participants\"  WHERE \"t_key\" = $t_key  AND \"ts_key\" = $ts_key "); 
 				$row = pg_fetch_array($quantity); 
 				$qty = $row['p_quantity']; */
 				$tsupdt = pg_query($dbconn, " UPDATE \"Tour Session\" SET \"Availability\" = (\"Availability\" - $qty) WHERE \"ts_key\" = $ts_key "); 
				
				//array_push($response['tours'], $tour);
			}
			
			$response['success'] = 1;
			echo json_encode($response);
		} else {
			$response['success'] = 0;
			$response['message'] = "No tours found on cart";
				
			echo json_encode($response);
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'header.php';?>
<script>
$(document).ready(function(){
	$("#language").attr("style", "");
});
</script>
</head>
<body>
<?php include 'navbar.php';?>
<h2>Thank you <?php echo $_SESSION['ufname'];?> !</h2>
<h2> Your order has been placed</h2>
<h4> Recent purchases are displayed in the Orders section of your account page</h4>
<div style="margin-right: 20px;margin-left: 20px;" class="list-group"> <?php echo $cartList;?></div>
</body>
</html>