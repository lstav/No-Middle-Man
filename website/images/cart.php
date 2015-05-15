<?php
session_start();
$checkOut = '';
$total = '';
$cartList = '';
$receiver = array(array());
	include_once("dbConnect.php");
	
	$uid = $_SESSION['uid'];
	$cquery = pg_query($dbconn, "SELECT * FROM \"Shopping Cart\"  NATURAL JOIN \"Location\" NATURAL JOIN \"Tour Guide\" WHERE t_key = $uid");
	$count = pg_num_rows($cquery);
	$totalPrice = '';
	if($count == 0)
	{
		$cartList = 'No items in cart';
	}
	else
	{
		$receiver = array(array());
		$totalPrice = 0;
		$totalRPrice = array();
		$i = 0;
		while($row = pg_fetch_array($cquery))
		{
			$tname = $row['tour_Name'];
			$tdescription = $row['tour_Desc'];
			$tid = $row['tour_key'];
			$tprice = $row['Price'];
			$quantity = $row['p_quantity'];
			$totalPrice += $quantity*(float)preg_replace("/([^0-9\\.])/i", "", $tprice);
			$tcity = $row['City'];
			$tstate = $row['State-Province'];
			//$cid = $row['cid'];
			$ts_key = $row['ts_key'];
			$tgkey = $row['g_key'];
			$tgemail = $row['g_Email'];
			$reserved_time = date("F/d/Y g:i a" , strtotime(substr($row['s_Time'], 0, -3)));
			$receiver[$tgkey]['email'] = $tgemail;
			$receiver[$tgkey]['tskey']  = $ts_key;
			$rprice = $quantity*(float)preg_replace("/([^0-9\\.])/i", "", $tprice);
			$totalRPrice[$tgkey] = 0;
			$totalRPrice[$tgkey] = (float)$totalRPrice[$tgkey] + (float)$rprice;
			$receiver[$tgkey]['total'] = $totalRPrice[$tgkey];
			$i++;
			$cartList .= '<article class="search-result row">
			<div class="col-xs-12 col-sm-12 col-md-3">
				<a title="Lorem ipsum" class="thumbnail" href="tour_page.php?tid='.$tid.'"><img src="images/'.$tid.'/1.jpg" alt="Lorem ipsum"></a>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-2">
				<ul class="meta-search">
					<li><span><h7>'.$tcity.'</h7></span></li>
					<li> <span>'.$tstate.'</span></li>
				</ul>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-7 excerpet">
				<h3><a href="tour_page.php?tid='.$tid.' title="">'.$tname.'</a></h3>
				<p>'.$tdescription.'</p>
				<h4><strong>Reserved time: '.$reserved_time.'</strong></h4>					
                <span style="text-align : right"><h4>'.$tprice.'</h4></span>
				<h4>Party of: '.$quantity.'</h4>
				<a style="float:right; color: white; background-color: #248dc1" type="button" class="btn btn-default btn-md" href = "remove_from_cart.php?t_key='.$uid.'&ts_key='.$ts_key.'">
  <span class="glyphicon glyphicon-minus" aria-hidden="true"></span></a>
			</div>
			<span class="clearfix borda"></span>
		</article>';
			$_SESSION['trankey'] = $ts_key;
		}
		
		$_SESSION['receiver'] = $receiver;
		$_SESSION['defaultreceiver'] = 'skydiving@test.com';
		$_SESSION['receivertotal'] = $totalPrice * 0.9;
		$_SESSION['nmmfee'] = $totalPrice *0.1;
		
		//setlocale(LC_MONETARY, 'en_US');
		//$totalPrice =  money_format('%(#10n', $totalPrice);
		
		$checkOut = '<div style = "float: right"> <h3> Total price: $'.$totalPrice.'</h3><form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
<!-- Saved buttons use the "secure click" command -->
<input type="hidden" name="cmd" value="_xclick">
<!-- Saved buttons are identified by their button IDs -->
<input type="hidden" name="business" value="joe_a_virella-facilitator@live.com">
<input type="hidden" name="item_name" value="No Middle Man">
<input type="hidden" name="amount" value="'.$totalPrice.'">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="return" value="paypal_success.php?uid ='.$uid.'">
<input type="hidden" name="cancel_return" value="paypal_cancel.php">

<!-- Saved buttons display an appropriate button image. -->
<input type="image" name="submit" border="0"
src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png"
alt="PayPal - The safer, easier way to pay online">
<img alt="" border="0" width="1" height="1"
src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png" >

</form> </div>';
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
<h2>Shopping Cart </h2>
<div style="margin-right: 20px;margin-left: 20px;" class="list-group"> <?php echo $cartList;?></div>
<?php echo $checkOut;?>
</body>
</html>